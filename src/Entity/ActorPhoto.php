<?php namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Vankosoft\CmsBundle\Model\File;

/**
 * @ORM\Entity
 * @ORM\Table(name="VVP_Actors_Photos")
 */
class ActorPhoto extends File
{
    /**
     * @ORM\ManyToOne(targetEntity=Actor::class, inversedBy="photos", cascade={"persist", "remove"})
     * @ORM\JoinColumn(name="owner_id", referencedColumnName="id", nullable=true, onDelete="CASCADE")
     */
    protected $owner;
    
    
    public function getActor()
    {
        return $this->owner;
    }
    
    public function setActor( Actor $actor ): self
    {
        $this->setOwner( $actor );
        
        return $this;
    }
}
