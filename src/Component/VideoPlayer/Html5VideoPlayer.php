<?php namespace App\Component\VideoPlayer;

use App\Component\VideoPlayer\Domain\Video;
use App\Component\VideoPlayer\Domain\VideoPlayer;

/**
 * MANUAL: https://videojs.com/getting-started
 *         https://www.npmjs.com/package/video.js?activeTab=readme
 */
class Html5VideoPlayer implements VideoPlayer
{
    
    private $template = <<<EOT
<video
    id="VideoPlayer"
    data-video-id="%s"
    data-video-slug="%s"
    class="video-js"
    controls
    preload="auto"
    poster="%s"
    data-setup="{}"
>
    <source src="%s" type="video/mp4" />

    <p class="vjs-no-js">
        To view this video please enable JavaScript, and consider upgrading to a
        web browser that
        <a href="https://videojs.com/html5-video-support/" target="_blank">
            supports HTML5 video
        </a>
    </p>
</video>
EOT;
    
    
    /**
     * Creates the necessary HTML to render the provided video
     *
     * @param Video $video
     * @return string
     */
    public function render( ?Video $video ): string
    {
        if ( $video ) {
            return sprintf( $this->template, $video->videoId(), $video->videoSlug(), $video->thumbnail(), $video->videoUrl() );
        }
        
        return $this->template;
    }
}