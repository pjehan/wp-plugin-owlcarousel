<?php

/**
 * Class Widget_Better_Starter_Widget
 */
class Widget_Main_Menus extends WP_Widget {

	/**
	 * Basic Widget Settings
	 */
	var $widget_name = "Multisite menus";
	var $widget_slug = "multisite-menus";
	var $widget_desc = "Display a menu from a sub-site";
	var $fields;
	var $site_menus;

	/**
	 * Construct the widget
	 */
	function __construct() {

		//We're going to use $this->textdomain as both the translation domain and the widget class name and ID
		$this->widget_slug = strtolower( get_class( $this ) );

		//Add fields
		$this->add_field( 'title', 'Enter title', '' );
		$this->add_select( 'site', 'Site', $this->get_subsites() );

		//Init the widget
		parent::__construct(
			$this->widget_slug,
			__( $this->widget_name, HRS\Widgets::TEXT_DOMAIN ),
			array(
				'description' => __( $this->widget_desc, HRS\Widgets::TEXT_DOMAIN ),
				'classname' => $this->widget_slug
			)
		);

		// @todo possible solution for the customizer
		// add_action( 'wp_ajax_dpe_fp_get_terms', array( &$this, 'terms_checklist' ) );
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
		$this->widget_output( $args, $instance );

		/* After widget */
		echo $args['after_widget'];
	}


	/**
	 * This function will execute the widget frontend logic.
	 * Everything you want in the widget should be output here.
	 */
	private function widget_output( $args, $instance ) {
		extract( $instance );

		$this->get_menu_from_blog( $site, $menu );
	}


	/**
	 * Widget backend
	 *
	 * @param array $instance
	 * @return string|void
	 */
	public function form( $instance ) {

		$this->get_menus( $instance['site'] );

		// Generate admin for fields
		foreach( $this->fields as $field_name => $field_data ) {
			if( $field_data['type'] === 'text' ) : ?>
				<p>
					<label for="<?php echo $this->get_field_id( $field_name ); ?>"><?php _e( $field_data['description'], HRS\Widgets::TEXT_DOMAIN ); ?></label>
					<input class="widefat" id="<?php echo $this->get_field_id( $field_name ); ?>" name="<?php echo $this->get_field_name( $field_name ); ?>" type="text" value="<?php echo esc_attr( isset( $instance[$field_name] ) ? $instance[$field_name] : $field_data['default_value'] ); ?>" />
				</p>
			<?php
			elseif( $field_data['type'] == 'select' ) : ?>
				<p>
					<select name="<?php echo $this->get_field_name( $field_name ); ?>" id="subsites">
					<?php foreach( $field_data['options'] as $key => $value ) : ?>
						<option value="<?php echo $key; ?>" <?php selected( $instance['site'], $key ); ?>><?php echo $value; ?></option>
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
			else:
				echo __( 'Error - Field type not supported', HRS\Widgets::TEXT_DOMAIN ) . ': ' . $field_data['type'];
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
	private function add_field( $field_name, $field_description = '', $field_default_value = '', $field_type = 'text' ) {
		if( ! is_array( $this->fields ) )
			$this->fields = array();

		$this->fields[$field_name] = array( 'name' => $field_name, 'description' => $field_description, 'default_value' => $field_default_value, 'type' => $field_type );
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

	public function get_menus( $blog_id ){

		switch_to_blog( $blog_id );

		$this->add_select( 'menu', 'Menu', wp_get_nav_menus(), 'menu' );

		restore_current_blog();
	}

	/**
	 * Get all subsites
	 *
	 * @access public
	 * @return void
	 */
	public function get_subsites() {
		$sites = wp_get_sites();
		$sites_new = array();

		foreach( $sites as $site ) {
			$blog_details = get_blog_details( $site['blog_id'] );

			if( ! $blog_details )
				continue;

			$sites_new[$site['blog_id']] = $blog_details->blogname;
		}

		return $sites_new;
	}


	/**
	 * get_menu_from_blog function.
	 *
	 * @access public
	 * @param mixed $blog_id
	 * @param mixed $menu_id
	 * @return void
	 */
	public function get_menu_from_blog( $blog_id, $menu_id ) {

		switch_to_blog( $blog_id );

		wp_nav_menu( array(
			'menu' => $menu_id,
			'items_wrap' => '<ul id="%1$s" class="side-nav %2$s">%3$s</ul>',
			'depth' => 1
		) );

		restore_current_blog();
	}

	/**
	 * Updating widget by replacing the old instance with new
	 *
	 * @param array $new_instance
	 * @param array $old_instance
	 * @return array
	 */
	public function update( $new_instance, $old_instance ) {

		$this->get_menus( $new_instance['site'] );

		return $new_instance;
	}
}
