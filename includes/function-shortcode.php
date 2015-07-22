<?php

/**
 * Plugin main function
 * @param type $atts Owl parameters
 * @param type $content
 * @return string Owl HTML code
 */
function owl_function( $atts, $content = null ) {
	extract( shortcode_atts( array(
				'category' => 'Uncategorized',
				'size' => 'owl-full-width'
			), $atts ) );

	$data_attr = "";

	foreach ( $atts as $key => $value ) {
		if ( $key != "category" ) {
			$data_attr .= ' data-' . $key . '="' . $value . '" ';
		}
	}

	$lazyLoad = array_key_exists( "lazyload", $atts ) && $atts["lazyload"] == true;

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

	$loop = new WP_Query( $args );
	while ( $loop->have_posts() ) {
		$loop->the_post();
		$img_src = wp_get_attachment_image_src( get_post_thumbnail_id( get_the_ID() ), $atts['size'] );

		$meta_link = apply_filters( 'owl_image_link', get_post_meta( get_post_thumbnail_id( get_the_ID() ), '_owlurl', true ) );
		$classes = apply_filters( 'owl_item_classes', array(), get_the_ID() );

		$result .= '<div class="item ' . implode( ' ', $classes ) . '">';

		if ( $img_src[0] ) {
			// $result .= '<div>';

			if ( ! empty( $meta_link ) ) {
				$result .= '<a href="'. $meta_link .'">';
			}

			if ( $lazyLoad ) {
				$result .= '<img class="lazyOwl" title="' . get_the_title() . '" data-src="' . $img_src[0] . '" alt="' . get_the_title() . '"/>';
			} else {
				// $result .= '<img title="' . get_the_title() . '" src="' . $img_src[0] . '" alt="' . get_the_title() . '"/>';
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

			$result .= apply_filters( 'owlcarousel_img_overlay', $img_overlay, $slide_title, $slide_content, $meta_link );

			// $result .= '</div>';
		}
		else {
			$result .= '<div class="owl-item-text">' . apply_filters( 'owl_carousel_img_overlay_content', get_the_content() ) . '</div>';
		}
		$result .= '</div>';
	}
	$result .= '</div>';

	return $result;
}

add_shortcode( 'owl-carousel', 'owl_function' );


/**
 * Owl Carousel for Wordpress image gallery
 * @param string $output Gallery output
 * @param array $attr Parameters
 * @return string Owl HTML code
 */
function owl_carousel_post_gallery( $output, $attr ) {
	global $post;

	if ( isset( $attr['orderby'] ) ) {
		$attr['orderby'] = sanitize_sql_orderby( $attr['orderby'] );
		if ( !$attr['orderby'] )
			unset( $attr['orderby'] );
	}

	extract( shortcode_atts( array(
				'order' => 'ASC',
				'orderby' => 'menu_order ID',
				'id' => $post->ID,
				'itemtag' => 'dl',
				'icontag' => 'dt',
				'captiontag' => 'dd',
				'columns' => 3,
				'size' => 'thumbnail',
				'include' => '',
				'exclude' => ''
			), $attr ) );

	$id = intval( $id );
	if ( 'RAND' == $order ) $orderby = 'none';

	if ( !empty( $include ) ) {
		$include = preg_replace( '/[^0-9,]+/', '', $include );
		$_attachments = get_posts( array( 'include' => $include, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => $order, 'orderby' => $orderby ) );

		$attachments = array();
		foreach ( $_attachments as $key => $val ) {
			$attachments[$val->ID] = $_attachments[$key];
		}
	}

	if ( empty( $attachments ) ) return '';

	// Add item number if not defined
	if ( !isset( $attr['items'] ) ) {
		$attr['items'] = '1';
	}

	$data_attr = "";
	foreach ( $attr as $key => $value ) {
		if ( $key != "category" ) {
			$data_attr .= ' data-' . $key . '="' . $value . '" ';
		}
	}

	$output .= '<div id="owl-carousel-' . rand() . '" class="owl-carousel-plugin" ' . $data_attr . '>';

	foreach ( $attachments as $id => $attachment ) {
		$img = wp_get_attachment_image_src( $id, 'full' );
		$meta_link = get_post_meta( $id, '_owlurl', true );

		$title = $attachment->post_title;

		$output .= "<div class=\"item\">";
		if ( !empty( $meta_link ) ) {
			$output .= "<a href=\"" . $meta_link . "\">";
		}

		$output .= '<div class="image" style="
			background-image: url(' . $img[0] . ');
			background-size: cover;
			background-position: center;
			background-repeat: no-repeat;
			height:' . $img[2] . 'px;
			padding-top:' . $img[2] / $img[1] * 100 . '%;
		"></div>';

		if ( !empty( $meta_link ) ) {
			$output .= "</a>";
		}
		$output .= "</div>";
	}

	$output .= "</div>";

	return $output;
}

if ( filter_var( get_option( 'owl_carousel_wordpress_gallery', false ), FILTER_VALIDATE_BOOLEAN ) ) {
	add_filter( 'post_gallery', 'owl_carousel_post_gallery', 10, 2 );
}
