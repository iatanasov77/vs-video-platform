<?php namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Sylius\Component\Resource\Model\ResourceInterface;
use Sylius\Component\Review\Model\ReviewableInterface;
use App\Entity\UserManagement\User;
use App\Entity\UsersSubscriptions\PayedService;
use App\Entity\Cms\SliderItem;

use Vankosoft\CatalogBundle\Model\ProductBase;

use Vankosoft\CatalogBundle\Model\Traits\Product\CategoriesAwareTrait;
use Vankosoft\CatalogBundle\Model\Traits\Product\PicturesAwareTrait;
use Vankosoft\CatalogBundle\Model\Traits\Product\FilesAwareTrait;

use Vankosoft\CatalogBundle\Model\Traits\ReviewableEntity;
use Vankosoft\CatalogBundle\Model\Traits\CommentableTrait;
use Vankosoft\CatalogBundle\Model\Interfaces\AssociationAwareInterface;
use Vankosoft\CatalogBundle\Model\Traits\AssociationAwareTrait;

#[ORM\Entity]
#[ORM\Table(name: "VVP_Videos")]
class Video extends ProductBase implements ResourceInterface, ReviewableInterface, AssociationAwareInterface
{
//     use CategoriesAwareTrait;
//     use PicturesAwareTrait;
//     use FilesAwareTrait;
    use AssociationAwareTrait;
    use ReviewableEntity;
    use CommentableTrait;
    
    /** @var VideoReview[] */
    #[ORM\OneToMany(targetEntity: "VideoReview", mappedBy: "reviewSubject", indexBy: "id", cascade: ["all"], orphanRemoval: true)]
    protected $reviews;
    
    /** @var VideoComment[] */
    #[ORM\OneToMany(targetEntity: "VideoComment", mappedBy: "commentSubject", indexBy: "id", cascade: ["all"], orphanRemoval: true)]
    protected $comments;
    
