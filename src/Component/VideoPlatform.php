<?php namespace App\Component;

use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

use Symfony\Component\Filesystem\Filesystem;
use League\Flysystem\Filesystem as LeagueFilesystem;

use FFMpeg\FFMpeg;
use App\Component\VideoUploader\Adapter\GaufretteAwsS3\GaufretteAwsS3Adapter;
use App\Component\Cloud\Exception\VideoPlatformSettingsException;
use App\Component\Cloud\Exception\VideoPlatformStorageException;
use App\Entity\VideoPlatformSettings;
use App\Entity\VideoFile;
use App\Entity\Video;
use App\Entity\CoconutJob;

final class VideoPlatform
{
    /** Coconut Storage Types */
    const STORAGE_TYPE_COCONUT  = 'coconut';
    const STORAGE_TYPE_LOCAL    = 'local';
    const STORAGE_TYPE_S3       = 's3';
    const STORAGE_TYPE_DO       = 'digitalocean';
    
    /** Coconut Input Url Types */
    const URL_INPUT_TYPE_LOCAL          = 'local';
    const URL_INPUT_TYPE_S3             = 's3';
    const URL_INPUT_TYPE_S3_PRESIGNED   = 's3_presigned';
    
    const STORAGE_TYPES = [
        self::STORAGE_TYPE_COCONUT  => 'Coconut',
        self::STORAGE_TYPE_LOCAL    => 'Local Storage',
        self::STORAGE_TYPE_S3       => 'AWS S3',
        self::STORAGE_TYPE_DO       => 'DigitalOcean Space'
    ];
    
    const INPUT_URL_TYPES = [
        self::URL_INPUT_TYPE_LOCAL          => 'Local URL',
        self::URL_INPUT_TYPE_S3             => 'S3 Public URL',
        self::URL_INPUT_TYPE_S3_PRESIGNED   => 'S3 Presigned URL',
    ];
    
    /** @var RouterInterface */
    private $router;
    
    /** @var VideoPlatformSettings */
    private $videoPlatformSettings;
    
    /** @var LeagueFilesystem */
    private $localFilesystem;
    
    /**
     * The Filesystem used from Coconut to Store Files by Ftp
     * 
     * @var LeagueFilesystem
     */
    private $coconutFilesystem;
    
    /** @var LeagueFilesystem */
    private $userSignedVideosFilesystem;
    
    /** @var GaufretteAwsS3Adapter */
    private $s3OriginalVideosAdapter;
    
    /** @var GaufretteAwsS3Adapter */
    private $s3CoconutOutputAdapter;
    
    /** @var RepositoryInterface */
    private $moviesCategoriesRepository;
    
    /** @var RepositoryInterface */
    private $moviesRepository;
    
    /** @var MoviesUserSignature */
    private $userSignature;
    
    /** @var string */
    private $localVideosDirectory;
    
    public function __construct(
        string $settingsKey,
        RouterInterface $router,
        RepositoryInterface $videoPlatformSettingsRepository,
        LeagueFilesystem $localFilesystem,
        LeagueFilesystem $coconutFilesystem,
        LeagueFilesystem $userSignedVideosFilesystem,
        GaufretteAwsS3Adapter $s3OriginalVideosAdapter,
        GaufretteAwsS3Adapter $s3CoconutOutputAdapter,
        RepositoryInterface $moviesCategoriesRepository,
        RepositoryInterface $moviesRepository,
        FFMpeg $ffMpeg,
        TokenStorageInterface $tokenStorage,
        string $localVideosDirectory,
        string $userSignaturesDirectory,
        ?MoviesUserSignature $userSignature = null
    ) {
        $this->router           = $router;
        
        $videoPlatformSettings  = $videoPlatformSettingsRepository->findOneBy( ['settingsKey' => $settingsKey] );
        if ( ! $videoPlatformSettings ) {
            throw new VideoPlatformSettingsException( 'Video Platform Settings Not Found !!!' );
        }
        
        $this->videoPlatformSettings        = $videoPlatformSettings;
        
        $this->localFilesystem              = $localFilesystem;
        $this->coconutFilesystem            = $coconutFilesystem;
        $this->userSignedVideosFilesystem   = $userSignedVideosFilesystem;
        
        $this->s3OriginalVideosAdapter      = $s3OriginalVideosAdapter;
        $this->s3CoconutOutputAdapter       = $s3CoconutOutputAdapter;
        
        $this->moviesCategoriesRepository   = $moviesCategoriesRepository;
        $this->moviesRepository             = $moviesRepository;
        
        $user                               = null;
        $token                              = $tokenStorage->getToken();
        if ( $token ) {
            $user   = $token->getUser();
        }
        $this->userSignature                = new MoviesUserSignature(
            $ffMpeg,
            $this,
            $user,
            $this->userSignedVideosFilesystem,
            $userSignaturesDirectory
        );
        
        $this->localVideosDirectory         = $localVideosDirectory;
    }
    
