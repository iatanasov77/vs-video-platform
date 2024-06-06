<?php namespace App\Controller\VideoPlatform;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Knp\Component\Pager\PaginatorInterface;
use App\Component\Cloud\GoogleVideoProvider;
use App\Component\VideoPlayer\Domain\VideoProviderRequest;
use App\Component\VideoPlayer\VideoService;
use App\Component\Cloud\Exception\YoutubeChannelException;

class YoutubeController extends AbstractController
{
    /** @var GoogleVideoProvider */
    private $youtube;
    
    /** @var RepositoryInterface */
    private $channelsRepository;
    
    /** @var int */
    private $moviesPerPage  = 12;
    
    public function __construct(
        GoogleVideoProvider $youtube,
        RepositoryInterface $channelsRepository
    ) {
        $this->youtube              = $youtube;
        $this->channelsRepository   = $channelsRepository;
    }
    
    public function listChannelsAction( Request $request, PaginatorInterface $paginator ): Response
    {
        $paginatorItems = $this->channelsRepository->findAll();
        $channels       = $paginator->paginate(
            $paginatorItems,
            $request->query->getInt( 'page', 1 ) /*page number*/,
            $this->moviesPerPage /*limit per page*/
        );
        
        return $this->render( 'Pages/Youtube/list-channels.html.twig', ['channels' => $channels] );
    }
    
    public function browseChannelAction( $slug, Request $request, PaginatorInterface $paginator ): Response
    {
        $channel    = $this->channelsRepository->findOneBy( ['slug' => $slug] );
        if ( ! $channel ) {
            throw new YoutubeChannelException( 'Youtube Channel Not Found !!!' );
        }
        
        $videoRequest   = new VideoProviderRequest( VideoService::REQUEST_COMMAND_CHANNEL, [
            'channel'   => $channel,
            'results'   => 100,
        ]);
        $paginatorItems = $this->youtube->videoList( $videoRequest );
        $movies         = $paginator->paginate(
            $paginatorItems,
            $request->query->getInt( 'page', 1 ) /*page number*/,
            $this->moviesPerPage /*limit per page*/
        );
        
        return $this->render( 'Pages/Youtube/channel.html.twig', ['channel' => $channel, 'movies' => $movies] );
    }
    
    public function videoDetailsAction( $channel, $videoId, Request $request ): Response
    {
        $channel    = $this->channelsRepository->findOneBy( ['slug' => $channel] );
        if ( ! $channel ) {
            throw new YoutubeChannelException( 'Youtube Channel Not Found !!!' );
        }
        
        $videoRequest   = new VideoProviderRequest( VideoService::REQUEST_COMMAND_GET_A_VIDEO, [
            'channel'   => $channel,
            'videoId'   => $videoId,
        ]);
        $video          = $this->youtube->videoList( $videoRequest );
        //echo '<pre>'; var_dump( $video ); die;
        
        return $this->render( 'Pages/Youtube/video.html.twig', ['mv' => $video[0]] );
    }
}