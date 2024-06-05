<?php namespace App\Controller\VanzVideoPlayer;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sylius\Component\Resource\Repository\RepositoryInterface;

use App\Component\VideoPlayer\VideoService;
use App\Component\VideoPlayer\Domain\VideoProviderRequest;
use App\Component\Cloud\Exception\YoutubeChannelException;

class YoutubePlayerController extends AbstractController
{
    /** @var VideoService */
    private $videos;
    
    /** @var RepositoryInterface */
    private $youtubeChannelsRepository;
    
    public function __construct( VideoService $videos, RepositoryInterface $youtubeChannelsRepository )
    {
        $this->videos                       = $videos;
        $this->youtubeChannelsRepository    = $youtubeChannelsRepository;
    }
    
    /**
     * 
     * 
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function search( Request $request ): Response
    {
        $pattern        = $request->query->get( 'pattern', '' );
        $results        = $request->query->get( 'results', '10' );
        
        $videoRequest   = new VideoProviderRequest( 'search', [
            'searchTerm'    => $pattern,
            'results'       => $results,
        ]);
        $videos         = $this->videos->videoList( $videoRequest );
        $player         = $this->videos->render( $this->videos->first() );
        
        return $this->render(
            'pages/YoutubePlayer/index.html.twig', [
                'videos'    => $videos,
                'player'    => $player,
                'pattern'   => $pattern,
                'results'   => $results
            ]
        );
    }
    
    /**
     *
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function mychannel( Request $request ): Response
    {
        $results    = $request->query->get( 'results', '10' );
        
        $youtubeChannel = $this->youtubeChannelsRepository->findOneBy(
            ['slug' => 'ivan-atanasov-channel']
        );
        if ( ! $youtubeChannel ) {
            throw new YoutubeChannelException( 'Youtube Channel Not Found !!!' );
        }
        
        $videoRequest   = new VideoProviderRequest( 'channel', [
            'channelId' => $youtubeChannel->getChannelId(),
            'results'   => $results,
        ]);
        $videos         = $this->videos->videoList( $videoRequest );
        $player         = $this->videos->render( $this->videos->first() );
        //echo "<pre>"; var_dump( $videos ); die;
        
        $player     = $this->videos->render( $this->videos->first() );
        
        return $this->render(
            'pages/YoutubePlayer/index.html.twig',[
                'videos'    => $videos,
                'player'    => $player,
                'results'   => $results
            ]
        );
    }
    
    /**
     *
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function player( Request $request ): Response
    {
        
        
        return $this->render( 'pages/YoutubePlayer/partial/_video-player.html.twig', ['video' => $video] );
    }
}