    /** @var User */
    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: "videos")]
    private $user;
    
    /** @var VideoFile */
    #[ORM\OneToOne(targetEntity: "VideoFile", mappedBy: "owner", cascade: ["persist", "remove"], orphanRemoval: true)]
    private $videoFile;
    
    /** @var Collection|VideoCategory[] */
    #[ORM\ManyToMany(targetEntity: "VideoCategory", inversedBy: "videos", indexBy: "id")]
    #[ORM\JoinTable(name: "VVP_Videos_Categories")]
    #[ORM\JoinColumn(name: "video_id", referencedColumnName: "id")]
    #[ORM\InverseJoinColumn(name: "category_id", referencedColumnName: "id")]
    private $categories;
    
    /** @var Collection|VideoGenre[] */
    #[ORM\ManyToMany(targetEntity: "VideoGenre", inversedBy: "videos", indexBy: "id")]
    #[ORM\JoinTable(name: "VVP_Videos_Genres")]
    #[ORM\JoinColumn(name: "video_id", referencedColumnName: "id")]
    #[ORM\InverseJoinColumn(name: "genre_id", referencedColumnName: "id")]
    private $genres;
    
    /** @var CoconutJob */
    #[ORM\OneToOne(targetEntity: "CoconutJob", mappedBy: "video", cascade: ["persist", "remove"], orphanRemoval: true)]
    private $coconutJob;
    
    /** @var Actor[] */
    #[ORM\ManyToMany(targetEntity: "Actor", inversedBy: "videos", indexBy: "id")]
    #[ORM\JoinTable(name: "VVP_Videos_Actors")]
    #[ORM\JoinColumn(name: "video_id", referencedColumnName: "id")]
    #[ORM\InverseJoinColumn(name: "actor_id", referencedColumnName: "id")]
    private $actors;
    
    /**
     * The Paid Services for wich the user has active payment can open this video
     * 
     * @var PayedService[]
     */
    #[ORM\ManyToMany(targetEntity: PayedService::class, indexBy: "id")]
    #[ORM\JoinTable(name: "VVP_Videos_PaidServices")]
    #[ORM\JoinColumn(name: "video_id", referencedColumnName: "id")]
    #[ORM\InverseJoinColumn(name: "paid_service_id", referencedColumnName: "id")]
    private $allowedPaidServices;
    
    /** @var VideoPhoto[] */
    #[ORM\OneToMany(targetEntity: "VideoPhoto", mappedBy: "owner", indexBy: "code", cascade: ["all"], orphanRemoval: true)]
    private $photos;
    
    /** @var Collection | User[] */
    #[ORM\ManyToMany(targetEntity: User::class, inversedBy: "watchedVideos", indexBy: "id")]
    #[ORM\JoinTable(name: "VVP_Videos_UsersWatched")]
    #[ORM\JoinColumn(name: "video_id", referencedColumnName: "id")]
    #[ORM\InverseJoinColumn(name: "user_id", referencedColumnName: "id")]
    private $watchedByUsers;
    
    /** @var SliderItem[] */
    #[ORM\OneToMany(targetEntity: SliderItem::class, mappedBy: "video", indexBy: "id", cascade: ["all"], orphanRemoval: true)]
    private $sliderItems;
    
    public function __construct()
    {
        parent::__construct();
        
        $this->categories           = new ArrayCollection();
        $this->genres               = new ArrayCollection();
        $this->actors               = new ArrayCollection();
        $this->allowedPaidServices  = new ArrayCollection();
        $this->photos               = new ArrayCollection();
        $this->watchedByUsers       = new ArrayCollection();
        $this->sliderItems          = new ArrayCollection();
        
        /** @var ArrayCollection<array-key, AssociationInterface> $this->associations */
        $this->associations         = new ArrayCollection();
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
    
    public function getCoconutJob()
    {
        return $this->coconutJob;
    }
    
    public function setCoconutJob($coconutJob)
    {
        $this->coconutJob   = $coconutJob;
        
        return $this;
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
    
    /**
     * @return Collection|PayedService[]
     */
    public function getAllowedPaidServices()
    {
        return $this->allowedPaidServices;
    }
    
    public function addAllowedPaidService( PayedService $allowedPaidService): self
    {
        if ( ! $this->allowedPaidServices->contains( $allowedPaidService ) ) {
            $this->allowedPaidServices[] = $allowedPaidService;
        }
        
        return $this;
    }
    
    public function removeAllowedPaidService( PayedService $allowedPaidService ): self
    {
        if ( $this->allowedPaidServices->contains( $allowedPaidService ) ) {
            $this->allowedPaidServices->removeElement( $allowedPaidService );
        }
        
        return $this;
    }
    
    public function getPhotos()
    {
        return $this->photos;
    }
    
    public function addPhoto( VideoPhoto $photo ): self
    {
        if ( ! $this->photos->contains( $photo ) ) {
            $this->photos[] = $photo;
            $photo->setVideo( $this );
        }
        
        return $this;
    }
    
    public function removePhoto( VideoPhoto $photo ): self
    {
        if ( $this->photos->contains( $photo ) ) {
            $this->photos->removeElement( $photo );
            $photo->setVideo( null );
        }
        
        return $this;
    }
    
    public function getPhoto( $photoId ):? VideoPhoto
    {
        if ( ! isset( $this->photos[$photoId] ) ) {
            return null;
        }
        
        return $this->photos[$photoId];
    }
    
    public function getWatchedByUsers()
    {
        return $this->watchedByUsers;
    }
    
    public function addWatchedByUsers( User $user ): self
    {
        if ( ! $this->watchedByUsers->contains( $user ) ) {
            $this->watchedByUsers[] = $user;
        }
        
        return $this;
    }
    
    public function removeWatchedByUsers( User $user ): self
    {
        if ( $this->watchedByUsers->contains( $user ) ) {
            $this->watchedByUsers->removeElement( $user );
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
    
    public function getTitle(): string
    {
        return $this->name;
    }
}
