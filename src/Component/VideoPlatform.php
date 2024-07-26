<?php namespace App\Component;

use Sylius\Component\Resource\Repository\RepositoryInterface;

use Vankosoft\ApplicationBundle\Component\Context\ApplicationContextInterface;
use App\Component\Cloud\Exception\VideoPlatformSettingsException;
use App\Entity\VideoPlatformSettings;
use App\Entity\VideoFile;
use App\Entity\Video;

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
    
    /** Video Photo Types */
    const VIDEO_PHOTO_TYPE_THUMBNAIL    = 'video_thumbnail';
    const VIDEO_PHOTO_TYPE_SLIDER       = 'video_slider_photo';
    const VIDEO_PHOTO_TYPE_OTHER        = 'video_other_photo';
    
    /** Video Actor Careers */
    const VIDEO_ACTOR_CAREER_ACTRESS    = 'vs_vvp.actor_career.video_actress';
    const VIDEO_ACTOR_CAREER_ACTOR      = 'vs_vvp.actor_career.video_actor';
    
    const STORAGE_TYPES = [
        self::STORAGE_TYPE_COCONUT  => 'Coconut',
        self::STORAGE_TYPE_LOCAL    => 'Local Storage',
        self::STORAGE_TYPE_S3       => 'AWS S3',
        self::STORAGE_TYPE_DO       => 'DigitalOcean Space'
    ];
    
    const INPUT_URL_TYPES = [
        self::URL_INPUT_TYPE_LOCAL          => 'vs_vvp.form.video_platform_settings.input_url_type_local',
        self::URL_INPUT_TYPE_S3             => 'vs_vvp.form.video_platform_settings.input_url_type_s3',
        self::URL_INPUT_TYPE_S3_PRESIGNED   => 'vs_vvp.form.video_platform_settings.input_url_type_s3_presigned',
    ];
    
    const VIDEO_PHOTO_TYPES = [
        self::VIDEO_PHOTO_TYPE_THUMBNAIL    => 'Video Thumbnail',
        self::VIDEO_PHOTO_TYPE_SLIDER       => 'Slider Photo',
        self::VIDEO_PHOTO_TYPE_OTHER        => 'Other Photo',
    ];
    
    const VIDEO_ACTOR_CAREERS = [
        self::VIDEO_ACTOR_CAREER_ACTOR      => 'Actor',
        self::VIDEO_ACTOR_CAREER_ACTRESS    => 'Actress',
    ];
    
    /** @var VideoPlatformSettings */
    private $videoPlatformSettings;
    
    /** @var VideoUrlsFactory */
    private $urlsFactory;
    
    /** @var VideoStreamsFactory */
    private $streamsFactory;
    
    /** @var VideoStorageBridge */
    private $storageBridge;
    
    /** @var VideoSignatory */
    private $videoSignatory;
    
    public function __construct(
        ApplicationContextInterface $applicationContext,
        VideoUrlsFactory $urlsFactory,
        VideoStreamsFactory $streamsFactory,
        VideoStorageBridge $storageBridge,
        VideoSignatory $videoSignatory
    ) {
        $videoPlatformApplication       = $applicationContext->getApplication()->getVideoPlatformApplication();
        if ( ! $videoPlatformApplication ) {
            throw new VideoPlatformSettingsException( 'Video Platform Settings IS NOT Configured for this Application !!!"' );
        }
        
        $this->videoPlatformSettings    = $videoPlatformApplication->getSettings();
        
        $this->urlsFactory              = $urlsFactory;
        $this->urlsFactory->setSettings( $this->videoPlatformSettings );
        
        $this->streamsFactory           = $streamsFactory;
        $this->streamsFactory->setSettings( $this->videoPlatformSettings );
        
        $this->storageBridge            = $storageBridge;
        $this->storageBridge->setSettings( $this->videoPlatformSettings );
        
        $this->videoSignatory           = $videoSignatory;
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
    
    public function getSuggestionsStrategy()
    {
        return $this->videoPlatformSettings->getVideoSuggestionsAssociationType();
    }
    
    public function getVideoUri( VideoFile $videoFile, string $bucket, bool $signed = true ): string
    {
        return $this->urlsFactory->getVideoUri( $videoFile, $bucket, $signed );
    }
    
    public function getTranscodedLocalUrl( $id, $format ): string
    {
        return $this->urlsFactory->getTranscodedLocalUrl( $id, $format );
    }
    
    public function getOriginalVideoStream( VideoFile $videoFile )
    {
        return $this->streamsFactory->getOriginalVideoStream( $videoFile );
    }
    
    public function getCoconutOutputStream( $id, $format )
    {
        return $this->streamsFactory->getCoconutOutputStream( $id, $format );
    }
    
    public function getUserSignedVideo( VideoFile $videoFile, string $bucket ): ?string
    {
        $videoUri   = $this->getVideoUri( $videoFile, $bucket );
        
        return $this->videoSignatory->getUserSignedVideo( $videoFile, $videoUri );
    }
    
    public function removeVideoFiles( VideoFile $videoFile, int $videoId ): void
    {
        $this->storageBridge->removeVideoFiles( $videoFile, $videoId );
    }
    
    public function getVideoFormats( Video $videoEntity, ?int $videoId = null ): array
    {
        return $this->urlsFactory->getVideoFormats( $videoEntity, $videoId );
    }
}