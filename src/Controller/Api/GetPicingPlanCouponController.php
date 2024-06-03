<?php namespace App\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use App\Entity\Payment\PromotionCoupon;

class GetPicingPlanCouponController extends AbstractController
{
    /** @var RepositoryInterface */
    private $pricingPlansRepository;
    
    public function __construct( RepositoryInterface $pricingPlansRepository )
    {
        $this->pricingPlansRepository   = $pricingPlansRepository;
    }
    
    public function __invoke( $pricingPlanId, $couponCode, Request $request ): ?PromotionCoupon
    {
        $pricingPlan    = $this->pricingPlansRepository->find( $pricingPlanId );
        
        $coupon         = null;
        if( $pricingPlan ) {
            $coupons    = $pricingPlan->getCoupons();
            $coupon     = $coupons->get( $couponCode );
        }
        
        return $coupon;
    }
}
