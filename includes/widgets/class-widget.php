<?php

namespace Owl;

/**
 * Class Owl_Widget
 */
class Owl_Widget extends \WP_Widget {

	/**
	 * Basic Widget Settings
	 */
	var $widget_name = 'Owl Carousel';
	var $widget_id = 'owl_widget';
	var $widget_desc = 'A Owl Carousel Widget';
	var $fields;
	var $site_menus;

	/**
	 * Construct the widget
	 */
	public function __construct() {

		$options = array( 'step' => 1, 'min' => 1, 'max' => '', 'std' => 1 );

		// Add fields
		$this->add_field( 'title', 'Enter title' );
		$this->add_field( 'category', 'Enter category', '', '', 'select-cat' );
		$this->add_field( 'items', 'Enter number of items', '', $options, 'number' );

		// Init the widget
		parent::__construct(
			$this->widget_id,
			__( $this->widget_name, Main::TEXT_DOMAIN ),
			array(
				'description' => __( $this->widget_desc, Main::TEXT_DOMAIN ),
				'classname' => $this->widget_id
			)
		);
	}


	/**
	 * Widget frontend
	 *
	 * @param array $args
	 * @param array $instance
	 */
	public function widget( $args, $instance ) {
		$title = apply_filters( 'widget_title', $instance['title'] );

		/* Before and after widget arguments are usually modified by themes */
		echo $args['before_widget'];

		if( ! empty( $title ) )
			echo $args['before_title'] . $title . $args['after_title'];

			/* Widget output here */
			echo owl_function( array(
				'category' => get_term_by( 'id', $instance['category'], 'Carousel' ),
				'items' => $instance['items']
			) );

		/* After widget */
		echo $args['after_widget'];
	}


	/**
	 * Widget backend
	 *
	 * @param array $instance
	 * @return string|void
	 */
	public function form( $instance ) {

		if ( ! isset( $instance['title'] ) ) {
			$instance['title'] = __( 'Widget Carousel', Main::TEXT_DOMAIN );
		}

		if ( ! isset( $instance['category'] ) ) {
			$instance['category'] = 'Uncategorized';
		}

		// Generate admin for fields
		foreach( $this->fields as $field_name => $field_data ) {

			$value = isset( $instance[ $field_name ] ) ? $instance[ $field_name ] : $field_data['default_value'];

			if( $field_data['type'] === 'text' ) : ?>
				<p>
					<label for="<?php echo $this->get_field_id( $field_name ); ?>"><?php _e( $field_data['description'], Main::TEXT_DOMAIN ); ?></label>
					<input class="widefat" id="<?php echo $this->get_field_id( $field_name ); ?>" name="<?php echo $this->get_field_name( $field_name ); ?>" type="text" value="<?php echo esc_attr( isset( $instance[$field_name] ) ? $instance[$field_name] : $field_data['default_value'] ); ?>" />
				</p>
			<?php
			elseif( $field_data['type'] == 'select' ) : ?>
				<p>
					<select name="<?php echo $this->get_field_name( $field_name ); ?>" id="select-options">
					<?php foreach( $field_data['options'] as $key => $value ) : ?>
						<option value="<?php echo $key; ?>" <?php selected( $instance[$field_name], $key ); ?>><?php echo $value; ?></option>
					<?php endforeach; ?>
					</select>
				</p>
			<?php
			elseif( $field_data['type'] == 'select-cat' ) : ?>
				<?php $terms = get_terms( 'Carousel' ); ?>
				<p>
					<label for="<?php echo $this->get_field_id( $field_name ); ?>"><?php _e( $field_data['description'], Main::TEXT_DOMAIN ); ?></label><br />
					<select name="<?php echo $this->get_field_name( $field_name ); ?>" id="taxonomies">
					<?php foreach( $terms as $key => $value ) : ?>
						<option value="<?php echo $value->term_id; ?>" <?php selected( $instance[$field_name], $value->term_id ); ?>><?php echo $value->name; ?></option>
					<?php endforeach; ?>
					</select>
				</p>
			<?php
			elseif( $field_data['type'] == 'menu' ) : ?>
				<p>
					<select name="<?php echo $this->get_field_name( $field_name ); ?>" id="submenus">
					<?php foreach( $field_data['options'] as $menu ) : ?>
						<?php $menu = (array) $menu; ?>
						<option value="<?php echo $menu['term_id']; ?>" <?php selected( $instance['menu'], $menu['term_id'] ); ?>><?php echo $menu['name']; ?></option>
					<?php endforeach; ?>
					</select>
				</p>
			<?php
			elseif( $field_data['type'] == 'number' ) : ?>
				<p>
					<label for="<?php echo $this->get_field_id( $field_name ); ?>"><?php _e( $field_data['description'], Main::TEXT_DOMAIN ); ?></label>
					<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( $field_name ) ); ?>" name="<?php echo $this->get_field_name( $field_name ); ?>" type="number" step="<?php echo esc_attr( $field_data['options']['step'] ); ?>" min="<?php echo esc_attr( $field_data['options']['min'] ); ?>" max="<?php echo esc_attr( $field_data['options']['max'] ); ?>" value="<?php echo esc_attr( $value ); ?>" />
				</p>
			<?php
			else:
				echo __( 'Error - Field type not supported', Main::TEXT_DOMAIN ) . ': ' . $field_data['type'];
			endif;
		}
	}


	/**
	 * Adds a text field to the widget
	 *
	 * @param $field_name
	 * @param string $field_description
	 * @param string $field_default_value
	 * @param string $field_type
	 * @return void
	 */
	private function add_field( $field_name, $field_description = '', $field_default_value = '', $field_options = array(), $field_type = 'text' ) {
		if( ! is_array( $this->fields ) )
			$this->fields = array();

		$this->fields[$field_name] = array( 'name' => $field_name, 'description' => $field_description, 'default_value' => $field_default_value, 'options' => $field_options, 'type' => $field_type );
	}



	/**
	 * Adds a select field to the widget
	 *
	 * @access private
	 * @param mixed $field_name
	 * @param string $field_description (default: '')
	 * @param array $options (default: array())
	 * @param string $field_type (default: 'select')
	 * @return void
	 */
	private function add_select( $field_name, $field_description = '', $options = array(), $field_type = 'select' ) {
		if( ! is_array( $this->fields ) )
			$this->fields = array();

		if( ! empty( $options ) ) {
			$this->fields[$field_name] = array( 'name' => $field_name, 'description' => $field_description, 'options' => $options, 'type' => $field_type );
		}

	}

	/**
	 * Updating widget by replacing the old instance with new
	 *
	 * @param array $new_instance
	 * @param array $old_instance
	 * @return array
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['category'] = strip_tags( $new_instance['category'] );
		$instance['items'] = strip_tags( $new_instance['items'] );

		return $instance;
	}
}