    public function getVideoPlatformSettings()
    {
        return $this->videoPlatformSettings;
    }
    
    public function getOriginalVideosStorage()
    {
        return $this->videoPlatformSettings->getOriginalVideosStorage();
    }
    
    public function getCoconutStorage()
    {
        return $this->videoPlatformSettings->getCoconutSettings()->getCoconutStorage();
    }
    
    public function getVideoUri( VideoFile $videoFile, string $bucket, bool $signed = true ): string
    {
        switch ( $videoFile->getStorageType() ) {
            case self::STORAGE_TYPE_S3:
            case self::STORAGE_TYPE_DO:
                return $signed ? $this->_getPresignedS3ObjectUrl( $videoFile, $bucket ) :
                                $this->_getS3ObjectUrl( $videoFile, $bucket );
                
                break;
            case self::STORAGE_TYPE_LOCAL:
            case self::STORAGE_TYPE_COCONUT:
            default:
                return $this->router->generate( 'app_video_player_read', [
                    'id' => $videoFile->getVideo()->getId()
                ], RouterInterface::ABSOLUTE_URL );
        }
    }
    
    public function getTranscodedLocalUrl( $id, $format ): string
    {
        return $this->router->generate( 'app_video_player_read_transcoded',
            ['id' => $id, 'format' => $format ],
            RouterInterface::ABSOLUTE_URL
        );
    }
    
    public function getVideoStream( VideoFile $videoFile, string $bucket )
    {
        if ( $this->videoPlatformSettings->getCreateUserSignedVideos() ) {
            // @NOTE User Signed Videos Not Working Yet
            //return $this->userSignedVideosFilesystem->readStream( $this->getUserSignedVideo( $videoFile, $bucket ) );
        }
        
        switch ( $videoFile->getStorageType() ) {
            case self::STORAGE_TYPE_S3:
            case self::STORAGE_TYPE_DO:
                return $this->_getS3ObjectStream( $videoFile );
                
                break;
            case self::STORAGE_TYPE_LOCAL:
            case self::STORAGE_TYPE_COCONUT:
            default:
                return $this->_getLocalObjectStream( $videoFile );
        }
    }
    
    public function getCoconutOutputStream( $id, $format )
    {
        $videoFilePath  = \sprintf( 'video-%s-%s.mp4', $id, $format );
        
        switch ( $this->getVideoPlatformSettings()->getCoconutSettings()->getCoconutStorage()->getStorageType() ) {
            case self::STORAGE_TYPE_S3:
            case self::STORAGE_TYPE_DO:
                $this->s3CoconutOutputAdapter->read( $videoFilePath );
                
                return \fopen( "s3://{$this->s3CoconutOutputAdapter->getBucket()}/{$videoFilePath}", 'rb' );
                
                break;
            default:
                return $this->coconutFilesystem->readStream( $videoFilePath );
        }
    }
    
    public function getUserSignedVideo( VideoFile $videoFile, string $bucket ): ?string
    {
        if ( $this->videoPlatformSettings->getUseFFMpeg() ) {
            return $this->userSignature->getUserSignedVideo( $videoFile, $bucket );
        }
        
        return null;
    }
    
