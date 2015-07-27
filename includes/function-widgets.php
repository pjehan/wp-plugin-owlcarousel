<?php
/**
 * Widget Functions
 *
 * Widget related functions and widget registration
 */

namespace Owl;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Include widget classes
include_once( 'widgets/class-widget.php' );


/**
 * Register Widgets
 */
function register_widgets() {
	\register_widget( 'Owl\Owl_Widget' );
}

add_action( 'widgets_init', 'Owl\register_widgets' );
