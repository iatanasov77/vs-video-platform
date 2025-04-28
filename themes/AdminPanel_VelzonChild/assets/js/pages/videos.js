require( '@/vendor/vs_tablesortable/tablesortable.js' );
require( 'jquery-easyui/css/easyui.css' );
require( 'jquery-easyui/js/jquery.easyui.min.js' );

import videojs from 'video.js';
require( 'video.js/dist/video-js.css' );

import { VsRemoveDuplicates } from '@/js/includes/vs_remove_duplicates.js';
import { VsPath } from '@/js/includes/fos_js_routes.js';
require( '@/js/includes/resource-delete.js' );

window.RecreateCoconutJobClicked = false;

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
    $( "#form_filterByCategory" ).on( 'change', function() {
        let filterCategory  = $( this ).val();
        let url             = VsPath( 'vs_vvp_video_index' );
        if ( filterCategory ) {
            url += filterCategory + '/';
        }
        
        document.location   = url;
    });

    $( '.btnUpdateStatus' ).on( 'click', function( e )
    {
        e.preventDefault();
        
        var url      = $( this ).attr( 'data-url' );
        var output   = $( this ).attr( 'data-output' );
        
        $.ajax({
            type: "GET",
            url: url,
            success: function( response )
            {
                if ( response.status == 'ok' ) {
                    $( '#' + output ).text( response.data.status );
                    $( '#modalBodyCoconutJob > div.card-body' ).html( '<pre>' + JSON.stringify( response.data, null, 2 ) + '</pre>' );
                } else {
                    $( '#modalBodyCoconutJob > div.card-body' ).html( '<pre>' + response.message + '</pre>' );
                }
                
                /** Bootstrap 5 Modal Toggle */
                const myModal = new bootstrap.Modal( '#coconut-job-modal', {
                    keyboard: false
                });
                myModal.show( $( '#coconut-job-modal' ).get( 0 ) );
                
                $( '#coconut-job-modal' ).get( 0 ).addEventListener( 'hidden.bs.modal', function ( e ) {
                    document.location = document.location;
                });
            },
            error: function()
            {
                alert( "SYSTEM ERROR!!!" );
            }
        });
    });
    
    $( '.btnRecreateCoconutJob' ).on( 'click', function( e )
    {
        e.preventDefault();
        
        if ( ! window.RecreateCoconutJobClicked ) {
            window.RecreateCoconutJobClicked   = true;
            
            var videoId = $( this ).attr( 'data-video-id' );
            var url     = VsPath( 'vvp_coconut_recreate_job', { 'videoId': videoId } );
            
            $.ajax({
                type: "GET",
                url: url,
                success: function( response )
                {
                    alert( response.message );
                    document.location   = document.location;
                },
                error: function()
                {
                    alert( "SYSTEM ERROR!!!" );
                }
            });
        }
    });
    
    $( '.videoPreview' ).on( 'click', function( e )
    {
        e.preventDefault();
        
        var url      = $( this ).attr( 'data-url' );
        
        $.ajax({
            type: "GET",
            url: url,
            success: function( response )
            {
                $( '#modalBodyVideoPreview > div.card-body' ).html( response );
                
                /** Bootstrap 5 Modal Toggle */
                const myModal = new bootstrap.Modal( '#video-preview-modal', {
                    keyboard: false
                });
                myModal.show( $( '#video-preview-modal' ).get( 0 ) );
                
                var videoSlug           = $( '#VideoPlayer' ).attr( 'data-video-slug' );
                var videoOriginalUrl    = VsPath( 'app_video_player_read', { 'id': $( '#VideoPlayer' ).attr( 'data-video-id' ) } );
                initPlayerFormats( videoSlug, videoOriginalUrl );
            },
            error: function()
            {
                alert( "SYSTEM ERROR!!!" );
            }
        });
    });
    
    $( '#modalBodyVideoPreview' ).on( 'change', '#video-player-select-format', function( e ) {
        //alert( $( '#VideoPlayerSource' ).attr( 'src' ) );
        
        //$( '#VideoPlayerSource' ).attr( 'src', $( this ).val() );
        loadVideo( $( this ).val() );
    });
});