    public function removeVideoFiles( VideoFile $videoFile, int $videoId ): void
    {
        switch ( $this->videoPlatformSettings->getCoconutSettings()->getCoconutStorage()->getStorageType() ) {
            case self::STORAGE_TYPE_LOCAL:
                $fileVideo  = $this->localVideosDirectory . '/' . $videoFile->getPath();
                
                $filesystem = new Filesystem();
                $filesystem->remove( $fileVideo );
                
                break;
            case self::STORAGE_TYPE_S3:
            case self::STORAGE_TYPE_DO:
                $this->s3OriginalVideosAdapter->delete( $videoFile->getPath() );
                
                foreach ( \array_keys( $this->getVideoFormats( $videoFile->getVideo(), $videoId ) ) as $format ) {
                    $formatParts    = \explode( ':', $format );
                    $videoFilePath  = \sprintf( 'video-%s-%s.mp4', $videoId, $formatParts[1] );
                    
                    $this->s3CoconutOutputAdapter->delete( $videoFilePath );
                }
                
                break;
                
            case VideoPlatform::STORAGE_TYPE_COCONUT:
            default:
                throw new VideoPlatformStorageException( 'Storage is Not Configured Properly !!!' );
        }
    }
    
    public function getVideoFormats( Video $videoEntity, ?int $videoId = null ): array
    {
        $formats            = [];
        $coconutJobEntity   = $videoEntity->getCoconutJob();
        $_videoId           = $videoId ?: $videoEntity->getId();
        
        if ( ! $coconutJobEntity ) {
            return $formats;
        }
        
        $coconutJobStatusData   = $coconutJobEntity->getJobData();
        if ( ! $coconutJobStatusData ) {
            return $formats;
        }
        
        $jobData   = \json_decode( $coconutJobStatusData );
        if ( \property_exists( $jobData, 'data' ) ) {
            // Job Data From Webhook 
            $coconutData   = $jobData->data;
        } else {
            // Job Data From Coconut API
            $coconutData   = $jobData;
        }
        
        if ( $coconutData->status == CoconutJob::EVENT_JOB_COMPLETED ) {
            foreach ( $coconutData->outputs as $output ) {
                if ( $output->key == 'httpstream' ) {
                    continue;
                }
                
                $formatParts            = \explode( ':', $output->key );
                
                if ( $this->videoPlatformSettings->getCreateUserSignedVideos() ) {
                    $formats[$output->key]  = $this->_getTranscodedPresignedS3ObjectUrl( $_videoId, $formatParts[1] );
                } else {
                    $formats[$output->key]  = $this->getTranscodedLocalUrl( $_videoId, $formatParts[1] );
                }
            }
        }
        
        return $formats;
    }
    
    private function _getS3ObjectUrl( VideoFile $videoFile, string $bucket ): string
    {
        return $this->s3OriginalVideosAdapter->getS3Client()->getObjectUrl( $bucket, $videoFile->getPath() );
    }
    
    private function _getPresignedS3ObjectUrl( VideoFile $videoFile, string $bucket ): string
    {
        //Creating a presigned URL
        $cmd        = $this->s3OriginalVideosAdapter->getS3Client()->getCommand( 'GetObject', [
            'Bucket'    => $bucket,
            //'Bucket'    => $do->getBucket(),
            'Key'       => $videoFile->getPath()
        ]);
        
        $request    = $this->s3OriginalVideosAdapter->getS3Client()->createPresignedRequest( $cmd, '+1440 minutes' );
        
        // Get the actual presigned-url
        return (string)$request->getUri();
    }
    
    private function _getS3ObjectStream( VideoFile $videoFile )
    {
        $this->s3OriginalVideosAdapter->read( $videoFile->getPath() );
        
        return \fopen( "s3://{$this->s3OriginalVideosAdapter->getBucket()}/{$videoFile->getPath()}", 'rb' );
    }
    
    private function _getLocalObjectStream( VideoFile $videoFile )
    {
        return $this->localFilesystem->readStream( $videoFile->getPath() );
    }
    
    private function _getTranscodedPresignedS3ObjectUrl( $id, $format ): string
    {
        //Creating a presigned URL
        $cmd    = $this->s3CoconutOutputAdapter->getS3Client()->getCommand( 'GetObject', [
            'Bucket'    => $this->s3CoconutOutputAdapter->getBucket(),
            'Key'       => \sprintf( 'video-%s-%s.mp4', $id, $format )
        ]);
        
        $request = $this->s3CoconutOutputAdapter->getS3Client()->createPresignedRequest( $cmd, '+1440 minutes' );
        
        // Get the actual presigned-url
        return (string)$request->getUri();
    }
}