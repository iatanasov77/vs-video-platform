<?php namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Sylius\Component\Resource\Model\ResourceInterface;

/**
 * @ORM\Entity
 * @ORM\Table(name="VVP_GoogleCloudProjects")
 */
class GoogleCloudProject implements ResourceInterface
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
     * @ORM\Column(type="string", length=64, nullable=false)
     */
    private $title;
    
    /**
     * @Gedmo\Slug(fields={"title", "id"})
     * @ORM\Column(name="slug", type="string", length=255, nullable=false, unique=true)
     */
    private $slug;
    
    /**
     * @ORM\Column(name="google_api_key", type="string", nullable=false)
     */
    private $googleApiKey;
    
    /**
     * @ORM\Column(name="google_client_id", type="string", nullable=false)
     */
    private $googleClientId;
    
    /**
     * @ORM\Column(name="google_client_secret", type="string", nullable=false)
     */
    private $googleClientSecret;
    
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
    
    public function getSlug()
    {
        return $this->slug;
    }
    
    public function setSlug($slug)
    {
        $this->slug    = $slug;
        
        return $this;
    }
    
    public function getGoogleApiKey()
    {
        return $this->googleApiKey;
    }
    
    public function setGoogleApiKey($googleApiKey)
    {
        $this->googleApiKey = $googleApiKey;
        
        return $this;
    }
    
    public function getGoogleClientId()
    {
        return $this->googleClientId;
    }
    
    public function setGoogleClientId($googleClientId)
    {
        $this->googleClientId   = $googleClientId;
        
        return $this;
    }
    
    public function getGoogleClientSecret()
    {
        return $this->googleClientSecret;
    }
    
    public function setGoogleClientSecret($googleClientSecret)
    {
        $this->googleClientSecret   = $googleClientSecret;
        
        return $this;
    }
}