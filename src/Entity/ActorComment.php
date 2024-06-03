<?php namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Tree\Entity\Repository\NestedTreeRepository;
use Vankosoft\CatalogBundle\Model\CatalogComment;
use App\Entity\UserManagement\User;

/*

#[ORM\Entity(repositoryClass: NestedTreeRepository::class)]
#[ORM\Table(name: "VVP_ActorComments")]


class ActorComment extends CatalogComment
{
    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: "comments")]
    #[ORM\JoinColumn(name: "author_id", referencedColumnName: "id", nullable: true, onDelete: "CASCADE")]
    protected $author;
    
    #[ORM\ManyToOne(targetEntity: "Actor", inversedBy: "comments")]
    #[ORM\JoinColumn(name: "subject_id", referencedColumnName: "id", nullable: false, onDelete: "CASCADE")]
    protected $commentSubject;
}
*/
