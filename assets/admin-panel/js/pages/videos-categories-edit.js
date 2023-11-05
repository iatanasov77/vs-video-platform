require ( '@/js/includes/BootstrapDropdown.js' );
require( 'jquery-easyui/css/easyui.css' );
require( 'jquery-easyui/js/jquery.easyui.min.js' );

import { VsRemoveDuplicates } from '@/js/includes/vs_remove_duplicates.js';

$( function()
{
    VsRemoveDuplicates();
});
