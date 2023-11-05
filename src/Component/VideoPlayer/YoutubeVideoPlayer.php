<?php namespace App\Component\VideoPlayer;

use App\Component\VideoPlayer\Domain\Video;
use App\Component\VideoPlayer\Domain\VideoPlayer;

class YoutubeVideoPlayer implements VideoPlayer
{
    
    private $template = <<<EOT
<object
    width="800"
    height="450"
    id="VideoPlayer"
    data="https://www.youtube.com/embed/%s"
></object>
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
            return sprintf( $this->template, $video->videoId() );
        }
        
        return $this->template;
    }
}