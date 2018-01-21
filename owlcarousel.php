<?php
/*
  Plugin Name: Owl Carousel
  Description: A simple plugin to include an Owl Carousel in any post
  Author: Pierre JEHAN
  Version: 0.5.3
  Author URI: http://www.pierre-jehan.com
  Licence: GPL2
 */

class OwlCarousel {
    /** Version ***************************************************************/
    /**
    * @public string plugin version
    */
    public $version = '0.5.3';
    /**
    * @public string plugin DB version
    */
    public $db_version = '053';
    /** Paths *****************************************************************/
    public $file = '';
    /**
    * @public string Basename of the plugin directory
    */
    public $basename = '';
    /**
    * @public string Absolute path to the plugin directory
    */
    public $plugin_dir = '';

    /**
    * @var The one true Instance
    */
    private static $instance;

    public $meta_name_options = 'owlc_options';
    
    var $menu_page;

    public static function instance() {
        
            if ( ! isset( self::$instance ) ) {
                    self::$instance = new OwlCarousel;
                    self::$instance->setup_globals();
                    self::$instance->includes();
                    self::$instance->setup_actions();
            }
            return self::$instance;
    }
    /**
        * A dummy constructor to prevent plugin from being loaded more than once.
        *
        * @since bbPress (r2464)
        * @see bbPress::instance()
        * @see bbpress();
        */
    private function __construct() { /* Do nothing here */ }
    
    function setup_globals() {
        
        /** Paths *************************************************************/
        $this->file       = __FILE__;
        $this->basename   = plugin_basename( $this->file );
        $this->plugin_dir = plugin_dir_path( $this->file );
        $this->plugin_url = plugin_dir_url ( $this->file );
        $this->options_default = array();
        
        $this->options = wp_parse_args(get_option( $this->meta_name_options), $this->options_default);

    }
    
    function includes(){
    }
    function setup_actions(){
        add_theme_support('post-thumbnails');

        add_action('init', array($this,'init'));
        add_action('wp_enqueue_scripts', array($this,'scripts_styles'));
        add_action('widgets_init', array($this,'widget_init'));
        add_action('manage_edit-owl-carousel_columns', array($this,'admin_columns_register'));
        add_action('manage_posts_custom_column', array($this,'admin_columns_content'));
        add_action('admin_menu', array($this,'admin_menu'));
        add_action('admin_enqueue_scripts', array($this,'admin_scripts_styles'));

        if (filter_var(get_option('owl_carousel_wordpress_gallery', false), FILTER_VALIDATE_BOOLEAN)) {
            add_filter('post_gallery', array($this,'handle_gallery'), 10, 2);
        }

        // Add functions to create a new attachments fields
        add_filter("attachment_fields_to_edit", array($this,'attachment_fields_to_edit'), null, 2);
        add_filter("attachment_fields_to_save", array($this,'attachment_fields_to_save'), null, 2);
    }
    /**
     * Initilize the plugin
     */
    function init() {

        $labels = array(
            'name' => __('Owl Carousel', 'wp-owlc'),
            'singular_name' => __('Carousel Slide', 'wp-owlc'),
            'add_new' => __('Add New Slide', 'wp-owlc'),
            'add_new_item' => __('Add New Carousel Slide', 'wp-owlc'),
            'edit_item' => __('Edit Carousel Slide', 'wp-owlc'),
            'new_item' => __('Add New Carousel Slide', 'wp-owlc'),
            'view_item' => __('View Slide', 'wp-owlc'),
            'search_items' => __('Search Carousel', 'wp-owlc'),
            'not_found' => __('No carousel slides found', 'wp-owlc'),
            'not_found_in_trash' => __('No carousel slides found in trash', 'wp-owlc'),
        );

        register_post_type('owl-carousel', array(
            'public' => true,
            'publicly_queryable' => false,
            'exclude_from_search' => true,
            'label' => 'Owl Carousel',
            'menu_icon' => $this->plugin_url . '_inc/images/owl-logo-16.png',
            'labels' => $labels,
            'capability_type' => 'post',
            'supports' => array(
                'title',
                'editor',
                'thumbnail'
            )
        ));

        $taxonomy_labels = array(
            'name' => __('Carousels', 'wp-owlc'),
            'singular_name' => __('Carousel', 'wp-owlc'),
            'search_items' => __('Search Carousels', 'wp-owlc'),
            'popular_items' => __('Popular Carousels', 'wp-owlc'),
            'all_items' => __('All Carousels', 'wp-owlc'),
            'parent_item' => null,
            'parent_item_colon' => null,
            'edit_item' => __('Edit Carousel', 'wp-owlc'),
            'update_item' => __('Update Carousel', 'wp-owlc'),
            'add_new_item' => __('Add New Carousel', 'wp-owlc'),
            'new_item_name' => __('New Carousel Name', 'wp-owlc'),
            'separate_items_with_commas' => __('Separate carousels with commas', 'wp-owlc'),
            'add_or_remove_items' => __('Add or remove carousels', 'wp-owlc'),
            'choose_from_most_used' => __('Choose from the most used carousels', 'wp-owlc'),
            'not_found' => __('No carousels found.', 'wp-owlc'),
            'menu_name' => __('Carousels', 'wp-owlc'),
        );

        register_taxonomy('Carousel', 'owl-carousel', array(
            'labels' => $taxonomy_labels,
            'rewrite' => array('slug' => 'carousel'),
            'hierarchical' => true,
            'show_admin_column' => true,
        ));

        add_image_size('owl_widget', 180, 100, true);
        add_image_size('owl_function', 600, 280, true);

        add_shortcode('owl-carousel', array($this,'register_shortcode') );
        add_filter("mce_external_plugins", array($this,"tinymce_register_plugin"));
        add_filter('mce_buttons', array($this,"tinymce_add_button"));

        // Add Wordpress Gallery option
        add_option('owl_carousel_wordpress_gallery', 'off');
        add_option('owl_carousel_orderby', 'post_date');
    }
    
