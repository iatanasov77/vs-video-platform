// Smooth Scrollbar
import Scrollbar from 'smooth-scrollbar';
window.Scrollbar = Scrollbar;

// Plyr Video Player
import Plyr from 'plyr';
require( '../../css/plyr.scss' );
window.Plyr = Plyr;

// Photoswipe Gallery
import PhotoSwipe from 'photoswipe';
import PhotoSwipeLightbox from 'photoswipe/lightbox';
import 'photoswipe/style.css';
window.PhotoSwipe   = PhotoSwipe;

const PhotoSwipeUI_Default  = require( '../../vendor/themeforest-flixgo-online-movies/js/photoswipe-ui-default.min.js' );
window.PhotoSwipeUI_Default = PhotoSwipeUI_Default;

const lightbox = new PhotoSwipeLightbox({
  gallery: '#video-gallery',
  children: 'a',
  pswpModule: () => import( 'photoswipe' )
});
lightbox.init();

require( '../../vendor/themeforest-flixgo-online-movies/js/main.js' );
require( '../../css/video-discover.css' );

// Rater Js
/*
var raterJs = require( "rater-js" );
require( 'rater-js/rater-js.css' );
require( '../../css/star-rating.css' );
*/

window.CommentLiked     = false;
window.CommentDisliked  = false;

$( function()
{
    const player        = new Plyr( '#player-plyr' );
    
    let watermarkText   = $( '#player-plyr' ).attr( 'data-watermarkText' );
    $( '.plyr--video' ).prepend( '<div class="WatermarkText"> ' + watermarkText + ' </div>' );
    
    /*
    var myRating        = $( '#basic-rater' ).attr( 'data-myRating' );
    var basicRating = raterJs({
        starSize: 22,
        rating: myRating,
        max: 10,
        element: document.querySelector( "#basic-rater" ),
        rateCallback: function rateCallback( rating, done ) {
           //this.setRating( rating );
           basicRating.setAvgRating( rating );
           done(); 
        },
        
        ratingText: "Rate: {rating}",
        readOnly: false
    });
    */
    
    $( '.btnLikeComment' ).on( 'click', function( e )
    {
        $.ajax({
            type: "GET",
            url: $( this ).attr( 'data-url' ),
            success: function( response )
            {
                document.location   = document.location;
            },
            error: function()
            {
                alert( "Like Comment SYSTEM ERROR!!!" );
            }
        });
    });
    
    $( '.btnDislikeComment' ).on( 'click', function( e )
    {
        $.ajax({
            type: "GET",
            url: $( this ).attr( 'data-url' ),
            success: function( response )
            {
                document.location   = document.location;
            },
            error: function()
            {
                alert( "Dislike Comment SYSTEM ERROR!!!" );
            }
        });
    });
    
    player.on( 'play', function()
    {
        $.ajax({
            type: "GET",
            url: $( '#player-plyr' ).attr( 'data-videoWatchingUrl' ),
            success: function( response )
            {
                return;
            },
            error: function()
            {
                alert( "Video Watching SYSTEM ERROR!!!" );
            }
        });
    });
});