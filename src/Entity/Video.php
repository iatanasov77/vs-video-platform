<?php namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Sylius\Component\Resource\Model\ResourceInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Sylius\Component\Resource\Model\ToggleableTrait;
use Sylius\Component\Resource\Model\TranslatableInterface;
use Sylius\Component\Resource\Model\TranslatableTrait;
use Sylius\Component\Resource\Model\TranslationInterface;

use App\Entity\UserManagement\User;

/**
 * @Gedmo\TranslationEntity(class="App\Entity\Application\Translation")
 * @ORM\Entity
 * @ORM\Table(name="VVP_Videos")
 */
class Video implements ResourceInterface, TranslatableInterface
{
    use TimestampableEntity;
    use ToggleableTrait;    // About enabled field - $enabled (public)
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
     * @ORM\ManyToOne(targetEntity="App\Entity\UserManagement\User", inversedBy="videos")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;
    
    /**
     * @ORM\OneToOne(targetEntity=VideoFile::class, mappedBy="owner", cascade={"persist", "remove"})
     */
    private $videoFile;
    
    /**
     * @ORM\ManyToMany(targetEntity=VideoCategory::class, inversedBy="videos", indexBy="id")
     * @ORM\JoinTable(name="VVP_Videos_Categories",
     *      joinColumns={@ORM\JoinColumn(name="video_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="category_id", referencedColumnName="id")}
     * )
     */
    private $categories;
    
    /**
     * @Gedmo\Slug(fields={"title", "id"})
     * @ORM\Column(name="slug", type="string", length=255, nullable=false, unique=true)
     */
    private $slug;
    
    /**
     * @Gedmo\Translatable
     * @ORM\Column(name="title", type="string", length=255, nullable=false)
     */
    private $title;
    
    /**
     * @Gedmo\Translatable
     * @ORM\Column(name="description", type="text", nullable=false)
     */
    private $description;
    
    /**
     * @ORM\OneToOne(targetEntity=VideoThumbnail::class, mappedBy="owner", cascade={"persist", "remove"})
     */
    private $videoThumbnail;
    
    /**
     * @var CoconutJob
     *
     * @ORM\OneToOne(targetEntity="App\Entity\CoconutJob", mappedBy="video", cascade={"persist", "remove"})
     */
    private $coconutJob;
    
    /**
     * @ORM\ManyToMany(targetEntity=Actor::class, inversedBy="videos", indexBy="id")
     * @ORM\JoinTable(name="VVP_Videos_Actors",
     *      joinColumns={@ORM\JoinColumn(name="video_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="actor_id", referencedColumnName="id")}
     * )
     */
    private $actors;
    
    /**
     * @ORM\Column(name="tags", type="string", length=255, nullable=false, options={"default":"Full HD,18+"})
     */
    private $tags   = 'Full HD,18+';
    
    /**
     * @Gedmo\Locale
     * Used locale to override Translation listener`s locale
     * this is not a mapped field of entity metadata, just a simple property
     */
    private $locale;
    
    /**
     * @var bool
     *
     * @ORM\Column(name="public", type="boolean", options={"default":"1"})
     */
    protected $enabled = true;
    
    public function __construct()
    {
        $this->categories   = new ArrayCollection();
        $this->actors       = new ArrayCollection();
    }
    
    public function getId()
    {
        return $this->id; 
    }
    
    public function getUser()
    {
        return $this->user;
    }
    
    public function setUser($user)
    {
        $this->user = $user;
        
        return $this;
    }
    
    public function getVideoFile()
    {
        return $this->videoFile;
    }
    
    public function setVideoFile($videoFile)
    {
        $this->videoFile = $videoFile;
        
        return $this;
    }
    
    /**
     * @return Collection|VideoCategory[]
     */
    public function getCategories()
    {
        return $this->categories;
    }
    
    public function getCategory()
    {
        //return $this->categories->current();
        return $this->categories->first();
    }
    
    public function addCategory( VideoCategory $category ): self
    {
        if ( ! $this->categories->contains( $category ) ) {
            $this->categories[] = $category;
        }
        
        return $this;
    }
    
    public function removeCategory( VideoCategory $category ): self
    {
        if ( $this->categories->contains( $category ) ) {
            $this->categories->removeElement( $category );
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
    
    public function getTitle()
    {
        return $this->title;
    }
    
    public function setTitle($title)
    {
        $this->title   = $title;
        
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
    
    public function getVideoThumbnail()
    {
        return $this->videoThumbnail;
    }
    
    public function setVideoThumbnail($videoThumbnail)
    {
        $this->videoThumbnail = $videoThumbnail;
        
        return $this;
    }
    
    public function getCoconutJob()
    {
        return $this->coconutJob;
    }
    
    public function setCoconutJob($coconutJob)
    {
        $this->coconutJob   = $coconutJob;
        
        return $this;
    }
    
    public function isPublic(): bool
    {
        return $this->enabled;
    }
    
    public function isPublished(): bool
    {
        return $this->enabled;
    }
    
    /**
     * @return Collection|Actor[]
     */
    public function getActors()
    {
        return $this->actors;
    }
    
    public function addActor( Actor $actor ): self
    {
        if ( ! $this->actors->contains( $actor ) ) {
            $this->actors[] = $actor;
        }
        
        return $this;
    }
    
    public function removeActor( Actor $actor ): self
    {
        if ( $this->actors->contains( $actor ) ) {
            $this->actors->removeElement( $actor );
        }
        
        return $this;
    }
    
    public function getTags()
    {
        return $this->tags;
    }
    
    public function setTags($tags)
    {
        $this->tags = $tags;
        
        return $this;
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
