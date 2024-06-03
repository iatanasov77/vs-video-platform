<?php namespace App\Controller\VideoPlatform;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Twig\Environment;
use Doctrine\Persistence\ManagerRegistry;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;

use Vankosoft\ApplicationBundle\Component\Context\ApplicationContextInterface;

class DefaultController extends AbstractController
{
    use GlobalFormsTrait;

    /** @var ApplicationContextInterface */
    private $applicationContext;
    
    /** @var Environment */
    private $templatingEngine;

    /** @var ManagerRegistry **/
    private $doctrine;
    
    /** @var EntityRepository */
    private $categoryRepository;
    
    /** @var EntityRepository */
    private $productRepository;
    
    /** @var EntityRepository */
    private $sliderRepository;
    
    /** @var string */
    private $sliderPhotosDir;
    
    /** @var EntityRepository */
    private $videoRepository;
    
    /** @var EntityRepository */
    private $actorRepository;
    
    /** @var EntityRepository */
    private $pricingPlanCategoryRepository;
    
    public function __construct(
        ApplicationContextInterface $applicationContext,
        Environment $templatingEngine,
        ManagerRegistry $doctrine,
        EntityRepository $categoryRepository,
        EntityRepository $productRepository,
        EntityRepository $sliderRepository,
        string $sliderPhotosDir,
        EntityRepository $videoRepository,
        EntityRepository $actorRepository,
        EntityRepository $pricingPlanCategoryRepository,
        EntityRepository $subscriptionsRepository
    ) {
        $this->applicationContext               = $applicationContext;
        $this->templatingEngine                 = $templatingEngine;
        
        $this->doctrine                         = $doctrine;
        $this->categoryRepository               = $categoryRepository;
        $this->productRepository                = $productRepository;
        $this->sliderRepository                 = $sliderRepository;
        $this->sliderPhotosDir                  = $sliderPhotosDir;
        $this->videoRepository                  = $videoRepository;
        $this->actorRepository                  = $actorRepository;
        $this->pricingPlanCategoryRepository    = $pricingPlanCategoryRepository;
        $this->subscriptionsRepository          = $subscriptionsRepository;
    }
    
    public function index( Request $request ): Response
    {
        $limit                  = 6;
        $latestVideos           = $this->videoRepository->findBy( [], ['updatedAt' => 'DESC'], $limit );
        
        $pricingPlanCategories  = $this->pricingPlanCategoryRepository->findAll();
        
        $movieTags  = [];
        foreach ( $latestVideos as $m ) {
            $movieTags[$m->getId()] = \json_decode( $m->getTags() );
        }
        
        $homePageSlider         = $this->sliderRepository->findBySlug( 'home-page-slider' );
        if ( ! $homePageSlider ) {
            throw new NotFoundHttpException( 'Home Page Slider Not Found !!!' );
        }
        
        $splideLimit    = 20;
        $featuredMovies = $this->videoRepository->findBy( [], ['updatedAt' => 'DESC'], $splideLimit );
        $featuredActors = $this->actorRepository->findBy( [], ['updatedAt' => 'DESC'], $splideLimit );
        
        return new Response( $this->templatingEngine->render( $this->getTemplate(), [
            //'shoppingCart'      => $this->getShoppingCart( $request ),
            'categories'            => $this->categoryRepository->findAll(),
            'latestProducts'        => $this->productRepository->findAll(),
            'sliderItems'           => $homePageSlider->getPublicItems(),
            'sliderPhotosDir'       => $this->sliderPhotosDir,
            'latestVideos'          => $latestVideos,
            'featuredMovies'        => $featuredMovies,
            'featuredActors'        => $featuredActors,
            'movieTags'             => $movieTags,
            'pricingPlanCategories' => $pricingPlanCategories,
            'subscriptions'         => $this->subscriptionsRepository->getSubscriptionsByUser( $this->_getAppUser() )
        ]));
    }
    
    protected function getTemplate(): string
    {
        $template   = 'sugarbabes/Pages/home.html.twig';
        
        $appSettings    = $this->applicationContext->getApplication()->getSettings();
        if ( ! $appSettings->isEmpty() && $appSettings[0]->getTheme() ) {
            $template   = 'Pages/home.html.twig';
        }
        
        return $template;
    }
}
