<?php namespace App\Component\Cloud;

use Coconut\Client;
use Coconut\Error as CoconutException;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Doctrine\Persistence\ManagerRegistry;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Exception\JWTEncodeFailureException;
use Vankosoft\ApiBundle\Exception\ApiLoginException;
use App\Entity\Video;
use App\Entity\CoconutJob;
use App\Component\Cloud\Exception\Coconut\JobNotFoundException;
use App\Component\Cloud\Exception\VideoPlatformSettingsException;
use App\Component\Cloud\Exception\VideoPlatformStorageException;
use App\Entity\VideoPlatformSettings;
use App\Component\VideoPlatform;

/**
 * Using KNP Gaufrette to Store Files in Cloud Storage
 * ====================================================
 * https://florian.ec/blog/symfony2-gaufrette-s3/
 */
class Coconut
{
    /** @var Client */
    private $client;
    
    /** @var RouterInterface */
    private $router;
    
    /** @var ManagerRegistry */
    private $doctrine;
    
    /** @var RepositoryInterface */
    private $jobsRepository;
    
    /** @var FactoryInterface */
    private $jobsFactory;
    
    /** @var array */
    private $jobOutputs;
    
    /** @var VideoPlatform */
    private $videoPlatform;
    
    /** @var VideoPlatformSettings */
    private $videoPlatformSettings;
    
    /** @var HttpClientInterface */
    private $httpClient;
    
    /** @var string */
    private $apiHost;
    
    /** @var string */
    private $localVideosDirectory;
    
    public function __construct(
        RouterInterface $router,
        ManagerRegistry $doctrine,
        RepositoryInterface $jobsRepository,
        FactoryInterface $jobsFactory,
        HttpClientInterface $httpClient,
        VideoPlatform $videoPlatform,
        string $apiHost
    ) {
        $this->videoPlatform            = $videoPlatform;
        $this->videoPlatformSettings    = $videoPlatform->getVideoPlatformSettings();
        $this->client                   = new Client( $this->videoPlatformSettings->getCoconutSettings()->getCoconutApiKey() );
        
        $this->router                   = $router;
        $this->doctrine                 = $doctrine;
        $this->jobsRepository           = $jobsRepository;
        $this->jobsFactory              = $jobsFactory;
        $this->httpClient               = $httpClient;
        $this->apiHost                  = $apiHost;
        
        $this->jobOutputs               = [];
        
        $this->_setup();
    }
    
    public function storage(): string
    {
        return $this->videoPlatform->getCoconutStorage()->getStorageType();
    }
    
    public function createJob( Video $video, ?string $watermark = null ): void
    {
        $this->_setupNotificationWebhook();
        
        if ( $this->videoPlatformSettings->getCoconutSettings()->getCoconutWatermark() ) {
            /*
            $watermark  = $this->router->generate( 'vs_cms_images_get_file',
                ['file' => 'public/shared_media/gaufrette/videos_user_signatured/anonymous/watermark.png' ],
                RouterInterface::ABSOLUTE_URL
            );
            */
            
            $watermark  = $this->router->generate( 'vs_cms_images_get_file',
                ['file' => 'anonymous/watermark.png' ],
                RouterInterface::ABSOLUTE_URL
            ) . '?filter=video_watermark';
            
            //$watermark  = 'http://admin.video-platform.vankosoft.org/images/public/shared_media/gaufrette/videos_user_signatured/anonymous/watermark.png';
            
            $this->_setupCoconutJobOutputs( $video, $watermark );
        } else {
            $this->_setupCoconutJobOutputs( $video );
        }
        
        $job    = $this->_createCoconutJob( $video );
        if ( ! $job ) {
            return;
        }
        
        $this->_createCoconutJobEntity( $video, $job );
    }
    
    public function getStatus( $jobId )
    {
        try {
            return $this->client->job->retrieve( $jobId );
            //return $this->client->metadata->retrieve( $jobId );
        } catch( CoconutException $e ) {
            throw new JobNotFoundException( 'JobNotFoundException: ' . $e->getMessage() );
        }
    }
    
