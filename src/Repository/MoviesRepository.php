<?php namespace App\Repository;

use Doctrine\ORM\QueryBuilder;
use Vankosoft\ApplicationBundle\Repository\TaxonDescendentRepository;

class MoviesRepository extends TaxonDescendentRepository
{
    public function getQueryBuilder( string $alias ): QueryBuilder
    {
        $qb = $this->createQueryBuilder( $alias );
        
        return $qb;
    }
    
    /**
     * https://digitalfortress.tech/php/get-random-rows-in-doctrine/
     * 
     * @param int $randCount
     * @return Query Result
     */
    public function getRandomVideos( int $randCount )
    {
        $em     = $this->getEntityManager();
        $conn   = $em->getConnection();
        
        // get random ID's using RAW SQL
        $sql    = 'SELECT id from VVP_Videos ORDER BY RAND() LIMIT ' . $randCount;
        $stmt   = $conn->prepare( $sql );
        $result = $stmt->execute();
        
        $randomIds = array();
        while ( $val = $result->fetch() ) {
            $randomIds[]    = $val['id'];
        }
        
        // native SQL in doctrine to load associated objects
        $query = $em->createQuery( "SELECT tt FROM App\Entity\Video tt WHERE tt.id in (:ids)" )->setParameter( 'ids', $randomIds );
        
        return $query->getResult();
    }
}