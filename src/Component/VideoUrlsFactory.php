<?php namespace App\Component;

use Symfony\Component\Routing\RouterInterface;
use App\Component\Cloud\Exception\VideoPlatformSettingsException;
use App\Component\VideoUploader\Adapter\GaufretteAwsS3\GaufretteAwsS3Adapter;
use App\Entity\VideoPlatformSettings;
use App\Entity\VideoFile;
use App\Entity\Video;
use App\Entity\CoconutJob;

class VideoUrlsFactory
{
    const URL_SYMFONY_ROUTE = 'symfony_route';
    const URL_CLOUD_PUBLIC  = 'cloud_public';
    const URL_CLOUD_SIGNED  = 'cloud_signed';
    
    const VIDEO_URL_TYPES     = [
        self::URL_SYMFONY_ROUTE => 'vs_vvp.form.video_platform_settings.video_url_type_symfony_route',
        self::URL_CLOUD_PUBLIC  => 'vs_vvp.form.video_platform_settings.video_url_type_cloud_public_url',
        self::URL_CLOUD_SIGNED  => 'vs_vvp.form.video_platform_settings.video_url_type_cloud_signed_url',
    ];
    
    /** @var RouterInterface */
    private $router;
    
    /** @var GaufretteAwsS3Adapter */
    private $s3OriginalVideosAdapter;
    
    /** @var GaufretteAwsS3Adapter */
    private $s3CoconutOutputAdapter;
    
    /** @var VideoPlatformSettings */
    private $videoPlatformSettings;
    
    public function __construct(
        RouterInterface $router,
        GaufretteAwsS3Adapter $s3OriginalVideosAdapter,
        GaufretteAwsS3Adapter $s3CoconutOutputAdapter
    ) {
        $this->router                   = $router;
        $this->s3OriginalVideosAdapter  = $s3OriginalVideosAdapter;
        $this->s3CoconutOutputAdapter   = $s3CoconutOutputAdapter;
    }
    
    public function setSettings( VideoPlatformSettings $videoPlatformSettings )
    {
        $this->videoPlatformSettings    = $videoPlatformSettings;
    }
    
    public function getVideoUri( VideoFile $videoFile, string $bucket, bool $signed = true ): string
    {
        switch ( $videoFile->getStorageType() ) {
            case VideoPlatform::STORAGE_TYPE_S3:
            case VideoPlatform::STORAGE_TYPE_DO:
                return $this->_getOriginalS3ObjectUrl( $videoFile, $bucket, $signed );
                
                break;
            case VideoPlatform::STORAGE_TYPE_LOCAL:
            case VideoPlatform::STORAGE_TYPE_COCONUT:
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
    
    public function getVideoFormats( Video $videoEntity, ?int $videoId = null ): array
    {
        $formats            = [];
        $coconutData        = $this->_coconutJobData( $videoEntity->getCoconutJob() );
        if ( ! $coconutData ) {
            return $formats;
        }
        
        $_videoId           = $videoId ?: $videoEntity->getId();
        if ( $coconutData->status == CoconutJob::EVENT_JOB_COMPLETED ) {
            //echo '<pre>'; var_dump( $coconutData->outputs ); die;
            
            foreach ( $coconutData->outputs as $output ) {
                if ( $output->key == 'httpstream' ) {
                    continue;
                }
                
                $formatParts            = \explode( ':', $output->key );
                switch ( $this->videoPlatformSettings->getTranscodedVideoUrlsType() ) {
                    case self::URL_SYMFONY_ROUTE:
                        $formats[$output->key]  = $this->getTranscodedLocalUrl( $_videoId, $formatParts[1] );
                        break;
                    case self::URL_CLOUD_PUBLIC:
                        $formats[$output->key]  = $this->_getTranscodedS3ObjectUrl( $_videoId, $formatParts[1] );
                        break;
                    case self::URL_CLOUD_SIGNED:
                        $formats[$output->key]  = $this->_getTranscodedS3ObjectUrl( $_videoId, $formatParts[1], true );
                        break;
                    default:
                        throw new VideoPlatformSettingsException( 'Video Platform Setting Transcoded Video Url Type Is Not Configured Properly !!!' );
                }
            }
        }
        //echo '<pre>'; var_dump( $formats ); die;
        
        return $formats;
    }
    
    private function _coconutJobData( $coconutJobEntity ): ?\stdClass
    {
        if ( ! $coconutJobEntity ) {
            return null;
        }
        
        $coconutJobStatusData   = $coconutJobEntity->getJobData();
        if ( ! $coconutJobStatusData ) {
            return null;
        }
        
        $jobData   = \json_decode( $coconutJobStatusData );
        if ( \property_exists( $jobData, 'data' ) ) {
            // Job Data From Webhook
            return $jobData->data;
        } else {
            // Job Data From Coconut API
            return $jobData;
        }
    }
    
    private function _getOriginalS3ObjectUrl( VideoFile $videoFile, string $bucket, bool $presigned = false ): string
    {
        $key    = $videoFile->getPath();
        
        if ( $presigned ) {
            $cmd        = $this->s3OriginalVideosAdapter->getS3Client()->getCommand( 'GetObject', [
                'Bucket'    => $bucket,
                'Key'       => $videoFile->getPath()
            ]);
            
            $request    = $this->s3OriginalVideosAdapter->getS3Client()->createPresignedRequest( $cmd, '+1440 minutes' );
            return (string)$request->getUri();
        }
        
        return $this->s3CoconutOutputAdapter->getS3Client()->getObjectUrl( $bucket, $key );
    }
    
    private function _getTranscodedS3ObjectUrl( $id, $format, bool $presigned = false ): string
    {
        $bucket = $this->s3CoconutOutputAdapter->getBucket();
        $key    = \sprintf( 'video-%s-%s.mp4', $id, $format );
        
        if ( $presigned ) {
            $cmd    = $this->s3CoconutOutputAdapter->getS3Client()->getCommand( 'GetObject', [
                'Bucket'    => $bucket,
                'Key'       => $key
            ]);
            
            $request = $this->s3CoconutOutputAdapter->getS3Client()->createPresignedRequest( $cmd, '+1440 minutes' );
            return (string)$request->getUri();
        }
        
        
        return $this->s3CoconutOutputAdapter->getS3Client()->getObjectUrl( $bucket, $key );
    }
}