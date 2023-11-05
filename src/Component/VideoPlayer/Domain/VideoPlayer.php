<?php namespace App\Component\VideoPlayer\Domain;

interface VideoPlayer
{
    
    /**
     * Creates the necessary HTML to render the provided video
     *
     * @param Video $video
     * @return string
     */
    public function render( ?Video $video ): string;
}