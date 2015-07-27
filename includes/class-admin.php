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
		add_action( 'admin_menu', [ $this, 'submenu_page' ] );

		// Scripts
		add_action( 'wp_enqueue_scripts',  [ $this, 'enqueue_v2' ] );
		add_action( 'admin_enqueue_scripts', [ $this, 'admin_enqueue' ] );
	}


	/**
	 * Add a submenu page in the admin
	 * @return void
	 */
	public function submenu_page() {
		add_submenu_page(
			'edit.php?post_type=owl-carousel',
			__( 'Parameters', 'owl-carousel-domain' ),
			__( 'Parameters', 'owl-carousel-domain' ),
			'manage_options',
			'owl-carousel-parameters',
			array( $this, 'submenu_parameters' )
		);
	}

	/**
	 * Add the form and the parameters
	 * @return void
	 */
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
