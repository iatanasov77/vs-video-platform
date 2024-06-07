<?php namespace App\Widgets;

use Vankosoft\ApplicationBundle\EventListener\Widgets\WidgetLoaderInterface;
use Vankosoft\ApplicationBundle\Component\Widget\Widget;
use Vankosoft\ApplicationBundle\Component\Widget\Builder\Item;
use Vankosoft\ApplicationBundle\EventListener\Event\WidgetEvent;
use Sylius\Component\Resource\Repository\RepositoryInterface;

class QuickLinksWidget implements WidgetLoaderInterface
{
    /** @var RepositoryInterface */
    private $quickLinksRepository;
    
    public function __construct(
        RepositoryInterface $quickLinksRepository
    ) {
        $this->quickLinksRepository = $quickLinksRepository;
    }
    
    public function builder( WidgetEvent $event )
    {
        $links  = $this->quickLinksRepository->findBy( ['enabled'=> 1] );
        
        /** @var Widget */
        $widgetContainer    = $event->getWidgetContainer();
        
        /** @var Item */
        $widgetItem = $widgetContainer->createWidgetItem( 'quick-links', false );
        if( $widgetItem ) {
            $widgetItem->setTemplate( 'Widgets/quick_links.html.twig', [
                'links' => $links,
            ]);
            
            // Add Widgets
            $widgetContainer->addWidget( $widgetItem );
        }
    }
}