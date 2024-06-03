<?php namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Sylius\Component\Review\Model\ReviewerInterface;
use Vankosoft\UsersBundle\Model\UserInterface;
use Vankosoft\CatalogBundle\Model\Interfaces\ReviewerAwareInterface;
use Vankosoft\CatalogBundle\Model\Review;
use App\Entity\UserManagement\User;

#[ORM\Entity]
#[ORM\Table(name: "VVP_VideoReviews")]
class VideoReview extends Review
{
    /** {@inheritDoc} */
    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: "videoReviews")]
    #[ORM\JoinColumn(name: "author_id", referencedColumnName: "id", nullable: false, onDelete: "CASCADE")]
    protected $author;
    
    /** {@inheritDoc} */
    #[ORM\ManyToOne(targetEntity: "Video", inversedBy: "reviews")]
    #[ORM\JoinColumn(name: "subject_id", referencedColumnName: "id", nullable: false, onDelete: "CASCADE")]
    protected $reviewSubject;
    
    public function getAuthor(): ?ReviewerInterface
    {
        if ( $this->author instanceof ReviewerAwareInterface ) {
            return $this->author->_toReviewer();
        }
        
        return null;
    }
    
    public function getUser(): UserInterface
    {
        return $this->author;
    }
    
    public function setUser( UserInterface $user ): void
    {
        $this->author = $user;
    }
}