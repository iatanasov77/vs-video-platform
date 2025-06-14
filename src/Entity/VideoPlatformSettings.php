<?php namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Sylius\Component\Resource\Model\ResourceInterface;
use App\Entity\Catalog\AssociationType;

/**
 * @Doctrine\Common\Annotations\Annotation\IgnoreAnnotation( "ORM\MappedSuperclass" )
 * @Doctrine\Common\Annotations\Annotation\IgnoreAnnotation("ORM\Column")
 */
#[ORM\Entity]
#[ORM\Table(name: "VVP_VideoPlatformSettings")]
class VideoPlatformSettings implements ResourceInterface
{
    /** @var int */
    #[ORM\Id, ORM\Column(type: "integer"), ORM\GeneratedValue(strategy: "IDENTITY")]
    private $id;
    
    /** @var VideoPlatformApplication */
    #[ORM\OneToMany(targetEntity: VideoPlatformApplication::class, mappedBy: "settings")]
    private $videoPlatformApplication;
    
    /** @var string */
    #[ORM\Column(name: "settings_key", type: "string", length: 32)]
    private $settingsKey;
    
    /** @var CoconutSettings */
    #[ORM\ManyToOne(targetEntity: CoconutSettings::class)]
    #[ORM\JoinColumn(name: "coconut_settings_id", referencedColumnName: "id", nullable: true)]
    private $coconutSettings;
    
    /** @var VideoPlatformStorage */
    #[ORM\ManyToOne(targetEntity: VideoPlatformStorage::class, inversedBy: "originalVideosSettings")]
    #[ORM\JoinColumn(name: "original_videos_storage_id", referencedColumnName: "id", nullable: true)]
    private $originalVideosStorage;
    
    /** @var AssociationType */
    #[ORM\ManyToOne(targetEntity: AssociationType::class)]
    #[ORM\JoinColumn(name: "video_suggestions_association_type_id", referencedColumnName: "id", nullable: true)]
    private $videoSuggestionsAssociationType;
    
    /** @var bool */
    #[ORM\Column(name: "use_ffmpeg", type: "boolean", options: ["default" => 0])]
    private $useFFMpeg  = false;
    
    /** @var bool */
    #[ORM\Column(name: "display_only_transcoded", type: "boolean", options: ["default" => 0])]
    private $displayOnlyTranscoded  = false;
    
    /** @var bool */
    #[ORM\Column(name: "disable_videos_for_non_authenticated", type: "boolean", options: ["default" => 0])]
    private $disableVideosForNonAuthenticated = false;
    
    /** @var string */
    #[ORM\Column(name: "transcoded_video_urls_type", type: "string", columnDefinition: "ENUM('symfony_route', 'cloud_public', 'cloud_signed')", options: ["default" => "symfony_route"])]
    private $transcodedVideoUrlsType;
    
    /** @var string */
    #[ORM\Column(name: "signed_url_expiration", type: "string", columnDefinition: "ENUM('24_hours', 'duration_x1', 'duration_x2', 'duration_x3')", options: ["default" => "24_hours"])]
    private $signedUrlExpiration;
    
    /** @var string */
    #[ORM\Column(name: "user_sign_with", type: "string", columnDefinition: "ENUM('none', 'username', 'email', 'fullname')", options: ["default" => "none"])]
    private $userSignWith;
    
    /** @var string */
    #[ORM\Column(name: "video_clip_maker", type: "string", columnDefinition: "ENUM('none', 'coconut', 'ffmpeg')", options: ["default" => "none"])]
    private $videoClipMaker;
    
    /** @var bool */
    #[ORM\Column(name: "use_onhover_player", type: "boolean", options: ["default" => 0])]
    private $useOnhoverPlayer  = false;
    
    /** @var string */
    #[ORM\Column(name: "video_player", type: "string", columnDefinition: "ENUM('plyr', 'videojs')", options: ["default" => "plyr"])]
    private $videoPlayer;
    
