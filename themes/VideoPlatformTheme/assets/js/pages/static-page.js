require( '../../vendor/themeforest-flixgo-online-movies/js/main.js' );

$( function ()
{
    $( ':input[required]' ).each( function( i, requiredField )
    {
        var placeholder = $( requiredField ).attr( 'placeholder' );
        if ( placeholder ) {
            $( requiredField ).attr( 'placeholder', placeholder + ' *' );
        }
    });
});