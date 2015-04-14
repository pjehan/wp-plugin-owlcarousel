<?php
/*
Plugin Name: Owl Carousel
Description: A simple plugin to include an Owl Carousel in any post
Author: Pierre Jehan
Contributer: Rasmus Taarnby
Version: 0.5.2
Text Domain: owl-carousel
Domain Path: /languages
Author URI: http://www.pierre-jehan.com
Licence: GPL2
*/

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
	 * @access public
	 * @return WooCommerce
	 */
	public function __construct() {

		// Setup variables and lanuage support
		$this->setup();

		// Include required files
		$this->includes();

		$this->register_widgets();

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
	public function includes(){
		include_once( 'includes/class-widget-main-menus.php' );
	}

	public function register_widgets() {
		register_widget( 'Widget_Main_Menus' );
	}

	/**
	 * Enqueue scripts and styles
	 */
	public function admin_enqueue() {
		wp_enqueue_script( 'widgets-scripts', plugins_url( 'assets/js/admin-scripts.js' , __FILE__ ), array( 'jquery' ), time(), true );
	}

} // end class

/**
 * Returns the main instance of Hjr_WC
 */
function main_init() {
	new Main();
}

add_action( 'plugins_loaded', 'Owl\Main' );