    public function getId()
    {
        return $this->id;
    }
    
    public function getVideoPlatformApplication()
    {
        return $this->videoPlatformApplication;
    }
    
    public function setVideoPlatformApplication($videoPlatformApplication)
    {
        $this->videoPlatformApplication  = $videoPlatformApplication;
        
        return $this;
    }
    
    public function getSettingsKey()
    {
        return $this->settingsKey;
    }
    
    public function setSettingsKey($settingsKey)
    {
        $this->settingsKey  = $settingsKey;
        
        return $this;
    }
    
    public function getCoconutSettings()
    {
        return $this->coconutSettings;
    }
    
    public function setCoconutSettings($coconutSettings)
    {
        $this->coconutSettings  = $coconutSettings;
        
        return $this;
    }
    
    public function getOriginalVideosStorage()
    {
        return $this->originalVideosStorage;
    }
    
    public function setOriginalVideosStorage($originalVideosStorage)
    {
        $this->originalVideosStorage  = $originalVideosStorage;
        
        return $this;
    }
    
    public function getVideoSuggestionsAssociationType()
    {
        return $this->videoSuggestionsAssociationType;
    }
    
    public function setVideoSuggestionsAssociationType($videoSuggestionsAssociationType)
    {
        $this->videoSuggestionsAssociationType  = $videoSuggestionsAssociationType;
        
        return $this;
    }
    
    public function getUseFFMpeg()
    {
        return $this->useFFMpeg;
    }
    
    public function setUseFFMpeg( $useFFMpeg )
    {
        $this->useFFMpeg    = $useFFMpeg;
        
        return $this;
    }
    
    public function getDisplayOnlyTranscoded()
    {
        return $this->displayOnlyTranscoded;
    }
    
    public function setDisplayOnlyTranscoded( $displayOnlyTranscoded )
    {
        $this->displayOnlyTranscoded    = $displayOnlyTranscoded;
        
        return $this;
    }
    
    public function getDisableVideosForNonAuthenticated()
    {
        return $this->disableVideosForNonAuthenticated;
    }
    
    public function setDisableVideosForNonAuthenticated( $disableVideosForNonAuthenticated )
    {
        $this->disableVideosForNonAuthenticated = $disableVideosForNonAuthenticated;
        
        return $this;
    }
    
    public function getTranscodedVideoUrlsType()
    {
        return $this->transcodedVideoUrlsType;
    }
    
    public function setTranscodedVideoUrlsType( $transcodedVideoUrlsType )
    {
        $this->transcodedVideoUrlsType   = $transcodedVideoUrlsType;
        
        return $this;
    }
    
    public function getSignedUrlExpiration()
    {
        return $this->signedUrlExpiration;
    }
    
    public function setSignedUrlExpiration( $signedUrlExpiration )
    {
        $this->signedUrlExpiration  = $signedUrlExpiration;
        
        return $this;
    }
    
    public function getUserSignWith()
    {
        return $this->userSignWith;
    }
    
    public function setUserSignWith( $userSignWith )
    {
        $this->userSignWith  = $userSignWith;
        
        return $this;
    }
    
    public function getVideoClipMaker()
    {
        return $this->videoClipMaker;
    }
    
    public function setVideoClipMaker( $videoClipMaker )
    {
        $this->videoClipMaker    = $videoClipMaker;
        
        return $this;
    }
    
    public function getUseOnhoverPlayer()
    {
        return $this->useOnhoverPlayer;
    }
    
    public function setUseOnhoverPlayer( $useOnhoverPlayer )
    {
        $this->useOnhoverPlayer    = $useOnhoverPlayer;
        
        return $this;
    }
    
    public function getVideoPlayer()
    {
        return $this->videoPlayer;
    }
    
    public function setVideoPlayer( $videoPlayer )
    {
        $this->videoPlayer    = $videoPlayer;
        
        return $this;
    }
}