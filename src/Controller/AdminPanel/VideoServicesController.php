<?php namespace App\Controller\AdminPanel;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\Persistence\ManagerRegistry;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Liip\ImagineBundle\Imagine\Cache\CacheManager;

use App\Component\VideoPlayer\Domain\VideoProviderRequest;
use App\Component\VideoPlayer\VideoService;
use App\Component\Cloud\Coconut;

class VideoServicesController extends AbstractController
{
    /** @var ManagerRegistry */
    private ManagerRegistry $doctrine;
    
    /** @var RepositoryInterface */
    private $videosRepository;
    
    /** @var VideoService */
    private $videos;
    
    /** @var Coconut */
    private Coconut $coconut;
    
    /** @var CacheManager */
    private $imagineCacheManager;
    
    public function __construct(
        ManagerRegistry $doctrine,
        RepositoryInterface $videosRepository,
        //VideoService $videos,
        Coconut $coconut,
        CacheManager $imagineCacheManager
    ) {
        $this->doctrine             = $doctrine;
        $this->videosRepository     = $videosRepository;
        //$this->videos               = $videos;
        $this->coconut              = $coconut;
        $this->imagineCacheManager  = $imagineCacheManager;
    }
    
    public function coconutJobStatus( $videoId, Request $request ): Response
    {
        $video              = $this->videosRepository->find( $videoId );
        $coconutJob         = $video->getCoconutJob();
        $coconutJobStatus   = $this->coconut->getStatus( $coconutJob->getJobId() );
        
        if ( $coconutJobStatus->status != $coconutJob->getStatus() ) {
            $coconutJob->setStatus( $coconutJobStatus->status );
            $coconutJob->setJobData( \json_encode( $coconutJobStatus ) );
            $this->doctrine->getManager()->persist( $coconutJob );
            $this->doctrine->getManager()->flush();
        }
        
        return new JsonResponse( $coconutJobStatus );
    }
    
    /**
     * THE OLD WAY
     * 
     * @param unknown $id
     * @param Request $request
     * @return Response
     */
    public function previewVideoByProvider( $id, Request $request ): Response
    {
        $videoRequest   = new VideoProviderRequest( VideoService::REQUEST_COMMAND_GET_A_VIDEO, [
            'video_id'  => $id,
        ]);
        $videos     = $this->videos->videoList( $videoRequest );
        $player     = $this->videos->render( $this->videos->first() );
        
        return $this->render(
            'admin-panel/pages/VideoServices/video-preview-by-provider.html.twig', [
                'player'            => $player,
            ]
        );
    }
    
    /**
     * THE NEW WAY
     * 
     * @param unknown $id
     * @param Request $request
     * @return Response
     */
    public function previewVideoDirectly( $id, Request $request ): Response
    {
        $video  = $this->videosRepository->find( $id );
        
        $thumbnailPath  = $video->getVideoThumbnail()->getPath();
        $thumbnailUrl   = $this->imagineCacheManager->getBrowserPath( $thumbnailPath, 'video_thumbnail' );
        
        return $this->render(
            'admin-panel/pages/VideoServices/video-preview-directly.html.twig', [
                'video'         => $video,
                'thumbnailUrl'  => $thumbnailUrl,
            ]
        );
    }
}