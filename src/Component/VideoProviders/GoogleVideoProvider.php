<?php namespace App\Component\VideoProviders;

use Google\Service\YouTube;
use Google\Service\YouTube\SearchResult;
use Google\Service\YouTube\SearchListResponse;

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
            default:
                throw new \Exception( '' );
        }
        
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
    private function createVideoFrom( SearchResult $item ): Video
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
     * @param $searchResult
     * @return array
     */
    private function loadSearchVideos( SearchListResponse $searchResult ): array
    {
        $videos = [];
        
        /** @var SearchResult $item */
        foreach ( $searchResult->getItems() as $item ) {
            $video      = $this->createVideoFrom( $item );
            $videos[]   = $video;
        }
        return $videos;
    }
    
    /**
     * @param string $searchTerm
     * @return SearchListResponse
     */
    private function search( string $searchTerm = null ): SearchListResponse
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
    
    private function listChannel( string $channelId ): SearchListResponse
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
}