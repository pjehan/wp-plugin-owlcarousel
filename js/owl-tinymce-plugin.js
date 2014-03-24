jQuery(document).ready(function($) {

    tinymce.create('tinymce.plugins.owl_plugin', {
        init: function(ed, url) {
            ed.addCommand('owl_insert_shortcode', function() {
                //selected = tinyMCE.activeEditor.selection.getContent();
                content = '[owl-carousel category="Uncategorized" singleItem="true" autoPlay="true"]';
                tinymce.execCommand('mceInsertContent', false, content);
            });
            ed.addButton('owl_button', {title: 'Insert shortcode', cmd: 'owl_insert_shortcode', image: url + '/../images/owl-logo-16.png'});
        }
    });
    
    tinymce.PluginManager.add('owl_button', tinymce.plugins.owl_plugin);

});