<?php namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Sylius\Component\Review\Model\ReviewerInterface;
use Vankosoft\UsersBundle\Model\UserInterface;
use Vankosoft\CatalogBundle\Model\Interfaces\ReviewerAwareInterface;
use Vankosoft\CatalogBundle\Model\Review;
use App\Entity\UserManagement\User;

#[ORM\Entity]
#[ORM\Table(name: "VVP_ActorReviews")]
class ActorReview extends Review
{
    /** {@inheritDoc} */
    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: "actorReviews")]
    #[ORM\JoinColumn(name: "author_id", referencedColumnName: "id", nullable: false, onDelete: "CASCADE")]
    protected $author;
    
    /** {@inheritDoc} */
    #[ORM\ManyToOne(targetEntity: Actor::class, inversedBy: "reviews")]
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