    function admin_menu(){
        add_submenu_page('edit.php?post_type=owl-carousel', __('Parameters', 'wp-owlc'), __('Parameters', 'wp-owlc'), 'manage_options', 'owl-carousel-parameters', array($this,'settings_page'));
    }
    
    function settings_page() {

        $isWordpressGallery = (filter_var(get_option('owl_carousel_wordpress_gallery', false), FILTER_VALIDATE_BOOLEAN)) ? 'checked' : '';
        $orderBy = get_option('owl_carousel_orderby', 'post_date');
        $orderByOptions = array('post_date', 'title');

        echo '<div class="wrap owl_carousel_page">';

        echo '<?php update_option("owl_carousel_wordpress_gallery", $_POST["wordpress_gallery"]); ?>';

        echo '<h2>' . __('Owl Carousel parameters', 'wp-owlc') . '</h2>';

        echo '<form action="' . $this->plugin_url . 'save_parameter.php" method="POST" id="owlcarouselparameterform">';

        echo '<h3>' . __('Wordpress Gallery', 'wp-owlc') . '</h3>';
        echo '<input type="checkbox" name="wordpress_gallery" ' . $isWordpressGallery . ' />';
        echo '<label>' . __('Use Owl Carousel with Wordpress Gallery', 'wp-owlc') . '</label>';
        echo '<br />';
        echo '<label>' . __('Order Owl Carousel elements by ', 'wp-owlc') . '</label>';
        echo '<select name="orderby" />';
        foreach ($orderByOptions as $option) {
            echo '<option value="' . $option . '" ' . (($option == $orderBy) ? 'selected="selected"' : '') . '>' . $option . '</option>';
        }
        echo '</select>';
        echo '<br />';
        echo '<br />';
        echo '<input type="submit" class="button-primary owl-carousel-save-parameter-btn" value="' . __('Save changes', 'wp-owlc') . '" />';
        echo '<span class="spinner"></span>';

        echo '</form>';

        echo '</div>';
    }
    
    /**
     * List of JavaScript / CSS files for admin
     */
    function admin_scripts_styles() {
        //CSS
        wp_register_style('wp-owlc-admin', $this->plugin_url . '_inc/css/wp-owlc-admin.css',null,$this->version);
        wp_enqueue_style('wp-owlc-admin');

        //JS
        wp_register_script('wp-owlc-admin', $this->plugin_url . '_inc/js/wp-owlc-admin.js',array('jquery'),$this->version);

        wp_enqueue_script('wp-owlc-admin');        
    }
    /**
     * List of JavaScript / CSS files for frontend
     */
    function scripts_styles() {
        //CSS
        wp_register_style('owlcarousel', $this->plugin_url . '_inc/css/owl.carousel.css');
        wp_register_style('owlcarousel-theme', $this->plugin_url . '_inc/css/owl.theme.css');
        wp_register_style('owlcarousel-transitions', $this->plugin_url . '_inc/css/owl.transitions.css');
        wp_register_style('wp-owlc', $this->plugin_url . '_inc/css/wp-owlc.css',array('owlcarousel','owlcarousel-theme','owlcarousel-transitions'),$this->version);
        wp_enqueue_style('wp-owlc');
        
        //JS
        wp_register_script('jquery.owlcarousel', $this->plugin_url . '_inc/js/owl.carousel.js',array('jquery'),'1.3.2',true);
        wp_register_script('wp-owlc', $this->plugin_url . '_inc/js/wp-owlc.js',array('jquery','jquery.owlcarousel'),$this->version,true);
        wp_enqueue_script('wp-owlc');
    }
    function tinymce_register_plugin($plugin_array) {
        $plugin_array['owl_button'] = $this->plugin_url . '_inc/js/owl-tinymce-plugin.js';
        return $plugin_array;
    }
    function tinymce_add_button($buttons) {
        $buttons[] = "owl_button";
        return $buttons;
    }
    /*
     * Initialize Owl Widget
     */

