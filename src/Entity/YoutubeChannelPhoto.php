<?php namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Vankosoft\CmsBundle\Model\File;

#[ORM\Entity]
#[ORM\Table(name: "VVP_YoutubeChannels_Photos")]
class YoutubeChannelPhoto extends File
{
    /** @var Actor */
    #[ORM\ManyToOne(targetEntity: YoutubeChannel::class, inversedBy: "photo", cascade: ["persist", "remove"])]
    #[ORM\JoinColumn(name: "owner_id", referencedColumnName: "id", nullable: true, onDelete: "CASCADE")]
    protected $owner;
    
    public function getYoutubeChannel()
    {
        return $this->owner;
    }
    
    public function setYoutubeChannel( YoutubeChannel $channel ): self
    {
        $this->setOwner( $channel );
        
        return $this;
    }
}