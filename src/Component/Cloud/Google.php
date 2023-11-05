<?php namespace App\Component\Cloud;

use Google\Client as GoogleApiClient;
use Google\Service\YouTube as YouTubeClient;

use Sylius\Component\Resource\Repository\RepositoryInterface;
use App\Component\Cloud\Exception\GoogleProjectException;

class Google
{
    /** @var GoogleApiClient */
    private $googleApiClient;
    
    /** @var YouTubeClient */
    private $youtubeClient;
    
    public function __construct( RepositoryInterface $googleProjectsRepository, string $googleProjectSlug )
    {
        $googleProject          = $googleProjectsRepository->findOneBy( ['slug' => $googleProjectSlug] );
        if ( ! $googleProject ) {
            throw new GoogleProjectException( 'Google Project Not Found !!!' );
        }
        
        $googleApiClientConfig  = [
            'developer_key' => $googleProject->getGoogleApiKey(),
            'client_id'     => $googleProject->getGoogleClientId(),
            'client_secret' => $googleProject->getGoogleClientSecret(),
        ];
        
        $this->googleApiClient  = new GoogleApiClient( $googleApiClientConfig );
    }
        
    public function apiClient()
    {
        return $this->googleApiClient;
    }
    
    public function youtubeClient()
    {
        if ( ! $this->youtubeClient ) {
            $this->youtubeClient    = new YouTubeClient( $this->googleApiClient );
        }
        
        return $this->youtubeClient;
    }
}