<?php

namespace Owl;

/**
 * Class Owl Settings
 */
class Admin {

	/**
	 * Constructor
	 */
	public function __construct() {

		$this->plugin = Main::instance();

		// Register the menu page
		add_action( 'admin_menu', [ $this, 'owl_submenu_page' ] );
		add_action( 'admin_init', [ $this, 'display_settings_fields' ] );

		// Scripts
		add_action( 'wp_enqueue_scripts',  [ $this, 'enqueue_v2' ] );
		add_action( 'admin_enqueue_scripts', [ $this, 'admin_enqueue' ] );
	}


	/**
	 * Add a submenu page in the admin
	 * @return void
	 */
	public function owl_submenu_page() {
		add_submenu_page(
			'edit.php?post_type=owl-carousel',
			__( 'Settings', Main::TEXT_DOMAIN ),
			__( 'Settings', Main::TEXT_DOMAIN ),
			'manage_options',
			'owl-carousel-settings',
			[ $this, 'owl_settings_page' ]
		);
	}

	/**
	 * Create the form
	 */
	 function owl_settings_page() {
     	?>
	 	<div class="wrap">

			<h1><?php _e( 'Owl Carousel Parameters', Main::TEXT_DOMAIN ) ?></h1>

			<form method="post" action="options.php">

				<?php
				settings_fields( 'gallery-section' );
				do_settings_sections( 'gallery-options' );
				submit_button();
				?>

			</form>

		</div>
    	<?php
	}

	/**
	 * Register the settings
	 */
	function display_settings_fields() {

		// Add section
		add_settings_section( 'gallery-section', __( 'Gallery', Main::TEXT_DOMAIN ), null, 'gallery-options' );

		// Setting owl_carousel_wordpress_gallery
		add_settings_field( 'owl_carousel_wordpress_gallery', __( 'Use Owl Carousel with Wordpress Gallery:', Main::TEXT_DOMAIN ), [ $this, 'display_wp_gallery_checkbox' ], 'gallery-options', 'gallery-section' );
		register_setting( 'gallery-section', 'owl_carousel_wordpress_gallery' );

		// Setting owl_carousel_orderby
		add_settings_field( 'owl_carousel_orderby', __( 'Order Owl Carousel elements by:', Main::TEXT_DOMAIN ), [ $this, 'display_gallery_order_select' ], 'gallery-options', 'gallery-section' );
		register_setting( 'gallery-section', 'owl_carousel_orderby' );
	}

	/**
	 * Callbacks for the settings fields
	 */
	function display_wp_gallery_checkbox() {
    	?>
        <input type="checkbox" name="owl_carousel_wordpress_gallery" value="1" <?php checked( 1, get_option( 'owl_carousel_wordpress_gallery' ), true); ?> />
		<?php
	}

	function display_gallery_order_select() {

		$orderby_options = array( 'post_date', 'title' );
		$orderby_value = get_option( 'owl_carousel_orderby', 'post_date' );
    	?>

		<select name="owl_carousel_orderby" />
			<?php foreach ( $orderby_options as $option ) : ?>
				<option value="<?php echo $option; ?>" <?php echo ( ( $option == $orderby_value ) ? 'selected="selected"' : '' ); ?>><?php echo $option; ?></option>
			<?php endforeach; ?>
		<?php
	}


	/**
	 * Enqueue frontend scripts and styles
	 */
	public function enqueue_v2() {
		// Vendor
		wp_enqueue_script( 'owl-carousel-js', $this->plugin->plugin_url . 'assets/vendor/owl-carousel-2.0.0-beta.2.4.4/owl.carousel.min.js', array( 'jquery' ) );

		// Compiled
		wp_enqueue_script( 'owl-carousel-js-script', $this->plugin->plugin_url . '/assets/js/scripts.min.js' );

		// Vendor
		wp_enqueue_style( 'owl-carousel-style', $this->plugin->plugin_url . 'assets/vendor/owl-carousel-2.0.0-beta.2.4.4/assets/owl.carousel.css' );

		// Compiled
		wp_enqueue_style( 'owl-carousel-style-main', $this->plugin->plugin_url . '/assets/css/main.min.css' );
	}


	/**
	 * Enqueue admin scripts and styles
	 */
	public function admin_enqueue() {
		wp_enqueue_style( 'owl-carousel-admin-style', $this->plugin->plugin_url . 'assets/css/admin-styles.css' );
		wp_enqueue_script( 'owl-carousel-admin-js', $this->plugin->plugin_url . 'assets/js/admin-script.js', array( 'jquery' ), time(), true );
	}
}
