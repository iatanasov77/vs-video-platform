<?php namespace App\Component\Cloud;

use Google\Service\YouTube;
use Google\Model as GoogleModel;
use Google\Service\YouTube\SearchResult;
use Google\Service\YouTube\Video as YouTubeVideo;
use Google\Collection as GoogleCollection;
use Google\Service\YouTube\SearchListResponse;
use Google\Service\YouTube\VideoListResponse;

use App\Component\Cloud\Google;
use App\Component\VideoPlayer\VideoService;
use App\Component\VideoPlayer\Domain\Video;
use App\Component\VideoPlayer\Domain\VideoPlayer;
use App\Component\VideoPlayer\Domain\VideoProviderRequest;
use App\Component\VideoPlayer\YoutubeVideoPlayer;
use App\Component\VideoPlayer\Exception\VideoProviderRequestException;
use App\Entity\YoutubeChannel;

/**
 * Google Cloud Project 
 * ====================
 * https://console.cloud.google.com/apis/api/youtube.googleapis.com/metrics?project=vanzvideoplayer
 */
class GoogleVideoProvider implements VideoProviderInterface
{
    /** @var Google */
    private $google;
    
    /** @var array */
    private $videos;
    
    public function __construct( Google $google )
    {
        $this->google   = $google;
    }
    
    /**
     * {@inheritdoc}
     */
    public function videoList( VideoProviderRequest $request ): array
    {
        switch ( $request->command ) {
            case VideoService::REQUEST_COMMAND_SEARCH:
                $searchResult   = $this->search( $request->params['searchTerm'] );
                break;
            case VideoService::REQUEST_COMMAND_CHANNEL:
                $searchResult   = $this->listChannel( $request->params['channel'] );
                break;
            case VideoService::REQUEST_COMMAND_GET_A_VIDEO:
                $searchResult   = $this->get( $request->params['channel'], $request->params['videoId'] );
                break;
            default:
                throw new VideoProviderRequestException( 'Unknown Video Provider Request !!!' );
        }
        //echo '<pre>'; var_dump( $searchResult ); die;
        
        $videos         = $this->loadSearchVideos( $searchResult );
        $this->videos   = $videos;
        
        return $videos;
    }
    
    /**
     * {@inheritdoc}
     */
    public function first():? Video
    {
        if ( ! empty( $this->videos ) ) {
            return reset( $this->videos );
        }
        
        return null;
    }
    
    /**
     * {@inheritdoc}
     */
    public function player(): VideoPlayer
    {
        return new YoutubeVideoPlayer();
    }
    
    /**
     * @param $item
     * @return Video
     */
    private function createVideoFromSearch( SearchResult $item ): Video
    {
        return ( new Video(
            $item->getId()->getVideoId(),
            $item->getSnippet()->getTitle(),
            $item->getSnippet()->getDescription()
        ))
        ->createdBy( $item->getSnippet()->getChannelTitle() )
        ->withThumbnail(
            $item->getSnippet()->getThumbnails()->getMedium()->getUrl()
        );
    }
    
    /**
     * @param $item
     * @return Video
     */
    private function createVideoFromVideo( YouTubeVideo $item ): Video
    {
        return ( new Video(
            $item->getId(),
            $item->getSnippet()->getTitle(),
            $item->getSnippet()->getDescription()
        ))
        ->createdBy( $item->getSnippet()->getChannelTitle() )
        ->withThumbnail(
            $item->getSnippet()->getThumbnails()->getMedium()->getUrl()
        );
    }
    
    /**
     * @param $searchResult
     * @return array
     */
    private function loadSearchVideos( GoogleCollection $searchResult ): array
    {
        $videos = [];
        
        /** @var SearchResult $item */
        foreach ( $searchResult->getItems() as $item ) {
            switch ( true ) {
                case ( $item instanceof SearchResult ):
                    $video      = $this->createVideoFromSearch( $item );
                    $videos[]   = $video;
                    break;
                case ( $item instanceof YouTubeVideo ):
                    $video      = $this->createVideoFromVideo( $item );
                    $videos[]   = $video;
                    break;
            }
        }
        
        return $videos;
    }
    
    /**
     * @param string $searchTerm
     * @return SearchListResponse
     */
    private function search( string $searchTerm = null ): GoogleCollection
    {
        $youtube        = $this->google->youtubeClient();
        
        $searchResult   = $youtube->search->listSearch(
            'id,snippet',
            [
                'q'             => $searchTerm,
                'type'          => 'video',
                'maxResults'    => 10
            ]
        );
        
        return $searchResult;
    }
    
    private function listChannel( YoutubeChannel $channel ): GoogleCollection
    {
        $youtube        = $this->google->youtubeClient( $channel );
        
        $searchResult   = $youtube->search->listSearch(
            'id,snippet',
            [
                'channelId'     => $channel->getChannelId(),
                'type'          => 'video',
                'maxResults'    => 20
            ]
        );
        //var_dump($searchResult); die;
        
        return $searchResult;
    }
    
    private function get( YoutubeChannel $channel, string $videoId ): GoogleCollection
    {
        $youtube        = $this->google->youtubeClient( $channel );
        
        $searchResult   = $youtube->videos->listVideos(
            'id,snippet',
            [
                'id'     => $videoId,
            ]
        );
        //var_dump($searchResult); die;
        
        return $searchResult;
    }
}