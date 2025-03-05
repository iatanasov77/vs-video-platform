require( 'jquery-validation' );
require( '../../css/videos-upload.css' );

require( '@/js/includes/bootstrap-5/file-input.js' );
//import { InitOneUpFileUpload, TestUploadProgressBar } from '@/js/includes/OneUpFileUpload/OneUpFileUpload_jQueryUiProgressbar.js';
import { InitOneUpFileUpload, TestUploadProgressBar } from '@/js/includes/OneUpFileUpload/OneUpFileUpload_EasyUiProgressbar.js';

import { VsFormSubmit } from '@/js/includes/vs_form.js';
import { VsPath } from '@/js/includes/fos_js_routes.js';
import { GetCkEditorData } from '@/js/includes/ckeditor.js';

window.TestUploadProgressBarStarted = false;
window.VideoSaved                   = false;
window.UploadedResources            = {};
window.RedirectUrl                  = false;

var RequiredResources               = [];

function getSelectedCategories()
{
    let selectedValues = new Array();
    $( '.combobox-checkbox-category_taxon').each( function(){
        if ( $( this ).parent().hasClass( 'combobox-item-selected' ) ) {
            selectedValues.push( $( this ).val() );
        }
    });
    
    return selectedValues;
}

function getVideoPhotos()
{
    $( '.fieldPhoto' ).each( function ()
    {
        if ( ! $( this ).hasClass( 'persistedPhoto' ) ) {
            let photoName   = $( this ).next( '.input-group-text' ).text();
            let photoCode   = $( this ).closest( 'div.row' ).find( '.photoCode' ).val();
            
            if ( photoName ) {
                alert( photoName );
                alert( photoCode );
            }
        }
    });
}

function updateTagsInput( newValue )
{
    let inputValue  = '';
    
    for ( let i = 0; i < newValue.length; i++ ) {
        inputValue  += newValue[i].value;
        
        if ( i != ( newValue.length - 1 ) ) {
            inputValue  += ',';
        }
    }
    
    return inputValue;
}

const preFormSubmit = function ()
{
    $( '#FormVideo' ).validate();
    
    return $( '#video_form_name' ).valid();
}

function saveVideo()
{
    //var formData    = new FormData( $( '#FormVideo' )[ 0 ] );
    let formData    = new FormData();
    let submitUrl   = VsPath( 'vvp_video_save' );
    
    formData.set( "id", $( '#video_form_id' ).val() );
    formData.set( "currentLocale", $( '#video_form_currentLocale' ).val() );
    
    //let selectedTaxons  = $( '#video_form_category_taxon' ).combobox( 'getValues' );
    let selectedTaxons  = getSelectedCategories();
    formData.set( "category_taxon", selectedTaxons );
    //console.log( selectedTaxons ); return;
    
    formData.set( "tags", updateTagsInput( JSON.parse( $( '#video_form_tags' ).val() ) ) );
    formData.set( "name", $( '#video_form_name' ).val() );
    formData.set( "description", GetCkEditorData( 'video_form_description' ) );
    //console.log( RequiredResources );
    
    // OLD WAY
    if ( RequiredResources.includes( "VsVvp_VideoThumbnail" ) ) {
        formData.set( "video_thumbnail", window.UploadedResources["VsVvp_VideoThumbnail"] );
    }
    
    // New Video Photo Upload
    let photoIndex  = 1;
    $( '.fieldPhoto' ).each( function ()
    {
        if ( ! $( this ).hasClass( 'persistedPhoto' ) ) {
            let photoName   = $( this ).next( '.input-group-text' ).text();
            let photoCode   = $( this ).closest( 'div.row' ).find( '.photoCode' ).val();
            let photoDesc   = $( this ).closest( 'div.row' ).find( '.photoDescription' ).val();
            
            if ( photoName ) {
                formData.set( "photos[" + photoIndex + "][code]", photoCode );
                formData.set( "photos[" + photoIndex + "][description]", photoDesc );
                formData.set( "photos[" + photoIndex + "][photo]", $( this )[0].files[0] );
                photoIndex++;
            }
        }
    });
    
    if ( RequiredResources.includes( "VsVvp_VideoFile" ) ) {
        formData.set( "video_file", window.UploadedResources["VsVvp_VideoFile"] );
    }
    
    // Debug FormData
    /*
    for ( var pair of formData.entries() ) {
        console.log( pair[0]+ ': ' + pair[1] );
    }
    return;
    */
    
    VsFormSubmit( formData, submitUrl, window.RedirectUrl );
}

