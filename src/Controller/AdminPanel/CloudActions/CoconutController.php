<?php namespace App\Controller\AdminPanel\CloudActions;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Vankosoft\ApplicationBundle\Component\Status;
use App\Component\Cloud\Coconut;

class CoconutController extends AbstractController
{
    /** @var Coconut */
    private $coconut;
    
    /** @var RepositoryInterface */
    private $videosRepository;
    
    public function __construct( Coconut $coconut, RepositoryInterface $videosRepository )
    {
        $this->coconut          = $coconut;
        $this->videosRepository = $videosRepository;
    }
    
    public function recreateCoconutJob( int $videoId, Request $request ): Response
    {
        $video  = $this->videosRepository->find( $videoId );
        
        $this->coconut->createJob( $video );
        
        return new JsonResponse([
            'status'    => Status::STATUS_OK,
            'message'   => 'Coconut Job Recreated.',
        ]);
    }
}