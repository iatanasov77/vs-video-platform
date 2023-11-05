<?php namespace App\EventListener;

use Symfony\Component\EventDispatcher\GenericEvent;
use App\Component\Cloud\Coconut;

class VideoResourceEventListener
{
    /** @var Coconut */
    private $coconut;
    
    public function __construct( Coconut $coconut )
    {
        $this->coconut  = $coconut;
    }
    
    public function onVideoCreate( GenericEvent $event )
    {
        $this->coconut->createJob( $event->getSubject() );
    }
}