<?php namespace App\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Vankosoft\PaymentBundle\CustomGateways\TelephoneCall\TelephoneCallResponse;
use App\Entity\Payment\PromotionCoupon;

class CheckValidPricingPlanCouponController extends AbstractController
{
    /** @var RepositoryInterface */
    private $pricingPlansRepository;
    
    public function __construct( RepositoryInterface $pricingPlansRepository )
    {
        $this->pricingPlansRepository   = $pricingPlansRepository;
    }
    
    public function __invoke( Request $request ): TelephoneCallResponse
    {
        $requestBody    = \json_decode( $request->getContent(), true );
        $pricingPlan    = $this->pricingPlansRepository->find( $requestBody['pricingPlanId'] );
        
        $coupon         = null;
        if( $pricingPlan ) {
            $coupons    = $pricingPlan->getCoupons();
            $coupon     = $coupons->get( $requestBody['couponCode'] );
        }
        $response   = $coupon ?
                        $this->createValidCouponResponse( $coupon ) :
                        $this->createInvalidCouponResponse( $coupon );
        
        return $response;
    }
    
    private function createValidCouponResponse( PromotionCoupon $coupon ): TelephoneCallResponse
    {
        $responseData   = [
            TelephoneCallResponse::FIELD_STATUS => TelephoneCallResponse::STATUS_OK,
            TelephoneCallResponse::FIELD_COUPON => $coupon,
        ];
        
        return new TelephoneCallResponse( $responseData );
    }
    
    private function createInvalidCouponResponse( ?PromotionCoupon $coupon ): TelephoneCallResponse
    {
        $errorReason    = 'Invalid Coupon';
        
        $responseData   = [
            TelephoneCallResponse::FIELD_STATUS         => TelephoneCallResponse::STATUS_ERROR,
            TelephoneCallResponse::FIELD_COUPON         => $coupon,
            TelephoneCallResponse::FIELD_ERROR_REASON   => $errorReason,
        ];
        
        return new TelephoneCallResponse( $responseData );
    }
}