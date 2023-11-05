const Splide    = require( '@splidejs/splide' );
window.Splide   = Splide.Splide;

require( '../../vendor/themeforest-flixgo-online-movies/js/main.js' );

import { ChoosePlan } from '../includes/pricing-plans.js';
import { SubmitCreditCardForm, SubmitPaymentForm } from '@@/js/Stripe/StripeJsV2.js';

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
});
