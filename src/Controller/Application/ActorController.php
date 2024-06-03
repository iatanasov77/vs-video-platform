<?php namespace App\Controller\Application;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\Persistence\ManagerRegistry;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Knp\Component\Pager\PaginatorInterface;
use Intervention\Zodiac\Calculator;

use Vankosoft\CatalogBundle\Component\ReviewFactory;
use Vankosoft\CatalogBundle\Model\Interfaces\ReviewInterface;
use App\Form\ActorsFilterForm;
use App\Form\ActorReviewForm;
use App\Form\ActorCommentForm;

class ActorController extends AbstractController
{
    /** @var ManagerRegistry */
    private $doctrine;
    
    /** @var RepositoryInterface */
    private $actorsRepository;
    
    /** @var ReviewFactory */
    private $reviewFactory;
    
    public function __construct(
        ManagerRegistry $doctrine,
        RepositoryInterface $actorsRepository,
        ReviewFactory $reviewFactory
    ) {
        $this->doctrine         = $doctrine;
        $this->actorsRepository = $actorsRepository;
        $this->reviewFactory    = $reviewFactory;
    }
    
    public function index( Request $request, PaginatorInterface $paginator ): Response
    {
        $filterForm         = $this->createForm( ActorsFilterForm::class, null, ['method' => 'POST'] );
        $mobileFilterForm   = $this->createForm( ActorsFilterForm::class, null, ['method' => 'POST'] );
        
        $actors = $paginator->paginate(
            $this->actorsRepository->getQueryBuilder( 'bp' )->orderBy( 'bp.updatedAt', 'DESC' ),
            $request->query->getInt( 'page', 1 ) /*page number*/,
            18 /*limit per page*/
        );
        
        return $this->render( 'Pages/Actors/index.html.twig', [
            'filterForm'        => $filterForm->createView(),
            'mobileFilterForm'  => $mobileFilterForm->createView(),
            'actors'            => $actors,
        ]);
    }
    
    public function details( $slug, Request $request ): Response
    {
        $actor  = $this->actorsRepository->findOneBy( ['slug' => $slug] );
        
        if ( $actor->getDateOfBirth() ) {
            $actorZodiac    = Calculator::make( $actor->getDateOfBirth() );
            
            $now            = new \DateTime();
            $interval       = $now->diff( $actor->getDateOfBirth() );
            $actorAge       = $interval->y;
        } else {
            $actorZodiac    = null;
            $actorAge       = null;
        }
        
        //$commentForm    = $this->createCommentForm( $actor );
        $reviewForm     = $this->getUser() ? $this->createReviewForm( $this->reviewFactory->createReview( $actor ) ) : null;
        
        return $this->render( 'Pages/Actors/details.html.twig', [
            'actor'         => $actor,
            'actorZodiac'   => $actorZodiac,
            'actorAge'      => $actorAge,
            'firstMovie'    => $actor->getVideos()->last(),
            'lastMovie'     => $actor->getVideos()->first(),
            'bestMovie'     => $actor->getBestVideo(),
            
            'reviewForm'    => $reviewForm ? $reviewForm->createView() : null,
            //'commentForm'   => $commentForm->createView(),
        ]);
    }
    
    public function createReviewAction( $actorSlug, Request $request ): Response
    {
        $actor          = $this->actorsRepository->findOneBy( ['slug' => $actorSlug] );
        if ( ! $actor ) {
            throw new NotFoundHttpException( 'Sorry not existing !' );
        }
        
        $reviewForm = $this->createReviewForm( $this->reviewFactory->createReview( $actor ) );
        
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
    
    public function createCommentAction( $actorSlug, Request $request ): Response
    {
        $actor          = $this->actorsRepository->findOneBy( ['slug' => $actorSlug] );
        if ( ! $actor ) {
            throw new NotFoundHttpException( 'Sorry not existing !' );
        }
        
        $commentForm = $this->createCommentForm( $actor );
        
        $commentForm->handleRequest( $request );
        if ( ! $commentForm->isSubmitted() ) {
            throw new \Exception( "'createReviewAction' is NOT Called Properly !!!" );
        }
        
        $em         = $this->doctrine->getManager();
        $comment    = $commentForm->getData();
        
        $comment->setAuthor( $this->getUser() );
        $comment->setCommentSubject( $actor );
        
        $em->persist( $comment );
        $em->flush();
        
        return $this->redirect( $request->headers->get( 'referer' ) );
    }
    
    protected function createReviewForm( ReviewInterface $review )
    {
        $form   = $this->createForm(
            ActorReviewForm::class,
            $review,
            [
                'action'            => $this->generateUrl( 'vvp_actors_create_review', [
                    'actorSlug' => $review->getReviewSubject()->getSlug()
                ]),
                'method'            => 'POST',
                'rating_steps'      => 10,
                'rating_expanded'   => false,
            ]
        );
        
        return $form;
    }
    
    protected function createCommentForm( $actor )
    {
        $form   = $this->createForm(
            ActorCommentForm::class,
            null,
            [
                'action'            => $this->generateUrl( 'vvp_actors_create_comment', [
                    'actorSlug' => $actor->getSlug()
                ]),
                'method'            => 'POST',
                'actor'             => $actor->getId(),
                'parent_comment'    => 0,
            ]
        );
        
        return $form;
    }
}