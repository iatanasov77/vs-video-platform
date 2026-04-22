<?php namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Sylius\Component\Resource\Model\ResourceInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Vankosoft\ApplicationBundle\Model\Interfaces\TaxonDescendentInterface;
use Vankosoft\ApplicationBundle\Model\Traits\TaxonDescendentEntity;

#[ORM\Entity]
#[ORM\Table(name: "VVP_VideoCategories")]
class VideoCategory implements ResourceInterface, TaxonDescendentInterface
{
    use TaxonDescendentEntity;
    
    /** @var int */
    #[ORM\Id, ORM\Column(type: "integer"), ORM\GeneratedValue(strategy: "IDENTITY")]
    private $id;
    
    /** @var VideoPlatformStorage */
    #[ORM\ManyToOne(targetEntity: VideoCategory::class, inversedBy: "children", cascade: ["all"])]
    private $parent;
    
    /** @var Collection|VideoCategory[] */
    #[ORM\OneToMany(targetEntity: VideoCategory::class, mappedBy: "parent", cascade: ["persist", "remove"], orphanRemoval: true)]
    private $children;
    
    /** @var Collection|Video[] */
    #[ORM\ManyToMany(targetEntity: Video::class, mappedBy: "categories")]
    #[ORM\OrderBy(["updatedAt" => "DESC"])]
    private $videos;
    
    public function __construct()
    {
        $this->children = new ArrayCollection();
        $this->videos   = new ArrayCollection();
    }
    
    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->id;
    }
    
    /**
     * {@inheritdoc}
     */
    public function getParent(): ?VideoCategory
    {
        return $this->parent;
    }
    
    /**
     * {@inheritdoc}
     */
    public function setParent(?VideoCategory $parent): VideoCategory
    {
        $this->parent = $parent;
        
        return $this;
    }
    
    public function getChildren(): Collection
    {
        return $this->children;
    }
    
    /**
     * @return Collection|Video[]
     */
    public function getVideos(): Collection
    {
        return $this->videos;
    }
    
    public function addVideo( Video $video ): VideoCategory
    {
        if ( ! $this->videos->contains( $video ) ) {
            $this->videos[] = $video;
            $video->addCategory( $this );
        }
        
        return $this;
    }
    
    public function removeVideo( Video $video ): VideoCategory
    {
        if ( $this->videos->contains( $video ) ) {
            $this->videos->removeElement( $video );
            $video->removeCategory( $this );
        }
        
        return $this;
    }
    
    public function __toString()
    {
        return $this->taxon ? $this->taxon->getName() : '';
    }
}