$( function()
{
    RequiredResources   = JSON.parse( $( '#video_form_requiredResources' ).val() );
    window.RedirectUrl  = VsPath( 'vs_vvp_video_index' );
    
    /** Test Progress Bar 
    TestUploadProgressBar({
        btnStartUploadSelector: "#video_form_btnSave",
        progressbarSelector: "#TestUploadProgressBar"
    });
    */
    
    /** Thumbnail 
    InitOneUpFileUpload({
        fileuploadSelector: "#OneUpFileUploadThumbnail",
        fileinputSelector: "#video_form_thumbnail",
        btnStartUploadSelector: "#video_form_btnSave",
        progressbarSelector: "#FileUploadProgressbarThumbnail",
        fileInputFieldName: "thumbnail",
        fileResourceId: $( '#video_form_thumbnailFileId' ).val(),
        fileResourceKey: "VsVvp_VideoThumbnail",
        fileResourceClass: "App\\Entity\\VideoThumbnail",
        maxChunkSize: 10000000
    }, preFormSubmit);
    */
    
    /** Video */
    InitOneUpFileUpload({
        fileuploadSelector: "#OneUpFileUploadVideo",
        fileinputSelector: "#video_form_video",
        btnStartUploadSelector: "#video_form_btnSave",
        progressbarSelector: "#FileUploadProgressbarVideo",
        fileInputFieldName: "video",
        fileResourceId: $( '#video_form_videoFileId' ).val(),
        fileResourceKey: "VsVvp_VideoFile",
        fileResourceClass: "App\\Entity\\VideoFile",
        maxChunkSize: 10000000
    }, preFormSubmit);
    
    
    window.addEventListener( 'resourceUploaded', event => {
        
        if ( event.detail.resourceKey && event.detail.resourceId ) {
            window.UploadedResources[event.detail.resourceKey]  = event.detail.resourceId;
        }
        
        for ( let i = 0; i < RequiredResources.length; i++ ) {
            if (
                ! ( RequiredResources[i] in window.UploadedResources ) ||
                ! window.UploadedResources[RequiredResources[i]]
            ) {
                return;
            }
        }
        
        if ( ! window.VideoSaved ) {
            saveVideo();
            window.VideoSaved   = true;
        }
    });
    
    $( '#video_form_btnApply').on( 'click', function( e )
    {
        e.preventDefault();
        e.stopPropagation();
        
        if ( RequiredResources.length ) {
            window.RedirectUrl  = VsPath( 'vs_vvp_video_update', {'id': $( '#video_form_id' ).val()} );
            $( "#video_form_btnSave" ).trigger( "click" );
        } else {
            $( '#FormVideo' ).submit();
        }
    });
    
    $( document ).on( 'change','#video_form_thumbnail' , function()
    { 
        if ( ! RequiredResources.includes( "VsVvp_VideoThumbnail" ) ) {
            RequiredResources.push( "VsVvp_VideoThumbnail" );
        }
    });
    
    $( document ).on( 'change','#video_form_video' , function()
    { 
        if ( ! RequiredResources.includes( "VsVvp_VideoFile" ) ) {
            RequiredResources.push( "VsVvp_VideoFile" );
        }
    });
    
    $( '#btnTestGetValues' ).on( 'click', function( e )
    {
        //let selectedValues  = getSelectedCategories();
        let selectedValues  = getVideoPhotos();
        
        alert( selectedValues );
    });
});