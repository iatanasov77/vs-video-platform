<?php namespace App\Controller\Application;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Knp\Component\Pager\PaginatorInterface;
use Twig\Environment;

use Vankosoft\ApplicationBundle\Component\Status;
use App\Form\MoviesFilterForm;
use App\Component\MoviesFilter;
use App\Form\ActorsFilterForm;
use App\Component\ActorsFilter;

use App\Entity\VideoPlatformSettings;
use App\Component\VideoPlatform;

class FiltersController extends AbstractController
{
    /** @var Environment */
    private $templatingEngine;
    
    /** @var MoviesFilter */
    private $moviesFilter;
    
    /** @var ActorsFilter */
    private $actorsFilter;
    
    /** @var VideoPlatformSettings */
    private $videoPlatformSettings;
    
    /** @var VideoPlatform */
    private $videoPlatform;
    
    /** @var string */
    private $videoClipsDir;
    
    /** @var int */
    private $moviesPerPage  = 12;
    
    public function __construct(
        Environment $templatingEngine,
        MoviesFilter $moviesFilter,
        ActorsFilter $actorsFilter,
        VideoPlatform $videoPlatform,
        string $videoClipsDir
    ) {
        $this->templatingEngine = $templatingEngine;
        $this->moviesFilter     = $moviesFilter;
        $this->actorsFilter     = $actorsFilter;
        
        $this->videoPlatform                = $videoPlatform;
        $this->videoPlatformSettings        = $this->videoPlatform->getVideoPlatformSettings();
        
        $this->videoClipsDir                = $videoClipsDir;
    }
    
    public function handleMoviesFilter( $categorySlug, Request $request, PaginatorInterface $paginator ): Response
    {
        $form   = $this->createForm( MoviesFilterForm::class, null, ['method' => 'POST'] );
        
        $form->handleRequest( $request );
        if ( $form->isSubmitted() ) {
            $formData   = $form->getData();
            //var_dump( $formData['category'] ); die;
            
            $paginatorItems = $this->moviesFilter->getMovies( $categorySlug, $formData );
            $movies         = $paginator->paginate(
                $paginatorItems,
                $request->query->getInt( 'page', 1 ) /*page number*/,
                $this->moviesPerPage /*limit per page*/
            );
            
            $response   = $this->templatingEngine->render( 'Pages/Movies/Partial/movies-listing.html.twig', [
                'movies'        => $movies,
                'categorySlug'  => $categorySlug,
                'videoClipsDir'     => $this->videoClipsDir,
                'useOnhoverPlayer'  => $this->videoPlatformSettings->getUseOnhoverPlayer(),
            ]);
            
            return new JsonResponse([
                'status'    => Status::STATUS_OK,
                'response'  => $response,
            ]);
        }
        
        return new JsonResponse([
            'status'    => Status::STATUS_ERROR,
            'message'   => 'The Action is not Properly Called !!!',
        ]);
    }
    
    public function handleActorsFilter( Request $request, PaginatorInterface $paginator ): Response
    {
        $form   = $this->createForm( ActorsFilterForm::class, null, ['method' => 'POST'] );
        
        $form->handleRequest( $request );
        if ( $form->isSubmitted() ) {
            $formData   = $form->getData();
            
            $paginatorItems = $this->actorsFilter->getActors( $formData );
            $actors         = $paginator->paginate(
                $paginatorItems,
                $request->query->getInt( 'page', 1 ) /*page number*/,
                $this->moviesPerPage /*limit per page*/
             );
            
            $response   = $this->templatingEngine->render( 'Pages/Actors/Partial/actors-listing.html.twig', [
                'actors'        => $actors,
            ]);
            
            return new JsonResponse([
                'status'    => Status::STATUS_OK,
                'response'  => $response,
            ]);
        }
        
        return new JsonResponse([
            'status'    => Status::STATUS_ERROR,
            'message'   => 'The Action is not Properly Called !!!',
        ]);
    }
}