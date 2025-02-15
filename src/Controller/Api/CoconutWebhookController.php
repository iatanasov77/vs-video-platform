<?php namespace App\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Psr\Log\LoggerInterface;

use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;

use Sylius\Component\Resource\Repository\RepositoryInterface;

use Vankosoft\ApplicationBundle\Component\Status;
use Vankosoft\UsersBundle\Component\UserNotifications;
use Vankosoft\ApiBundle\Security\ApiManager;
use App\Component\VideoClipMaker;
use App\Entity\CoconutJob;

class CoconutWebhookController extends AbstractController
{
    /** @var ManagerRegistry */
    private $doctrine;
    
    /** @var MailerInterface */
    private $mailer;
    
    /** @var UserNotifications */
    private $notifications;
    
    /** @var RepositoryInterface */
    private $coconutJobsRepository;
    
    /** @var ApiManager */
    private $apiManager;
    
    /** @var LoggerInterface */
    private $logger;
    
    /** @var VideoClipMaker */
    private $clipMaker;
    
    public function __construct(
        ManagerRegistry $doctrine,
        MailerInterface $mailer,
        UserNotifications $notifications,
        RepositoryInterface $coconutJobsRepository,
        ApiManager $apiManager,
        LoggerInterface $logger,
        VideoClipMaker $clipMaker
    ) {
        $this->doctrine                 = $doctrine;
        $this->mailer                   = $mailer;
        $this->notifications            = $notifications;
        $this->coconutJobsRepository    = $coconutJobsRepository;
        $this->apiManager               = $apiManager;
        $this->logger                   = $logger;
        $this->clipMaker                = $clipMaker;
    }
    
    public function notification( string $apiToken, Request $request ): JsonResponse
    {
        $this->logger->info( 'Coconut Webhook Requested From "{remoteAddr}", Referer: "{referer}"', [
            'remoteAddr'    => $request->getClientIp(),
            'referer'       => $request->headers->get( 'referer' )
        ]);
        
        // FUCKING WORKAROUND
        $apiToken					= \rtrim( $apiToken, '.' );
        
        $refreshTokeknsRepository   = $this->doctrine->getRepository( \App\Entity\Api\RefreshToken::class );
        $token                      = $refreshTokeknsRepository->findOneBy( ['refreshToken' => $apiToken] );
        //var_dump( $apiToken ); die;
        //var_dump( $token ); die;
        
        if ( ! $token ) {
            $response   = [
                'status'    => Status::STATUS_ERROR,
                'message'   => 'Invalid API Token !!!',
            ];
            
            return new JsonResponse( $response );
        }
        
        $coconutData        = $request->getContent();
        $coconutDataDecoded = \json_decode( $coconutData, true );
        
        if ( isset( $coconutDataDecoded['event'] ) ) {
            $this->logger->info( 'Coconut Webhook - Job ID: "{jobId}", Event: "{coconutEvent}"', [
                'jobId'         => $coconutDataDecoded['job_id'],
                'coconutEvent'  => $coconutDataDecoded['event']
            ]);
            
            $job = $this->updateJob( $coconutData );
            $this->sendNotification( $coconutData );
            //$this->sendEmail( $data, $contactEmail );
            
            if ( $coconutDataDecoded['event'] == CoconutJob::EVENT_JOB_COMPLETED ) {
                $this->doctrine->getManager()->remove( $token );
                $this->doctrine->getManager()->flush();
                
                $this->createVideoClip( $job );
            }
        }
        
        $response   = [
            'status'    => Status::STATUS_OK,
            'message'   => 'Coconut Notification Success !!!',
        ];
        
        return new JsonResponse( $response );
    }
    
    private function updateJob( $data ): CoconutJob
    {
        $dataDecoded    = \json_decode( $data, true );
        
        $coconutJob         = $this->coconutJobsRepository->findOneBy( ['jobId' => $dataDecoded['job_id']] );
        $coconutJob->setStatus( $dataDecoded['event'] );
        
        $coconutJob->setJobData( $data );
        
        $this->doctrine->getManager()->persist( $coconutJob );
        $this->doctrine->getManager()->flush();
        
        return $coconutJob;
    }
    
    private function sendNotification( $data )
    {
        $dataDecoded    = \json_decode( $data, true );
        
        $this->notifications->sentNotificationByRole( 'role-super-admin', 'Coconut Webhook', $dataDecoded['event'], $data );
    }
    
    private function sendEmail( $data, $contactEmail )
    {
        $email = ( new TemplatedEmail() )
                ->from( $data['email'] )
                ->to( $contactEmail )
                ->subject( 'You have Contact Email From Vankosoft.Org' )
                ->htmlTemplate( 'email/contact_email.html.twig' )
                ->context([
                    'fromName'      => $data['name'],
                    'emailSubject'  => $data['subject'],
                    'emailBody'     => $data['message'],
                ]);
        
        $this->mailer->send( $email );
    }
    
    private function createVideoClip( CoconutJob $job )
    {
        $jobData    = $job->getJobData();
        if ( ! is_array( $jobData ) ) {
            return;
        }
        
        foreach( $jobData['outputs'] as $output ) {
            if( $output['key'] == 'mp4:576p' ) {
                $videoUri = $output['url'];
            }
        }
        
        if ( isset( $videoUri ) ) {
            $this->clipMaker->createVideoClip( $job->getVideo(), $videoUri );
        }
    }
}