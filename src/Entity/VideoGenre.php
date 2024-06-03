<?php namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Sylius\Component\Resource\Model\ResourceInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Vankosoft\ApplicationBundle\Model\Interfaces\TaxonDescendentInterface;
use Vankosoft\ApplicationBundle\Model\Traits\TaxonDescendentEntity;

#[ORM\Entity]
#[ORM\Table(name: "VVP_VideoGenres")]
class VideoGenre implements ResourceInterface, TaxonDescendentInterface
{
    use TaxonDescendentEntity;
    
    /** @var int */
    #[ORM\Id, ORM\Column(type: "integer"), ORM\GeneratedValue(strategy: "IDENTITY")]
    private $id;
    
    /** @var Collection|Video[] */
    #[ORM\ManyToMany(targetEntity: "Video", mappedBy: "genres")]
    #[ORM\OrderBy(["updatedAt" => "DESC"])]
    private $videos;
    
    /** @var Collection|Actor[] */
    #[ORM\ManyToMany(targetEntity: "Actor", mappedBy: "genres")]
    #[ORM\OrderBy(["updatedAt" => "DESC"])]
    private $actors;
    
    public function __construct()
    {
        $this->videos   = new ArrayCollection();
        $this->actors   = new ArrayCollection();
    }
    
    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->id;
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
            $video->addGenre( $this );
        }
        
        return $this;
    }
    
    public function removeVideo( Video $video ): VideoCategory
    {
        if ( $this->videos->contains( $video ) ) {
            $this->videos->removeElement( $video );
            $video->removeGenre( $this );
        }
        
        return $this;
    }
    
    /**
     * @return Collection|Actor[]
     */
    public function getActors(): Collection
    {
        return $this->actors;
    }
    
    public function addActor( Actor $actor ): self
    {
        if ( ! $this->actors->contains( $actor ) ) {
            $this->actors[] = $actor;
            $actor->addGenre( $this );
        }
        
        return $this;
    }
    
    public function removeActor( Actor $actor ): self
    {
        if ( $this->actors->contains( $actor ) ) {
            $this->actors->removeElement( $actor );
            $actor->removeGenre( $this );
        }
        
        return $this;
    }
    
    public function __toString()
    {
        return $this->taxon ? $this->taxon->getName() : '';
    }
}
