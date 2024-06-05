<?php namespace App\Component\VideoProviders;

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
use App\Component\VideoPlayer\Domain\VideoProvider;
use App\Component\VideoPlayer\Domain\VideoProviderRequest;
use App\Component\VideoPlayer\YoutubeVideoPlayer;

/**
 * Google Cloud Project 
 * ====================
 * https://console.cloud.google.com/apis/api/youtube.googleapis.com/metrics?project=vanzvideoplayer
 */
class GoogleVideoProvider implements VideoProvider
{
    /** @var YouTube */
    private $youtube;
    
    /** @var array */
    private $videos;
    
    public function __construct( Google $google )
    {
        $this->youtube  = $google->youtubeClient();
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
                $searchResult   = $this->listChannel( $request->params['channelId'] );
                break;
            case VideoService::REQUEST_COMMAND_GET_A_VIDEO:
                $searchResult   = $this->get( $request->params['videoId'] );
                break;
            default:
                throw new \Exception( '' );
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
        $searchResult = $this->youtube->search->listSearch(
            'id,snippet',
            [
                'q'             => $searchTerm,
                'type'          => 'video',
                'maxResults'    => 10
            ]
        );
        
        return $searchResult;
    }
    
    private function listChannel( string $channelId ): GoogleCollection
    {
        $searchResult = $this->youtube->search->listSearch(
            'id,snippet',
            [
                'channelId'     => $channelId,
                'type'          => 'video',
                'maxResults'    => 20
            ]
        );
        //var_dump($searchResult); die;
        
        return $searchResult;
    }
    
    private function get( string $videoId ): GoogleCollection
    {
        $searchResult = $this->youtube->videos->listVideos(
            'id,snippet',
            [
                'id'     => $videoId,
            ]
        );
        //var_dump($searchResult); die;
        
        return $searchResult;
    }
}