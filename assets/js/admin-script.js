jQuery(document).ready(function($) {

    var parameterForm = $("#owlcarouselparameterform");

    // Save parameters data
    parameterForm.submit(function(e){
        e.preventDefault();

        $(".spinner").show();

        var wordpressGallery = $(this).find("input[name='wordpress_gallery']").is(':checked');
        var orderBy = $(this).find("select[name='orderby']").attr('value');

        $.post(parameterForm.attr("action"), { wordpress_gallery: wordpressGallery, orderby: orderBy })
            .done(function( data ) {
                $(".spinner").hide();
            });
    });

});
