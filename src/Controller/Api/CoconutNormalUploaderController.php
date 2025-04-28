<?php namespace App\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

use Symfony\Component\Mailer\MailerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Psr\Log\LoggerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use League\Flysystem\Filesystem;
use Vankosoft\UsersBundle\Component\UserNotifications;
use Vankosoft\ApplicationBundle\Component\Status;
use App\Controller\Traits\RefreshTokenTrait;

class CoconutNormalUploaderController extends AbstractController
{
    use RefreshTokenTrait;
    
    /** @var LoggerInterface */
    private $logger;
    
    /** @var ManagerRegistry */
    private $doctrine;
    
    /** @var MailerInterface */
    private $mailer;
    
    /** @var RepositoryInterface */
    private $videosRepository;
    
    /** @var Filesystem */
    private $filesystem;
    
    /** @var UserNotifications */
    private $notifications;
    
    public function __construct(
        LoggerInterface $logger,
        ManagerRegistry $doctrine,
        MailerInterface $mailer,
        RepositoryInterface $videosRepository,
        Filesystem $filesystem,
        UserNotifications $notifications
    ) {
        $this->logger           = $logger;
        $this->doctrine         = $doctrine;
        $this->mailer           = $mailer;
        $this->videosRepository = $videosRepository;
        $this->filesystem       = $filesystem;
        $this->notifications    = $notifications;
    }
    
    public function storeVideoClip( $videoId, $coconutFile, Request $request ): JsonResponse
    {
        $coconutData        = $request->getContent();
        $coconutDataDecoded = \json_decode( $coconutData, true );
        
        $this->notifications->sentNotificationByRole( 'role-super-admin', 'Coconut Storage', 'coconut-storage-debug', $coconutDataDecoded );
        if ( ! $coconutDataDecoded ) {
            return new JsonResponse([
                'status'    => Status::STATUS_ERROR,
            ]);
        }
        
        $video  = $this->videosRepository->find( $videoId );
        $stream = \fopen( $coconutDataDecoded['input']['url'], 'r' );
        
        //$this->filesystem->writeStream( $coconutFile, $stream );
        $this->filesystem->writeStream( $video->getVideoFile()->getPath(), $stream );
        \fclose( $stream );
        
        return new JsonResponse([
            'status'    => Status::STATUS_OK,
        ]);
    }
}
