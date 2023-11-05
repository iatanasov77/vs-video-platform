require ( '@/js/includes/BootstrapDropdown.js' );
require ( 'jquery-duplicate-fields/jquery.duplicateFields.js' );

$( function ()
{
    $( '.SettingsContainer' ).duplicateFields({
        btnRemoveSelector: ".btnRemoveField",
        btnAddSelector:    ".btnAddField"
    });
});