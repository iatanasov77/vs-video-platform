<?php namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Sylius\Component\Resource\Model\ResourceInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Sylius\Component\Resource\Model\TranslatableInterface;
use Sylius\Component\Resource\Model\TranslatableTrait;
use Sylius\Component\Resource\Model\TranslationInterface;

/**
 * @Gedmo\TranslationEntity(class="App\Entity\Application\Translation")
 * @ORM\Entity
 * @ORM\Table(name="VVP_Actors")
 */
class Actor implements ResourceInterface, TranslatableInterface
{
    use TimestampableEntity;
    use TranslatableTrait;
    
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;
    
    /**
     * @var Collection|Video[]
     *
     * @ORM\ManyToMany(targetEntity=Video::class, mappedBy="actors", indexBy="id")
     */
    private $videos;
    
    /**
     * @Gedmo\Translatable
     * @Gedmo\Slug(fields={"name", "id"})
     * @ORM\Column(name="slug", type="string", length=255, nullable=false, unique=true)
     */
    private $slug;
    
    /**
     * @Gedmo\Translatable
     * @ORM\Column(name="name", type="string", length=255, nullable=false)
     */
    private $name;
    
    /**
     * @Gedmo\Translatable
     * @ORM\Column(name="description", type="text", nullable=false)
     */
    private $description;
    
    /**
     * @ORM\OneToMany(targetEntity=ActorPhoto::class, mappedBy="owner", indexBy="id", cascade={"persist", "remove"})
     */
    private $photos;
    
    /**
     * @Gedmo\Locale
     * Used locale to override Translation listener`s locale
     * this is not a mapped field of entity metadata, just a simple property
     */
    private $locale;
    
    public function __construct()
    {
        $this->videos   = new ArrayCollection();
        $this->photos   = new ArrayCollection();
    }
    
    public function getId()
    {
        return $this->id;
    }
    
    /**
     * @return Collection|Video[]
     */
    public function getVideos()
    {
        return $this->videos;
    }
    
    public function addVideo( Video $video ): self
    {
        if ( ! $this->videos->contains( $video ) ) {
            $this->videos[] = $video;
            $video->addActor( $this );
        }
        
        return $this;
    }
    
    public function removeVideo( Video $video ): self
    {
        if ( $this->videos->contains( $video ) ) {
            $this->videos->removeElement( $video );
            $video->removeActor( $this );
        }
        
        return $this;
    }
    
    public function getSlug(): ?string
    {
        return $this->slug;
    }
    
    public function setSlug($slug): self
    {
        $this->slug = $slug;
        
        return $this;
    }
    
    public function getName()
    {
        return $this->name;
    }
    
    public function setName($name)
    {
        $this->name   = $name;
        
        return $this;
    }
    
    public function getDescription()
    {
        return $this->description;
    }
    
    public function setDescription($description)
    {
        $this->description  = $description;
        
        return $this;
    }
    
    public function getPhotos()
    {
        return $this->photos;
    }
    
    public function addPhoto( ActorPhoto $photo ): self
    {
        if ( ! $this->photos->contains( $photo ) ) {
            $this->photos[] = $photo;
            $photo->setActor( $this );
        }
        
        return $this;
    }
    
    public function removePhoto( ActorPhoto $photo ): self
    {
        if ( $this->photos->contains( $photo ) ) {
            $this->photos->removeElement( $photo );
            $photo->setActor( null );
        }
        
        return $this;
    }
    
    public function getPhoto( $photoId ):? ActorPhoto
    {
        if ( ! isset( $this->photos[$photoId] ) ) {
            return null;
        }
        
        return $this->photos[$photoId];
    }
    
    public function getLocale()
    {
        return $this->currentLocale;
    }
    
    public function getTranslatableLocale(): ?string
    {
        return $this->locale;
    }
    
    public function setTranslatableLocale($locale): self
    {
        $this->locale = $locale;
        
        return $this;
    }
    
    protected function createTranslation(): TranslationInterface
    {
        
    }
}