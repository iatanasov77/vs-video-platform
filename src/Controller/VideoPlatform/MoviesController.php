<?php namespace App\Controller\VideoPlatform;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Doctrine\Persistence\ManagerRegistry;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Knp\Component\Pager\PaginatorInterface;
use Twig\Environment;
use Psr\Log\LoggerInterface;

use Vankosoft\ApplicationBundle\Component\Status;
use Vankosoft\CatalogBundle\Component\ReviewFactory;
use Vankosoft\CatalogBundle\Model\Interfaces\ReviewInterface;
use App\Form\MovieReviewForm;
use App\Form\MovieCommentForm;
use App\Component\Cloud\Coconut\CoconutVideoJobBuilder;
use App\Form\MoviesFilterForm;
use App\Component\MoviesFilter;
use App\Entity\VideoPlatformSettings;
use App\Component\VideoPlatform;
use App\Entity\VideoReview;

class MoviesController extends AbstractController
{
    /** @var LoggerInterface */
    private $logger;
    
    /** @var ManagerRegistry */
    private $doctrine;
    
    /** @var TranslatorInterface */
    private $translator;
    
    /** @var int */
    private $moviesPerPage  = 12;
    
    /** @var RepositoryInterface */
    private $moviesRepository;
    
    /** @var RepositoryInterface */
    private $videoCommentsRepository;
    
    /** @var CoconutVideoJobBuilder */
    private $coconut;
    
    /** @var MoviesFilter */
    private $moviesFilter;
    
    /** @var VideoPlatformSettings */
    private $videoPlatformSettings;
    
    /** @var VideoPlatform */
    private $videoPlatform;
    
    /** @var ReviewFactory */
    private $reviewFactory;
    
    public function __construct(
        LoggerInterface $logger,
        ManagerRegistry $doctrine,
        TranslatorInterface $translator,
        RepositoryInterface $moviesRepository,
        RepositoryInterface $videoCommentsRepository,
        CoconutVideoJobBuilder $coconut,
        MoviesFilter $moviesFilter,
        VideoPlatform $videoPlatform,
        ReviewFactory $reviewFactory
    ) {
        $this->logger                       = $logger;
        $this->doctrine                     = $doctrine;
        $this->translator                   = $translator;
        $this->moviesRepository             = $moviesRepository;
        $this->videoCommentsRepository      = $videoCommentsRepository;
        $this->coconut                      = $coconut;
        $this->moviesFilter                 = $moviesFilter;
        
        $this->videoPlatform                = $videoPlatform;
        $this->videoPlatformSettings        = $this->videoPlatform->getVideoPlatformSettings();
        
        $this->reviewFactory                = $reviewFactory;
    }
    
