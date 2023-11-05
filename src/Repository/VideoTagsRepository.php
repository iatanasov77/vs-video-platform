<?php namespace App\Repository;

use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Query\ResultSetMapping;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use App\Entity\VideoTag;

class VideoTagsRepository extends EntityRepository
{
    public function getTags(): array
    {
        $query  = $this->createQueryBuilder( 'vt' )->select( 'vt.tag AS tag' )->getQuery();
        
        $resultMapping = new ResultSetMapping(); 
        $resultMapping->addScalarResult( 'tag', 'tag' );
        
        return $query->setResultSetMapping( $resultMapping )->getSingleColumnResult();
    }
    
    public function updateTags( array $tags ): void
    {
        $existingTags   = $this->getTags();
        
        $newTags        = \array_diff( $tags, $existingTags );
        
        $em             = $this->getEntityManager();
        foreach ( $newTags as $tag ) {
            $oTag   = new VideoTag();
            $oTag->setTag( $tag );
            $em->persist( $oTag );
        }
        $em->flush();
    }
}