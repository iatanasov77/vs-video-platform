require ( 'jquery-duplicate-fields/jquery.duplicateFields.js' );

$( function ()
{
    $( '.OutputFormatsContainer' ).duplicateFields({
        btnRemoveSelector: ".btnRemoveField",
        btnAddSelector:    ".btnAddField"
    });
});