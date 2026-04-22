<?php namespace App\Entity\UserManagement;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Vankosoft\UsersBundle\Model\User as BaseUser;
use Vankosoft\UsersSubscriptionsBundle\Model\Interfaces\SubscribedUserInterface;
use Vankosoft\UsersSubscriptionsBundle\Model\Traits\SubscribedUserEntity;
use Vankosoft\PaymentBundle\Model\Interfaces\UserPaymentAwareInterface;
use Vankosoft\PaymentBundle\Model\Traits\UserPaymentAwareEntity;
use Vankosoft\PaymentBundle\Model\Interfaces\CustomerInterface;
use Vankosoft\PaymentBundle\Model\Traits\CustomerEntity;

use Vankosoft\CatalogBundle\Model\Interfaces\UserSubscriptionAwareInterface;
use Vankosoft\CatalogBundle\Model\Traits\UserSubscriptionAwareEntity;

use Vankosoft\CatalogBundle\Model\Interfaces\CommenterInterface;
use Vankosoft\CatalogBundle\Model\Interfaces\ReviewerAwareInterface;
use Vankosoft\CatalogBundle\Model\Interfaces\ReviewerInterface;
use Vankosoft\CatalogBundle\Model\Reviewer;
use App\Entity\VideoReview;
use App\Entity\ActorReview;
use App\Entity\VideoComment;
use App\Entity\ActorComment;
use App\Entity\Video;

