require ( '@/js/includes/BootstrapDropdown.js' );
require ( 'jquery-duplicate-fields/jquery.duplicateFields.js' );

require( 'jquery-easyui/css/easyui.css' );
require( 'jquery-easyui/js/jquery.easyui.min.js' );

import { VsRemoveDuplicates } from '@/js/includes/vs_remove_duplicates.js';

import { EasyuiCombobox } from 'jquery-easyui-extensions/EasyuiCombobox.js';

import Tagify from '@yaireo/tagify';
import '@yaireo/tagify/dist/tagify.css';

import DragSort from '@yaireo/dragsort';
import '@yaireo/dragsort/dist/dragsort.css';

import { VsPath } from '@/js/includes/fos_js_routes.js';

var tagsInput;
var tagify;
var dragsort;

window.formInitialized  = false;

// must update Tagify's value according to the re-ordered nodes in the DOM
function onDragEnd( elm )
{
    tagify.updateValueByDOMTags();
}

function initForm()
{
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
    
    let categorySelector    = "#FormContainer > #FormVideo > #CategoriesFormGroup > #video_form_category_taxon";
    //let categorySelector    = "#video_form_category_taxon";
    let selectedCategories  = JSON.parse( $( '#video_form_videoCategories').val() );
    EasyuiCombobox( $( categorySelector ), {
        required: true,
        multiple: true,
        checkboxId: "category_taxon",
        values: selectedCategories
    });
    VsRemoveDuplicates();
    
    let selectedGenres  = JSON.parse( $( '#video_form_videoGenres').val() );
    EasyuiCombobox( $( '#video_form_genres' ), {
        required: false,
        multiple: true,
        checkboxId: "genres",
        values: selectedGenres
    });
    
    /* */
    let actorsSelector    = "#FormContainer > #FormVideo > #ActorsFormGroup > #video_form_actors";
    //let actorsSelector    = "#video_form_actors";
    let selectedActors  = JSON.parse( $( '#video_form_videoActors').val() );
    EasyuiCombobox( $( actorsSelector ), {
        required: false,
        multiple: true,
        checkboxId: "videoActors",
        values: selectedActors,
        debug: false,
    });
    
    var tagsInputWhitelist  = $( '#video_form_tagsInputWhitelist' ).val().split( ',' );
    //console.log( tagsInputWhitelist );
    
    tagsInput   = $( '#video_form_tags' )[0];
    tagify      = new Tagify( tagsInput, {
        whitelist : tagsInputWhitelist,
        dropdown : {
            classname     : "color-blue",
            enabled       : 0,              // show the dropdown immediately on focus
            maxItems      : 5,
            position      : "text",         // place the dropdown near the typed text
            closeOnSelect : false,          // keep the dropdown open after selecting a suggestion
            highlightFirst: true
        }
    });
    
    // bind "DragSort" to Tagify's main element and tell
    // it that all the items with the below "selector" are "draggable"
    dragsort    = new DragSort( tagify.DOM.scope, {
        selector: '.'+tagify.settings.classNames.tag,
        callbacks: {
            dragEnd: onDragEnd
        }
    });
    
    /* */
    let paidServicesSelector    = "#FormContainer > #FormVideo > #PaidServicesFormGroup > #video_form_allowedPaidServices";
    let selectedPaidServices    = JSON.parse( $( '#video_form_videoAllowedPaidServices').val() );
    EasyuiCombobox( $( paidServicesSelector ), {
        required: false,
        multiple: true,
        checkboxId: "paidServices",
        values: selectedPaidServices,
        debug: false,
    });
    
    $( '.persistedPhoto' ).removeAttr( 'required' );
}

$( function()
{
    initForm();
    
    // bin/console fos:js-routing:dump --format=json --target=public/shared_assets/js/fos_js_routes_admin.json
    $( '#FormContainer' ).on( 'change', '#video_form_locale', function( e ) {
        e.stopPropagation();
        if ( window.formInitialized ) {
            return false;
        }
        //window.formInitialized  = true;
        
        var videoId  = $( '#FormContainer' ).attr( 'data-itemId' );
        var locale  = $( this ).val();
        //alert( locale );
        
        if ( videoId ) {
            $.ajax({
                type: 'GET',
                url: VsPath( 'vvp_videos_form_in_locale', { 'itemId': videoId, 'locale': locale } ),
                success: function ( data ) {
                    $( '#FormContainer' ).html( data );
                    
                    initForm();
                    //window.formInitialized  = false;
                }, 
                error: function(XMLHttpRequest, textStatus, errorThrown) {
                    alert( 'FATAL ERROR!!!' );
                }
            });
        }
    });
    
    /*  
    $( '#FormVideo' ).on( 'submit', function ( e )
    {
        e.preventDefault();
        e.stopPropagation();
        
        //var description = $( '#video_form_description' ).val();
        
        require( 'ckeditor4/ckeditor.js' );
        var description = CKEDITOR.instances.video_form_description.getData();
        
        alert( description );
        
        return false;
    });
    */
});
