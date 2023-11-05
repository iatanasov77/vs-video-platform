<?php namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Sylius\Component\Resource\Model\ResourceInterface;

/**
 * @ORM\Entity
 * @ORM\Table(name="VVP_VideoTags")
 */
class VideoTag implements ResourceInterface
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;
    
    /**
     * @ORM\Column(name="tag", type="string", length=32, unique=true, nullable=false)
     */
    private $tag;
    
    public function getId()
    {
        return $this->id;
    }
    
    public function getTag()
    {
        return $this->tag;
    }
    
    public function setTag($tag)
    {
        $this->tag  = $tag;
        
        return $this;
    }
}