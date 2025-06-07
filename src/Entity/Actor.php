<?php namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\DBAL\Types\Types;
use Gedmo\Mapping\Annotation as Gedmo;
use Sylius\Component\Resource\Model\ResourceInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Sylius\Component\Review\Model\ReviewableInterface;

use Vankosoft\ApplicationBundle\Model\Traits\TranslatableTrait;
use Vankosoft\ApplicationBundle\Model\Interfaces\TranslatableInterface;
use Vankosoft\CatalogBundle\Model\Traits\ReviewableEntity;
use Vankosoft\CatalogBundle\Model\Traits\CommentableTrait;
use App\Entity\Application\Translation;
use App\Entity\Cms\SliderItem;

/**
 * @Doctrine\Common\Annotations\Annotation\IgnoreAnnotation( "ORM\MappedSuperclass" )
 * @Doctrine\Common\Annotations\Annotation\IgnoreAnnotation("ORM\Column")
 */
#[ORM\Entity]
#[ORM\Table(name: "VVP_Actors")]
#[Gedmo\TranslationEntity(class: Translation::class)]
class Actor implements ResourceInterface, ReviewableInterface, TranslatableInterface
{
    //use CommentableTrait;
    use ReviewableEntity;
    use TimestampableEntity;
    use TranslatableTrait;
    
    /** @var int */
    #[ORM\Id, ORM\Column(type: "integer"), ORM\GeneratedValue(strategy: "IDENTITY")]
    private $id;
    
    /** @var Collection|Video[] */
    #[ORM\ManyToMany(targetEntity: Video::class, mappedBy: "actors", indexBy: "id")]
    #[ORM\OrderBy(["createdAt" => "DESC"])]
    private $videos;
    
    /** @var string */
    #[ORM\Column(type: "string", length: 255, unique: true)]
    #[Gedmo\Translatable]
    #[Gedmo\Slug(fields: ["name", "id"])]
    private $slug;
    
    /** @var string */
    #[ORM\Column(type: "string", length: 255)]
    #[Gedmo\Translatable]
    private $name;
    
    /** @var string */
    #[ORM\Column(type: "text")]
    #[Gedmo\Translatable]
    private $description;
    
    /** @var Collection|ActorPhoto[] */
    #[ORM\OneToMany(targetEntity: ActorPhoto::class, mappedBy: "owner", indexBy: "id", cascade: ["persist", "remove"], orphanRemoval: true)]
    private $photos;
    
    /** @var string | null */
    #[ORM\Column(type: "string", length: 255, nullable: true)]
    private $career;
    
    /** @var string | null */
    #[ORM\Column(type: "string", length: 8, nullable: true)]
    private $height;
    
    /** @var \DateTime | null */
    #[ORM\Column(name: "date_of_birth", type: Types::DATETIME_MUTABLE, nullable: true)]
    private $dateOfBirth;
    
    /** @var string | null */
    #[ORM\Column(name: "place_of_birth", type: "string", length: 255, nullable: true)]
    #[Gedmo\Translatable]
    private $placeOfBirth;
    
    /** @var Collection|VideoGenre[] */
    #[ORM\ManyToMany(targetEntity: VideoGenre::class, inversedBy: "actors", indexBy: "id")]
    #[ORM\JoinTable(name: "VVP_Actors_Genres")]
    #[ORM\JoinColumn(name: "actor_id", referencedColumnName: "id")]
    #[ORM\InverseJoinColumn(name: "genre_id", referencedColumnName: "id")]
    private $genres;
    
    /** {@inheritDoc} */
    #[Gedmo\Locale]
    protected $locale;
    
    /** @var VideoComment[] */
    /*
    #[ORM\OneToMany(targetEntity: ActorComment::class, mappedBy: "commentSubject", indexBy: "id", cascade: ["all"], orphanRemoval: true)]
    protected $comments;
    */
    
    /** @var VideoReview[] */
    #[ORM\OneToMany(targetEntity: ActorReview::class, mappedBy: "reviewSubject", indexBy: "id", cascade: ["all"], orphanRemoval: true)]
    protected $reviews;
    
    /** @var SliderItem[] */
    #[ORM\OneToMany(targetEntity: SliderItem::class, mappedBy: "actor", indexBy: "id", cascade: ["all"], orphanRemoval: true)]
    private $sliderItems;
    
    public function __construct()
    {
        $this->genres       = new ArrayCollection();
        $this->videos       = new ArrayCollection();
        $this->photos       = new ArrayCollection();
        $this->comments     = new ArrayCollection();
        $this->reviews      = new ArrayCollection();
        $this->sliderItems  = new ArrayCollection();
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
    
    public function getName(): ?string
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
    
    public function getCareer(): ?string
    {
        return $this->career;
    }
    
    public function setCareer($career): self
    {
        $this->career = $career;
        
        return $this;
    }
    
    public function getHeight(): ?string
    {
        return $this->height;
    }
    
    public function setHeight($height): self
    {
        $this->height = $height;
        
        return $this;
    }
    
    public function getDateOfBirth(): ?\DateTime
    {
        return $this->dateOfBirth;
    }
    
    public function setDateOfBirth($dateOfBirth): self
    {
        $this->dateOfBirth = $dateOfBirth;
        
        return $this;
    }
    
    public function getPlaceOfBirth(): ?string
    {
        return $this->placeOfBirth;
    }
    
    public function setPlaceOfBirth($placeOfBirth): self
    {
        $this->placeOfBirth = $placeOfBirth;
        
        return $this;
    }
    
    /**
     * @return Collection|VideoGenre[]
     */
    public function getGenres()
    {
        return $this->genres;
    }
    
    public function addGenre( VideoGenre $genre ): self
    {
        if ( ! $this->genres->contains( $genre ) ) {
            $this->genres[] = $genre;
        }
        
        return $this;
    }
    
    public function removeGenre( VideoGenre $genre ): self
    {
        if ( $this->genres->contains( $genre ) ) {
            $this->genres->removeElement( $genre );
        }
        
        return $this;
    }
    
    /**
     * @return Collection|SliderItem[]
     */
    public function getSliderItems()
    {
        return $this->sliderItems;
    }
    
    public function addSliderItem( SliderItem $sliderItem ): self
    {
        if ( ! $this->sliderItems->contains( $sliderItem ) ) {
            $this->sliderItems[] = $sliderItem;
        }
        
        return $this;
    }
    
    public function removeSliderItem( SliderItem $sliderItem ): self
    {
        if ( $this->sliderItems->contains( $sliderItem ) ) {
            $this->sliderItems->removeElement( $sliderItem );
        }
        
        return $this;
    }
    
    public function getBestVideo(): ?Video
    {
        $maxRating      = 0;
        $video  = $this->getVideos()->filter( function( Video $video ) use ( &$maxRating )
        {
            $condition  = $video->getAverageRating() > $maxRating;
            $maxRating  = $video->getAverageRating();
            
            return $condition;
        });
        
        return $video->count() ? $video->first() : null;
    }
}