<?php namespace App\Controller\AdminPanel;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
//use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\Resource\Doctrine\Persistence\RepositoryInterface;
use FFMpeg\FFProbe;
use App\Component\VideoPlatform;

class TestFfmpegController extends AbstractController
{
    /** @var VideoPlatform */
    private $videoPlatform;
    
    /** @var RepositoryInterface */
    private $videosRepository;
    
    /** @var FFProbe */
    private $ffprobe;
    
    public function __construct(
        VideoPlatform $videoPlatform,
        RepositoryInterface $videosRepository,
        FFProbe $ffprobe
    ) {
        $this->videoPlatform    = $videoPlatform;
        $this->videosRepository = $videosRepository;
        $this->ffprobe          = $ffprobe;
    }
    
    public function index( Request $request ): Response
    {
        $storageSettings    = $this->videoPlatform->getOriginalVideosStorage()->getSettings();
        $video              = $this->videosRepository->findAll()[0];
        
        $filmDuration   = $this->ffprobe->streams(
            $this->videoPlatform->getVideoUri( $video->getVideoFile(), $storageSettings['bucket'] )
        )->videos()->first()->get( 'duration' );
        
        return $this->render( 'Pages/TestPages/test_ffmpeg.html.twig', [
            'video'     => $video,
            'duration'  => $filmDuration,
        ]);
    }
}
