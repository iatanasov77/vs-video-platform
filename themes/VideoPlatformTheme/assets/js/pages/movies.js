import { VsPath } from '@/js/includes/fos_js_routes.js';
require( '../../vendor/themeforest-flixgo-online-movies/js/main.js' );

const routes  = require( '../../../../../public/shared_assets/js/fos_js_routes_application.json' );

$( function()
{
    $( '#btnMoviesFilterApply' ).on( 'click', function ( e )
    {
        var categorySlug    = $( this ).attr( 'data-category-slug' );
        let formData        = new FormData( $( '#MoviesFilterForm' )[0] );
        
        $.ajax({
            type: 'POST',
            url: VsPath( 'vvp_movies_filter_handle', { 'categorySlug': categorySlug }, routes ),
            data: formData,
            processData: false,
            contentType: false,
            cache: false,
            success: function ( data )
            {
                if ( data['status'] == 'ok' ) {
                    $( '#MoviesContainer' ).html( data['response'] )
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
    
    $( '#btnMoviesFilterClear' ).on( 'click', function ( e )
    {
        var categorySlug    = $( this ).attr( 'data-category-slug' );
        document.location   = VsPath( 'vvp_movies_category_index', { 'categorySlug': categorySlug }, routes );
    });
    
    $( '#btnMoviesMobileFilterApply' ).on( 'click', function ( e )
    {
        var categorySlug    = $( this ).attr( 'data-category-slug' );
        let formData        = new FormData( $( '#MobileMoviesFilterForm' )[0] );
        
        $.ajax({
            type: 'POST',
            url: VsPath( 'vvp_movies_filter_handle', { 'categorySlug': categorySlug }, routes ),
            data: formData,
            processData: false,
            contentType: false,
            cache: false,
            success: function ( data )
            {
                if ( data['status'] == 'ok' ) {
                    $( '#MoviesContainer' ).html( data['response'] )
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
    
    $( '#btnMoviesMobileFilterClear' ).on( 'click', function ( e )
    {
        var categorySlug    = $( this ).attr( 'data-category-slug' );
        document.location   = VsPath( 'vvp_movies_category_index', { 'categorySlug': categorySlug }, routes );
    });
});