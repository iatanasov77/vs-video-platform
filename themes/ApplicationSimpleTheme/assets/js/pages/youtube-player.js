$( function()
{
    $( '.btnDisplayVideo' ).on( 'click', function()
    {
        var videoUrl    = 'https://www.youtube.com/embed/' + $( this ).attr( 'data-videoId' );
        //alert( videoUrl );
        
        $( '#VideoPlayer' ).attr( 'data', videoUrl );
    });
});