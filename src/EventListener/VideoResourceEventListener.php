<?php namespace App\EventListener;

use Symfony\Component\EventDispatcher\GenericEvent;
use App\Component\Cloud\Coconut\CoconutVideoJobBuilder;
use App\Component\Cloud\Coconut\CoconutClipJobBuilder;
use App\Component\VideoPlatform;
use App\Component\VideoClipMaker;

final class VideoResourceEventListener
{
    /** @var CoconutVideoJobBuilder */
    private $coconutVideoJob;
    
    /** @var CoconutClipJobBuilder */
    private $coconutClipJob;
    
    /** @var VideoPlatform */
    private $videoPlatform;
    
    /** @var VideoClipMaker */
    private $clipMaker;
    
    public function __construct(
        CoconutVideoJobBuilder $coconutVideoJob,
        CoconutClipJobBuilder $coconutClipJob,
        VideoPlatform $videoPlatform,
        VideoClipMaker $clipMaker
    ) {
        $this->coconutVideoJob  = $coconutVideoJob;
        $this->coconutClipJob   = $coconutClipJob;
        $this->videoPlatform    = $videoPlatform;
        $this->clipMaker        = $clipMaker;
    }
    
    public function onVideoCreate( GenericEvent $event )
    {
        $videoEntity    = $event->getSubject();
        $apiToken       = $this->coconutVideoJob->apiLogin();
        
        $this->coconutVideoJob->createJob( $videoEntity, $apiToken );
        
        $settings   = $this->videoPlatform->getVideoPlatformSettings();
        if ( $settings->getVideoClipMaker() == VideoClipMaker::CLIP_MAKER_COCONUT ) {
            $this->coconutClipJob->createJob( $videoEntity, $apiToken );
        } else {
            $videoUri   = $this->videoPlatform->getVideoUri(
                $videoEntity->getVideoFile(),
                $this->videoPlatform->getVideoPlatformSettings()->getOriginalVideosStorage()->getSettings()['bucket'],
                false
            );
            $this->clipMaker->createVideoClip( $videoEntity, $videoUri );
        }
    }
}