<?php namespace App\Component\Cloud;

use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Google\Client as GoogleApiClient;
use Google\Service\YouTube as YouTubeClient;

use Sylius\Component\Resource\Repository\RepositoryInterface;
use App\Component\Cloud\Exception\GoogleProjectException;
use App\Entity\YoutubeChannel;

class Google
{
    /** @var Collection */
    private $googleProjects;
    
    /** @var GoogleApiClient */
    private $googleApiClient;
    
    /** @var YouTubeClient[] */
    private $youtubeClients;
    
    public function __construct( RepositoryInterface $googleProjectsRepository )
    {
        $googleProjects         = $googleProjectsRepository->findAll();
        $this->googleProjects   = new ArrayCollection( $googleProjects ?: [] );
        if ( $this->googleProjects->isEmpty() ) {
            throw new GoogleProjectException( 'No Google Projects Defined !!!' );
        }
        
        $googleApiClientConfig  = [
            'developer_key' => $this->googleProjects->first()->getGoogleApiKey(),
            'client_id'     => $this->googleProjects->first()->getGoogleClientId(),
            'client_secret' => $this->googleProjects->first()->getGoogleClientSecret(),
        ];
        
        $this->googleApiClient  = new GoogleApiClient( $googleApiClientConfig );
    }
        
    public function apiClient(): GoogleApiClient
    {
        return $this->googleApiClient;
    }
    
    public function youtubeClient( ?YoutubeChannel $channel ): YouTubeClient
    {
        $channelSlug    = $channel ? $channel->getSlug() : 'global';
        
        if ( ! isset( $this->youtubeClients[$channelSlug] ) ) {
            $this->youtubeClients[$channelSlug]    = $this->createYoutubeClient( $channel );
        }
        
        return $this->youtubeClients[$channelSlug];
    }
    
    private function createYoutubeClient( ?YoutubeChannel $channel ): YouTubeClient
    {
        if ( ! $channel ) {
            return new YouTubeClient( $this->googleApiClient );
        }
        
        $googleApiClientConfig  = [
            'developer_key' => $channel->getProject()->getGoogleApiKey(),
            'client_id'     => $channel->getProject()->getGoogleClientId(),
            'client_secret' => $channel->getProject()->getGoogleClientSecret(),
        ];
        $googleApiClient        = new GoogleApiClient( $googleApiClientConfig );
        
       return new YouTubeClient( $googleApiClient );
    }
}