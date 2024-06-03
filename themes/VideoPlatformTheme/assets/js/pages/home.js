import Splide from '@splidejs/splide';
window.Splide   = Splide;

require( '../../vendor/themeforest-flixgo-online-movies/js/main.js' );

import { ChoosePlan } from '../includes/pricing-plans.js';
import { SubmitCreditCardForm, SubmitPaymentForm } from '@@/js/Stripe/StripeJsV2.js';
import { SubmitPayumCreditCardForm } from '@@/js/Payum/Payum.js';

$( function()
{
    /*
    $( '.hero__btn' ).each( function( i, v )
    {
        $( v ).contents().eq( 1 ).wrap( '<span/>' );
    });
    */
    
    $( 'div.splide__arrows' ).show();
    
    $( '.btnChoosePlan' ).on( 'click', function()
    {
        ChoosePlan( $( this ).attr( 'data-url' ) );
    });
    
    $( '#selectPricingPlanForm' ).on( 'submit', '#credit_card_form', SubmitCreditCardForm );
    $( '#payment-form' ).on( 'submit', SubmitPaymentForm );
    
    $( '#selectPricingPlanForm' ).on( 'submit', '#payum_credit_card_form', SubmitPayumCreditCardForm );
});
