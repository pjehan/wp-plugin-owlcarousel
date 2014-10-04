<?php
/*
  Plugin Name: Owl Carousel
  Description: A simple plugin to include an Owl Carousel in any post
  Author: Pierre JEHAN
  Version: 0.5.1
  Author URI: http://www.pierre-jehan.com
  Licence: GPL2
 */

add_theme_support('post-thumbnails');

add_action('init', 'owlcarousel_init');
add_action('wp_print_scripts', 'owl_register_scripts');
add_action('wp_print_styles', 'owl_register_styles');
add_action('widgets_init', 'owl_widgets_init');
add_action('manage_edit-owl-carousel_columns', 'owl_columnfilter');
add_action('manage_posts_custom_column', 'owl_column');
add_action('admin_menu', 'owl_carousel_menu');
add_action('admin_enqueue_scripts', 'owl_carousel_admin_register_scripts');

if(filter_var(get_option('owl_carousel_wordpress_gallery', false), FILTER_VALIDATE_BOOLEAN)) {
    add_filter('post_gallery', 'owl_carousel_post_gallery', 10, 2);
}

// Add functions to create a new attachments fields
add_filter("attachment_fields_to_edit", "owl_carousel_attachment_fields_to_edit", null, 2);
add_filter("attachment_fields_to_save", "owl_carousel_attachment_fields_to_save", null, 2);

/**
 * Initilize the plugin
 */
function owlcarousel_init() {

    $labels = array(
        'name' => __('Owl Carousel', 'owl-carousel-domain'),
        'singular_name' => __('Carousel Item', 'owl-carousel-domain'),
        'add_new' => __('Add New Item', 'owl-carousel-domain'),
        'add_new_item' => __('Add New Carousel Item', 'owl-carousel-domain'),
        'edit_item' => __('Edit Carousel Item', 'owl-carousel-domain'),
        'new_item' => __('Add New Carousel Item', 'owl-carousel-domain'),
        'view_item' => __('View Item', 'owl-carousel-domain'),
        'search_items' => __('Search Carousel', 'owl-carousel-domain'),
        'not_found' => __('No carousel items found', 'owl-carousel-domain'),
        'not_found_in_trash' => __('No carousel items found in trash', 'owl-carousel-domain'),
    );

    register_post_type('owl-carousel', array(
        'public' => true,
        'publicly_queryable' => false,
        'exclude_from_search' => true,
        'label' => 'Owl Carousel',
        'menu_icon' => plugins_url('/owl-carousel/images/owl-logo-16.png'),
        'labels' => $labels,
        'capability_type' => 'post',
        'supports' => array(
            'title',
            'editor',
            'thumbnail'
        )
    ));

    register_taxonomy(
		'Carousel',
		'owl-carousel',
		array(
			'label' => __( 'Carousel' ),
			'rewrite' => array( 'slug' => 'carousel' ),
			'hierarchical' => true,
            'show_admin_column' => true,
		)
	);

    add_image_size('owl_widget', 180, 100, true);
    add_image_size('owl_function', 600, 280, true);

    add_shortcode('owl-carousel', 'owl_function');
    add_filter("mce_external_plugins", "owl_register_tinymce_plugin");
    add_filter('mce_buttons', 'owl_add_tinymce_button');

    // Add Wordpress Gallery option
    add_option('owl_carousel_wordpress_gallery', 'off');
    add_option('owl_carousel_orderby', 'post_date');
}

function owl_carousel_menu() {
    add_submenu_page('edit.php?post_type=owl-carousel', __('Parameters', 'owl-carousel-domain'), __('Parameters', 'owl-carousel-domain'), 'manage_options', 'owl-carousel-parameters', 'submenu_parameters');
}

