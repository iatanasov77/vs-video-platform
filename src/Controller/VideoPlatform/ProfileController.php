<?php namespace App\Controller\VideoPlatform;

use Vankosoft\UsersBundle\Controller\ProfileController as BaseProfileController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\Persistence\ManagerRegistry;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Vankosoft\CmsBundle\Component\Uploader\FileUploaderInterface;
use Vankosoft\UsersBundle\Security\UserManager;
use Vankosoft\PaymentBundle\Component\Payment\Payment;
use Vankosoft\AgentBundle\Component\VankosoftAgent;

class ProfileController extends BaseProfileController
{
    /** @var Payment */
    private $vsPayment;
    
    /** @var RepositoryInterface */
    private $pricingPlanRepository;
    
    /** @var RepositoryInterface */
    private $pricingPlanCategoryRepository;
    
    /** @var RepositoryInterface */
    private $pricingPlanSubscriptionRepository;
    
    public function __construct(
        ManagerRegistry $doctrine,
        string $usersClass,
        UserManager $userManager,
        FactoryInterface $avatarImageFactory,
        FileUploaderInterface $imageUploader,
        VankosoftAgent $vankosoftAgent,
        Payment $vsPayment,
        RepositoryInterface $pricingPlanRepository,
        RepositoryInterface $pricingPlanCategoryRepository,
        RepositoryInterface $pricingPlanSubscriptionRepository
    ) {
        parent::__construct(
            $doctrine,
            $usersClass,
            $userManager,
            $avatarImageFactory,
            $imageUploader,
            $vankosoftAgent
        );
        
        $this->vsPayment                            = $vsPayment;
        $this->pricingPlanRepository                = $pricingPlanRepository;
        $this->pricingPlanCategoryRepository        = $pricingPlanCategoryRepository;
        $this->pricingPlanSubscriptionRepository    = $pricingPlanSubscriptionRepository;
    }
    
    public function showAction( Request $request ): Response
    {
        if ( ! $this->getUser() ) {
            return $this->redirectToRoute( 'app_home' );
        }
        
        $profileEditForm        = $this->getProfileEditForm();
        $otherForms             = $this->getOtherForms();
        
        $subscriptions          = $this->getUser()->getPricingPlanSubscriptions();
        $subscriptionsRoutes    = [];
        foreach ( $subscriptions as $subscription ) {
            if ( $subscription->getGateway() ) {
                $subscriptionsRoutes[$subscription->getId()]    = [
                    'createRecurring'   => $this->vsPayment->getPaymentCreateRecurringUrl( $subscription ),
                    'cancelRecurring'   => $this->vsPayment->getPaymentCancelRecurringUrl( $subscription ),
                ];
            }
        }
        
        $params = [
            'profileEditForm'       => $profileEditForm->createView(),
            'changePasswordForm'    => $otherForms['changePasswordForm']->createView(),
            
            'subscriptions'         => $subscriptions,
            'subscriptionsRoutes'   => $subscriptionsRoutes,
        ];
        
        return $this->render( '@VSUsers/Profile/show.html.twig',
            array_merge( $params, $this->templateParams( $profileEditForm ) )
        );
    }
}