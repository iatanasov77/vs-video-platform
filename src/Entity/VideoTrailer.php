<?php namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Vankosoft\CmsBundle\Model\File;

#[ORM\Entity]
#[ORM\Table(name: "VVP_Videos_Trailers")]
class VideoTrailer extends File
{
    /** @var Video */
    #[ORM\OneToOne(targetEntity: Video::class, inversedBy: "videoTrailer", cascade: ["persist"])]
    #[ORM\JoinColumn(name: "owner_id", referencedColumnName: "id", nullable: true)]
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
