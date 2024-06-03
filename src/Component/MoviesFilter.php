<?php namespace App\Component;

use Symfony\Contracts\Translation\TranslatorInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use App\Entity\CoconutJob;

final class MoviesFilter
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
    private $moviesRepository;
    
    public function __construct(
        TranslatorInterface $translator,
        VideoPlatform $videoPlatform,
        RepositoryInterface $moviesCategoriesRepository,
        RepositoryInterface $moviesRepository
    ) {
        $this->translator                   = $translator;
        $this->videoPlatform                = $videoPlatform;
        $this->moviesCategoriesRepository   = $moviesCategoriesRepository;
        $this->moviesRepository             = $moviesRepository;
    }
    
    public function qualities(): array
    {
        return [];
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
    
    public function getMovies( $categorySlug, $formData = [] ): Collection
    {
        if( empty( $formData ) ) {
            return $this->getNonFilterdMovies( $categorySlug );
        }
        
        $movies     = new ArrayCollection();
        if ( $formData['category'] && $formData['category'] instanceof \App\Entity\VideoCategory ) {
            $movies     = $formData['category']->getVideos();
        } else {
            $movies = $this->getNonFilterdMovies( $categorySlug, $movies );
        }
        
        if ( $formData['genre'] && $formData['genre'] instanceof \App\Entity\VideoGenre ) {
            $movies = $formData['genre']->getVideos();
        } else {
            $movies = $this->getNonFilterdMovies( $categorySlug, $movies );
        }
        
        if ( $formData['rating'] ) {
            $movies = $this->orderByAverageRating( $movies, $formData );
        }
        
        if ( $formData['order_by'] ) {
            $movies = $this->orderByDate( $movies, $formData );
        }
        
        //return $movies;
        return $this->_removeNotEnabledMoviesFromCollection( $movies );
    }
    
    private function orderByAverageRating( Collection $movies, array $formData ): Collection
    {
        if ( $formData['rating'] == self::ORDERBY_HIGHEST_RATING ) {
            // get a new ArrayIterator
            $iterator   = $movies->getIterator();
            
            // define ordering closure, using preferred comparison method/field
            $iterator->uasort( function ( $first, $second )
            {
                return $first->getAverageRating() < $second->getAverageRating() ? 1 : -1;
            });
            
            $movies = new ArrayCollection( \iterator_to_array( $iterator ) );
        }
        
        if ( $formData['rating'] == self::ORDERBY_LOWEST_RATING ) {
            // get a new ArrayIterator
            $iterator   = $movies->getIterator();
            
            // define ordering closure, using preferred comparison method/field
            $iterator->uasort( function ( $first, $second )
            {
                return $first->getAverageRating() > $second->getAverageRating() ? 1 : -1;
            });
            
            $movies = new ArrayCollection( \iterator_to_array( $iterator ) );
        }
        
        return $movies;
    }
    
    private function orderByDate( Collection $movies, array $formData ): Collection
    {
        if ( $formData['order_by'] == self::ORDERBY_OLDEST ) {
            // get a new ArrayIterator
            $iterator   = $movies->getIterator();
            
            // define ordering closure, using preferred comparison method/field
            $iterator->uasort( function ( $first, $second )
            {
                return $first->getUpdatedAt() > $second->getUpdatedAt() ? 1 : -1;
            });
            
            $movies = new ArrayCollection( \iterator_to_array( $iterator ) );
        }
        
        if ( $formData['order_by'] == self::ORDERBY_NEWEST ) {
            // get a new ArrayIterator
            $iterator   = $movies->getIterator();
            
            // define ordering closure, using preferred comparison method/field
            $iterator->uasort( function ( $first, $second )
            {
                return $first->getUpdatedAt() < $second->getUpdatedAt() ? 1 : -1;
            });
            
            $movies = new ArrayCollection( \iterator_to_array( $iterator ) );
        }
        
        return $movies;
    }
    
    private function getNonFilterdMovies( string $categorySlug, ?Collection $movies = null ): Collection
    {
        if ( ! $movies || $movies->isEmpty() ) {
            
            if ( $categorySlug == 'latest' ) {
                $movies = $this->moviesRepository->getQueryBuilder( 'mv' )
                                                ->orderBy( 'mv.updatedAt', 'DESC' )
                                                ->getQuery()
                                                ->getResult();
                
                $movies = new ArrayCollection( $movies );
            } else {
                $category   = $this->moviesCategoriesRepository->findByTaxonCode( $categorySlug );
                $movies     = $category->getVideos();
            }
            
        }
        
        return $this->_removeNotEnabledMoviesFromCollection( $movies );
    }
    
    private function _removeNotEnabledMoviesFromCollection( Collection $movies ): Collection
    {
        $settings   = $this->videoPlatform->getVideoPlatformSettings();
        
        foreach( $movies as $movie ) {
            if ( ! $movie->isEnabled() ) {
                $movies->removeElement( $movie );
                continue;
            }
            
            if (
                $settings->getDisplayOnlyTranscoded() &&
                $movie->getCoconutJob()->getStatus() !== CoconutJob::EVENT_JOB_COMPLETED
            ) {
                $movies->removeElement( $movie );
                continue;
            }
        }
        
        return $movies;
    }
}