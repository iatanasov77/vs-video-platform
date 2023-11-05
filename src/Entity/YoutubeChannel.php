<?php namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Sylius\Component\Resource\Model\ResourceInterface;

/**
 * @ORM\Entity
 * @ORM\Table(name="VVP_YoutubeChannels")
 */
class YoutubeChannel implements ResourceInterface
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
     * @ORM\Column(name="channel_id", type="string", nullable=false)
     */
    private $channelId;
    
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
    
    public function getChannelId()
    {
        return $this->channelId;
    }
    
    public function setChannelId($channelId)
    {
        $this->channelId    = $channelId;
        
        return $this;
    }
}