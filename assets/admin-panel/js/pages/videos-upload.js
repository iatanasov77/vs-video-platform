require( 'jquery-validation' );
require ( '@/js/includes/BootstrapDropdown.js' );
require( '../../css/videos-upload.css' );

require( '@/js/includes/bootstrap-5/file-input.js' );
//import { InitOneUpFileUpload, TestUploadProgressBar } from '@/js/includes/OneUpFileUpload/OneUpFileUpload_jQueryUiProgressbar.js';
import { InitOneUpFileUpload, TestUploadProgressBar } from '@/js/includes/OneUpFileUpload/OneUpFileUpload_EasyUiProgressbar.js';

import { VsFormSubmit } from '@/js/includes/vs_form.js';
import { VsPath } from '@/js/includes/fos_js_routes.js';

window.TestUploadProgressBarStarted = false;
window.VideoSaved                   = false;
window.UploadedResources            = {};
window.RedirectUrl                  = false;

var RequiredResources               = [];

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
    
    return $( '#video_form_title' ).valid();
}

function saveVideo()
{
    //var formData    = new FormData( $( '#FormVideo' )[ 0 ] );
    let formData    = new FormData();
    let submitUrl   = VsPath( 'vvp_video_save' );
    
    formData.set( "id", $( '#video_form_id' ).val() );
    formData.set( "currentLocale", $( '#video_form_currentLocale' ).val() );
    
    let selectedTaxons  = $( '#video_form_category_taxon' ).combotree( 'getValues' );
    formData.set( "category_taxon", selectedTaxons );
    //console.log( selectedTaxons ); return;
    
    formData.set( "tags", updateTagsInput( JSON.parse( $( '#video_form_tags' ).val() ) ) );
    
    formData.set( "title", $( '#video_form_title' ).val() );
    
    require( 'ckeditor4/ckeditor.js' );
    var description = CKEDITOR.instances.video_form_description.getData();
    //var description = $( '#video_form_description' ).val();
    //alert( description );
    formData.set( "description", description );
    
    //console.log( RequiredResources );
    
    if ( RequiredResources.includes( "VsVvp_VideoThumbnail" ) ) {
        formData.set( "video_thumbnail", window.UploadedResources["VsVvp_VideoThumbnail"] );
    }
    
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
        fileResourceClass: "App\\Entity\\VideoThumbnail"
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
        fileResourceClass: "App\\Entity\\VideoFile"
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
});