    private function _setup()
    {
        switch ( $this->videoPlatform->getCoconutStorage()->getStorageType() ) {
            case VideoPlatform::STORAGE_TYPE_LOCAL:
                $this->_setupLocalStorage();
                break;
            case VideoPlatform::STORAGE_TYPE_S3:
                $this->_setupS3Storage();
                break;
                
            case VideoPlatform::STORAGE_TYPE_DO:
                $this->_setupDigitalOceanStorage();
                break;
                
            case VideoPlatform::STORAGE_TYPE_COCONUT:
            default:
                $this->_setupCoconutStorage();
        }
    }
    
    private function _setupCoconutStorage()
    {
        $this->client->storage = [
            'service' => 'coconut',
        ];
    }
    
    private function _setupLocalStorage()
    {
        //$storageUrl = 'http://admin.video-platform.vankosoft.org/api/coconut/storage-oneup';
        $settings   = $this->videoPlatform->getCoconutStorage()->getSettings();
        if ( ! isset( $settings['local_url'] ) ) {
            throw new VideoPlatformStorageException( 'Local Storage is Not Configured Properly !!!' );
        }
        
        $this->client->storage = [
            'url'   => $settings['local_url'],
        ];
    }
    
    private function _setupS3Storage()
    {
        $credentials    = $this->videoPlatform->getCoconutStorage()->getSettings();
        if ( ! isset( $credentials['access_key'] ) || ! isset( $credentials['secret_key'] ) ) {
            throw new VideoPlatformStorageException( 'AWS Storage is Not Configured Properly !!!' );
        }
        
        /** Created on johnny2000@abv.bg Account */
        $this->client->storage = [
            'service'       => 's3',
            'bucket'        => 'my-video-platform',
            'region'        => 'eu-central-1',
            'credentials'   => [
                'access_key_id'     => $credentials['access_key'],
                'secret_access_key' => $credentials['secret_key']
            ]
        ];
    }
    
    private function _setupDigitalOceanStorage()
    {
        $storageSettings    = $this->videoPlatform->getCoconutStorage()->getSettings();
        //echo '<pre>'; var_dump( $credentials ); die;
        
        if (
            ! isset( $storageSettings['spaces_access_id'] ) ||
            ! isset( $storageSettings['spaces_secret_key'] ) ||
            ! isset( $storageSettings['endpoint'] ) ||
            ! isset( $storageSettings['region'] ) ||
            ! isset( $storageSettings['bucket'] )
        ) {
            throw new VideoPlatformStorageException( 'DigitalOcean Storage is Not Configured Properly !!!' );
        }
        
        /** 
         * Any S3 compatible services: https://docs.coconut.co/jobs/storage#any-s3-compatible-services
         * ===============================================================================================
         * https://docs.digitalocean.com/products/spaces/reference/s3-sdk-examples/
         */
        $this->client->storage = [
            //'service'           => 's3other',
            //'force_path_style'  => false, // Configures to use subdomain/virtual calling format.
            //'endpoint'          => $credentials['endpoint'],
            
            'service'           => 'dospaces',
            //'bucket'            => $credentials['coconut_output_bucket'],
            'bucket'            => $storageSettings['bucket'],
            'region'            => $storageSettings['region'],
            'acl'               => isset( $storageSettings['acl'] ) ? $storageSettings['acl'] : 'private',
            
            'credentials'       => [
                'access_key_id'     => $storageSettings['spaces_access_id'],
                'secret_access_key' => $storageSettings['spaces_secret_key']
            ],
        ];
    }
    
    private function _setupNotificationWebhook()
    {
        /** ============================================================ */
        /** @NOTE Add To /etc/hosts - 10.3.3.2   admin.sugarbabes.lh */
        /** ============================================================ */
        //$apiLoginUrl    = $this->router->generate( 'vs_api_login_check', [], RouterInterface::ABSOLUTE_URL );
        $apiLoginUrl    = \sprintf( '%s/api/login_check', $this->apiHost );
        
        try {
            $response       = $this->httpClient->request( 'POST', $apiLoginUrl, [
                'json' => [
                    'username' => $this->videoPlatformSettings->getCoconutSettings()->getCoconutSystemUser(),
                    'password' => $this->videoPlatformSettings->getCoconutSettings()->getCoconutSystemPassword()
                ],
            ]);
        }  catch ( JWTEncodeFailureException $e ) {
            throw new ApiLoginException( 'JWTEncodeFailureException: ' . $e->getMessage() );
        }
        
        $decodedPayload = $response->toArray( false );
        //echo '<pre>'; var_dump( $decodedPayload ); die;
        
//         $webhookUrl = $this->router->generate(
//             'vs_api_coconut_webhook', ['apiToken' => $decodedPayload['refresh_token']], RouterInterface::ABSOLUTE_URL
//         );
        $webhookUrl = \sprintf( '%s/api/coconut/webhook/%s', $this->apiHost, $decodedPayload['refresh_token'] );
        
        $this->client->notification = [
            'type'      => 'http',
            'url'       => $webhookUrl,
            "events"    => true,
            "metadata"  => true
        ];
    }
    
