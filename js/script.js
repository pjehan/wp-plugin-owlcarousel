jQuery(document).ready(function($) {

    var carouselIds = new Array();

    $(".owl-carousel").each(function() {
        carouselIds.push($(this).attr("id"));
    });

    for (var i in carouselIds) {
        var params = {};
        var datas = $(document.getElementById(carouselIds[i])).data();
        for (var paramName in datas) {
            var data = $(document.getElementById(carouselIds[i])).data(paramName);
            if (data !== "") {
                // If it's an array (contains comma) parse the string to array
                if(String(data).indexOf(",") > -1) {
                    data = data.split(",");
                }

                // New random param not available in Owl Carousel
                if(paramName == "random") {
                    params[owlCarouselParamName("beforeInit")] = function(elem) {
                        random(elem);
                    };
                } else {
                    params[owlCarouselParamName(paramName)] = data;
                }
            }
        }

        $(document.getElementById(carouselIds[i])).owlCarousel(params);
    }

    /**
     * Sort random function
     * @param {Selector} owlSelector Owl Carousel selector
     */
    function random(owlSelector){
        owlSelector.children().sort(function(){
            return Math.round(Math.random()) - 0.5;
        }).each(function(){
            $(this).appendTo(owlSelector);
        });
    }

});

/**
 * Fix Owl Carousel parameter name case.
 * @param {String} paramName Parameter name
 * @returns {String} Fixed parameter name
 */
function owlCarouselParamName(paramName) {

    var parameterArray = {
        ADDCLASSACTIVE: "addClassActive",
        AFTERACTION: "afterAction",
        AFTERINIT: "afterInit",
        AFTERLAZYLOAD: "afterLazyLoad",
        AFTERMOVE: "afterMove",
        AFTERUPDATE: "afterUpdate",
        AUTOHEIGHT: "autoHeight",
        AUTOPLAY: "autoPlay",
        BASECLASS: "baseClass",
        BEFOREINIT: "beforeInit",
        BEFOREMOVE: "beforeMove",
        BEFOREUPDATE: "beforeUpdate",
        DRAGBEFOREANIMFINISH: "dragBeforeAnimFinish",
        ITEMS: "items",
        ITEMSCUSTOM: "itemsCustom",
        ITEMSDESKTOP: "itemsDesktop",
        ITEMSDESKTOPSMALL: "itemsDesktopSmall",
        ITEMSMOBILE: "itemsMobile",
        ITEMSSCALEUP: "itemsScaleUp",
        ITEMSTABLET: "itemsTablet",
        ITEMSTABLETSMALL: "itemsTabletSmall",
        JSONPATH: "jsonPath",
        JSONSUCCESS: "jsonSuccess",
        LAZYLOAD: "lazyLoad",
        LAZYFOLLOW: "lazyFollow",
        LAZYEFFECT: "lazyEffect",
        MOUSEDRAG: "mouseDrag",
        NAVIGATION: "navigation",
        NAVIGATIONTEXT: "navigationText",
        PAGINATION: "pagination",
        PAGINATIONNUMBERS: "paginationNumbers",
        PAGINATIONSPEED: "paginationSpeed",
        RESPONSIVE: "responsive",
        RESPONSIVEBASEWIDTH: "responsiveBaseWidth",
        RESPONSIVEREFRESHRATE: "responsiveRefreshRate",
        REWINDNAV: "rewindNav",
        REWINDSPEED: "rewindSpeed",
        SCROLLPERPAGE: "scrollPerPage",
        SINGLEITEM: "singleItem",
        SLIDESPEED: "slideSpeed",
        STARTDRAGGING: "startDragging",
        STOPONHOVER: "stopOnHover",
        THEME: "theme",
        TOUCHDRAG: "touchDrag",
        TRANSITIONSTYLE: "transitionStyle",
    };

    return parameterArray[paramName.toUpperCase()];
}
