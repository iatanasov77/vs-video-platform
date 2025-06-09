<?php namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Vankosoft\CmsBundle\Model\File;

/**
 * @Doctrine\Common\Annotations\Annotation\IgnoreAnnotation( "ORM\MappedSuperclass" )
 * @Doctrine\Common\Annotations\Annotation\IgnoreAnnotation("ORM\Column")
 */
#[ORM\Entity]
#[ORM\Table(name: "VVP_Videos_Files")]
class VideoFile extends File
{
    /** @var Video */
    #[ORM\OneToOne(targetEntity: Video::class, inversedBy: "videoFile", cascade: ["persist", "remove"], orphanRemoval: true)]
    #[ORM\JoinColumn(name: "owner_id", referencedColumnName: "id", nullable: true, onDelete: "CASCADE")]
    protected $owner;
    
    /** @var string */
    #[ORM\Column(name: "storage_type", type: "string", columnDefinition: "ENUM('coconut', 'local' , 's3' , 'digitalocean')")]
    private $storageType = 'local';
    
    /** @var string */
    #[ORM\Column(name: "duration", type: "string", length: 255, options: ["default" => 0])]
    private $duration   = '0';
    
    public function getVideo()
    {
        return $this->owner;
    }
    
    public function setVideo( Video $video ): self
    {
        $this->setOwner( $video );
        
        return $this;
    }
    
    public function getStorageType()
    {
        return $this->storageType;
    }
    
    public function setStorageType($storageType)
    {
        $this->storageType  = $storageType;
        
        return $this;
    }
    
    public function getDuration()
    {
        return $this->duration;
    }
    
    public function setDuration($duration)
    {
        $this->duration  = $duration;
        
        return $this;
    }
}
