<?php namespace App\Component\Cloud\DigitalOcean;

use DigitalOceanV2\Client as DigitalOceanClient;
use Aws\S3\S3Client;
use Aws\S3\S3ClientInterface;
use Aws\Credentials\Credentials;
use App\Component\Cloud\Exception\VideoPlatformStorageException;
use App\Entity\VideoPlatformStorage;

use App\Component\VideoPlatform;

/**
 * MANUAL: https://packagist.org/packages/toin0u/digitalocean-v2
 */
class OriginalVideos implements DigitalOceanInterface
{
    /** @var VideoPlatformStorage */
    private $videoPlatform;
    
    /** @var DigitalOceanClient */
    private $client;
    
    /** @var S3Client */
    private $s3Client;
    
    public function __construct(
        VideoPlatform $videoPlatform
    ) {
        $this->videoPlatform    = $videoPlatform;
        
        $this->_setupDigitalOceanClient();
        $this->_setupS3Client();
    }
    
    public function getS3Client(): S3ClientInterface
    {
        return $this->s3Client;
    }
    
    public function getBucket(): string
    {
        $storageSettings    = $this->videoPlatform->getOriginalVideosStorage()->getSettings();
        if ( ! isset( $storageSettings['bucket'] ) ) {
            throw new VideoPlatformStorageException( 'Video Platform Storage Not Configured Properly !!!' );
        }
        
        return $storageSettings['bucket'];
    }
    
    private function _setupDigitalOceanClient()
    {
        $storageSettings    = $this->videoPlatform->getOriginalVideosStorage()->getSettings();
        if ( ! isset( $storageSettings['access_token'] ) ) {
            throw new VideoPlatformStorageException( 'Video Platform Storage Not Configured Properly !!!' );
        }
        
        // create a new DigitalOcean client
        $this->client       = new DigitalOceanClient();
        
        // authenticate the client with your access token which can be
        // generated at https://cloud.digitalocean.com/settings/applications
        $this->client->authenticate( $storageSettings['access_token'] );
    }
    
    private function _setupS3Client()
    {
        $storageSettings    = $this->videoPlatform->getOriginalVideosStorage()->getSettings();
        if (
            ! isset( $storageSettings['spaces_access_id'] ) ||
            ! isset( $storageSettings['spaces_secret_key'] ) ||
            ! isset( $storageSettings['endpoint'] ) ||
            ! isset( $storageSettings['region'] )
        ) {
            throw new VideoPlatformStorageException( 'Video Platform Storage Not Configured Properly !!!' );
        }
        
        $credentials        = new Credentials( $storageSettings['spaces_access_id'], $storageSettings['spaces_secret_key'] );
        
        // MANUAL: https://docs.zerops.io/knowledge-base/how-to-do/using-object-storage-in-php.html#how-to-get-an-s3-api-client
        $this->s3Client     = new S3Client([
            // The 'version' is a required property in PHP SDK.
            'version'                   => 'latest',
            
            'endpoint'                  => $storageSettings['endpoint'],
            'region'                    => $storageSettings['region'],
            'use_path_style_endpoint'   => false,
            'credentials'               => $credentials
        ]);
    }
}