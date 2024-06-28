<?php namespace App\Widgets;

use Vankosoft\ApplicationBundle\EventListener\Widgets\WidgetLoaderInterface;
use Vankosoft\ApplicationBundle\Component\Widget\Builder\Item;
use Vankosoft\ApplicationBundle\EventListener\Event\WidgetEvent;

use Symfony\Component\HttpFoundation\RequestStack;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Vankosoft\CatalogBundle\Component\AssociationStrategy;
use App\Component\VideoPlatform;

/**
 * @TODO Create Catalog Associations to use for suggestions
 * 
 * EXAMPLES
 * ========
 * 'product_association', 'product_association_type' Resources Defined in Sylius\Bundle\ProductBundle\DependencyInjection\Configuration
 * 
 * https://docs.sylius.com/en/latest/book/products/product_associations.html
 */
class VideoSuggestionsWidget implements WidgetLoaderInterface
{
    /** @var RequestStack */
    private $requestStack;
    
    /** @var RepositoryInterface */
    private $videosRepository;
    
    /** @var VideoPlatform */
    private $videoPlatform;
    
    public function __construct(
        RequestStack $requestStack,
        RepositoryInterface $videosRepository,
        VideoPlatform $videoPlatform
    ) {
        $this->requestStack     = $requestStack;
        $this->videosRepository = $videosRepository;
        $this->videoPlatform    = $videoPlatform;
    }
    
    public function builder( WidgetEvent $event )
    {
        $request    = $this->requestStack->getMainRequest();
        //$request    = $this->requestStack->getCurrentRequest();
        $videoSlug  = $request->attributes->get( 'slug' );
        
        /** @var Widget */
        $widgetContainer    = $event->getWidgetContainer();
        
        /** @var Item */
        $widgetItem = $widgetContainer->createWidgetItem( 'video-suggestions' );
        if( $widgetItem ) {
            
            $widgetItem->setTemplate( 'Widgets/video_suggestions.html.twig', [
                //'locales'   => $this->localesRepository->findAll(),
                'videoSlug'         => $videoSlug,
                'videoSuggestions'  => $this->getVideoSuggestions( $videoSlug ),
            ]);
            
            // Add Widgets
            $widgetContainer->addWidget( $widgetItem );
        }
    }
    
    private function getVideoSuggestions( $videoSlug )
    {
        $video                  = $this->videosRepository->findOneBy( ['slug' => $videoSlug] );
        $suggestionsStrategy    = $this->videoPlatform->getSuggestionsStrategy()->getAssociationStrategy();
        
        switch ( $suggestionsStrategy ) {
            case AssociationStrategy::STRATEGY_SIMILAR:
                $videoSuggestions   = new ArrayCollection();
                foreach ( $video->getGenres() as $genre ) {
                    $videoSuggestions = new ArrayCollection( $videoSuggestions->toArray() + $genre->getVideos()->toArray() );
                }
                break;
            case AssociationStrategy::STRATEGY_RANDOM:
            default:
                $videoSuggestions       = $this->videosRepository->getRandomVideos( 6 );
        }
        
        return $videoSuggestions;
    }
}