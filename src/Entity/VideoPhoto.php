<?php namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Vankosoft\CmsBundle\Model\File;

#[ORM\Entity]
#[ORM\Table(name: "VVP_Videos_Photos")]
class VideoPhoto extends File
{
    /** @var Video */
    #[ORM\ManyToOne(targetEntity: "Video", inversedBy: "photos", cascade: ["persist", "remove"])]
    #[ORM\JoinColumn(name: "owner_id", referencedColumnName: "id", nullable: true, onDelete: "CASCADE")]
    protected $owner;
    
    /** @var string */
    #[ORM\Column(name: "code", type: "string", length: 255)]
    private $code;
    
    /** @var string */
    #[ORM\Column(name: "description", type: "string", length: 255, nullable: true)]
    private $description;
    
    public function getVideo()
    {
        return $this->owner;
    }
    
    public function setVideo( Video $video ): self
    {
        $this->setOwner( $video );
        
        return $this;
    }
    
    public function getCode()
    {
        return $this->code;
    }
    
    public function setCode( $code ): self
    {
        $this->code = $code;
        
        return $this;
    }
    
    public function getDescription()
    {
        return $this->description;
    }
    
    public function setDescription( $description ): self
    {
        $this->description = $description;
        
        return $this;
    }
}
