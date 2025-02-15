<?php namespace App\EventListener;

use Symfony\Component\EventDispatcher\GenericEvent;
use App\Component\Cloud\Coconut;
use App\Component\VideoPlatform;
use App\Component\VideoClipMaker;

final class VideoResourceEventListener
{
    /** @var Coconut */
    private $coconut;
    
    /** @var VideoPlatform */
    private $videoPlatform;
    
    /** @var VideoClipMaker */
    private $clipMaker;
    
    public function __construct( Coconut $coconut, VideoPlatform $videoPlatform, VideoClipMaker $clipMaker )
    {
        $this->coconut          = $coconut;
        $this->videoPlatform    = $videoPlatform;
        $this->clipMaker        = $clipMaker;
    }
    
    public function onVideoCreate( GenericEvent $event )
    {
        $videoEntity    = $event->getSubject();
        $this->coconut->createJob( $videoEntity );
        
        $settings   = $this->videoPlatform->getVideoPlatformSettings();
        if ( ! $settings->getDisplayOnlyTranscoded() ) {
            $videoUri   = $this->videoPlatform->getVideoUri(
                $videoEntity->getVideoFile(),
                $this->videoPlatform->getVideoPlatformSettings()->getOriginalVideosStorage()->getSettings()['bucket'],
                false
            );
            $this->clipMaker->createVideoClip( $videoEntity, $videoUri );
        }
    }
}