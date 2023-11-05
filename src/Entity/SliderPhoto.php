<?php namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Vankosoft\CmsBundle\Model\File;

/**
 * @ORM\Entity
 * @ORM\Table(name="VVP_SlidersPhotos")
 */
class SliderPhoto extends File
{
    /**
     * @ORM\OneToOne(targetEntity=Slider::class, inversedBy="photo", cascade={"persist", "remove"})
     * @ORM\JoinColumn(name="owner_id", referencedColumnName="id", nullable=true, onDelete="CASCADE")
     */
    protected $owner;
    
    public function getSlider()
    {
        return $this->owner;
    }
    
    public function setSlider( Slider $slider ): self
    {
        $this->setOwner( $slider );
        
        return $this;
    }
}