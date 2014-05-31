jQuery(document).ready(function($) {
    
    var parameterForm = $("#owlcarouselparameterform");
    
    // Save parameters data
    parameterForm.submit(function(e){
        e.preventDefault();
        
        $(".spinner").show();
        
        var wordpressGallery = $(this).find("input[name='wordpress_gallery']").is(':checked');
        
        $.post(parameterForm.attr("action"), { wordpress_gallery: wordpressGallery })
            .done(function( data ) {
                $(".spinner").hide();
            });
    });
    
});