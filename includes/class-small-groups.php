<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * SMALL_GRPS class.
 */
class SMALL_GRPS {

	// Refers to a single instance of this class.
    private static $instance = null;

	
    /**
	 * __construct function.
	 *
	 * @access public
	 * @return void
	 */
	public function __construct() {

		add_action( 'init', array( $this, 'register_small_group_post_type' ) );
	}        

	public function register_small_group_post_type() {	 
	
                
		register_post_type('small_groups', 
							  array(	'label' => 'Small Groups',
								'description' => 'Post Type for Small Group Members.',
								'public' => true,   // true to see on the front of site the small_groups post type needs access permissions (currently proviced by 'limit post types' plugin)
								'show_ui' => true,
								'show_in_menu' => true,
								'capability_type' => 'small_group',
								'hierarchical' => false,
								'rewrite' => array('slug' => 'small-groups'),
								'query_var' => true,
								'has_archive' => true,
								//'supports' => array('title','editor','revisions','author', 'thumbnail'),
								'supports' => array('title','editor','revisions'),
								//'taxonomies' => array('preacher','subject'),
								'menu_icon'			  => apply_filters( 'sg_dashicon', 'dashicons-groups' ),
								'labels' => array ('name' => 'Small Groups',
													'singular_name' => 'Group',
													'menu_name' => 'Small Groups',
													'add_new' => 'Add Group',
													'add_new_item' => 'Add New Group',
													'edit' => 'Edit',
													'edit_item' => 'Edit Group',
													'new_item' => 'New Group',
													'view' => 'View Group',
													'view_item' => 'View Group',
													'search_items' => 'Search Groups',
													'not_found' => 'No Groups Found',
													'not_found_in_trash' => 'No Group Found in Trash',
													'parent' => 'Parent Group',
													)
							  ) 
		);
	}

	
			
	/**
     * Creates or returns an instance of this class.
     *
     * @return   A single instance of this class.
     */
    public static function get_instance() {
 
        if ( null == self::$instance ) {
            self::$instance = new self;
        }
 
        return self::$instance;
 
    }		
}

/**
 * Init class
 */
 
SMALL_GRPS::get_instance();


/* 
 * 
 */
function users_to_small_groups_connection_types() {
    p2p_register_connection_type( array(
        'name' => 'small_groups_to_user',
        'from' => 'user',
        'to' => 'small_groups',
		'admin_column' => 'any',
       // 'admin_dropdown' => 'any',
        //'sortable' => true,
    ) );
}
add_action( 'p2p_init', 'users_to_small_groups_connection_types' );


?>