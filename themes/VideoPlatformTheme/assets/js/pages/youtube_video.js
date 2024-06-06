// Plyr Video Player
import Plyr from 'plyr';
require( '../../css/plyr.scss' );
window.Plyr = Plyr;

require( '../../vendor/themeforest-flixgo-online-movies/js/main.js' );

$( function()
{
    const player        = new Plyr( '#player-plyr' );
});