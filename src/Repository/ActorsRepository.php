<?php namespace App\Repository;

use Doctrine\ORM\QueryBuilder;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;

class ActorsRepository extends EntityRepository
{
    public function getQueryBuilder( string $alias ): QueryBuilder
    {
        $qb = $this->createQueryBuilder( $alias );
        
        return $qb;
    }
}