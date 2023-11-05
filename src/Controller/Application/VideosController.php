<?php namespace App\Controller\Application;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\HttpFoundation\HeaderUtils;

use Doctrine\Persistence\ManagerRegistry;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;

use Vankosoft\UsersBundle\Security\SecurityBridge;
use App\Component\VideoPlatform;

class VideosController extends AbstractController
{
    use GlobalFormsTrait;
    
    /** @var ManagerRegistry */
    protected ManagerRegistry $doctrine;
    
    /** @var SecurityBridge */
    protected SecurityBridge $securityBridge;
    
    /** @var EntityRepository */
    protected EntityRepository $videosRepository;
    
    /** @var VideoPlatform */
    protected VideoPlatform $videoPlatform;
    
    public function __construct(
        ManagerRegistry $doctrine,
        SecurityBridge $securityBridge,
        EntityRepository $videosRepository,
        VideoPlatform $videoPlatform
    ) {
        $this->doctrine             = $doctrine;
        $this->securityBridge       = $securityBridge;
        $this->videosRepository     = $videosRepository;
        $this->videoPlatform        = $videoPlatform;
    }
    
    /**
     * Read a video file from storage dir
     *
     * examples: https://ourcodeworld.com/articles/read/329/how-to-send-a-file-as-response-from-a-controller-in-symfony-3
     */
    public function read( $id, Request $request ): Response
    {
        $oVideo = $this->videosRepository->find( $request->attributes->get( 'id' ) );
        
        if ( ! $this->checkHasAccess( $oVideo ) ) {
            return $this->redirectToRoute( 'app_video_player_access_denied' );
        }
        
        $oFile      = $oVideo->getVideoFile();
        //$fileStream = $this->videoPlatform->getVideoStream( $oFile ); die;
        $response   = new StreamedResponse( function() use ( $oFile )
        {
            $outputStream   = \fopen( 'php://output', 'wb' );
            $fileStream     = $this->videoPlatform->getVideoStream( $oFile, 'videos-original' );
            
            while ( ! feof( $fileStream ) ) \fwrite( $outputStream, \fread( $fileStream, 1000000 ) );
        });
        
        $transliterator = \Transliterator::create( 'Any-Latin' );
        $transliteratorToASCII = \Transliterator::create( 'Latin-ASCII' );
        $originalName   = $transliteratorToASCII->transliterate( $transliterator->transliterate( $oFile->getOriginalName() ) );
        //var_dump( $originalName ); die;
        
        $disposition    = HeaderUtils::makeDisposition(
            HeaderUtils::DISPOSITION_ATTACHMENT,
            $originalName
        );
        
        $response->headers->set( 'Content-Type', $oFile->getType() );
        $response->headers->set( 'Content-Disposition', $disposition );
        
        return $response;
    }
    
    public function readTranscoded( $id, $format, Request $request ): Response
    {
        $oVideo = $this->videosRepository->find( $request->attributes->get( 'id' ) );
        
        if ( ! $this->checkHasAccess( $oVideo ) ) {
            return $this->redirectToRoute( 'app_video_player_access_denied' );
        }
        
        $oFile      = $oVideo->getVideoFile();
        //$fileStream = $this->videoPlatform->getCoconutOutputStream( $id, $format );
        
        $response   = new StreamedResponse( function() use ( $id, $format )
        {
            $outputStream   = \fopen( 'php://output', 'wb' );
            $fileStream     = $this->videoPlatform->getCoconutOutputStream( $id, $format );
            
            while ( ! feof( $fileStream ) ) \fwrite( $outputStream, \fread( $fileStream, 1000000 ) );
        });
        
        $disposition    = HeaderUtils::makeDisposition(
            HeaderUtils::DISPOSITION_ATTACHMENT,
            $oFile->getOriginalName()
        );
        
        $response->headers->set( 'Content-Type', $oFile->getType() );
        $response->headers->set( 'Content-Disposition', $disposition );
        
        return $response;
    }
}
