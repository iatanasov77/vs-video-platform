<?php namespace App\Component\Cloud\Coconut;

use Coconut\Client as CoconutClient;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Doctrine\Persistence\ManagerRegistry;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

use App\Component\VideoPlatform;
use App\Entity\Video;
use App\Entity\VideoPlatformSettings;

use Lexik\Bundle\JWTAuthenticationBundle\Exception\JWTEncodeFailureException;
use Coconut\Error as CoconutException;
use Vankosoft\ApiBundle\Exception\ApiLoginException;
use App\Component\Cloud\Exception\Coconut\JobNotFoundException;
use App\Component\Cloud\Exception\VideoPlatformStorageException;

abstract class AbstractCoconutJobBuilder implements CoconutJobBuilderInterface
{
    /** @var CoconutClient */
    protected $client;
    
    /** @var RouterInterface */
    protected $router;
    
    /** @var ManagerRegistry */
    protected $doctrine;
    
    /** @var RepositoryInterface */
    protected $jobsRepository;
    
    /** @var FactoryInterface */
    protected $jobsFactory;
    
    /** @var array */
    protected $jobOutputs;
    
    /** @var VideoPlatform */
    protected $videoPlatform;
    
    /** @var VideoPlatformSettings */
    protected $videoPlatformSettings;
    
    /** @var HttpClientInterface */
    protected $httpClient;
    
    /** @var string */
    protected $apiHost;
    
    /** @var string */
    protected $localVideosDirectory;
    
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
        
        $this->router                   = $router;
        $this->doctrine                 = $doctrine;
        $this->jobsRepository           = $jobsRepository;
        $this->jobsFactory              = $jobsFactory;
        $this->httpClient               = $httpClient;
        $this->apiHost                  = $apiHost;
        $this->jobOutputs               = [];
        
        $this->client                   = new CoconutClient(
            $this->videoPlatformSettings->getCoconutSettings()->getCoconutApiKey()
        );
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
    
    public function apiLogin(): string
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
        
        return $decodedPayload['refresh_token'];
    }
    
    protected function _createCoconutJob( Video $video ): \stdClass
    {
        /** @var \stdClass */
        $job    = $this->client->job->create([
            // 'input' => [ 'url' => 'https://mysite/path/file.mp4' ],
            'input'     => [ 'url' => $this->_getCoconutJobInput( $video ) ],
            'outputs'   => $this->jobOutputs
        ]);
        //echo '<pre>'; var_dump( $job ); die;
        
        return $job;
    }
    
    protected function _getCoconutJobInput( Video $video ): string
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
    
    abstract protected function _setupNotificationWebhook( string $apiToken ): void;
    abstract protected function _createCoconutJobEntity( Video $video, \stdClass $job ): void;
}
