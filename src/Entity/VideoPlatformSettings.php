<?php namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Sylius\Component\Resource\Model\ResourceInterface;

/**
 * @ORM\Entity
 * @ORM\Table(name="VVP_VideoPlatformSettings")
 */
class VideoPlatformSettings implements ResourceInterface
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;
    
    /**
     * @ORM\Column(name="settings_key", type="string", length=32, nullable=false)
     */
    private $settingsKey;
    
    /**
     * @var Video
     *
     * @ORM\ManyToOne(targetEntity=CoconutSettings::class)
     * @ORM\JoinColumn(name="coconut_settings_id", referencedColumnName="id", nullable=true)
     */
    private $coconutSettings;
    
    /**
     * @var Video
     *
     * @ORM\ManyToOne(targetEntity=VideoPlatformStorage::class)
     * @ORM\JoinColumn(name="original_videos_storage_id", referencedColumnName="id", nullable=true)
     */
    private $originalVideosStorage;
    
    /**
     * @var bool
     *
     * @ORM\Column(name="use_ffmpeg", type="boolean", options={"default":"0"})
     */
    private $useFFMpeg  = false;
    
    /**
     * @var bool
     *
     * @ORM\Column(name="display_only_transcoded", type="boolean", options={"default":"0"})
     */
    private $displayOnlyTranscoded  = false;
    
    /**
     * @var bool
     *
     * @ORM\Column(name="create_user_signed_videos", type="boolean", options={"default":"0"})
     */
    private $createUserSignedVideos = false;
    
    /**
     * @var bool
     *
     * @ORM\Column(name="disable_videos_for_non_authenticated", type="boolean", options={"default":"0"})
     */
    private $disableVideosForNonAuthenticated = false;
    
    public function __construct()
    {
        $this->coconutOutputFormats = [];
    }

    public function getId()
    {
        return $this->id;
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
    
    public function getCreateUserSignedVideos()
    {
        return $this->createUserSignedVideos;
    }
    
    public function setCreateUserSignedVideos( $createUserSignedVideos )
    {
        $this->createUserSignedVideos   = $createUserSignedVideos;
        
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
}