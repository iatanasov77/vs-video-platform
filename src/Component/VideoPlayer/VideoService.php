<?php namespace App\Component\VideoPlayer;

use App\Component\VideoPlayer\Domain\Video;
use App\Component\VideoPlayer\Domain\VideoProvider;
use App\Component\VideoPlayer\Domain\VideoProviderRequest;

class VideoService
{
    const REQUEST_COMMAND_SEARCH        = 'search';
    const REQUEST_COMMAND_CHANNEL       = 'channel';
    const REQUEST_COMMAND_LATEST        = 'latest';
    const REQUEST_COMMAND_CATEGORY      = 'category';
    const REQUEST_COMMAND_GET_A_VIDEO   = 'get_a_video';
    
    /**
     * @var VideoProvider
     */
    private $provider;
    
    public function __construct( VideoProvider $provider )
    {
        $this->provider = $provider;
    }
    
    /**
     * @param VideoProviderRequest $request
     * @return array
     */
    public function videoList( VideoProviderRequest $request ): array
    {
        return $this->provider->videoList( $request );
    }
    
    public function first():? Video
    {
        return $this->provider->first();
    }
    
    public function render( ?Video $video ): string
    {
        $player = $this->provider->player();
        
        return $player->render( $video );
    }
}