function submenu_parameters() {

    $isWordpressGallery = (filter_var(get_option('owl_carousel_wordpress_gallery', false), FILTER_VALIDATE_BOOLEAN)) ? 'checked' : '';
    $orderBy = get_option('owl_carousel_orderby', 'post_date');
    $orderByOptions = array('post_date', 'title');

    echo '<div class="wrap owl_carousel_page">';

        echo '<?php update_option("owl_carousel_wordpress_gallery", $_POST["wordpress_gallery"]); ?>';

		echo '<h2>' . __('Owl Carousel parameters', 'owl-carousel-domain') . '</h2>';

		echo '<form action="' . plugin_dir_url( __FILE__ ) . 'save_parameter.php" method="POST" id="owlcarouselparameterform">';

		    echo '<h3>' . __('Wordpress Gallery', 'owl-carousel-domain') . '</h3>';
		    echo '<input type="checkbox" name="wordpress_gallery" ' . $isWordpressGallery . ' />';
		    echo '<label>' . __('Use Owl Carousel with Wordpress Gallery', 'owl-carousel-domain') . '</label>';
		    echo '<br />';
        echo '<label>' . __('Order Owl Carousel elements by ', 'owl-carousel-domain') . '</label>';
        echo '<select name="orderby" />';
        foreach($orderByOptions as $option) {
            echo '<option value="' . $option . '" ' . (($option == $orderBy) ? 'selected="selected"' : '') . '>' . $option . '</option>';
        }
        echo '</select>';
        echo '<br />';
		    echo '<br />';
		    echo '<input type="submit" class="button-primary owl-carousel-save-parameter-btn" value="' . __('Save changes', 'owl-carousel-domain') . '" />';
		    echo '<span class="spinner"></span>';

		echo '</form>';

	echo '</div>';
}

/**
 * List of JavaScript / CSS files for admin
 */
function owl_carousel_admin_register_scripts() {
    wp_register_style('owl.carousel.admin.styles', plugin_dir_url( __FILE__ ) . 'css/admin_styles.css');
    wp_enqueue_style('owl.carousel.admin.styles');

    wp_register_script('owl.carousel.admin.script', plugin_dir_url( __FILE__ ) . 'js/admin_script.js');
    wp_enqueue_script('owl.carousel.admin.script');
}

/**
 * List of JavaScript files
 */
function owl_register_scripts() {
    wp_register_script('js.owl.carousel', plugins_url('/owl-carousel/js/owl.carousel.js'));
    wp_register_script('js.owl.carousel.script', plugins_url('/owl-carousel/js/script.js'));

    wp_enqueue_script('jquery');
    wp_enqueue_script('js.owl.carousel');
    wp_enqueue_script('js.owl.carousel.script');
}

/**
 * List of CSS files
 */
function owl_register_styles() {
    wp_register_style('style.owl.carousel', plugins_url('/owl-carousel/css/owl.carousel.css'));
    wp_register_style('style.owl.carousel.theme', plugins_url('/owl-carousel/css/owl.theme.css'));
    wp_register_style('style.owl.carousel.transitions', plugins_url('/owl-carousel/css/owl.transitions.css'));
    wp_register_style('style.owl.carousel.styles', plugins_url('/owl-carousel/css/styles.css'));

    wp_enqueue_style('style.owl.carousel');
    wp_enqueue_style('style.owl.carousel.theme');
    wp_enqueue_style('style.owl.carousel.transitions');
    wp_enqueue_style('style.owl.carousel.styles');
}

function owl_register_tinymce_plugin($plugin_array) {
    $plugin_array['owl_button'] = plugins_url('/owl-carousel/js/owl-tinymce-plugin.js');
    return $plugin_array;
}

function owl_add_tinymce_button($buttons) {
    $buttons[] = "owl_button";
    return $buttons;
}

/*
 * Initialize Owl Widget
 */
function owl_widgets_init() {
    register_widget("owl_Widget");
}

class owl_Widget extends WP_Widget {

    public function __construct() {
        parent::__construct('owl_Widget', 'Owl Carousel', array('description' => __('A Owl Carousel Widget', 'text_domain')));
    }

