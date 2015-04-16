<?php

namespace Owl;

// Do not access this file directly
if ( !defined( 'ABSPATH' ) ) {
	exit;
}

function mce_filters(){
	add_filter( "mce_external_plugins", "register_tinymce_plugin" );
	add_filter( 'mce_buttons', 'add_tinymce_button' );
}

function register_tinymce_plugin( $plugin_array ) {
	$plugin_array['owl_button'] = plugins_url( '/owl-carousel/assets/js/tinymce-plugin.js' );
	return $plugin_array;
}

function add_tinymce_button( $buttons ) {
	$buttons[] = "owl_button";
	return $buttons;
}