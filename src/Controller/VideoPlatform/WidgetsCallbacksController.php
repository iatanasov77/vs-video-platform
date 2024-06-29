<?php namespace App\Controller\VideoPlatform;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Vankosoft\CatalogBundle\Component\AssociationStrategy;
use App\Component\VideoPlatform;

class WidgetsCallbacksController extends AbstractController
{
    /** @var VideoPlatform **/
    private $videoPlatform;
    
    /** @var RepositoryInterface **/
    private $videosRepository;
    
    public function __construct(
        VideoPlatform $videoPlatform,
        RepositoryInterface $videosRepository
    ) {
        $this->videoPlatform    = $videoPlatform;
        $this->videosRepository = $videosRepository;
    }
    
    public function getVideoSuggestionsAction( $videoSlug, Request $request ): Response
    {
        $video  = $this->videosRepository->findOneBy( ['slug' => $videoSlug] );
        $suggestionsStrategy    = $this->videoPlatform->getSuggestionsStrategy();
        $associationStrategy    = $suggestionsStrategy ? $suggestionsStrategy->getAssociationStrategy() : null;
        if ( ! $associationStrategy ) {
            return $this->render( 'WidgetsCallbacks/video-suggestions.html.twig', [
                'videoSuggestions'   => new ArrayCollection(),
            ]);
        }
        
        switch ( $associationStrategy ) {
            case AssociationStrategy::STRATEGY_SIMILAR:
                $videoSuggestions    = new ArrayCollection();
                foreach ( $video->getGenres() as $genre ) {
                    $videoSuggestions = new ArrayCollection( $videoSuggestions->toArray() + $genre->getVideos()->toArray() );
                }
                break;
            case AssociationStrategy::STRATEGY_RANDOM:
            default:
                $videoSuggestions       = $this->videosRepository->getRandomProducts( 6 );
        }
        
        return $this->render( 'WidgetsCallbacks/video-suggestions.html.twig', [
            'videoSuggestions'   => $videoSuggestions,
        ]);
    }
}