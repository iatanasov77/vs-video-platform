import videojs from 'video.js';
require( 'video.js/dist/video-js.css' );

var routes  = require( '../../../../../public/shared_assets/js/fos_js_routes_application.json' );
import { VsPath } from '@/js/includes/fos_js_routes.js';

function initPlayerFormats( videoSlug, videoOriginalUrl )
{
    $( '#video-player-select-format' )
        .empty()
        .append( '<option selected="selected" value="' + videoOriginalUrl + '">Original</option>' )
    ;
    
    //console.log( window.availableFormats );
    for ( const [key, value] of Object.entries( window.availableFormats[videoSlug] ) ) {
        //console.log(`${key}: ${value}`);
        $( '#video-player-select-format' ).append( `<option value="${value}">${key}</option>` );
    }    
}

/**
 * MANUAL:  https://videojs.com/getting-started
 *          https://www.npmjs.com/package/video.js?activeTab=readme
 *          https://videojs.com/guides/options/
 */
 function initPlayer()
 {
    var options = {};
    var player = videojs( 'VideoPlayer', options, function onPlayerReady()
    {
        videojs.log( 'Your player is ready!' );
        
        // In this context, `this` is the player that was created by Video.js.
        //this.play();
    });
    
    var videoUrl    = VsPath( 'app_video_player_read', {'id': $( '#VideoPlayer').attr( 'data-video-id' ) }, routes );
    player.src({
        type: 'video/mp4',
        src: videoUrl
    });
    
    initPlayerFormats( $( '#VideoPlayer').attr( 'data-video-slug' ), videoUrl );
}
 
function loadVideo( videoUrl, posterUrl = null )
{
    var player = videojs( 'VideoPlayer' );
    
    if ( posterUrl ) {
        player.poster( posterUrl );
    }
    
    player.src({
        type: 'video/mp4',
        src: videoUrl
    });
}
 
$( function()
{
    initPlayer();
    
    $( '#video-player-select-format' ).on( 'change', function()
    {
        loadVideo( $( this ).val() );
    });
    
    $( '.btnDisplayVideo' ).on( 'click', function()
    {
        var posterUrl   = $( this ).children( 'img' ).first().attr( 'src' );
        var videoUrl    = VsPath( 'app_video_player_read', {'id': $( this ).attr( 'data-videoId' ) }, routes );
        //alert( posterUrl + "\n" + videoUrl );
        
        let videoSlug   = $( this ).attr( 'data-videoSlug' );
        $( '#VideoPlayer' ).attr( 'data-video-slug', videoSlug );
        $( '#VideoPlayer_html5_api' ).attr( 'data-video-slug', videoSlug );
        
        initPlayerFormats( videoSlug, videoUrl );
        loadVideo( videoUrl, posterUrl );
    });
});