    function widget_init() {
        register_widget("owl_Widget");
    }
    
    /**
     * Add custom column filters in administration
     * @param array $columns
     */
    function admin_columns_register($columns) {
        $thumb = array('thumbnail' => 'Image');
        $columns = array_slice($columns, 0, 2) + $thumb + array_slice($columns, 2, null);

        return $columns;
    }
    
    /**
     * Add custom column contents in administration
     * @param string $columnName
     */
    function admin_columns_content($columnName) {
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
    function attachment_fields_to_edit($form_fields, $post) {
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
    function attachment_fields_to_save($post, $attachment) {
        if (isset($attachment['owlurl'])) {
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
    function register_shortcode($atts, $content = null) {
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

        $result = '<div id="owl-carousel-' . rand() . '" class="owl-carousel owl-carousel-' . sanitize_title($atts['category']) . '" ' . $data_attr . '>';

        $loop = new WP_Query($args);
        while ($loop->have_posts()) {
            $loop->the_post();

            $img_src = wp_get_attachment_image_src(get_post_thumbnail_id(get_the_ID()), get_post_type());
            $meta_link = get_post_meta(get_post_thumbnail_id(get_the_ID()), '_owlurl', true);

            $result .= '<div class="item">';
            if ($img_src[0]) {
                $result .= '<div>';
                if (!empty($meta_link)) {
                    $result .= '<a href="' . $meta_link . '">';
                }
                if ($lazyLoad) {
                    $result .= '<img class="lazyOwl" title="' . get_the_title() . '" data-src="' . $img_src[0] . '" alt="' . get_the_title() . '"/>';
                } else {
                    $result .= '<img title="' . get_the_title() . '" src="' . $img_src[0] . '" alt="' . get_the_title() . '"/>';
                }
                if (!empty($meta_link)) {
                    $result .= '</a>';
                }

                // Add image overlay with hook
                $slide_title = get_the_title();
                $slide_content = get_the_content();
                $img_overlay = '<div class="owl-carousel-item-imgoverlay">';
                $img_overlay .= '<div class="owl-carousel-item-imgtitle">' . $slide_title . '</div>';
                $img_overlay .= '<div class="owl-carousel-item-imgcontent">' . wpautop($slide_content) . '</div>';
                $img_overlay .= '</div>';
                $result .= apply_filters('owlcarousel_img_overlay', $img_overlay, $slide_title, $slide_content, $meta_link);

                $result .= '</div>';
            } else {
                $result .= '<div class="owl-carousel-item-text">' . get_the_content() . '</div>';
            }
            $result .= '</div>';
        }
        $result .= '</div>';

        /* Restore original Post Data */
        wp_reset_postdata();

        return $result;
    }
    
    /**
     * Owl Carousel for Wordpress image gallery
     * @param string $output Gallery output
     * @param array $attr Parameters
     * @return string Owl HTML code
     */
    function handle_gallery($output, $attr) {
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
        if ('RAND' == $order)
            $orderby = 'none';

        if (!empty($include)) {
            $include = preg_replace('/[^0-9,]+/', '', $include);
            $_attachments = get_posts(array('include' => $include, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => $order, 'orderby' => $orderby));

            $attachments = array();
            foreach ($_attachments as $key => $val) {
                $attachments[$val->ID] = $_attachments[$key];
            }
        }

        if (empty($attachments))
            return '';


        // Add item number if not defined
        if (!isset($attr['items'])) {
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
            if (!empty($meta_link)) {
                $output .= "<a href=\"" . $meta_link . "\">";
            }
            $output .= "<img src=\"{$img[0]}\" width=\"{$img[1]}\" height=\"{$img[2]}\" alt=\"$title\" />\n";
            if (!empty($meta_link)) {
                $output .= "</a>";
            }
            $output .= "</div>";
        }

        $output .= "</div>";

        return $output;
    }
    
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

function owlc() {
	return OwlCarousel::instance();
}

owlc();
