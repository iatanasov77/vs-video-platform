require ( '@/js/includes/BootstrapDropdown.js' );
require( '@kanety/jquery-simple-tree-table/dist/jquery-simple-tree-table.js' );
require( '@/js/includes/resource-delete.js' );

$( function()
{
    $( '#tblCategories_SimpleTreeTable' ).simpleTreeTable({
        expander: $( '#expander' ),
        collapser: $( '#collapser' )
    });
    
    $('#collapsed').simpleTreeTable({
      opened: 'all',
    });
});
