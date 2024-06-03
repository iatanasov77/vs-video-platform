import { VsPath } from '@/js/includes/fos_js_routes.js';
require( '../../vendor/themeforest-flixgo-online-movies/js/main.js' );

const routes  = require( '../../../../../public/shared_assets/js/fos_js_routes_application.json' );

$( function()
{
    $( '#btnActorsFilterApply' ).on( 'click', function ( e )
    {
        let formData        = new FormData( $( '#ActorsFilterForm' )[0] );
        
        $.ajax({
            type: 'POST',
            url: VsPath( 'vvp_actors_filter_handle', {}, routes ),
            data: formData,
            processData: false,
            contentType: false,
            cache: false,
            success: function ( data )
            {
                if ( data['status'] == 'ok' ) {
                    $( '#ActorsContainer' ).html( data['response'] )
                } else {
                    alert( data['message'] );
                }
            }, 
            error: function( XMLHttpRequest, textStatus, errorThrown )
            {
                alert( 'FATAL ERROR !!!' );
            }
        });
    });
    
    $( '#btnActorsFilterClear' ).on( 'click', function ( e )
    {
        document.location   = VsPath( 'vvp_actors', {}, routes );
    });
    
    $( '#btnActorsMobileFilterApply' ).on( 'click', function ( e )
    {
        let formData        = new FormData( $( '#MobileActorsFilterForm' )[0] );
        
        $.ajax({
            type: 'POST',
            url: VsPath( 'vvp_actors_filter_handle', {}, routes ),
            data: formData,
            processData: false,
            contentType: false,
            cache: false,
            success: function ( data )
            {
                if ( data['status'] == 'ok' ) {
                    $( '#ActorsContainer' ).html( data['response'] )
                } else {
                    alert( data['message'] );
                }
            }, 
            error: function( XMLHttpRequest, textStatus, errorThrown )
            {
                alert( 'FATAL ERROR !!!' );
            }
        });
    });
    
    $( '#btnActorsMobileFilterClear' ).on( 'click', function ( e )
    {
        document.location   = VsPath( 'vvp_actors', {}, routes );
    });
});