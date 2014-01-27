jQuery(document).ready(function($) {

    var carouselIds = new Array();

    $(".owl-carousel").each(function() {
        carouselIds.push($(this).attr("id"));
    });
    
    for (var i in carouselIds) {
        var params = {};
        var data = $("#" + carouselIds[i]).data();
        for (var paramName in data) {
            if ($("#" + carouselIds[i]).data(paramName) !== "") {
                params[owlCarouselParamName(paramName)] = $("#" + carouselIds[i]).data(paramName);
            }
        }

        $("#" + carouselIds[i]).owlCarousel(params);
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
        ITEMSDESKTOSMALL: "itemsDesktopSmall",
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