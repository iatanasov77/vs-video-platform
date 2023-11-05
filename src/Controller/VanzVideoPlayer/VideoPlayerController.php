<?php namespace App\Controller\VanzVideoPlayer;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sylius\Component\Resource\Repository\RepositoryInterface;

use App\Component\VideoPlayer\VideoService;
use App\Component\VideoPlayer\Domain\VideoProviderRequest;
use App\Component\Cloud\Coconut;
use App\Entity\CoconutJob;
use App\Component\Cloud\Exception\Coconut\JobNotFoundException;

class VideoPlayerController extends AbstractController
{
    /** @var VideoService */
    private $videos;
    
    /** @var RepositoryInterface */
    private $videosRepository;
    
    /** @var Coconut */
    private $coconut;
    
    public function __construct( VideoService $videos, RepositoryInterface $videosRepository, Coconut $coconut )
    {
        $this->videos           = $videos;
        $this->videosRepository = $videosRepository;
        $this->coconut          = $coconut;
    }
    
    /**
     * 
     * 
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index( Request $request ): Response
    {
        $results        = $request->query->get( 'results', '10' );
        
        $videoRequest   = new VideoProviderRequest( VideoService::REQUEST_COMMAND_LATEST, [
            'category'  => null,
            'results'   => $results,
        ]);
        $videos     = $this->videos->videoList( $videoRequest );
        $player     = $this->videos->render( $this->videos->first() );
        
        return $this->render(
            'pages/VideoPlayer/index.html.twig', [
                'videos'            => $videos,
                'player'            => $player,
                'availableFormats'  => $this->_getVideoFormats( $videos ),
            ]
        );
    }
    
    protected function _getVideoFormats( array $videos ): array
    {
        $formats        = [];
        foreach ( $videos as $video ) {
            $videoEntity                        = $this->videosRepository->find( $video->videoId() );
            $formats[$videoEntity->getSlug()]   = $this->videoPlatform->getVideoFormats( $videoEntity );
        }
        
        return $formats;
    }
}