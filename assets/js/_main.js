jQuery( document ).ready( function( $ ) {

    var carouselIds = new Array();

    $( '.owl-carousel-plugin' ).each( function() {
        carouselIds.push( $( this ).attr( 'id' ) );
    } );

    for ( var i in carouselIds ) {
        var params = {};
        var datas = $( document.getElementById( carouselIds[i] ) ).data();
        for ( var paramName in datas ) {
            var data = $( document.getElementById( carouselIds[i] ) ).data( paramName );
            if ( data !== '' ) {
                // If it's an array (contains comma) parse the string to array
                if( String( data ).indexOf( ',' ) > -1 ) {
                    data = data.split( ',');
                }

                // New random param not available in Owl Carousel
                if( paramName == 'random' ) {
                    params[ owlCarouselParamName( 'beforeInit' ) ] = function( elem ) {
                        random( elem );
                    };
                } else {
                    params[ owlCarouselParamName( paramName ) ] = data;
                }
            }
        }

        $( document.getElementById( carouselIds[i] ) ).owlCarousel( params );
    }

    /**
     * Sort random function
     * @param {Selector} owlSelector Owl Carousel selector
     */
    function random( owlSelector ){
        owlSelector.children().sort( function() {
            return Math.round( Math.random() ) - 0.5;
        } ).each( function() {
            $( this ).appendTo( owlSelector );
        } );
    }

    /**
     * Add slides orientation for gallery
     */
    var slides = $( '.owl-carousel .item .image' );

 	slides.each( function() {
        var img = new Image;
        img.src = $( this ).css( 'background-image' ).replace( /url\(|\)|'|"/g, '' );
        var ratio = img.height / img.width;

        if ( ratio > 1 ) {
 			$( this ).addClass( 'portrait' );
            $( this ).css( 'width', img.width );
 		} else if ( ratio === 1 ) {
 			$( this ).addClass( 'square' );
            $( this ).css( 'width', img.width );
 		} else if ( ratio < 1 ) {
 			$( this ).addClass( 'landscape' );
            $( this ).css( 'width', img.width / ratio );
 		}
 	} );

    /**
     * Fullscreen slides
     */
    $( '.owl-carousel.fullscreen .item a' ).on( 'click', function(e) {
		e.preventDefault();
		screenfull.request();
		$( 'body' ).addClass( 'gallery-fullscreen' );
	} );

	$( '.fullscreen-close' ).on( 'click', function() {
		exitSliderFullscreen( ! screenfull.isFullscreen );
	} );

	$( document ).on( screenfull.raw.fullscreenchange, function () {
		exitSliderFullscreen( screenfull.isFullscreen );
    } );

	function exitSliderFullscreen( isFullscreen ) {
		if( ! isFullscreen ) {
        	screenfull.exit();
			$( 'body' ).removeClass( 'gallery-fullscreen' );
		}
	}
} );

/**
 * Fix Owl Carousel parameter name case.
 * @param {String} paramName Parameter name
 * @returns {String} Fixed parameter name
 */
function owlCarouselParamName( paramName ) {

    var parameterArray = {
        ANIMATEIN: "animateIn",
        ANIMATEOUT: "animateOut",
        AUTOPLAY: "autoplay",
        AUTOPLAYSPEED: "autoplaySpeed",
        AUTOPLAYTIMEOUT: "autoplayTimeout",
        AUTOPLAYHOVERPAUSE: "autoplayHoverPause",
        AUTOWIDTH: "autoWidth",
        CALLBACKS: "callbacks",
        CENTER: "center",
        DOTS: "dots",
        DOTSCONTAINER: "dotsContainer",
        DOTSDATA: "dotsData",
        DOTSEACH: "dotsEach",
        DOTSSPEED: "dotsSpeed",
        DRAGENDSPEED: "dragEndSpeed",
        FALLBACKEASING: "fallbackEasing",
        FLUIDSPEED: "fluidSpeed",
        FREEDRAG: "freeDrag",
        INFO: "info",
        ITEMELEMENT: "itemElement",
        ITEMS: "items",
        LAZYCONTENT: "lazyContent",
        LAZYLOAD: "lazyLoad",
        LOOP: "loop",
        MARGIN: "margin",
        MERGE: "merge",
        MERGEFIT: "mergeFit",
        MOUSEDRAG: "mouseDrag",
        NAV: "nav",
        NAVCONTAINER: "navContainer",
        NAVSPEED: "navSpeed",
        NAVREWIND: "navRewind",
        NAVTEXT: "navText",
        NESTEDITEMSELECTOR: "nestedItemSelector",
        PULLDRAG: "pullDrag",
        RESPONSIVE: "responsive",
        RESPONSIVECLASS: "responsiveClass",
        RESPONSIVEREFRESHRATE: "responsiveRefreshRate",
        SLIDEBY: "slideBy",
        SMARTSPEED: "smartSpeed",
        STAGEELEMENT: "stageElement",
        STAGEPADDING: "stagePadding",
        STARTPOSITION: "startPosition",
        URLHASHLISTENER: "URLhashListener",
        TOUCHDRAG: "touchDrag",
        VIDEO: "video",
        VIDEOHEIGHT: "videoHeight",
        VIDEOWIDTH: "videoWidth",
    };

    return parameterArray[ paramName.toUpperCase() ];
}
