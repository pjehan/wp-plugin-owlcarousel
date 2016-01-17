<?php

/**
 * Plugin main function
 * @param type $atts Owl parameters
 * @param type $content
 * @return string Owl HTML code
 */

namespace Owl;

if ( ! defined( 'ABSPATH' ) ) {
 	exit;
 }

function owl_function( $atts, $content = null ) {

	// Default attributes
	$default_atts = array(
		'category' => 'Uncategorized',
		'size' => 'owl-full-width',
	);

	// owl_custom_default_atts filter
	$atts = apply_filters( 'owl_custom_default_atts', $atts, $default_atts );

	$data_attr = "";

	foreach ( $atts as $key => $value ) {
		if ( $key != "category" ) {
			$data_attr .= ' data-' . strtolower( $key ) . '="' . $value . '" ';
		}
	}

	$lazyLoad = array_key_exists( "lazyload", $atts ) && $atts["lazyload"] == true;

	// Loop
	$args = array(
		'post_type' => 'owl-carousel',
		'orderby' => get_option( 'owl_carousel_orderby', 'post_date' ),
		'order' => 'asc',
		'tax_query' => array(
			array(
				'taxonomy' => 'Carousel',
				'field' => 'slug',
				'terms' => $atts['category']
			)
		),
		'nopaging' => true
	);

	$result = '<div id="owl-carousel-' . rand() . '" class="owl-carousel-plugin" ' . $data_attr . '>';

	$loop = new \WP_Query( $args );
	while ( $loop->have_posts() ) {

		$loop->the_post();
		$size = isset( $atts['size'] ) ? $atts['size'] : 'full';
		$img_src = wp_get_attachment_image_src( get_post_thumbnail_id( get_the_ID() ), $size );

		// owl_image_link filter
		$meta_link = apply_filters( 'owl_image_link', get_post_meta( get_post_thumbnail_id( get_the_ID() ), '_owlurl', true ) );

		// owl_video_link filter
		$video_link = apply_filters( 'owl_video_link', get_post_meta( get_post_thumbnail_id( get_the_ID() ), '_owlvideo', true ) );

		// owl_item_classes filter
		$classes = apply_filters( 'owl_item_classes', array(), get_the_ID() );

		$result .= '<div class="' . ( ( $video_link ) ? 'item-video' : 'item' ) .' ' . implode( ' ', $classes ) . '">';

		if ( $video_link ) {

			$result .= '<a class="owl-video" href="' . $video_link . '"></a>';

		} elseif ( $img_src[0] ) {

			if ( ! empty( $meta_link ) ) {
				$result .= '<a href="'. $meta_link .'">';
			}

			if ( $lazyLoad ) {
				$result .= '<img class="lazyOwl" title="' . get_the_title() . '" data-src="' . $img_src[0] . '" alt="' . get_the_title() . '"/>';
			} else {

				$result .= '<div class="image" style="
								background-image: url(' . $img_src[0] . ');
								background-size: cover;
								background-position: center;
								background-repeat: no-repeat;
								height:' . $img_src[2] . 'px;
								padding-top:' . $img_src[2] / $img_src[1] * 100 . '%;
							"></div>';
			}

			if ( ! empty( $meta_link ) ) {
				$result .= '</a>';
			}

			// Add image overlay with hook
			$slide_title  = get_the_title();
			$slide_content  = wpautop( get_the_content() );

			$img_overlay  = '<div class="owl-item-overlay">';
			$img_overlay  .= '<div class="owl-item-title">' . apply_filters( 'owl_carousel_img_overlay_title', $slide_title ) . '</div>';
			$img_overlay  .= '<div class="owl-item-content">' . apply_filters( 'owl_carousel_img_overlay_content', $slide_content, get_the_ID() ) . '</div>';
			$img_overlay  .= '</div>';

			// owlcarousel_img_overlay filter
			$result .= apply_filters( 'owlcarousel_img_overlay', $img_overlay, $slide_title, $slide_content, $meta_link );

		}
		else {
			$result .= '<div class="owl-item-text">' . apply_filters( 'owl_carousel_img_overlay_content', get_the_content() ) . '</div>';
		}
		$result .= '</div>';
	}
	$result .= '</div>';

	return $result;
}

add_shortcode( 'owl-carousel', 'Owl\owl_function' );
