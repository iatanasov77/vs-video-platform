<?php namespace App\Component;

use League\Flysystem\Filesystem as LeagueFilesystem;
use App\Component\VideoUploader\Adapter\AwsS3AdapterInterface;
use App\Entity\VideoPlatformSettings;
use App\Entity\VideoFile;

class VideoStreamsFactory
{
    /** @var AwsS3AdapterInterface */
    private $s3OriginalVideosAdapter;
    
    /** @var AwsS3AdapterInterface */
    private $s3CoconutOutputAdapter;
    
    /** @var LeagueFilesystem */
    private $localFilesystem;
    
    /**
     * The Filesystem used from Coconut to Store Files by Ftp
     *
     * @var LeagueFilesystem
     */
    private $coconutFilesystem;
    
    /** @var VideoPlatformSettings */
    private $videoPlatformSettings;
    
    public function __construct(
        AwsS3AdapterInterface $s3OriginalVideosAdapter,
        AwsS3AdapterInterface $s3CoconutOutputAdapter,
        LeagueFilesystem $localFilesystem,
        LeagueFilesystem $coconutFilesystem,
    ) {
        $this->s3OriginalVideosAdapter  = $s3OriginalVideosAdapter;
        $this->s3CoconutOutputAdapter   = $s3CoconutOutputAdapter;
        $this->localFilesystem          = $localFilesystem;
        $this->coconutFilesystem        = $coconutFilesystem;
    }
    
    public function setSettings( VideoPlatformSettings $videoPlatformSettings )
    {
        $this->videoPlatformSettings    = $videoPlatformSettings;
    }
    
    public function getOriginalVideoStream( VideoFile $videoFile )
    {
        switch ( $videoFile->getStorageType() ) {
            case VideoPlatform::STORAGE_TYPE_S3:
            case VideoPlatform::STORAGE_TYPE_DO:
                return $this->_getS3ObjectStream( $this->s3OriginalVideosAdapter, $videoFile->getPath() );
                
                break;
            case VideoPlatform::STORAGE_TYPE_LOCAL:
            case VideoPlatform::STORAGE_TYPE_COCONUT:
            default:
                return $this->_getLocalObjectStream( $this->localFilesystem, $videoFile->getPath() );
        }
    }
    
    public function getCoconutOutputStream( $id, $format )
    {
        $videoFilePath  = \sprintf( 'video-%s-%s.mp4', $id, $format );
        
        switch ( $this->videoPlatformSettings->getCoconutSettings()->getCoconutStorage()->getStorageType() ) {
            case VideoPlatform::STORAGE_TYPE_S3:
            case VideoPlatform::STORAGE_TYPE_DO:
                return $this->_getS3ObjectStream( $this->s3CoconutOutputAdapter, $videoFilePath );
                
                break;
            default:
                return $this->_getLocalObjectStream( $this->coconutFilesystem, $videoFilePath );
        }
    }
    
    private function _getS3ObjectStream( AwsS3AdapterInterface $adapter, string $filePath )
    {
        $adapter->read( $filePath );
        
        return \fopen( "s3://{$adapter->getBucket()}/{$filePath}", 'rb' );
        //return \fopen( "fly://{$adapter->getBucket()}/{$filePath}", 'rb' );
    }
    
    private function _getLocalObjectStream( LeagueFilesystem $fileSystem, string $filePath )
    {
        return $fileSystem->readStream( $filePath );
    }
}