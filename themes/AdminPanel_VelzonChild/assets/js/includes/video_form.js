
export function GetDescription()
{
    let useCkEditor = $( '#FormVideo' ).attr( 'data-useCkEditor' );
    var description;
    
    if ( useCkEditor == '5' ) {
        let editor = editors['video_form_description'];
        //alert( editor );
        
        description = editor.getData();
    } else {
        require( 'ckeditor4/ckeditor.js' );
        description = CKEDITOR.instances.video_form_description.getData();
    }
    
    //description = $( '#video_form_description' ).val();
    //alert( description );
    
    return description;
}
