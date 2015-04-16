<?php

namespace Owl;

/**
 * Do not access this file directly
 */
if ( !defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Main class
 */
class Widget_Setup {

	public $widget_class  = '';

	/**
	 * @access public
	 * @return WooCommerce
	 */
	public function __construct( $widget_class = '' ) {

		// Setup variables and lanuage support
		$this->widget_class  = $widget_class;

		// Include required files
		$this->includes();

		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue' ) );

		if( ! empty( $this->widget_class ) ) {
			add_action( 'widgets_init', array( $this, 'register_widget' ) );
		}

	}

	/**
	 * Include classes
	 */
/*
	public function includes(){
		include_once( 'includes/class-widget-main-menus.php' );
	}
*/

	public function register_widget() {

        register_widget( $this->widget_class );

        return true;
    }

/*
	public function register_widgets() {
		register_widget( 'HRS\Widget_Main_Menus' );
	}
*/

	/**
	 * Enqueue scripts and styles
	 */
	public function admin_enqueue() {
		wp_enqueue_script( 'owl-widgets-scripts', plugins_url( 'assets/js/admin-scripts.js' , __FILE__ ), array( 'jquery' ), time(), true );
	}

} // end class

/**
 * Returns the main instance
 */
/*
function widgets_init() {
	new Widgets();
}
*/

// add_action( 'widgets_init', 'HRS\widgets_init' );