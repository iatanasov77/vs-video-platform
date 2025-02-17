<?php namespace App\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Psr\Log\LoggerInterface;

use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;

use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;

use Vankosoft\ApplicationBundle\Component\Status;
use Vankosoft\UsersBundle\Component\UserNotifications;
use Vankosoft\ApiBundle\Security\ApiManager;
use App\Component\VideoClipMaker;
use App\Component\Cloud\Coconut\Coconut;
use App\Entity\CoconutVideoJob;
use App\Entity\CoconutClipJob;

class CoconutWebhookController extends AbstractController
{
    /** @var ManagerRegistry */
    private $doctrine;
    
    /** @var MailerInterface */
    private $mailer;
    
    /** @var UserNotifications */
    private $notifications;
    
    /** @var RepositoryInterface */
    private $coconutVideoJobsRepository;
    
    /** @var RepositoryInterface */
    private $coconutClipJobsRepository;
    
    /** @var FactoryInterface */
    private $videoClipFactory;
    
    /** @var ApiManager */
    private $apiManager;
    
    /** @var LoggerInterface */
    private $logger;
    
    /** @var VideoClipMaker */
    private $clipMaker;
    
    public function __construct(
        LoggerInterface $logger,
        ManagerRegistry $doctrine,
        MailerInterface $mailer,
        UserNotifications $notifications,
        RepositoryInterface $coconutVideoJobsRepository,
        RepositoryInterface $coconutClipJobsRepository,
        FactoryInterface $videoClipFactory,
        ApiManager $apiManager,
        VideoClipMaker $clipMaker
    ) {
        $this->logger                       = $logger;
        $this->doctrine                     = $doctrine;
        $this->mailer                       = $mailer;
        $this->notifications                = $notifications;
        $this->coconutVideoJobsRepository   = $coconutVideoJobsRepository;
        $this->coconutClipJobsRepository    = $coconutClipJobsRepository;
        $this->videoClipFactory             = $videoClipFactory;
        $this->apiManager                   = $apiManager;
        $this->clipMaker                    = $clipMaker;
    }
    
    public function videoJobNotification( string $apiToken, Request $request ): JsonResponse
    {
        $this->logger->info( 'Coconut Video Job Webhook Requested From "{remoteAddr}", Referer: "{referer}"', [
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
            
            $job = $this->updateVideoJob( $coconutData );
            $this->sendNotification( $coconutData );
            //$this->sendEmail( $data, $contactEmail );
            
            if ( $coconutDataDecoded['event'] == Coconut::EVENT_JOB_COMPLETED ) {
                /** Dont Remove Refresh Token. It's needed for more than one Job
                $this->doctrine->getManager()->remove( $token );
                $this->doctrine->getManager()->flush();
                */
                
                //$this->createVideoClip( $job );
            }
        }
        
        $response   = [
            'status'    => Status::STATUS_OK,
            'message'   => 'Coconut Notification Success !!!',
        ];
        
        return new JsonResponse( $response );
    }
    
    public function clipJobNotification( string $apiToken, Request $request ): JsonResponse
    {
        $this->logger->info( 'Coconut Clip Job Webhook Requested From "{remoteAddr}", Referer: "{referer}"', [
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
            
            $job = $this->updateClipJob( $coconutData );
            $this->sendNotification( $coconutData );
            //$this->sendEmail( $data, $contactEmail );
            
            if ( $coconutDataDecoded['event'] == Coconut::EVENT_JOB_COMPLETED ) {
                $video      = $job->getVideo();
                $videoClip  = $this->videoClipFactory->createNew();
                
                $videoClip->setVideo( $video );
                $videoClip->setType( $video->getVideoFile()->getType() );
                $videoClip->setPath( $video->getVideoFile()->getPath() );
                $videoClip->setOriginalName( $video->getVideoFile()->getOriginalName() );
                
                $this->doctrine->getManager()->persist( $videoClip );
                $this->doctrine->getManager()->flush();
            }
        }
        
        $response   = [
            'status'    => Status::STATUS_OK,
            'message'   => 'Coconut Notification Success !!!',
        ];
        
        return new JsonResponse( $response );
    }
    
    private function updateVideoJob( $data ): CoconutVideoJob
    {
        $dataDecoded    = \json_decode( $data, true );
        
        $coconutJob         = $this->coconutVideoJobsRepository->findOneBy( ['jobId' => $dataDecoded['job_id']] );
        $coconutJob->setStatus( $dataDecoded['event'] );
        
        $coconutJob->setJobData( $data );
        
        $this->doctrine->getManager()->persist( $coconutJob );
        $this->doctrine->getManager()->flush();
        
        return $coconutJob;
    }
    
    private function updateClipJob( $data ): CoconutClipJob
    {
        $dataDecoded    = \json_decode( $data, true );
        
        $coconutJob         = $this->coconutClipJobsRepository->findOneBy( ['jobId' => $dataDecoded['job_id']] );
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
    
    private function createVideoClip( CoconutVideoJob $job )
    {
        $jobData    = $job->getJobData();
        if ( ! is_array( $jobData ) ) {
            return;
        }
        
        foreach( $jobData['outputs'] as $output ) {
            if( $output['key'] == 'mp4:576p' ) {
                $bucket = $this->s3CoconutOutputAdapter->getBucket();
                $key    = \sprintf( 'video-%s-%s.mp4', $job->getVideo()->getId(), '576p' );
                
                $cmd    = $this->s3CoconutOutputAdapter->getS3Client()->getCommand( 'GetObject', [
                    'Bucket'    => $bucket,
                    'Key'       => $key
                ]);
                
                $request = $this->s3CoconutOutputAdapter->getS3Client()->createPresignedRequest( $cmd, '+1440 minutes' );
                $videoUri   = (string)$request->getUri();
            }
        }
        
        if ( isset( $videoUri ) ) {
            $this->clipMaker->createVideoClip( $job->getVideo(), $videoUri );
        }
    }
}