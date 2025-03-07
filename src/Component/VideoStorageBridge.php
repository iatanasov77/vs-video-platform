<?php namespace App\Component;

use Symfony\Component\Filesystem\Filesystem;
use App\Component\VideoUploader\Adapter\AwsS3AdapterInterface;
use App\Component\Cloud\Exception\VideoPlatformStorageException;
use App\Entity\VideoPlatformSettings;
use App\Entity\VideoFile;

class VideoStorageBridge
{
    /** @var VideoUrlsFactory */
    private $urlsFactory;
    
    /** @var AwsS3AdapterInterface */
    private $s3OriginalVideosAdapter;
    
    /** @var AwsS3AdapterInterface */
    private $s3CoconutOutputAdapter;
    
    /** @var string */
    private $localVideosDirectory;
    
    /** @var VideoPlatformSettings */
    private $videoPlatformSettings;
    
    public function __construct(
        VideoUrlsFactory $urlsFactory,
        AwsS3AdapterInterface $s3OriginalVideosAdapter,
        AwsS3AdapterInterface $s3CoconutOutputAdapter,
        string $localVideosDirectory
    ) {
        $this->urlsFactory              = $urlsFactory;
        $this->s3OriginalVideosAdapter  = $s3OriginalVideosAdapter;
        $this->s3CoconutOutputAdapter   = $s3CoconutOutputAdapter;
        $this->localVideosDirectory     = $localVideosDirectory;
    }
    
    public function setSettings( VideoPlatformSettings $videoPlatformSettings )
    {
        $this->videoPlatformSettings    = $videoPlatformSettings;
    }
    
    public function removeVideoFiles( VideoFile $videoFile, int $videoId ): void
    {
        switch ( $this->videoPlatformSettings->getCoconutSettings()->getCoconutStorage()->getStorageType() ) {
            case VideoPlatform::STORAGE_TYPE_LOCAL:
                $fileVideo  = $this->localVideosDirectory . '/' . $videoFile->getPath();
                
                $filesystem = new Filesystem();
                $filesystem->remove( $fileVideo );
                
                break;
            case VideoPlatform::STORAGE_TYPE_S3:
            case VideoPlatform::STORAGE_TYPE_DO:
                $this->s3OriginalVideosAdapter->delete( $videoFile->getPath() );
                
                foreach ( \array_keys( $this->urlsFactory->getVideoFormats( $videoFile->getVideo(), $videoId ) ) as $format ) {
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
}