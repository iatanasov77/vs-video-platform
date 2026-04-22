<?php namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Sylius\Component\Resource\Model\ResourceInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

#[ORM\Entity]
#[ORM\Table(name: "VVP_VideoPlatformStorages")]
class VideoPlatformStorage implements ResourceInterface
{
    /** @var int */
    #[ORM\Id, ORM\Column(type: "integer"), ORM\GeneratedValue(strategy: "IDENTITY")]
    private $id;
    
    /** @var string */
    #[ORM\Column(type: "string", length: 64)]
    private $title;
    
    /** @var string */
    #[ORM\Column(name: "storage_type", type: "string", columnDefinition: "ENUM('coconut', 'local' , 's3' , 'digitalocean')")]
    private $storageType;
    
    /** @var array */
    #[ORM\Column(type: "json")]
    private $settings;
    
    /** @var Collection|VideoPlatformSettings[] */
    #[ORM\OneToMany(targetEntity: VideoPlatformSettings::class, mappedBy: "originalVideosStorage")]
    private $originalVideosSettings;
    
    /** @var Collection|CoconutSettings[] */
    #[ORM\OneToMany(targetEntity: CoconutSettings::class, mappedBy: "coconutStorage")]
    private $coconutOutputSettings;
    
    public function __construct()
    {
        $this->settings                 = [];
        $this->originalVideosSettings   = new ArrayCollection();
        $this->coconutOutputSettings    = new ArrayCollection();
    }
    
    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }
    
    public function getTitle()
    {
        return $this->title;
    }
    
    public function setTitle($title)
    {
        $this->title    = $title;
        
        return $this;
    }
    
    public function getStorageType()
    {
        return $this->storageType;
    }
    
    public function setStorageType($storageType)
    {
        $this->storageType  = $storageType;
        
        return $this;
    }
    
    public function getSettings()
    {
        return $this->settings;
    }
    
    public function setSettings($settings)
    {
        $this->settings = $settings;
        
        return $this;
    }
    
    public function getOriginalVideosSettings(): Collection
    {
        return $this->originalVideosSettings;
    }
    
    public function setOriginalVideosSettings($originalVideosSettings)
    {
        $this->originalVideosSettings = $originalVideosSettings;
        
        return $this;
    }
    
    public function getCoconutOutputSettings(): Collection
    {
        return $this->coconutOutputSettings;
    }
    
    public function setCoconutOutputSettings($coconutOutputSettings)
    {
        $this->coconutOutputSettings = $coconutOutputSettings;
        
        return $this;
    }
}