    public function index( $categorySlug, Request $request, PaginatorInterface $paginator ): Response
    {
        $filterForm         = $this->createForm( MoviesFilterForm::class, null, ['method' => 'POST'] );
        $mobileFilterForm   = $this->createForm( MoviesFilterForm::class, null, ['method' => 'POST'] );
        
        $paginatorItems = $this->moviesFilter->getMovies( $categorySlug );
        $movies         = $paginator->paginate(
            $paginatorItems,
            $request->query->getInt( 'page', 1 ) /*page number*/,
            $this->moviesPerPage /*limit per page*/
        );
        
        return $this->render( 'Pages/Movies/index.html.twig', [
            'filterForm'        => $filterForm->createView(),
            'mobileFilterForm'  => $mobileFilterForm->createView(),
            'movies'            => $movies,
            'categorySlug'      => $categorySlug,
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
        
        $movieTags  = [];
        foreach ( $movies as $m ) {
            $movieTags[$m->getId()] = \json_decode( $m->getTags() );
        }
        
        return $this->render( 'Pages/pricing_plans.html.twig', [
            'movies'    => $movies,
            'movieTags' => $movieTags,
        ]);
    }
    
    public function details( $categorySlug, $slug, Request $request ): Response
    {
        $settings       = $this->videoPlatform->getVideoPlatformSettings();
        
        $movie          = $this->moviesRepository->findOneBy( ['slug' => $slug] );
        if ( ! $movie ) {
            throw new NotFoundHttpException( 'Sorry not existing !' );
        }
        
        if ( $redirectResponse  = $this->hasPermissionOrRedirect( $settings, $movie ) ) {
            return $redirectResponse;
        }
        
        $movieFormats	= $this->videoPlatform->getVideoFormats( $movie );
        $reviewForm     = $this->getUser() ? $this->createReviewForm( $this->reviewFactory->createReview( $movie ) ) : null;
        $commentForm    = $this->createCommentForm( $movie );
        
        $this->debugMovie( $movieFormats );
        
        return $this->render( 'Pages/Movies/details.html.twig', [
            'movie'                 => $movie,
            'movieTags'             => \json_decode( $movie->getTags() ),
            'formats'               => $movieFormats,
            'displayOnlyTranscoded' => $settings->getDisplayOnlyTranscoded(),
            'watermarkText'         => $this->getWatermarkText( $settings ),
            'reviewForm'            => $reviewForm ? $reviewForm->createView() : null,
            'commentForm'           => $commentForm->createView(),
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
    
    public function createReviewAction( $movieSlug, Request $request ): Response
    {
        $movie          = $this->moviesRepository->findOneBy( ['slug' => $movieSlug] );
        if ( ! $movie ) {
            throw new NotFoundHttpException( 'Sorry not existing !' );
        }
        
        $reviewForm = $this->createReviewForm( $this->reviewFactory->createReview( $movie ) );
        
        $reviewForm->handleRequest( $request );
        if ( ! $reviewForm->isSubmitted() ) {
            throw new \Exception( "'createReviewAction' is NOT Called Properly !!!" );
        }
        
        $em     = $this->doctrine->getManager();
        $review = $reviewForm->getData();
        
        $review->setUser( $this->getUser() );
        $review->setStatus( ReviewInterface::STATUS_ACCEPTED );
        
        $em->persist( $review );
        $em->flush();
        
        return $this->redirect( $request->headers->get( 'referer' ) );
    }
    
    public function createCommentAction( $movieSlug, Request $request ): Response
    {
        $movie          = $this->moviesRepository->findOneBy( ['slug' => $movieSlug] );
        if ( ! $movie ) {
            throw new NotFoundHttpException( 'Sorry not existing !' );
        }
        
        $commentForm = $this->createCommentForm( $movie );
        
        $commentForm->handleRequest( $request );
        if ( ! $commentForm->isSubmitted() ) {
            throw new \Exception( "'createReviewAction' is NOT Called Properly !!!" );
        }
        
        $em         = $this->doctrine->getManager();
        $comment    = $commentForm->getData();
        
        $comment->setAuthor( $this->getUser() );
        $comment->setCommentSubject( $movie );
        
        $em->persist( $comment );
        $em->flush();
        
        return $this->redirect( $request->headers->get( 'referer' ) );
    }
    
    public function likeCommentAction( $commentId, Request $request ): Response
    {
        $comment    = $this->videoCommentsRepository->find( $commentId );
        $user       = $this->getUser();
        if ( ! $user ) {
            return new JsonResponse([
                'status'    => Status::STATUS_ERROR
            ]);
        }
        
        $em = $this->doctrine->getManager();
        
        $newComment = $comment->toggleUserLike( $user );
        $em->persist( $comment );
        $em->flush();
        
        return new JsonResponse([
            'status'    => Status::STATUS_OK,
            'data'      => [
                'likes'     => $newComment->getLikes(),
                'dislikes'  => $newComment->getDislikes(),
            ]
        ]);
    }
    
    public function dislikeCommentAction( $commentId, Request $request ): Response
    {
        $comment    = $this->videoCommentsRepository->find( $commentId );
        $user       = $this->getUser();
        if ( ! $user ) {
            return new JsonResponse([
                'status'    => Status::STATUS_ERROR
            ]);
        }
        
        $em = $this->doctrine->getManager();
        
        $newComment = $comment->toggleUserDislike( $user );
        $em->persist( $comment );
        $em->flush();
        
        return new JsonResponse([
            'status'    => Status::STATUS_OK,
            'data'      => [
                'likes'     => $newComment->getLikes(),
                'dislikes'  => $newComment->getDislikes(),
            ]
        ]);
    }
    
    public function userWatchingVideoAction( $videoId, Request $request ): Response
    {
        $video    = $this->moviesRepository->find( $videoId );
        $user       = $this->getUser();
        if ( ! $user ) {
            return new JsonResponse([
                'status'    => Status::STATUS_ERROR,
            ]);
        }
        
        $em = $this->doctrine->getManager();
        
        $video->addWatchedByUsers( $user );
        $em->persist( $video );
        $em->flush();
        
        return new JsonResponse([
            'status'    => Status::STATUS_OK,
        ]);
    }
    
    protected function hasPermissionOrRedirect( $settings, $video ): ?Response
    {
        if ( ! $settings->getDisableVideosForNonAuthenticated() ) {
            return null;
        }
            
        $user   = $this->getUser();
        if ( ! $user ) {
            $this->addFlash(
                'error',
                $this->translator->trans( 'vs_vvp.template.alerts.user_not_authenticated', [], 'VanzVideoPlayer' )
            );
            return $this->redirectToRoute( 'vs_users_register_form' );
        }
        
        if ( $user->hasRole( 'ROLE_SUPER_ADMIN' ) || $user->hasRole( 'ROLE_ADMIN' ) || $user->hasRole( 'ROLE_APPLICATION_ADMIN' ) ) {
            return null;
        }
        
        $hasPermission = false;
        foreach ( $video->getAllowedPaidServices() as $service ) {
            $subscription   = $user->getActivePricingPlanSubscriptionByService( $service );
            if ( $subscription ) {
                $hasPermission  = true;
            }
        }
        
        if ( ! $hasPermission ) {
            $this->addFlash(
                'error',
                $this->translator->trans( 'vs_vvp.template.alerts.missing_active_subscription', [], 'VanzVideoPlayer' )
            );
            return $this->redirectToRoute( 'vs_payment_pricing_plans' );
        }
        
        return null;
    }
    
    protected function getWatermarkText( $settings ): ?string
    {
        if ( ! $settings->getDisableVideosForNonAuthenticated() ) {
            //return null;
        }
        
        if ( $this->getUser() && $this->getUser()->getInfo() ) {
            return $this->getUser()->getInfo()->getFullName();
        }
        
        return null;
    }
    
    protected function createReviewForm( ReviewInterface $review )
    {
        $form   = $this->createForm(
            MovieReviewForm::class,
            $review,
            [
                'action'            => $this->generateUrl( 'vvp_movies_create_review', [
                    'movieSlug' => $review->getReviewSubject()->getSlug()
                ]),
                'method'            => 'POST',
                'rating_steps'      => 10,
                'rating_expanded'   => false,
            ]
        );
        
        return $form;
    }
    
    protected function createCommentForm( $video )
    {
        $form   = $this->createForm(
            MovieCommentForm::class,
            null,
            [
                'action'            => $this->generateUrl( 'vvp_movies_create_comment', [
                    'movieSlug' => $video->getSlug()
                ]),
                'method'            => 'POST',
                'video'             => $video->getId(),
                'parent_comment'    => 0,
            ]
        );
        
        return $form;
    }
    
    private function debugMovie( array $movieFormats )
    {
        //echo '<pre>'; var_dump( $movieFormats ); die;
        if ( $this->getUser() && $this->getUser()->hasRole( 'ROLE_SUPER_ADMIN' ) ) {
            foreach( $movieFormats as $key => $format ) {
                $this->logger->debug( \sprintf( '[DEBUG_MOVIE] %s - %s', $key, $format ) );
            }
        }
    }
}