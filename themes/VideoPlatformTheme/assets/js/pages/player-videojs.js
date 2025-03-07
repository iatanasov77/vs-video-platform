import videojs from "!video.js";
require( '!style-loader!css-loader!video.js/dist/video-js.css' )

$( function()
{
    var options = {};

    const player = videojs( 'player-plyr', options, function onPlayerReady() {
        videojs.log( 'Your player is ready!' );
        
        // In this context, `this` is the player that was created by Video.js.
        this.play();
        
        // How about an event listener?
        this.on( 'ended', function() {
            videojs.log( 'Awww...over so soon?!' );
        });
    });
    
});
