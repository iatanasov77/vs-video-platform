require ( 'jquery-duplicate-fields/jquery.duplicateFields.js' );

$( function ()
{
    $( '.OutputFormatsContainer' ).duplicateFields({
        btnRemoveSelector: ".btnRemoveField",
        btnAddSelector:    ".btnAddField"
    });
    
    let transcodedVideoUrlsType = $( '.transcodedVideoUrlsType:checked' ).val();
    alert( transcodedVideoUrlsType );
    if( transcodedVideoUrlsType == 'cloud_signed' ) {
        $( '#SignedUrlExpirationFormGroup' ).show();
    }
    
    $( '.transcodedVideoUrlsType' ).on( 'change', function( e ) {
        if ( this.value == 'cloud_signed' ) {
            $( '#SignedUrlExpirationFormGroup' ).show();
        }
        else {
            $( '#SignedUrlExpirationFormGroup' ).hide();
        } 
    });
});