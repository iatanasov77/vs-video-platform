<?php namespace App\Controller\AdminPanel;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\Persistence\ManagerRegistry;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Liip\ImagineBundle\Imagine\Cache\CacheManager;

use Vankosoft\ApplicationBundle\Component\Status;
use App\Component\Cloud\Exception\Coconut\JobNotFoundException;
use App\Component\Cloud\Coconut\CoconutVideoJobBuilder;

class VideoServicesController extends AbstractController
{
    /** @var ManagerRegistry */
    private $doctrine;
    
    /** @var RepositoryInterface */
    private $videosRepository;
    
    /** @var CoconutVideoJobBuilder */
    private $coconut;
    
    /** @var CacheManager */
    private $imagineCacheManager;
    
    public function __construct(
        ManagerRegistry $doctrine,
        RepositoryInterface $videosRepository,
        CoconutVideoJobBuilder $coconut,
        CacheManager $imagineCacheManager
    ) {
        $this->doctrine             = $doctrine;
        $this->videosRepository     = $videosRepository;
        $this->coconut              = $coconut;
        $this->imagineCacheManager  = $imagineCacheManager;
    }
    
    public function coconutJobStatus( $videoId, Request $request ): Response
    {
        $video              = $this->videosRepository->find( $videoId );
        $coconutJob         = $video->getCoconutVideoJob();
        
        try {
            $coconutJobStatus   = $this->coconut->getStatus( $coconutJob->getJobId() );
            
            if ( $coconutJobStatus->status != $coconutJob->getStatus() ) {
                $coconutJob->setStatus( $coconutJobStatus->status );
                $coconutJob->setJobData( \json_encode( $coconutJobStatus ) );
                $this->doctrine->getManager()->persist( $coconutJob );
                $this->doctrine->getManager()->flush();
            }
            
            return new JsonResponse([
                'status'    => Status::STATUS_OK,
                'data'      => $coconutJobStatus
            ]);
        } catch ( JobNotFoundException $e ) {
            return new JsonResponse([
                'status'    => Status::STATUS_ERROR,
                'message'   => $e->getMessage()
            ]);
        } 
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
        
        $thumbnail      = $video->getPhoto( 'video_thumbnail' );
        $thumbnailUrl   = '';
        
        if ( $thumbnail ) {
            $thumbnailPath  = $thumbnail->getPath();
            $thumbnailUrl   = $this->imagineCacheManager->getBrowserPath( $thumbnailPath, 'video_thumbnail' );
        }
        
        return $this->render(
            'Pages/VideoServices/video-preview-directly.html.twig', [
                'video'         => $video,
                'thumbnailUrl'  => $thumbnailUrl,
            ]
        );
    }
}