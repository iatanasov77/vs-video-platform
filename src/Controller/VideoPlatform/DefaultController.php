<?php namespace App\Controller\VideoPlatform;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Intl\Currencies;
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
        EntityRepository $pricingPlanCategoryRepository
    ) {
        $this->applicationContext               = $applicationContext;
        $this->templatingEngine                 = $templatingEngine;

        $this->doctrine                         = $doctrine;
        $this->categoryRepository               = $categoryRepository;
        $this->productRepository                = $productRepository;
        $this->sliderRepository                 = $sliderRepository;
        $this->sliderPhotosDir                  = $sliderPhotosDir;
        $this->videoRepository                  = $videoRepository;
        $this->pricingPlanCategoryRepository    = $pricingPlanCategoryRepository;
    }
    
    public function index( Request $request ): Response
    {
        $limit                  = 6;
        $latestVideos           = $this->videoRepository->findBy( [], ['updatedAt' => 'DESC'], $limit );
        
        $pricingPlanCategories  = $this->pricingPlanCategoryRepository->findAll();
        $currencies             = [
            'EUR'   => Currencies::getSymbol( 'EUR' )
        ];
        
        return new Response( $this->templatingEngine->render( $this->getTemplate(), [
            //'shoppingCart'      => $this->getShoppingCart( $request ),
            'categories'            => $this->categoryRepository->findAll(),
            'latestProducts'        => $this->productRepository->findAll(),
            'sliderItems'           => $this->sliderRepository->findAll(),
            'sliderPhotosDir'       => $this->sliderPhotosDir,
            'latestVideos'          => $latestVideos,
            'pricingPlanCategories' => $pricingPlanCategories,
            'intlCurrencies'        => $currencies,
        ]));
    }
    
    protected function getTemplate(): string
    {
        $template   = 'video-platform/Pages/home.html.twig';
        
        $appSettings    = $this->applicationContext->getApplication()->getSettings();
        if ( ! $appSettings->isEmpty() && $appSettings[0]->getTheme() ) {
            $template   = 'Pages/home.html.twig';
        }
        
        return $template;
    }
}
