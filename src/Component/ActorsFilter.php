<?php namespace App\Component;

use Symfony\Contracts\Translation\TranslatorInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use App\Entity\CoconutJob;

final class ActorsFilter
{
    const ORDERBY_NEWEST            = 'newest';
    const ORDERBY_OLDEST            = 'oldest';
    
    const ORDERBY_HIGHEST_RATING    = 'highest_rating';
    const ORDERBY_LOWEST_RATING     = 'lowest_rating';
    
    /** @var TranslatorInterface */
    private $translator;
    
    /** @var VideoPlatform */
    private $videoPlatform;
    
    /** @var RepositoryInterface */
    private $moviesCategoriesRepository;
    
    /** @var RepositoryInterface */
    private $actorsRepository;
    
    public function __construct(
        TranslatorInterface $translator,
        RepositoryInterface $actorsRepository
    ) {
        $this->translator                   = $translator;
        $this->actorsRepository             = $actorsRepository;
    }
    
    public function ratings(): array
    {
        return [
            self::ORDERBY_HIGHEST_RATING    => $this->translator->trans( 'vs_vvp.form.movies_filter.highest_rating', [], 'VanzVideoPlayer' ),
            self::ORDERBY_LOWEST_RATING     => $this->translator->trans( 'vs_vvp.form.movies_filter.lowest_rating', [], 'VanzVideoPlayer' ),
        ];
    }
    
    public function orderBy(): array
    {
        return [
            self::ORDERBY_NEWEST    => $this->translator->trans( 'vs_vvp.form.movies_filter.newest', [], 'VanzVideoPlayer' ),
            self::ORDERBY_OLDEST    => $this->translator->trans( 'vs_vvp.form.movies_filter.oldest', [], 'VanzVideoPlayer' ),
        ];
    }
    
    public function getActors( $formData = [] ): Collection
    {
        if( empty( $formData ) ) {
            return $this->getNonFilterdActors();
        }
        
        $actors     = new ArrayCollection();
        
        if ( $formData['genre'] && $formData['genre'] instanceof \App\Entity\VideoGenre ) {
            $actors = $formData['genre']->getActors();
        } else {
            $actors = $this->getNonFilterdActors( $actors );
        }
        
        if ( $formData['rating'] ) {
            $actors = $this->orderByAverageRating( $actors, $formData );
        }
        
        if ( $formData['order_by'] ) {
            $actors = $this->orderByDate( $actors, $formData );
        }
        
        //return $this->_removeNotEnabledActorsFromCollection( $actors );
        return $actors;
    }
    
    private function orderByAverageRating( Collection $actors, array $formData ): Collection
    {
        if ( $formData['rating'] == self::ORDERBY_HIGHEST_RATING ) {
            // get a new ArrayIterator
            $iterator   = $actors->getIterator();
            
            // define ordering closure, using preferred comparison method/field
            $iterator->uasort( function ( $first, $second )
            {
                return $first->getAverageRating() < $second->getAverageRating() ? 1 : -1;
            });
            
            $actors = new ArrayCollection( \iterator_to_array( $iterator ) );
        }
        
        if ( $formData['rating'] == self::ORDERBY_LOWEST_RATING ) {
            // get a new ArrayIterator
            $iterator   = $actors->getIterator();
            
            // define ordering closure, using preferred comparison method/field
            $iterator->uasort( function ( $first, $second )
            {
                return $first->getAverageRating() > $second->getAverageRating() ? 1 : -1;
            });
            
            $actors = new ArrayCollection( \iterator_to_array( $iterator ) );
        }
        
        return $actors;
    }
    
    private function orderByDate( Collection $actors, array $formData ): Collection
    {
        if ( $formData['order_by'] == self::ORDERBY_OLDEST ) {
            // get a new ArrayIterator
            $iterator   = $actors->getIterator();
            
            // define ordering closure, using preferred comparison method/field
            $iterator->uasort( function ( $first, $second )
            {
                return $first->getUpdatedAt() > $second->getUpdatedAt() ? 1 : -1;
            });
            
            $actors = new ArrayCollection( \iterator_to_array( $iterator ) );
        }
        
        if ( $formData['order_by'] == self::ORDERBY_NEWEST ) {
            // get a new ArrayIterator
            $iterator   = $actors->getIterator();
            
            // define ordering closure, using preferred comparison method/field
            $iterator->uasort( function ( $first, $second )
            {
                return $first->getUpdatedAt() < $second->getUpdatedAt() ? 1 : -1;
            });
            
            $actors = new ArrayCollection( \iterator_to_array( $iterator ) );
        }
        
        return $actors;
    }
    
    private function getNonFilterdActors( ?Collection $actors = null ): Collection
    {
        if ( ! $actors || $actors->isEmpty() ) {
            
            $actors = $this->actorsRepository->getQueryBuilder( 'ac' )
                            ->orderBy( 'ac.updatedAt', 'DESC' )
                            ->getQuery()
                            ->getResult();
            
            $actors = new ArrayCollection( $actors );
        }
        
        //return $this->_removeNotEnabledActorsFromCollection( $actors );
        return $actors;
    }
    
    private function _removeNotEnabledActorsFromCollection( Collection $actors ): Collection
    {
        foreach( $actors as $actor ) {
            if ( ! $actor->isEnabled() ) {
                $actors->removeElement( $actor );
                continue;
            }
        }
        
        return $actors;
    }
}