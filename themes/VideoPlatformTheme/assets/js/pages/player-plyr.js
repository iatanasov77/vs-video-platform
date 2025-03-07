import Plyr from 'plyr';
require( '../../css/plyr.scss' );
window.Plyr = Plyr;

$( function()
{
    const player        = new Plyr( '#player-plyr' );
    
    let watermarkText   = $( '#player-plyr' ).attr( 'data-watermarkText' );
    $( '.plyr--video' ).prepend( '<div class="WatermarkText"> ' + watermarkText + ' </div>' );
    
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
