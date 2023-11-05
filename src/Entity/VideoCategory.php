<?php namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Sylius\Component\Resource\Model\ResourceInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Vankosoft\ApplicationBundle\Model\Interfaces\TaxonInterface;

/**
 * @ORM\Entity
 * @ORM\Table(name="VVP_VideoCategories")
 */
class VideoCategory implements ResourceInterface
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
     * @var VideoCategory
     *
     * @ORM\ManyToOne(targetEntity=VideoCategory::class, inversedBy="children", cascade={"all"})
     */
    private $parent;
    
    /**
     * @var Collection|VideoCategory[]
     *
     * @ORM\OneToMany(targetEntity=VideoCategory::class, mappedBy="parent")
     */
    private $children;
    
     /**
      * @var Collection|Video[]
      * 
     * @ORM\ManyToMany(targetEntity=Video::class, mappedBy="categories")
     * @ORM\OrderBy({"updatedAt" = "DESC"})
     */
    private $videos;
    
    /**
     * @var TaxonInterface
     *
     * @ORM\OneToOne(targetEntity="App\Entity\Application\Taxon", cascade={"all"})
     * @ORM\JoinColumn(name="taxon_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    private $taxon;
    
    public function __construct()
    {
        $this->children     = new ArrayCollection();
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
    
    /**
     * {@inheritdoc}
     */
    public function getTaxon(): ?TaxonInterface
    {
        return $this->taxon;
    }
    
    /**
     * {@inheritdoc}
     */
    public function setTaxon(?TaxonInterface $taxon): void
    {
        $this->taxon = $taxon;
    }

    public function getSlug()
    {
        return $this->taxon ? $this->taxon->getCode() : '';
    }
    
    public function getName()
    {
        return $this->taxon ? $this->taxon->getName() : '';
    }
    
    public function setName( string $name ) : self
    {
        if ( ! $this->taxon ) {
            // Create new taxon into the controller and set the properties passed from form
            return $this;
        }
        $this->taxon->setName( $name );
        
        return $this;
    }
    
    public function __toString()
    {
        return $this->taxon ? $this->taxon->getName() : '';
    }
    
    public function getNameTranslated( string $locale )
    {
        return $this->taxon ? $this->taxon->getTranslation( $locale )->getName() : '';
    }
}
