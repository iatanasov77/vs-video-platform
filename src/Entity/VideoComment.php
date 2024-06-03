<?php namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Tree\Entity\Repository\NestedTreeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Vankosoft\CatalogBundle\Model\CatalogComment;
use App\Entity\UserManagement\User;

#[ORM\Entity(repositoryClass: NestedTreeRepository::class)]
#[ORM\Table(name: "VVP_VideoComments")]
class VideoComment extends CatalogComment
{
    /** {@inheritDoc} */
    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: "comments")]
    #[ORM\JoinColumn(name: "author_id", referencedColumnName: "id", nullable: true, onDelete: "CASCADE")]
    protected $author;
    
    /** {@inheritDoc} */
    #[ORM\ManyToOne(targetEntity: "Video", inversedBy: "comments")]
    #[ORM\JoinColumn(name: "subject_id", referencedColumnName: "id", nullable: false, onDelete: "CASCADE")]
    protected $commentSubject;
    
    #[ORM\ManyToMany(targetEntity: User::class, inversedBy: "videoCommentLikes", indexBy: "id")]
    #[ORM\JoinTable(name: "VVP_VideoComment_UserLikes")]
    #[ORM\JoinColumn(name: "comment_id", referencedColumnName: "id")]
    #[ORM\InverseJoinColumn(name: "user_id", referencedColumnName: "id")]
    private $userLikes;
    
    #[ORM\ManyToMany(targetEntity: User::class, inversedBy: "videoCommentDislikes", indexBy: "id")]
    #[ORM\JoinTable(name: "VVP_VideoComment_UserDislikes")]
    #[ORM\JoinColumn(name: "comment_id", referencedColumnName: "id")]
    #[ORM\InverseJoinColumn(name: "user_id", referencedColumnName: "id")]
    private $userDislikes;
    
    public function __construct()
    {
        parent::__construct();
        
        $this->userLikes    = new ArrayCollection();
        $this->userDislikes = new ArrayCollection();
    }
    
    public function getUserLikes()
    {
        return $this->userLikes;
    }
    
    public function addUserLike( User $user ): self
    {
        if ( ! $this->userLikes->contains( $user ) ) {
            $this->userLikes[] = $user;
        }
        
        return $this;
    }
    
    public function removeUserLike( User $user ): self
    {
        if ( $this->userLikes->contains( $user ) ) {
            $this->userLikes->removeElement( $user );
        }
        
        return $this;
    }
    
    public function getUserDislikes()
    {
        return $this->userDislikes;
    }
    
    public function addUserDislike( User $user ): self
    {
        if ( ! $this->userDislikes->contains( $user ) ) {
            $this->userDislikes[] = $user;
        }
        
        return $this;
    }
    
    public function removeUserDislike( User $user ): self
    {
        if ( $this->userDislikes->contains( $user ) ) {
            $this->userDislikes->removeElement( $user );
        }
        
        return $this;
    }
    
    public function toggleUserLike( User $user ): self
    {
        $this->removeUserDislike( $user );
        if ( $this->userLikes->contains( $user ) ) {
            $this->userLikes->removeElement( $user );
        } else {
            $this->userLikes[] = $user;
        }
        $this->likes    = $this->userLikes->count();
        $this->dislikes = $this->userDislikes->count();
        
        return $this;
    }
    
    public function toggleUserDislike( User $user ): self
    {
        $this->removeUserLike( $user );
        if ( $this->userDislikes->contains( $user ) ) {
            $this->userDislikes->removeElement( $user );
        } else {
            $this->userDislikes[] = $user;
        }
        $this->likes    = $this->userLikes->count();
        $this->dislikes = $this->userDislikes->count();
        
        return $this;
    }
}