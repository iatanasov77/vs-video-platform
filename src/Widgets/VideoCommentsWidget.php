<?php namespace App\Widgets;

use Vankosoft\ApplicationBundle\EventListener\Widgets\WidgetLoaderInterface;
use Vankosoft\ApplicationBundle\Component\Widget\Widget;
use Vankosoft\ApplicationBundle\Component\Widget\Builder\Item;
use Vankosoft\ApplicationBundle\EventListener\Event\WidgetEvent;

use Symfony\Component\HttpFoundation\RequestStack;
use Sylius\Component\Resource\Repository\RepositoryInterface;

class VideoCommentsWidget implements WidgetLoaderInterface
{
    /** @var RequestStack */
    private $requestStack;
    
    /** @var RepositoryInterface */
    private $videosRepository;
    
    public function __construct(
        RequestStack $requestStack,
        RepositoryInterface $videosRepository
    ) {
        $this->requestStack     = $requestStack;
        $this->videosRepository = $videosRepository;
    }
    
    public function builder( WidgetEvent $event )
    {
        $request    = $this->requestStack->getMainRequest();
        //$request    = $this->requestStack->getCurrentRequest();
        $videoSlug  = $request->attributes->get( 'slug' );
        $video      = $this->videosRepository->findOneBy( ['slug' => $videoSlug] );
        
        /** @var Widget */
        $widgetContainer    = $event->getWidgetContainer();
        
        /** @var Item */
        $widgetItem = $widgetContainer->createWidgetItem( 'video-comments' );
        if( $widgetItem ) {
            $widgetItem->setTemplate( 'Widgets/video_comments.html.twig', [
                //'locales'   => $this->localesRepository->findAll(),
                'videoSlug' => $videoSlug,
            ]);
            
            // Add Widgets
            $widgetContainer->addWidget( $widgetItem );
        }
    }
}