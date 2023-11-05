<?php namespace App\Controller\VideoPlatform;

use Vankosoft\PaymentBundle\Controller\PricingPlans\PricingPlanCheckoutController as BasePricingPlanCheckoutController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Intl\Currencies;

class PricingPlanCheckoutController extends BasePricingPlanCheckoutController
{
	use GlobalFormsTrait;
	
    public function showPricingPlans( Request $request ): Response
    {
        $pricingPlanCategories  = $this->pricingPlanCategoryRepository->findAll();
        $currencies             = [
            'EUR'   => Currencies::getSymbol( 'EUR' )
        ];
        
        return $this->render( '@VSPayment/Pages/PricingPlansCheckout/pricing_plans.html.twig', [
            'pricingPlanCategories' => $pricingPlanCategories,
            'intlCurrencies'        => $currencies,
        ]);
    }
}