#[ORM\Entity]
#[ORM\Table(name: "VSUM_Users")]
class User extends BaseUser implements
    SubscribedUserInterface,
    UserPaymentAwareInterface,
    CustomerInterface,
    UserSubscriptionAwareInterface,
    CommenterInterface,
    ReviewerAwareInterface
{
    use SubscribedUserEntity;
    use UserPaymentAwareEntity;
    use CustomerEntity;
    use UserSubscriptionAwareEntity;
    
    /** @var Collection */
    #[ORM\OneToMany(targetEntity: Video::class, mappedBy: "user", indexBy: "id")]
    private $videos;
    
    /** @var Collection */
    #[ORM\OneToMany(targetEntity: VideoReview::class, mappedBy: "author", indexBy: "id", cascade: ["all"])]
    private $videoReviews;
    
    /** @var Collection */
    #[ORM\OneToMany(targetEntity: ActorReview::class, mappedBy: "author", indexBy: "id", cascade: ["all"])]
    private $actorReviews;
    
    #[ORM\OneToMany(targetEntity: VideoComment::class, mappedBy: "author", indexBy: "id", cascade: ["all"])]
    private $videoComments;
    
    /*
    #[ORM\OneToMany(targetEntity: ActorComment::class, mappedBy: "author", indexBy: "id", cascade: ["all"])]
    private $actorComments;
    */
    
    #[ORM\ManyToMany(targetEntity: VideoComment::class, mappedBy: "userLikes", indexBy: "id")]
    private $videoCommentLikes;
    
    #[ORM\ManyToMany(targetEntity: VideoComment::class, mappedBy: "userDislikes", indexBy: "id")]
    private $videoCommentDislikes;
    
    #[ORM\ManyToMany(targetEntity: Video::class, mappedBy: "watchedByUsers", indexBy: "id")]
    private $watchedVideos;
    
    public function __construct()
    {
        $this->newsletterSubscriptions  = new ArrayCollection();
        $this->orders                   = new ArrayCollection();
        $this->pricingPlanSubscriptions = new ArrayCollection();
        
        $this->videos                   = new ArrayCollection();
        $this->actorReviews             = new ArrayCollection();
        $this->videoReviews             = new ArrayCollection();
        $this->actorComments            = new ArrayCollection();
        $this->videoComments            = new ArrayCollection();
        $this->videoCommentLikes        = new ArrayCollection();
        $this->videoCommentDislikes     = new ArrayCollection();
        $this->watchedVideos            = new ArrayCollection();
        
        parent::__construct();
    }
    
    /**
     * {@inheritDoc}
     */
    public function getRoles(): array
    {
        /* Use RolesArray ( OLD WAY )*/
        //return $this->getRolesFromArray();
        
        /* Use RolesCollection */
        return $this->getRolesFromCollection();
    }
    
    /**
     * @return Collection|VideoReview[]
     */
    public function getVideoReviews()
    {
        return $this->videoReviews;
    }
    
    public function addVideoReview( VideoReview $videoReview ): self
    {
        if ( ! $this->videoReviews->contains( $videoReview ) ) {
            $this->videoReviews[] = $videoReview;
            $videoReview->setAuthor( $this->_toReviewer() );
        }
        
        return $this;
    }
    
    public function removeVideoReview( VideoReview $videoReview ): self
    {
        if ( $this->videoReviews->contains( $videoReview ) ) {
            $this->videoReviews->removeElement( $videoReview );
            $videoReview->setAuthor( null );
        }
        
        return $this;
    }
    
    /**
     * @return Collection|VideoReview[]
     */
    public function getActorReviews()
    {
        return $this->actorReviews;
    }
    
    public function addActorReview( ActorReview $actorReview ): self
    {
        if ( ! $this->actorReviews->contains( $actorReview ) ) {
            $this->actorReviews[] = $actorReview;
            $actorReview->setAuthor( $this->_toReviewer() );
        }
        
        return $this;
    }
    
    public function removeActorReview( ActorReview $actorReview ): self
    {
        if ( $this->actorReviews->contains( $actorReview ) ) {
            $this->actorReviews->removeElement( $actorReview );
            $actorReview->setAuthor( null );
        }
        
        return $this;
    }
    
    /**
     * @return Collection|CatalogCommentInterface[]
     */
    public function getVideoComments(): Collection
    {
        return $this->videoComments;
    }
    
    public function setVideoComments( Collection $videoComments ): self
    {
        $this->videoComments  = $videoComments;
        
        return $this;
    }
    
    public function addVideoComment( CatalogCommentInterface $videoComment ): self
    {
        if ( ! $this->videoComments->contains( $videoComment ) ) {
            $this->videoComments[]    = $videoComment;
            $videoComment->setAuthor( $this );
        }
        
        return $this;
    }
    
    public function removeVideoComment( CatalogCommentInterface $videoComment ): self
    {
        if ( $this->videoComments->contains( $videoComment ) ) {
            $this->videoComments->removeElement( $videoComment );
            $videoComment->setAuthor( null );
        }
        
        return $this;
    }
    
    /*
    public function getActorComments(): Collection
    {
        return $this->actorComments;
    }
    
    public function setActorComments( Collection $actorComments ): self
    {
        $this->actorComments  = $actorComments;
        
        return $this;
    }
    
    public function addActorComment( CatalogCommentInterface $actorComment ): self
    {
        if ( ! $this->actorComments->contains( $actorComment ) ) {
            $this->actorComments[]    = $actorComment;
            $actorComment->setAuthor( $this );
        }
        
        return $this;
    }
    
    public function removeActorComment( CatalogCommentInterface $actorComment ): self
    {
        if ( $this->actorComments->contains( $actorComment ) ) {
            $this->actorComments->removeElement( $actorComment );
            $actorComment->setAuthor( null );
        }
        
        return $this;
    }
    */
    
    public function getComments(): Collection
    {
        return $this->videoComments;
    }
    
    public function getVideoCommentLikes(): Collection
    {
        return $this->videoCommentLikes;
    }
    
    public function getVideoCommentDislikes(): Collection
    {
        return $this->videoCommentDislikes;
    }
    
    public function getWatchedVideos(): Collection
    {
        return $this->watchedVideos;
    }
    
    public function _toReviewer(): ReviewerInterface
    {
        return Reviewer::fromUser( $this );
    }
}
