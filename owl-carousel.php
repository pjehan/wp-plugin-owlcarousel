<?php
/*
Plugin Name: Owl Carousel
Description: A simple plugin to include an Owl Carousel in any post
Author: Pierre Jehan
Contributer: Rasmus Taarnby
Version: 1.0.2
Text Domain: owl-carousel
Domain Path: /languages
Author URI: http://www.pierre-jehan.com
Licence: GPL2
*/

namespace Owl;

/**
 * Do not access this file directly
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Main class
 */
class Main {

	/**
	 * Text domain for translators
	 */
	const TEXT_DOMAIN = 'owl-carousel';

	/**
	 * @var string Filename of this class.
	 */
	public $file;

	/**
	 * @var string Basename of this class.
	 */
	public $basename;

	/**
	 * @var string Plugins directory for this plugin.
	 */
	public $plugin_dir;

	/**
	 * @var string Plugins url for this plugin.
	 */
	public $plugin_url;

	/**
	 * @var string Lang dir for this plugin.
	 */
	public $lang_dir;

	/**
	 * @var object Instance of this class.
	 */
	private static $instance = null;

	/**
	 * Returns the instance of this class.
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}


	/**
	 * @access public
	 * @return WooCommerce
	 */
	public function __construct() {

		// Setup variables and lanuage support
		$this->setup();

		// Include required files
		$this->includes();
		$this->init_hooks();

		add_action( 'admin_menu', array( $this, 'submenu_page' ) );
		add_action( 'wp_enqueue_scripts',  array( $this, 'enqueue_v1' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue' ) );
	}


	/**
	 * General setup.
	 */
	private function setup() {
		$this->file       = __FILE__;
		$this->basename   = plugin_basename( $this->file );
		$this->plugin_url = plugin_dir_url( $this->file );
		$this->plugin_dir = plugin_dir_path( $this->file );
		$this->rel_dir    = dirname( $this->basename );
		$this->lang_dir   = $this->rel_dir . '/languages';

		load_plugin_textdomain( $this::TEXT_DOMAIN, false, $this->lang_dir );
	}


	/**
	 * Include classes
	 */
	public function includes() {
		include_once( 'includes/function-shortcode.php' );
		include_once( 'includes/class-widget.php' );
		include_once 'includes/tinymce.php';
	}


	public function init_hooks() {
		add_action( 'init', array( $this, 'init' ) );
		add_action( 'widgets_init', array( $this, 'register_widgets' ) );

		add_action( 'manage_edit-owl-carousel_columns', array( $this, 'owl_columnfilter' ) );
		add_action( 'manage_posts_custom_column', array( $this, 'owl_column' ) );

		// Add functions to create a new attachments fields
		add_filter( "attachment_fields_to_edit", array( $this, 'owl_carousel_attachment_fields_to_edit' ), null, 2 );
		add_filter( "attachment_fields_to_save", array( $this, 'owl_carousel_attachment_fields_to_save' ), null, 2 );
	}


	public function submenu_page() {
		add_submenu_page( 'edit.php?post_type=owl-carousel', __( 'Parameters', 'owl-carousel-domain' ), __( 'Parameters', 'owl-carousel-domain' ), 'manage_options', 'owl-carousel-parameters', array( $this, 'submenu_parameters' ) );
	}


	function submenu_parameters() {

		$isWordpressGallery = ( filter_var( get_option( 'owl_carousel_wordpress_gallery', false ), FILTER_VALIDATE_BOOLEAN ) ) ? 'checked' : '';
		$orderBy = get_option( 'owl_carousel_orderby', 'post_date' );
		$orderByOptions = array( 'post_date', 'title' );

		echo '<div class="wrap owl_carousel_page">';

		echo '<?php update_option("owl_carousel_wordpress_gallery", $_POST["wordpress_gallery"]); ?>';

		echo '<h2>' . __( 'Owl Carousel parameters', 'owl-carousel-domain' ) . '</h2>';

		echo '<form action="' . plugin_dir_url( __FILE__ ) . 'save_parameter.php" method="POST" id="owlcarouselparameterform">';

		echo '<h3>' . __( 'Wordpress Gallery', 'owl-carousel-domain' ) . '</h3>';
		echo '<input type="checkbox" name="wordpress_gallery" ' . $isWordpressGallery . ' />';
		echo '<label>' . __( 'Use Owl Carousel with Wordpress Gallery', 'owl-carousel-domain' ) . '</label>';
		echo '<br />';
		echo '<label>' . __( 'Order Owl Carousel elements by ', 'owl-carousel-domain' ) . '</label>';
		echo '<select name="orderby" />';
		foreach ( $orderByOptions as $option ) {
			echo '<option value="' . $option . '" ' . ( ( $option == $orderBy ) ? 'selected="selected"' : '' ) . '>' . $option . '</option>';
		}
		echo '</select>';
		echo '<br />';
		echo '<br />';
		echo '<input type="submit" class="button-primary owl-carousel-save-parameter-btn" value="' . __( 'Save changes', 'owl-carousel-domain' ) . '" />';
		echo '<span class="spinner"></span>';

		echo '</form>';

		echo '</div>';
	}


	/**
	 * Initilize the plugin
	 */
	public function init() {

		add_theme_support( 'post-thumbnails' );

		$labels = array(
			'name' => __( 'Owl Carousel', $this::TEXT_DOMAIN ),
			'singular_name' => __( 'Carousel Item', $this::TEXT_DOMAIN ),
			'add_new' => __( 'Add New Item', $this::TEXT_DOMAIN ),
			'add_new_item' => __( 'Add New Carousel Item', $this::TEXT_DOMAIN ),
			'edit_item' => __( 'Edit Carousel Item', $this::TEXT_DOMAIN ),
			'new_item' => __( 'Add New Carousel Item', $this::TEXT_DOMAIN ),
			'view_item' => __( 'View Item', $this::TEXT_DOMAIN ),
			'search_items' => __( 'Search Carousel', $this::TEXT_DOMAIN ),
			'not_found' => __( 'No carousel items found', $this::TEXT_DOMAIN ),
			'not_found_in_trash' => __( 'No carousel items found in trash', $this::TEXT_DOMAIN ),
		);

		register_post_type( 'owl-carousel', array(
				'public' => true,
				'publicly_queryable' => false,
				'exclude_from_search' => true,
				'label' => 'Owl Carousel',
				'menu_icon' => \plugins_url( '/assets/images/owl-logo-16.png', __FILE__ ),
				'labels' => $labels,
				'capability_type' => 'post',
				'supports' => array(
					'title',
					'editor',
					'thumbnail'
				)
			) );

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

		add_image_size( 'owl_widget', 180, 100, true );
		add_image_size( 'owl_function', 600, 280, true );
		add_image_size( 'owl-full-width', 1200, 675, false ); // 16/9 full-width

		// Add Wordpress Gallery option
		/*
		add_option( 'owl_carousel_wordpress_gallery', 'off' );
		add_option( 'owl_carousel_orderby', 'post_date' );
*/

	}


	public function register_widgets() {
		register_widget( 'Owl\Owl_Widget' );
	}


	/**
	 * Enqueue frontend scripts and styles
	 */
	public function enqueue_v1() {
		// Vendor
		wp_enqueue_script( 'owl-carousel-js', \plugins_url( 'assets/vendor/owl-carousel-1.3.2/owl-carousel/owl.carousel.min.js', __FILE__ ), array( 'jquery' ) );

		// Compiled
		wp_enqueue_script( 'owl-carousel-js-main', \plugins_url( '/assets/js/scripts.min.js', __FILE__ ) );

		// Vendor
		wp_enqueue_style( 'owl-carousel-style', \plugins_url( 'assets/vendor/owl-carousel-1.3.2/owl-carousel/owl.carousel.css', __FILE__ ) );
		wp_enqueue_style( 'owl-carousel-style-theme', \plugins_url( 'assets/vendor/owl-carousel-1.3.2/owl-carousel/owl.theme.css', __FILE__ ) );
		wp_enqueue_style( 'owl-carousel-style-transitions', \plugins_url( 'assets/vendor/owl-carousel-1.3.2/owl-carousel/owl.transitions.css', __FILE__ ) );

		// Compiled
		wp_enqueue_style( 'owl-carousel-style-main', \plugins_url( '/assets/css/main.min.css', __FILE__ ) );
	}


	public function enqueue_v2() {
		// Vendor
		wp_enqueue_script( 'owl-carousel-js', \plugins_url( 'assets/vendor/owl-carousel-2.0.0-beta.2.4.4/owl.carousel.min.js', __FILE__ ), array( 'jquery' ) );

		// Compiled
		wp_enqueue_script( 'owl-carousel-js-script', \plugins_url( '/assets/js/scripts.min.js', __FILE__ ) );

		// Vendor
		wp_enqueue_style( 'owl-carousel-style', \plugins_url( 'assets/vendor/owl-carousel-2.0.0-beta.2.4.4/assets/owl.carousel.css', __FILE__ ) );

		// Compiled
		wp_enqueue_style( 'owl-carousel-style-main', \plugins_url( '/assets/css/main.min.css', __FILE__ ) );
	}


	/**
	 * Enqueue admin scripts and styles
	 */
	public function admin_enqueue() {
		wp_enqueue_style( 'owl-carousel-admin-style', \plugins_url( 'assets/css/admin-styles.css', __FILE__ ) );
		wp_enqueue_script( 'owl-carousel-admin-js', \plugins_url( 'assets/js/admin-script.js' , __FILE__ ), array( 'jquery' ), time(), true );
	}


	/**
	 * Add custom column filters in administration
	 * @param array $columns
	 */
	public function owl_columnfilter( $columns ) {
		$thumb = array( 'thumbnail' => 'Image' );
		$columns = array_slice( $columns, 0, 2 ) + $thumb + array_slice( $columns, 2, null );

		return $columns;
	}


	/**
	 * Add custom column contents in administration
	 * @param string $columnName
	 */
	public function owl_column( $columnName ) {
		global $post;
		if ( $columnName == 'thumbnail' ) {
			echo edit_post_link( get_the_post_thumbnail( $post->ID, 'thumbnail' ), null, null, $post->ID );
		}
	}


	/**
	 * Adding our images custom fields to the $form_fields array
	 * @param array $form_fields
	 * @param object $post
	 * @return array
	 */
	public function owl_carousel_attachment_fields_to_edit( $form_fields, $post ) {
		// add our custom field to the $form_fields array
		// input type="text" name/id="attachments[$attachment->ID][custom1]"
		$form_fields["owlurl"] = array(
			"label" => __( "Owl Carousel URL" ),
			"input" => "text",
			"value" => get_post_meta( $post->ID, "_owlurl", true )
		);

		return $form_fields;
	}


	/**
	 * Save images custom fields
	 * @param array $post
	 * @param array $attachment
	 * @return array
	 */
	public function owl_carousel_attachment_fields_to_save( $post, $attachment ) {
		if ( isset( $attachment['owlurl'] ) ) {
			update_post_meta( $post['ID'], '_owlurl', $attachment['owlurl'] );
		}

		return $post;
	}


} // end class

/**
 * Returns the main instance
 */
function main() {
	return Main::instance();;
}


main();

