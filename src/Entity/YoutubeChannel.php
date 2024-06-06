<?php namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Sylius\Component\Resource\Model\ResourceInterface;

#[ORM\Entity]
#[ORM\Table(name: "VVP_YoutubeChannels")]
class YoutubeChannel implements ResourceInterface
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
    #[ORM\Column(name: "channel_id", type: "string")]
    private $channelId;
    
    /** @var Collection|ActorPhoto[] */
    #[ORM\OneToOne(targetEntity: "YoutubeChannelPhoto", mappedBy: "owner", cascade: ["persist", "remove"], orphanRemoval: true)]
    private $photo;
    
    #[ORM\ManyToOne(targetEntity: GoogleCloudProject::class, inversedBy: "channels", cascade: ["persist", "remove"])]
    #[ORM\JoinColumn(name: "project_id", referencedColumnName: "id", nullable: true, onDelete: "CASCADE")]
    private $project;
    
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
    
    public function getPhoto()
    {
        return $this->photo;
    }
    
    public function setPhoto( $photo )
    {
        $this->photo    = $photo;
        
        return $this;
    }
    
    public function getProject()
    {
        return $this->project;
    }
    
    public function setProject( $project )
    {
        $this->project    = $project;
        
        return $this;
    }
}