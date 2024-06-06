<?php namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Gedmo\Mapping\Annotation as Gedmo;
use Sylius\Component\Resource\Model\ResourceInterface;

#[ORM\Entity]
#[ORM\Table(name: "VVP_GoogleCloudProjects")]
class GoogleCloudProject implements ResourceInterface
{
    /** @var int */
    #[ORM\Id, ORM\Column(type: "integer"), ORM\GeneratedValue(strategy: "IDENTITY")]
    private $id;
    
    /** @var string */
    #[ORM\Column(type: "string", length: 64)]
    private $title;
    
    /** @var string */
    #[ORM\Column(type: "string", length: 255, unique: true)]
    #[Gedmo\Slug(fields: ["title", "id"])]
    private $slug;
    
    /** @var string */
    #[ORM\Column(name: "google_api_key", type: "string")]
    private $googleApiKey;
    
    /** @var string */
    #[ORM\Column(name: "google_client_id", type: "string")]
    private $googleClientId;
    
    /** @var string */
    #[ORM\Column(name: "google_client_secret", type: "string")]
    private $googleClientSecret;
    
    /** @var YoutubeChannel[] */
    #[ORM\OneToMany(targetEntity: "YoutubeChannel", mappedBy: "project", indexBy: "id", cascade: ["all"], orphanRemoval: true)]
    private $channels;
    
    public function __construct()
    {
        $this->channels = new ArrayCollection();
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
    
    public function getChannels()
    {
        return $this->channels;
    }
    
    public function addChannel( YoutubeChannel $channel ): self
    {
        if ( ! $this->channels->contains( $channel ) ) {
            $this->channels[] = $channel;
        }
        
        return $this;
    }
    
    public function removeChannel( YoutubeChannel $channel ): self
    {
        if ( $this->channels->contains( $channel ) ) {
            $this->channels->removeElement( $channel );
        }
        
        return $this;
    }
}