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
    
    public function __construct(
        ManagerRegistry $doctrine,
        MailerInterface $mailer,
        UserNotifications $notifications,
        RepositoryInterface $coconutJobsRepository,
        ApiManager $apiManager,
        LoggerInterface $logger
    ) {
        $this->doctrine                 = $doctrine;
        $this->mailer                   = $mailer;
        $this->notifications            = $notifications;
        $this->coconutJobsRepository    = $coconutJobsRepository;
        $this->apiManager               = $apiManager;
        $this->logger                   = $logger;
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
            
            $this->updateJob( $coconutData );
            $this->sendNotification( $coconutData );
            //$this->sendEmail( $data, $contactEmail );
            
            if ( $coconutDataDecoded['event'] == 'job.completed' ) {
                $this->doctrine->getManager()->remove( $token );
                $this->doctrine->getManager()->flush();
            }
        }
        
        $response   = [
            'status'    => Status::STATUS_OK,
            'message'   => 'Coconut Notification Success !!!',
        ];
        
        return new JsonResponse( $response );
    }
    
    private function updateJob( $data )
    {
        $dataDecoded    = \json_decode( $data, true );
        
        $coconutJob         = $this->coconutJobsRepository->findOneBy( ['jobId' => $dataDecoded['job_id']] );
        $coconutJob->setStatus( $dataDecoded['event'] );
        
        $coconutJob->setJobData( $data );
        
        $this->doctrine->getManager()->persist( $coconutJob );
        $this->doctrine->getManager()->flush();
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
}