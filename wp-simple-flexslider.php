<?php
/*
 Plugin Name: WP Simple Flexslider
 Plugin URI: http://fikrirasy.id/project/wp-simple-flexslider/
 Description: Simple plugin to create Flexslider based slideshow
 Author: Fikri Rasyid
 Version: 0.1
 Author URI: http://fikrirasy.id
*/

/**
 * Constants
 */
if (!defined('WP_SIMPLE_FLEXSLIDER_DIR'))
    define('WP_SIMPLE_FLEXSLIDER_DIR', plugin_dir_path( __FILE__ ));


if (!defined('WP_SIMPLE_FLEXSLIDER_URL'))
    define('WP_SIMPLE_FLEXSLIDER_URL', plugin_dir_url( __FILE__ ));	 

/**
 * Requiring external files
 */
require_once( 'includes/class-wp-simple-flexslider-editor.php' );
// require_once( 'includes/class-wp-simple-flexslider-frontend.php' );

/**
 * Setup plugin
 */
class WP_Simple_Flexslider_Setup{
	function __construct(){
		register_activation_hook( __FILE__, 	array( $this, 'activation' ) );
		register_deactivation_hook( __FILE__, 	array( $this, 'deactivation' ) );
		add_action( 'init', 					array( $this, 'register_post_type' ) );
	}

	/**
	 * Activation task. Do this when the plugin is activated
	 * 
	 * @access public 
	 * 
	 * @return void
	 */
	public function activation(){
		// Registering post type here so it can be flushed right away
		$this->register_post_type();

		flush_rewrite_rules();		
	}

	/**
	 * Deactivation task. Do this when the plugin is deactivated
	 * 
	 * @return void
	 */
	public function deactivation(){
	}

	/**
	 * Adding CPT for wp simple flexslider
	 * 
	 * @access public
	 * 
	 * @return void
	 */
	public function register_post_type(){
		
		/* Set up the arguments for the post type. */
		$args = array(

			'description'         => __( 'Simple Flexslider plugin', 'wp-simple-flexslider' ), // string
			'public'              => false, // bool (default is FALSE)
			'publicly_queryable'  => false, // bool (defaults to 'public').
			'exclude_from_search' => true, // bool (defaults to FALSE - the default of 'internal')
			'show_in_nav_menus'   => false, // bool (defaults to 'public')
			'show_ui'             => true, // bool (defaults to 'public')
			'show_in_menu'        => true, // bool (defaults to 'show_ui')
			'show_in_admin_bar'   => true, // bool (defaults to 'show_in_menu')
			'menu_position'       => 22, // int (defaults to 25 - below comments)
			'menu_icon'           => 'dashicons-images-alt2', // string (defaults to use the post icon)
			'can_export'          => true, // bool (defaults to TRUE)
			'delete_with_user'    => false, // bool (defaults to TRUE if the post type supports 'author')
			'hierarchical'        => false, // bool (defaults to FALSE)
			'has_archive'         => false, // bool|string (defaults to FALSE)
			'query_var'           => false, // bool|string (defaults to TRUE - post type name)
			'capability_type'     => 'post', // string|array (defaults to 'post')
			'rewrite' => array(
				'slug'       => 'wp_simple_flexslider', // string (defaults to the post type name)
				'with_front' => false, // bool (defaults to TRUE)
				'pages'      => false, // bool (defaults to TRUE)
				'feeds'      => false, // bool (defaults to the 'has_archive' argument)
				'ep_mask'    => EP_PERMALINK, // const (defaults to EP_PERMALINK)
			),
			'supports' => array(
				'title'
			),
			'labels' => array(
				'name'               => __( 'Slideshows',                   'wp-simple-flexslider' ),
				'singular_name'      => __( 'Slideshow',                    'wp-simple-flexslider' ),
				'menu_name'          => __( 'Slideshows',                   'wp-simple-flexslider' ),
				'name_admin_bar'     => __( 'Slideshows',                   'wp-simple-flexslider' ),
				'add_new'            => __( 'Add New',                    'wp-simple-flexslider' ),
				'add_new_item'       => __( 'Add New Slideshow',            'wp-simple-flexslider' ),
				'edit_item'          => __( 'Edit Slideshow',               'wp-simple-flexslider' ),
				'new_item'           => __( 'New Slideshow',                'wp-simple-flexslider' ),
				'view_item'          => __( 'View Slideshow',               'wp-simple-flexslider' ),
				'search_items'       => __( 'Search Slideshows',            'wp-simple-flexslider' ),
				'not_found'          => __( 'No flexsliders found',          'wp-simple-flexslider' ),
				'not_found_in_trash' => __( 'No flexsliders found in trash', 'wp-simple-flexslider' ),
				'all_items'          => __( 'All Slideshows',               'wp-simple-flexslider' ),
				'parent_item'        => __( 'Parent Slideshow',             'wp-simple-flexslider' ),
				'parent_item_colon'  => __( 'Parent Slideshow:',            'wp-simple-flexslider' ),
				'archive_title'      => __( 'Slideshows',                   'wp-simple-flexslider' ),
			)
		);

		/* Register the post type. */
		register_post_type(
			'wp_simple_flexslider', // Post type name. Max of 20 characters. Uppercase and spaces not allowed.
			apply_filters( 'wp_simple_flexslider_post_type_args', $args )      // Arguments for post type.
		);
	}
}
new WP_Simple_Flexslider_Setup;