<?php namespace App\Controller\VideoPlatform;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Knp\Component\Pager\PaginatorInterface;
use FFMpeg\FFProbe;
use Twig\Environment;
use Vankosoft\ApplicationBundle\Component\Status;

use App\Component\Cloud\Coconut;
use App\Form\MoviesFilterForm;
use App\Component\MoviesFilter;
use App\Entity\VideoPlatformSettings;
use App\Component\Cloud\Exception\VideoPlatformStorageException;
use App\Component\VideoPlatform;

class MoviesController extends AbstractController
{
    /** @var int */
    private $moviesPerPage  = 12;
    
    /** @var RepositoryInterface */
    private $moviesRepository;
    
    /** @var FFProbe */
    private $ffprobe;
    
    /** @var Coconut */
    private $coconut;
    
    /** @var Environment */
    private $templatingEngine;
    
    /** @var MoviesFilter */
    private $moviesFilter;
    
    /** @var VideoPlatformSettings */
    private $videoPlatformSettings;
    
    /** @var VideoPlatform */
    private $videoPlatform;
    
    public function __construct(
        RepositoryInterface $moviesRepository,
        FFProbe $ffprobe,
        Coconut $coconut,
        Environment $templatingEngine,
        MoviesFilter $moviesFilter,
        VideoPlatform $videoPlatform
    ) {
        $this->moviesRepository             = $moviesRepository;
        $this->ffprobe                      = $ffprobe;
        $this->coconut                      = $coconut;
        $this->templatingEngine             = $templatingEngine;
        $this->moviesFilter                 = $moviesFilter;
        
        $this->videoPlatform                = $videoPlatform;
        $this->videoPlatformSettings        = $this->videoPlatform->getVideoPlatformSettings();
    }
    
    public function index( $categorySlug, Request $request, PaginatorInterface $paginator ): Response
    {
        $filterForm = $this->createForm( MoviesFilterForm::class, null, ['method' => 'POST'] );
        
        $paginatorItems = $this->moviesFilter->getMovies( $categorySlug );
        $movies         = $paginator->paginate(
            $paginatorItems,
            $request->query->getInt( 'page', 1 ) /*page number*/,
            $this->moviesPerPage /*limit per page*/
        );
        
        return $this->render( 'Pages/Movies/index.html.twig', [
            'filterForm'    => $filterForm->createView(),
            'movies'        => $movies,
            'categorySlug'  => $categorySlug,
        ]);
    }
    
    public function latest( Request $request, PaginatorInterface $paginator ): Response
    {
        $paginatorItems = $this->moviesFilter->getMovies( 'latest' );
        $movies         = $paginator->paginate(
            $paginatorItems,
            $request->query->getInt( 'page', 1 ) /*page number*/,
            $this->moviesPerPage /*limit per page*/
        );
        
        return $this->render( 'Pages/pricing_plans.html.twig', [
            'movies'    => $movies,
        ]);
    }
    
    public function details( $categorySlug, $slug, Request $request ): Response
    {
        $settings       = $this->videoPlatform->getVideoPlatformSettings();
        
        if ( $redirectResponse  = $this->hasPermissionOrRedirect( $settings ) ) {
            return $redirectResponse;
        }
        
        $movie          = $this->moviesRepository->findOneBy( ['slug' => $slug] );
        //var_dump(  $this->videoPlatform->getVideoUri( $movie->getVideoFile(), 'videos-original' ) ); die;
        if ( ! $movie ) {
            throw new NotFoundHttpException( 'Sorry not existing !' );
        }
        
        $storageSettings    = $this->videoPlatform->getOriginalVideosStorage()->getSettings();
        if ( ! isset( $storageSettings['bucket'] ) ) {
            throw new VideoPlatformStorageException( 'Video Platform Storage Not Configured Properly !!!' );
        }
        
        $filmDuration   = null;
        if ( $this->videoPlatformSettings->getUseFFMpeg() ) {
            $oFile          = $movie->getVideoFile();
            
            $filmDuration   = $this->ffprobe->streams(
                $this->videoPlatform->getVideoUri( $oFile, $storageSettings['bucket'] )
            )->videos()->first()->get( 'duration' );
        }
        
        return $this->render( 'Pages/Movies/details.html.twig', [
            'movie'                 => $movie,
            'movieTags'             => \json_decode( $movie->getTags( )),
            'duration'              => $filmDuration,
            'formats'               => $this->videoPlatform->getVideoFormats( $movie ),
            'displayOnlyTranscoded' => $settings->getDisplayOnlyTranscoded(),
        ]);
    }
    
    public function dvdCollection( Request $request ): Response
    {
        return $this->render( 'Pages/pricing_plans.html.twig', [
            'movies'    => [],
        ]);
    }
    
    public function englishSubtitles( Request $request ): Response
    {
        return $this->render( 'Pages/pricing_plans.html.twig', [
            'movies'    => [],
        ]);
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
    
    protected function hasPermissionOrRedirect( $settings ): ?Response
    {
        if ( ! $settings->getDisableVideosForNonAuthenticated() ) {
            return null;
        }
        
        if ( ! $this->getUser() ) {
            return $this->redirectToRoute( 'vs_users_register_form' );
        }
        
        $subscription   = $this->getUser()->getPricingPlanSubscriptions()->last();
        //var_dump( $subscription->isActive() ); die;
        if ( ! $subscription || ! $subscription->isActive() ) {
            return $this->redirectToRoute( 'vs_payment_pricing_plans' );
        }
        
        return null;
    }
}