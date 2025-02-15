<?php namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Sylius\Component\Resource\Model\ResourceInterface;

#[ORM\Entity]
#[ORM\Table(name: "VVP_CoconutSettings")]
class CoconutSettings implements ResourceInterface
{
    /** @var int */
    #[ORM\Id, ORM\Column(type: "integer"), ORM\GeneratedValue(strategy: "IDENTITY")]
    private $id;
    
    /** @var string */
    #[ORM\Column(type: "string", length: 64)]
    private $title;
    
    /** @var VideoPlatformStorage */
    #[ORM\ManyToOne(targetEntity: VideoPlatformStorage::class, inversedBy: "coconutOutputSettings")]
    #[ORM\JoinColumn(name: "storage_id", referencedColumnName: "id", nullable: false)]
    private $coconutStorage;
    
    /** @var string */
    #[ORM\Column(name: "coconut_api_key", type: "string")]
    private $coconutApiKey;
    
    /** @var array */
    #[ORM\Column(name: "coconut_output_formats", type: "json", nullable: false)]
    private $coconutOutputFormats;
    
    /** @var string */
    #[ORM\Column(name: "coconut_system_user", type: "string", length: 32)]
    private $coconutSystemUser;
    
    /** @var string */
    #[ORM\Column(name: "coconut_system_password", type: "string", length: 32)]
    private $coconutSystemPassword;
    
    /** @var string */
    #[ORM\Column(name: "coconut_input_url_type", type: "string", length: 32)]
    private $coconutInputUrlType;
    
    /** @var bool */
    #[ORM\Column(name: "coconut_watermark", type: "boolean", options: ["default" => 0])]
    private $coconutWatermark   = false;
    
    public function __construct()
    {
        $this->coconutOutputFormats = [];
    }
    
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
        $this->title = $title;
        
        return $this;
    }
    
    public function getCoconutStorage()
    {
        return $this->coconutStorage;
    }
    
    public function setCoconutStorage($coconutStorage)
    {
        $this->coconutStorage = $coconutStorage;
        
        return $this;
    }
    
    public function getCoconutApiKey()
    {
        return $this->coconutApiKey;
    }
    
    public function setCoconutApiKey($coconutApiKey)
    {
        $this->coconutApiKey    = $coconutApiKey;
        
        return $this;
    }
    
    public function getCoconutOutputFormats()
    {
        return $this->coconutOutputFormats;
    }
    
    public function setCoconutOutputFormats($coconutOutputFormats)
    {
        $this->coconutOutputFormats = $coconutOutputFormats;
        
        return $this;
    }
    
    public function getCoconutSystemUser()
    {
        return $this->coconutSystemUser;
    }
    
    public function setCoconutSystemUser( $coconutSystemUser )
    {
        $this->coconutSystemUser    = $coconutSystemUser;
        
        return $this;
    }
    
    public function getCoconutSystemPassword()
    {
        return $this->coconutSystemPassword;
    }
    
    public function setCoconutSystemPassword( $coconutSystemPassword )
    {
        $this->coconutSystemPassword    = $coconutSystemPassword;
        
        return $this;
    }
    
    public function getCoconutInputUrlType()
    {
        return $this->coconutInputUrlType;
    }
    
    public function setCoconutInputUrlType( $coconutInputUrlType )
    {
        $this->coconutInputUrlType  = $coconutInputUrlType;
        
        return $this;
    }
    
    public function getCoconutWatermark()
    {
        return $this->coconutWatermark;
    }
    
    public function setCoconutWatermark( $coconutWatermark )
    {
        $this->coconutWatermark = $coconutWatermark;
        
        return $this;
    }
}