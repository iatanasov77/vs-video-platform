<?php namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Sylius\Component\Resource\Model\ResourceInterface;

/**
 * @ORM\Entity
 * @ORM\Table(name="VVP_CoconutSettings")
 */
class CoconutSettings implements ResourceInterface
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
     * @ORM\Column(name="title", type="string", length=64, nullable=false)
     */
    private $title;
    
    /**
     * @var Video
     *
     * @ORM\ManyToOne(targetEntity=VideoPlatformStorage::class, inversedBy="coconutOutputSettings")
     * @ORM\JoinColumn(name="storage_id", referencedColumnName="id", nullable=false)
     */
    private $coconutStorage;
    
    /**
     * @ORM\Column(name="coconut_api_key", type="string", nullable=false)
     */
    private $coconutApiKey;
    
    /**
     * @var array
     *
     * @ORM\Column(name="coconut_output_formats", type="json", nullable=false)
     */
    private $coconutOutputFormats;
    
    /**
     * @ORM\Column(name="coconut_system_user", type="string", length=32, nullable=false)
     */
    private $coconutSystemUser;
    
    /**
     * @ORM\Column(name="coconut_system_password", type="string", length=32, nullable=false)
     */
    private $coconutSystemPassword;
    
    /**
     * @ORM\Column(name="coconut_input_url_type", type="string", length=32, nullable=false)
     */
    private $coconutInputUrlType;
    
    /**
     * @var bool
     *
     * @ORM\Column(name="coconut_watermark", type="boolean", options={"default":"0"})
     */
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