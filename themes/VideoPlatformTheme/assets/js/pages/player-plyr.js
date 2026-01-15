require( '../../css/plyr.scss' );

//const Plyr = require( '../../vendor/themeforest-flixgo-online-movies/js/plyr.min.js' );
import Plyr from 'plyr';
window.Plyr = Plyr;

var watermarkText;
var currentClass        = 0;
var watermarkClasses    = [
    'WatermarkText_BottomRight',
    'WatermarkText_BottomLeft',
    'WatermarkText_TopLeft',
    'WatermarkText_TopRight',
];
    
function moveWatermark()
{
    setTimeout( () => {
        $( '.' + watermarkClasses[currentClass] ).remove();
        
        currentClass    = currentClass == 3 ? 0 : currentClass + 1;
        $( '.plyr--video' ).prepend( '<div class="' + watermarkClasses[currentClass] + '"> ' + watermarkText + ' </div>' );
        
        moveWatermark();
    }, "10000" ); // Delayed for 10 seconds
}

$( function()
{
    /**
     * Player Controls Options
     * ========================
     * https://github.com/sampotts/plyr/blob/master/CONTROLS.md
     */
    const player    = new Plyr( '#player-plyr', {
        tooltips: { controls: true, seek: true }
    });
    
    let watermarkText   = $( '#player-plyr' ).attr( 'data-watermarkText' );
    $( '.plyr--video' ).prepend( '<div class="WatermarkText"> ' + watermarkText + ' </div>' );
    moveWatermark();
    
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