    public function form($instance) {
        if (isset($instance['title'])) {
            $title = $instance['title'];
        } else {
            $title = __('Widget Carousel', 'text_domain');
        }
        if (isset($instance['category'])) {
            $carousel = $instance['category'];
        } else {
            $carousel = 'Uncategorized';
        }
        ?>
        <p>
            <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" />
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('category'); ?>"><?php _e('Carousel:'); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('category'); ?>" name="<?php echo $this->get_field_name('category'); ?>" type="text" value="<?php echo esc_attr($carousel); ?>" />
        </p>
        <?php
    }

    public function update($new_instance, $old_instance) {
        $instance = array();
        $instance['title'] = strip_tags($new_instance['title']);
        $instance['category'] = strip_tags($new_instance['category']);

        return $instance;
    }

    public function widget($args, $instance) {
        extract($args);
        $title = apply_filters('widget_title', $instance['title']);
        echo $before_widget;
        if (!empty($title))
            echo $before_title . $title . $after_title;
        echo owl_function(array(category => $instance['category'], singleItem => "true", autoPlay => "true", pagination => "false"));
        echo $after_widget;
    }

}

/**
 * Add custom column filters in administration
 * @param array $columns
 */
function owl_columnfilter($columns) {
    $thumb = array('thumbnail' => 'Image');
    $columns = array_slice($columns, 0, 2) + $thumb + array_slice($columns, 2, null);

    return $columns;
}

/**
 * Add custom column contents in administration
 * @param string $columnName
 */
function owl_column($columnName) {
    global $post;
    if ($columnName == 'thumbnail') {
        echo edit_post_link(get_the_post_thumbnail($post->ID, 'thumbnail'), null, null, $post->ID);
    }
}

/**
 * Adding our images custom fields to the $form_fields array
 * @param array $form_fields
 * @param object $post
 * @return array
 */
function owl_carousel_attachment_fields_to_edit($form_fields, $post) {
    // add our custom field to the $form_fields array
    // input type="text" name/id="attachments[$attachment->ID][custom1]"
    $form_fields["owlurl"] = array(
        "label" => __("Owl Carousel URL"),
        "input" => "text",
        "value" => get_post_meta($post->ID, "_owlurl", true)
    );

    return $form_fields;
}

/**
 * Save images custom fields
 * @param array $post
 * @param array $attachment
 * @return array
 */
function owl_carousel_attachment_fields_to_save($post, $attachment) {
    if( isset($attachment['owlurl']) ){
        update_post_meta($post['ID'], '_owlurl', $attachment['owlurl']);
    }

    return $post;
}

/**
 * Plugin main function
 * @param type $atts Owl parameters
 * @param type $content
 * @return string Owl HTML code
 */
