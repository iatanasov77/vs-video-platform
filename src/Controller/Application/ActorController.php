<?php namespace App\Controller\Application;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Knp\Component\Pager\PaginatorInterface;

class ActorController extends AbstractController
{
    /** @var RepositoryInterface */
    private $actorsRepository;
    
    public function __construct(
        RepositoryInterface $actorsRepository
    ) {
        $this->actorsRepository = $actorsRepository;
    }
    
    public function index( Request $request, PaginatorInterface $paginator ): Response
    {
        $actors = $paginator->paginate(
            $this->actorsRepository->getQueryBuilder( 'bp' )->orderBy( 'bp.updatedAt', 'DESC' ),
            $request->query->getInt( 'page', 1 ) /*page number*/,
            18 /*limit per page*/
        );
        
        return $this->render( 'Pages/Actors/index.html.twig', [
            'actors'    => $actors,
        ]);
    }
    
    public function details( $slug, Request $request ): Response
    {
        return $this->render( 'Pages/Actors/details.html.twig', [
            'actor'    => $this->actorsRepository->findOneBy( ['slug' => $slug] ),
        ]);
    }
}