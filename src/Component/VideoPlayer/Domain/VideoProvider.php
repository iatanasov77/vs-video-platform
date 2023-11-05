<?php namespace App\Component\VideoPlayer\Domain;

interface VideoProvider
{
    /**
     * Returns a list of videos for a specific provider request
     *
     * @param VideoProviderRequest $request
     * @return array|Video[]
     */
    public function videoList( VideoProviderRequest $request ): array;
    
    /**
     * It will return the first video of the last search
     *
     * @return Video
     */
    public function first():? Video;
    
    /**
     * Video player for this provider
     *
     * @return VideoPlayer
     */
    public function player(): VideoPlayer;
}