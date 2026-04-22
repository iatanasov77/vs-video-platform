<?php namespace App\Entity\Cms;

use Doctrine\ORM\Mapping as ORM;
use Vankosoft\CmsBundle\Model\SliderItem as BaseSliderItem;
use App\Entity\Actor;
use App\Entity\Video;

#[ORM\Entity]
#[ORM\Table(name: "VSCMS_SlidersItems")]
class SliderItem extends BaseSliderItem
{
    /** @var Actor */
    #[ORM\ManyToOne(targetEntity: Actor::class, inversedBy: "sliderItems")]
    #[ORM\JoinColumn(name: "actor_id", referencedColumnName: "id", nullable: true)]
    private $actor;
    
    /** @var Video */
    #[ORM\ManyToOne(targetEntity: Video::class, inversedBy: "sliderItems")]
    #[ORM\JoinColumn(name: "video_id", referencedColumnName: "id", nullable: true)]
    private $video;
    
    public function getActor(): ?Actor
    {
        return $this->actor;
    }
    
    public function setActor( $actor ): self
    {
        $this->actor = $actor;
        
        return $this;
    }
    
    public function getVideo(): ?Video
    {
        return $this->video;
    }
    
    public function setVideo( $video ): self
    {
        $this->video = $video;
        
        return $this;
    }
}