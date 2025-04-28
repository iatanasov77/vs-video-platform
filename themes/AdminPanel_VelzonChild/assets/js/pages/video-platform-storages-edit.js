require ( 'jquery-duplicate-fields/jquery.duplicateFields.js' );

$( function ()
{
    $( '.SettingsContainer' ).duplicateFields({
        btnRemoveSelector: ".btnRemoveField",
        btnAddSelector:    ".btnAddField"
    });
    
    if ( $( '#video_platform_storage_form_storageType' ).val() == 'local' ) {
        $( '#StorageSettingsInfo' ).show();
    }
    
    $( '#video_platform_storage_form_storageType' ).on( 'change', function ( e ) {
        if ( $( this ).val() == 'local' ) {
            $( '#StorageSettingsInfo' ).show();
        } else {
            $( '#StorageSettingsInfo' ).hide();
        }
    });
});