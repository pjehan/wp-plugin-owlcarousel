<?php

namespace Owl;

/**
 * Class Owl Post Type
 */
class Post_Type {

	/**
	 * Constructor
	 */
	public function __construct() {

		$this->plugin = Main::instance();

		// Initialize post type
		add_action( 'init', [ $this, 'init_post_type' ] );

		// Add admin column
		add_action( 'manage_edit-owl-carousel_columns', [ $this, 'owl_columnfilter' ] );
		add_action( 'manage_posts_custom_column', [ $this, 'owl_column' ] );

		// Add functions to create a new attachments fields
		add_filter( 'attachment_fields_to_edit', [ $this, 'owl_carousel_attachment_fields_to_edit' ], null, 2 );
		add_filter( 'attachment_fields_to_save', [ $this, 'owl_carousel_attachment_fields_to_save' ], null, 2 );
	}

	/**
	 * Initialize post type
	 */
	public function init_post_type() {

		// Register the post type
		add_theme_support( 'post-thumbnails' );

		$labels = array(
			'name'               => __( 'Owl Carousel', Main::TEXT_DOMAIN ),
			'singular_name'      => __( 'Carousel Item', Main::TEXT_DOMAIN ),
			'add_new'            => __( 'Add New Item', Main::TEXT_DOMAIN ),
			'add_new_item'       => __( 'Add New Carousel Item', Main::TEXT_DOMAIN ),
			'edit_item'          => __( 'Edit Carousel Item', Main::TEXT_DOMAIN ),
			'new_item'           => __( 'Add New Carousel Item', Main::TEXT_DOMAIN ),
			'view_item'          => __( 'View Item', Main::TEXT_DOMAIN ),
			'search_items'       => __( 'Search Carousel', Main::TEXT_DOMAIN ),
			'not_found'          => __( 'No carousel items found', Main::TEXT_DOMAIN ),
			'not_found_in_trash' => __( 'No carousel items found in trash', Main::TEXT_DOMAIN ),
		);

		register_post_type(
			'owl-carousel',
			array(
				'public'              => true,
				'publicly_queryable'  => false,
				'exclude_from_search' => true,
				'label'               => 'Owl Carousel',
				'menu_icon'           => $this->plugin->plugin_url . 'assets/images/owl-logo-16.png',
				'labels'              => $labels,
				'capability_type'     => 'post',
				'supports' => array(
					'title',
					'editor',
					'thumbnail'
				)
			)
		);

		// Register a taxonomy
		register_taxonomy(
			'Carousel',
			'owl-carousel',
			array(
				'label' => __( 'Carousel', Main::TEXT_DOMAIN ),
				'rewrite' => array( 'slug' => 'carousel' ),
				'hierarchical' => true,
				'show_admin_column' => true,
			)
		);

		// Images sizes
		add_image_size( 'owl_widget', 180, 100, true );
		add_image_size( 'owl_function', 600, 280, true );
		add_image_size( 'owl-full-width', 1200, 675, false ); // 16/9 full-width
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
		$form_fields['owlurl'] = array(
			'label' => __( 'Owl Carousel URL', Main::TEXT_DOMAIN ),
			'input' => 'text',
			'value' => get_post_meta( $post->ID, '_owlurl', true )
		);

		$form_fields['owlvideo'] = array(
			'label' => __( 'Owl Carousel Video URL' ),
			'input' => 'text',
			'value' => get_post_meta( $post->ID, '_owlvideo', true )
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

		if ( isset( $attachment['owlvideo'] ) ) {
			update_post_meta( $post['ID'], '_owlvideo', $attachment['owlvideo'] );
		}

		return $post;
	}
}