function owl_function($atts, $content = null) {
    extract(shortcode_atts(array(
        'category' => 'Uncategoryzed'
                    ), $atts));

    $data_attr = "";
    foreach ($atts as $key => $value) {
        if ($key != "category") {
            $data_attr .= ' data-' . $key . '="' . $value . '" ';
        }
    }

    $lazyLoad = array_key_exists("lazyload", $atts) && $atts["lazyload"] == true;

    $args = array(
        'post_type' => 'owl-carousel',
        'orderby' => get_option('owl_carousel_orderby', 'post_date'),
        'order' => 'asc',
        'tax_query' => array(
            array(
                'taxonomy' => 'Carousel',
                'field' => 'slug',
                'terms' => $atts['category']
            )
        ),
        'nopaging' => true
    );

    $result = '<div id="owl-carousel-' . rand() . '" class="owl-carousel" ' . $data_attr . '>';

    $loop = new WP_Query($args);
    while ($loop->have_posts()) {
        $loop->the_post();

        $img_src = wp_get_attachment_image_src(get_post_thumbnail_id(get_the_ID()), get_post_type());
        $meta_link = get_post_meta(get_post_thumbnail_id(get_the_ID()), '_owlurl', true);

        $result .= '<div class="item">';
        if ($img_src[0])
        {
            $result .= '<div>';
                if(!empty($meta_link)) {
                    $result .= '<a href="'. $meta_link .'">';
                }
                if ($lazyLoad){
                    $result .= '<img class="lazyOwl" title="' . get_the_title() . '" data-src="' . $img_src[0] . '" alt="' . get_the_title() . '"/>';
                } else {
                    $result .= '<img title="' . get_the_title() . '" src="' . $img_src[0] . '" alt="' . get_the_title() . '"/>';
                }
                if(!empty($meta_link)) {
                    $result .= '</a>';
                }

                // Add image overlay with hook
                $slide_title = get_the_title();
                $slide_content = get_the_content();
                $img_overlay = '<div class="owl-carousel-item-imgoverlay">';
                    $img_overlay .= '<div class="owl-carousel-item-imgtitle">' . $slide_title . '</div>';
                    $img_overlay .= '<div class="owl-carousel-item-imgcontent">' . $slide_content . '</div>';
                $img_overlay .= '</div>';
                $result .= apply_filters( 'owlcarousel_img_overlay', $img_overlay, $slide_title, $slide_content, $meta_link );

            $result .= '</div>';
        }
        else
        {
            $result .= '<div class="owl-carousel-item-text">' . get_the_content() . '</div>';
        }
        $result .= '</div>';
    }
    $result .= '</div>';

    return $result;
}


/**
 * Owl Carousel for Wordpress image gallery
 * @param string $output Gallery output
 * @param array $attr Parameters
 * @return string Owl HTML code
 */
function owl_carousel_post_gallery($output, $attr) {
    global $post;

    if (isset($attr['orderby'])) {
        $attr['orderby'] = sanitize_sql_orderby($attr['orderby']);
        if (!$attr['orderby'])
            unset($attr['orderby']);
    }

    extract(shortcode_atts(array(
        'order' => 'ASC',
        'orderby' => 'menu_order ID',
        'id' => $post->ID,
        'itemtag' => 'dl',
        'icontag' => 'dt',
        'captiontag' => 'dd',
        'columns' => 3,
        'size' => 'thumbnail',
        'include' => '',
        'exclude' => ''
    ), $attr));

    $id = intval($id);
    if ('RAND' == $order) $orderby = 'none';

    if (!empty($include)) {
        $include = preg_replace('/[^0-9,]+/', '', $include);
        $_attachments = get_posts(array('include' => $include, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => $order, 'orderby' => $orderby));

        $attachments = array();
        foreach ($_attachments as $key => $val) {
            $attachments[$val->ID] = $_attachments[$key];
        }
    }

    if (empty($attachments)) return '';


    // Add item number if not defined
    if(!isset($attr['items'])) {
        $attr['items'] = '1';
    }

    $data_attr = "";
    foreach ($attr as $key => $value) {
        if ($key != "category") {
            $data_attr .= ' data-' . $key . '="' . $value . '" ';
        }
    }

    $output .= '<div id="owl-carousel-' . rand() . '" class="owl-carousel" ' . $data_attr . '>';

    foreach ($attachments as $id => $attachment) {
        $img = wp_get_attachment_image_src($id, 'full');
        $meta_link = get_post_meta($id, '_owlurl', true);

        $title = $attachment->post_title;

        $output .= "<div class=\"item\">";
        if(!empty($meta_link)) {
            $output .= "<a href=\"" . $meta_link . "\">";
        }
        $output .= "<img src=\"{$img[0]}\" width=\"{$img[1]}\" height=\"{$img[2]}\" alt=\"$title\" />\n";
        if(!empty($meta_link)) {
            $output .= "</a>";
        }
        $output .= "</div>";
    }

    $output .= "</div>";

    return $output;
}
?>
