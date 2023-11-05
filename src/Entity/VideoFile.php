<?php namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Vankosoft\CmsBundle\Model\File;

/**
 * @ORM\Entity
 * @ORM\Table(name="VVP_Videos_Files")
 */
class VideoFile extends File
{
    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Video", inversedBy="videoFile", cascade={"persist", "remove"})
     * @ORM\JoinColumn(name="owner_id", referencedColumnName="id", nullable=true, onDelete="CASCADE")
     */
    protected $owner;
    
    /**
     * @ORM\Column(name="storage_type", type="string", columnDefinition="ENUM('coconut', 'local' , 's3' , 'digitalocean')", nullable=false)
     */
    private $storageType = 'local';
    
    public function getVideo()
    {
        return $this->owner;
    }
    
    public function setVideo( Video $video ): self
    {
        $this->setOwner( $video);
        
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
}
