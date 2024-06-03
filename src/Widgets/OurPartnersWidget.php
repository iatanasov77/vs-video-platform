<?php namespace App\Widgets;

use Vankosoft\ApplicationBundle\EventListener\Widgets\WidgetLoaderInterface;
use Vankosoft\ApplicationBundle\Component\Widget\Builder\Item;
use Vankosoft\ApplicationBundle\EventListener\Event\WidgetEvent;

use Symfony\Component\HttpFoundation\RequestStack;
use Sylius\Component\Resource\Repository\RepositoryInterface;

class OurPartnersWidget implements WidgetLoaderInterface
{
    /** @var RequestStack */
    private $requestStack;
    
    /** @var RepositoryInterface */
    private $pagesRepository;
    
    public function __construct(
        RequestStack $requestStack,
        RepositoryInterface $pagesRepository
    ) {
        $this->requestStack     = $requestStack;
        $this->pagesRepository  = $pagesRepository;
    }
    
    public function builder( WidgetEvent $event )
    {
        $request    = $this->requestStack->getMainRequest();
        //$request    = $this->requestStack->getCurrentRequest();
        
        $OurPartnersDescription = $this->pagesRepository->findOneBy( ['slug' => 'our-partners-description'] );
        $OurPartners            = $this->pagesRepository->findOneBy( ['slug' => 'our-partners'] );
        
        /** @var Widget */
        $widgetContainer    = $event->getWidgetContainer();
        
        /** @var Item */
        $widgetItem = $widgetContainer->createWidgetItem( 'our-partners' );
        if( $widgetItem ) {
            
            $widgetItem->setTemplate( 'Widgets/our_partners.html.twig', [
                'OurPartnersDescription'    => $OurPartnersDescription,
                'OurPartners'               => $OurPartners,
            ]);
            
            // Add Widgets
            $widgetContainer->addWidget( $widgetItem );
        }
    }
}