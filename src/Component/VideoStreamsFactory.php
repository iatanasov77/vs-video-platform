<?php namespace App\Component;

use League\Flysystem\Filesystem as LeagueFilesystem;
use App\Component\VideoUploader\Adapter\GaufretteAwsS3\GaufretteAwsS3Adapter;
use App\Entity\VideoPlatformSettings;
use App\Entity\VideoFile;

class VideoStreamsFactory
{
    /** @var GaufretteAwsS3Adapter */
    private $s3OriginalVideosAdapter;
    
    /** @var GaufretteAwsS3Adapter */
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
        GaufretteAwsS3Adapter $s3OriginalVideosAdapter,
        GaufretteAwsS3Adapter $s3CoconutOutputAdapter,
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
                return $this->_getS3ObjectStream( $videoFile );
                
                break;
            case VideoPlatform::STORAGE_TYPE_LOCAL:
            case VideoPlatform::STORAGE_TYPE_COCONUT:
            default:
                return $this->_getLocalObjectStream( $videoFile );
        }
    }
    
    public function getCoconutOutputStream( $id, $format )
    {
        $videoFilePath  = \sprintf( 'video-%s-%s.mp4', $id, $format );
        
        switch ( $this->videoPlatformSettings->getCoconutSettings()->getCoconutStorage()->getStorageType() ) {
            case VideoPlatform::STORAGE_TYPE_S3:
            case VideoPlatform::STORAGE_TYPE_DO:
                $this->s3CoconutOutputAdapter->read( $videoFilePath );
                
                return \fopen( "s3://{$this->s3CoconutOutputAdapter->getBucket()}/{$videoFilePath}", 'rb' );
                
                break;
            default:
                return $this->coconutFilesystem->readStream( $videoFilePath );
        }
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
}