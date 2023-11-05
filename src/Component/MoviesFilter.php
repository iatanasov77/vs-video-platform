<?php namespace App\Component;

use Symfony\Contracts\Translation\TranslatorInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use App\Entity\CoconutJob;

final class MoviesFilter
{
    const ORDERBY_NEWEST    = 'newest';
    const ORDERBY_OLDEST    = 'oldest';
    
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
        return [];
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
        
        $movies = [];
        if ( $formData['category'] && $formData['category'] instanceof \App\Entity\VideoCategory ) {
            $movies = $formData['category']->getVideos();
        } else {
            $movies = $this->getNonFilterdMovies( $categorySlug );
        }
        
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
        
        //return $movies;
        return $this->_removeNotEnabledMoviesFromCollection( $movies );
    }
    
    private function getNonFilterdMovies( $categorySlug ): Collection
    {
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
        
        return $this-> _removeNotEnabledMoviesFromCollection( $movies );
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