<?php namespace App\Controller\VideoPlatform;

use Vankosoft\UsersBundle\Controller\ProfileController as BaseProfileController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Intl\Currencies;
use Doctrine\Persistence\ManagerRegistry;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Vankosoft\CmsBundle\Component\Uploader\FileUploaderInterface;
use Vankosoft\UsersBundle\Security\UserManager;

class ProfileController extends BaseProfileController
{
    /** @var RepositoryInterface */
    private $pricingPlanRepository;
    
    /** @var RepositoryInterface */
    private $pricingPlanCategoryRepository;
    
    public function __construct(
        ManagerRegistry $doctrine,
        string $usersClass,
        UserManager $userManager,
        FactoryInterface $avatarImageFactory,
        FileUploaderInterface $imageUploader,
        RepositoryInterface $pricingPlanRepository,
        RepositoryInterface $pricingPlanCategoryRepository
    ) {
        parent::__construct(
            $doctrine,
            $usersClass,
            $userManager,
            $avatarImageFactory,
            $imageUploader
        );
        
        $this->pricingPlanRepository            = $pricingPlanRepository;
        $this->pricingPlanCategoryRepository    = $pricingPlanCategoryRepository;
    }
    
    public function showAction( Request $request ): Response
    {
        $pricingPlans               = $this->pricingPlanRepository->findAll();
        $currentSubscription        = null;
        $currentPlanCurrencySign    = null;
        
        $userPricingPlansSubscriptions  = $this->getUser()->getPricingPlanSubscriptions();
        if( ! $userPricingPlansSubscriptions->isEmpty() ) {
            $currentSubscription        = $userPricingPlansSubscriptions->last();
            $currentPlanCurrencySign    = Currencies::getSymbol( $currentSubscription->getPricingPlan()->getCurrencyCode() );
        }
        
        $profileEditForm    = $this->getProfileEditForm();
        $otherForms         = $this->getOtherForms();
        
        $currencies             = [
            'EUR'   => Currencies::getSymbol( 'EUR' )
        ];
            
        $params = [
            'pricingPlans'              => $pricingPlans,
            'planCategories'            => $this->pricingPlanCategoryRepository->findAll(),
            'currentSubscription'       => $currentSubscription,
            'currentPlanCurrencySign'   => $currentPlanCurrencySign,
            'profileEditForm'           => $profileEditForm->createView(),
            'changePasswordForm'        => $otherForms['changePasswordForm']->createView(),
            'intlCurrencies'            => $currencies,
        ];
        
        return $this->render( '@VSUsers/Profile/show.html.twig',
            array_merge( $params, $this->templateParams( $this->getProfileEditForm() ) )
        );
    }
}