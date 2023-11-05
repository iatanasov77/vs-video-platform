<?php namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Vankosoft\CmsBundle\Model\File;

/**
 * @ORM\Entity
 * @ORM\Table(name="VVP_Videos_Thumbnails")
 */
class VideoThumbnail extends File
{
    /**
     * @ORM\OneToOne(targetEntity=Video::class, inversedBy="videoThumbnail", cascade={"persist", "remove"})
     * @ORM\JoinColumn(name="owner_id", referencedColumnName="id", nullable=true, onDelete="CASCADE")
     */
    protected $owner;
    
    public function getVideo()
    {
        return $this->owner;
    }
    
    public function setVideo( Video $video ): self
    {
        $this->setOwner( $video );
        
        return $this;
    }
}
