<?php namespace App\Widgets;

use Vankosoft\ApplicationBundle\EventListener\Widgets\WidgetLoaderInterface;
use Vankosoft\ApplicationBundle\Component\Widget\Builder\Item;
use Vankosoft\ApplicationBundle\EventListener\Event\WidgetEvent;

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
    public function builder( WidgetEvent $event )
    {
        /** @var Widget */
        $widgetContainer    = $event->getWidgetContainer();
        
        /** @var Item */
        $widgetItem = $widgetContainer->createWidgetItem( 'video-suggestions', false );
        if( $widgetItem ) {
            
            $widgetItem->setTemplate( 'Widgets/video_suggestions.html.twig' );
            
            // Add Widgets
            $widgetContainer->addWidget( $widgetItem );
        }
    }
}