    private function _setupCoconutJobOutputs( Video $video, ?string $watermark = null )
    {
        $outputFormats  = $this->videoPlatformSettings->getCoconutSettings()->getCoconutOutputFormats();
        foreach( $outputFormats as $format ) {
            switch ( $format ) {
                case 'httpstrem':
                    // https://developer.apple.com/streaming/
                    $this->jobOutputs['httpstrem']  = [
                        'hls'   => [
                            'path' => "/video-{$video->getId()}-hls"
                        ],
                        'dash' => [
                            'path'     => "/video-{$video->getId()}-dash",
                            'hlsfmp4'  => true
                        ]
                    ];
                    break;
                default:
                    $formatParts    = \explode( ':', $format );
                    $this->jobOutputs[$format]  = [ 'path' => "/video-{$video->getId()}-{$formatParts[1]}.{$formatParts[0]}" ];
                    if ( $watermark ) {
                        $this->jobOutputs[$format]['watermark'] = [
                            'url'       => $watermark,
                            'position'  => "bottomright"
                        ];
                    }
            }
        }
    }
    
    private function _getCoconutJobInput( Video $video ): string
    {
        $storageSettings    = $this->videoPlatform->getOriginalVideosStorage()->getSettings();
        if ( ! isset( $storageSettings['bucket'] ) ) {
            throw new VideoPlatformStorageException( 'Video Platform Storage Not Configured Properly !!!' );
        }
        
        switch ( $this->videoPlatformSettings->getCoconutSettings()->getCoconutInputUrlType() ) {
            case VideoPlatform::URL_INPUT_TYPE_S3:
                return $this->videoPlatform->getVideoUri( $video->getVideoFile(), $storageSettings['bucket'], false );
                break;
            case VideoPlatform::URL_INPUT_TYPE_S3_PRESIGNED:
                return $this->videoPlatform->getVideoUri( $video->getVideoFile(), $storageSettings['bucket'], true );
                break;
            case VideoPlatform::URL_INPUT_TYPE_LOCAL: 
            default:
                return $this->router->generate( 'app_video_player_read', ['id' => $video->getId()], RouterInterface::ABSOLUTE_URL );
        }
    }
    
    private function _createCoconutJob( Video $video ): ?\stdClass
    {
        try {
            /** @var \stdClass */
            $job    = $this->client->job->create([
                // 'input' => [ 'url' => 'https://mysite/path/file.mp4' ],
                'input'     => [ 'url' => $this->_getCoconutJobInput( $video ) ],
                'outputs'   => $this->jobOutputs
            ]);
            //echo '<pre>'; var_dump( $job ); die;
            
            return $job;

        } catch( \Exception $e ) {
            echo $e->getMessage();
            return null;
        }
    }
    
    private function _createCoconutJobEntity( Video $video, \stdClass $job ): void
    {
        $em         = $this->doctrine->getManager();
        $jobEntity  = $this->jobsFactory->createNew();
        
        if ( $video->getCoconutJob() ) {
            $em->remove( $video->getCoconutJob() );
            $em->flush();
            
            $video->setCoconutJob( null );
        }
        
        $jobEntity->setVideo( $video );
        $jobEntity->setJobId( $job->id );
        $jobEntity->setJobData( \json_encode( (array) $job ) );
        $jobEntity->setStatus( CoconutJob::STATUS_JOB_STARTING );
        
        $em->persist( $jobEntity );
        $em->flush();
    }
}