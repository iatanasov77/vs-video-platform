<?php namespace App\Component\VideoPlayer\Domain;

class VideoProviderRequest
{
    public string $command;
    public array $params;
    
    public function __construct( string $command, array $params )
    {
        $this->command  = $command;
        $this->params   = $params;
    }
}