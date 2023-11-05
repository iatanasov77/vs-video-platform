<?php namespace App\Controller\VanzVideoPlayer;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

use Vankosoft\ApplicationBundle\Component\Context\ApplicationContextInterface;

class DefaultController extends AbstractController
{
    /** @var ApplicationContextInterface */
    private $applicationContext;
    
    /** @var Environment */
    private $templatingEngine;
    
    public function __construct(
        ApplicationContextInterface $applicationContext,
        Environment $templatingEngine
    ) {
        $this->applicationContext   = $applicationContext;
        $this->templatingEngine     = $templatingEngine;
    }
    
    public function index( Request $request ): Response
    {
        /* https://github.com/DigitalOceanPHP/Client
         * ***********************************************
        // create a new DigitalOcean client
        $client = new \DigitalOceanV2\Client();
        
        // authenticate the client with your access token which can be
        // generated at https://cloud.digitalocean.com/settings/applications
        $client->authenticate( 'your_access_token' );
        */
        
        
        return new Response( $this->templatingEngine->render( $this->getTemplate(), [] ) );
    }
    
    protected function getTemplate(): string
    {
        $template   = 'vanzvideoplayer/pages/Dashboard/index.html.twig';
        
        $appSettings    = $this->applicationContext->getApplication()->getSettings();
        if ( ! $appSettings->isEmpty() && $appSettings[0]->getTheme() ) {
            $template   = 'pages/Dashboard/index.html.twig';
        }
        
        return $template;
    }
}
