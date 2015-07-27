<?php
/*
Plugin Name: Owl Carousel
Description: A simple plugin to include an Owl Carousel in any post
Author: Pierre Jehan
Contributer: Rasmus Taarnby
Version: 1.0.4
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
	 * @var object Instance of this class.
	 */
	private static $instance;

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
	 * @var array Objects of this class.
	 */
	public $objects = array();

	/**
	 * Do not load this more than once.
	 */
	private function __construct() {}

	/**
	 * Returns the instance of this class.
	 */
	public static function instance() {
		if( ! isset( self::$instance ) ) {
			self::$instance = new self;
			self::$instance->setup();
			self::$instance->includes();
			self::$instance->run();
		}

		return self::$instance;
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
	 * Include files.
	 */
	private function includes() {
		include_once( 'includes/class-admin.php' );
		include_once( 'includes/class-post-type.php' );
		include_once( 'includes/function-shortcode.php' );
		include_once( 'includes/function-gallery.php' );
		include_once( 'includes/function-widgets.php' );
		include_once( 'includes/tinymce.php' );
	}

	/**
	 * Run classes.
	 */
	public function run() {
		$admin = new Admin();
		$post_type = new Post_Type();
	}

} // end class

/**
 * Returns the main instance
 */
function main() {
	return Main::instance();;
}

main();
