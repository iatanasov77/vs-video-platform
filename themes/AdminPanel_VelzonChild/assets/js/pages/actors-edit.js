require ( 'jquery-duplicate-fields/jquery.duplicateFields.js' );

require( 'jquery-easyui/css/easyui.css' );
require( 'jquery-easyui/js/jquery.easyui.min.js' );
require( '@/js/includes/bootstrap-5/file-input.js' );

import { VsPath } from '@/js/includes/fos_js_routes.js';

import { VsRemoveDuplicates } from '@/js/includes/vs_remove_duplicates.js';
import { EasyuiCombobox } from 'jquery-easyui-extensions/EasyuiCombobox.js';
import { GetCkEditorData } from '@/js/includes/ckeditor.js';

$( function ()
{
    $( '#btnTestCkEditor' ).on( 'click', function ( e ) {
        alert( GetCkEditorData( 'actor_form_description' ) );
    });
    
    $( '#PhotosContainer' ).duplicateFields({
        btnRemoveSelector: ".btnRemoveField",
        btnAddSelector:    ".btnAddField",
        onCreate: function( newElement ) {
            let fileInputId = newElement.find( '.fieldPhoto' ).attr( 'id' );
            newElement.find( '.input-group-text' ).attr( 'for', fileInputId );
        }
    });
    
    $( '#PhotosContainer' ).on( 'change', '.fieldPhoto', function() {
        var filename = $( this ).val().split('\\').pop();
        $( this ).next( '.input-group-text' ).text( filename );
    });
    
    let selectedVideos  = JSON.parse( $( '#actor_form_actorVideos').val() );
    EasyuiCombobox( $( '#actor_form_videos' ), {
        required: false,
        multiple: true,
        checkboxId: "videos",
        values: selectedVideos
    });
    VsRemoveDuplicates();
    
    let selectedGenres  = JSON.parse( $( '#actor_form_actorGenres').val() );
    EasyuiCombobox( $( '#actor_form_genres' ), {
        required: false,
        multiple: true,
        checkboxId: "genres",
        values: selectedGenres
    });
    
    $( '.persistedPhoto' ).removeAttr( 'required' );
    
    // bin/console fos:js-routing:dump --format=json --target=public/shared_assets/js/fos_js_routes_admin.json
    $( '#FormContainer' ).on( 'change', '#actor_form_locale', function( e ) {
        var actorId  = $( '#FormContainer' ).attr( 'data-itemId' );
        var locale  = $( this ).val();
        //alert( locale );
        
        if ( actorId ) {
            $.ajax({
                type: 'GET',
                url: VsPath( 'vvp_actors_form_in_locale', { 'itemId': actorId, 'locale': locale } ),
                success: function ( data ) {
                    $( '#FormContainer' ).html( data );
                }, 
                error: function(XMLHttpRequest, textStatus, errorThrown) {
                    alert( 'FATAL ERROR!!!' );
                }
            });
        }
    });
    
});