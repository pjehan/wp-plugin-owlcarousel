<?php
/*
Plugin Name: Owl Carousel
Description: A simple plugin to include an Owl Carousel in any post
Author:  Peytz & Co (Rasmus Taarnby, Kristoffe Biglete)
Contributors: Rasmus Taarnby
<<<<<<< HEAD
Version: 2.3.6
=======
Version: 2.0.2
>>>>>>> fd4c4b072b91024fad7bc501713c2205d0ee5360
Text Domain: owl-carousel
Domain Path: /languages
Author URI: http://peytz.dk/medarbejdere/
Licence: GPL2
*/

// Do not access this file directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Check version as we use version specific methods
if ( version_compare( '5.4.0', PHP_VERSION, '<=' ) ) {
	require( plugin_dir_path( __FILE__ ) . '/init.php' );
} else {
	// Plugin is activated but nothing is loaded
	/**
	 * Output PHP error message.
	 */
	function owl_php_error_admin_notice() {
		echo '<div class="error">';
			echo '<p>Unfortunately, <a href="' . admin_url( 'plugins.php#owl-carousel' ) . '">Owl Carousel</a> can not run on PHP versions older than 5.4.0 Read more information about <a href="http://www.wpupdatephp.com/update/" target="_blank">how you can update</a>.</p>';
		echo '</div>';
	}
	add_action( 'admin_notices', 'owl_php_error_admin_notice' );
}
