import PhotoSwipe from 'photoswipe';
import PhotoSwipeLightbox from 'photoswipe/lightbox';
import 'photoswipe/style.css';
window.PhotoSwipe   = PhotoSwipe;

const PhotoSwipeUI_Default  = require( '../../vendor/themeforest-flixgo-online-movies/js/photoswipe-ui-default.min.js' );
window.PhotoSwipeUI_Default = PhotoSwipeUI_Default;

const lightbox = new PhotoSwipeLightbox({
  gallery: '#actor-gallery',
  children: 'a',
  pswpModule: () => import( 'photoswipe' )
});
lightbox.init();

require( '../../vendor/themeforest-flixgo-online-movies/js/main.js' );

$( function()
{

});
