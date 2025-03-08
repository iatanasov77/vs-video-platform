/** Init Main Slider */
new Splide( '#mainSlider', {
    type: 'loop',
    perPage: 1,
    drag: true,
    pagination: true,
    speed: 1200,
    gap: 24,
    //arrows: false,
    focus: 0,
    breakpoints: {
        767: {
            gap: 20,
        },
        1199: {
            gap: 24,
        },
    },
}).mount();

setInterval( function() {
    $( '#mainSliderNext' ).trigger( 'click' );
}, 5000 );

var elms = document.getElementsByClassName( 'section__homepage__featured' );
for ( var i = 0; i < elms.length; i++ ) {
    new Splide( elms[ i ], {
        type: 'loop',
        perPage: 6,
        drag: true,
        pagination: false,
        autoWidth: false,
        autoHeight: false,
        speed: 800,
        gap: 24,
        //arrows: false,
        focus: 0,
        breakpoints: {
            575: {
                gap: 24,
                pagination: true,
                arrows: false,
                perPage: 2,
            },
            767: {
                gap: 24,
                pagination: true,
                arrows: false,
                perPage: 3,
            },
            991: {
                pagination: true,
                arrows: false,
                perPage: 3,
                gap: 24,
            },
            1199: {
                pagination: true,
                arrows: false,
                perPage: 4,
                gap: 24,
            },
        }
    }).mount();
}
