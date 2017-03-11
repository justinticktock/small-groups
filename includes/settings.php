<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


// Append new links to the Plugin admin side
add_filter( 'plugin_action_links_' . SMALL_GROUPS::get_instance()->plugin_file , 'SMALL_GROUPS_plugin_action_links');

function SMALL_GROUPS_plugin_action_links( $links ) {

	if ( current_user_can( 'promote_users') ) {
		$SMALL_GROUPS = SMALL_GROUPS::get_instance();
		
		//$settings_link = '<a href="' . admin_url( 'options-general.php' ) . '">' . __( 'Add User Roles', 'small-groups' ) . "</a>";
		$settings_link = '<a href="options-general.php?page=' . $SMALL_GROUPS->menu . '">' . __( 'Settings', 'small-groups' ) . "</a>";
		
		array_push( $links, $settings_link );
	}
	return $links;	
}


	
// add action after the settings save hook.
add_action( 'tabbed_settings_after_update', 'SMALL_GROUPS_after_settings_update' );

function SMALL_GROUPS_after_settings_update( ) {

	flush_rewrite_rules();	
	
}




/**
 * SMALL_GROUPS_Settings class.
 *
 * Main Class which inits the CPTs and plugin
 */
class SMALL_GROUPS_Settings {

	// Refers to a single instance of this class.
    private static $instance = null;
	
	/**
	 * __construct function.
	 *
	 * @access public
	 * @return void
	 */
	private function __construct() {
	}

		
	
	/**
     * Creates or returns an instance of this class.
     *
     * @return   A single instance of this class.
     */
    public static function get_instance() {
		
		$SMALL_GROUPS = SMALL_GROUPS::get_instance();
		
		$config = array(
				'default_tab_key' => 'include_multiple_roles',					// Default settings tab, opened on first settings page open.
				'menu_parent' => 'options-general.php',    								// menu options page slug name.( 'Null' to remove from the menu )
				'menu_access_capability' => 'promote_users',    				// menu options page access required capability
				'menu' => $SMALL_GROUPS->menu,    								// menu options page slug name.
				'menu_title' => $SMALL_GROUPS->menu_title,    					// menu options page slug name.
				'page_title' => __( 'Role Include', 'small-groups' ), 			// $SMALL_GROUPS->page_title,    		// menu options page title.
				);

		$settings = 	apply_filters( 'SMALL_GROUPS_settings', 
										array(
											'small_groups_general' => array(
												'access_capability' => 'promote_users',
												'title' 		=> __( 'General', 'SMALL_GROUPS' ),
												//'description' 	=> __( 'Enable the roles for users.', 'SMALL_GROUPS' ),
												//'form_action'   => admin_url( 'admin-post.php' ),
												'settings' 		=> array(					
																		),			
											),
											'small_groups_plugin_extension' => array(
													'access_capability' => 'install_plugins',
													'title' 		=> __( 'Plugin Suggestions', 'role_excluder' ),
													'description' 	=> __( 'Any of the following plugins will allow you to define new roles and capabilties for the site, only use one of these.  Selection of a plugin will prompt you through the installation and the plugin will be forced active while this is selected; deselecting will not remove the plugin, you will need to manually deactivate and un-install from the site/network.', 'role_excluder' ),					
													'settings' 		=> array(
																			array(
																				'access_capability' => 'install_plugins',
																				'name' 		=> 'SMALL_GROUPS_posts_to_posts_plugin',
																				'std' 		=> true,
																				'label' 	=> 'Posts 2 Posts',
																				'desc'		=> __( "This plugin adds user meta box functionality.", 'SMALL_GROUPS' ),
																				'type'      => 'field_plugin_checkbox_option',
																				// the following are for tgmpa_register activation of the plugin
																				'slug'      			=> 'posts-to-posts',
																				'plugin_dir'			=> SMALL_GROUPS_PLUGIN_DIR,
																				'required'              => false,
																				'force_deactivation' 	=> false,
																				'force_activation'      => true,		
																				),
																			),
												)
										
											)
									);
			
        if ( null == self::$instance ) {
            self::$instance = new Tabbed_Settings( $settings, $config );
        }

        return self::$instance;
 
    } 
}

/**
 * SMALL_GROUPS_Settings_Additional_Methods class.
 */
class SMALL_GROUPS_Settings_Additional_Methods {

		
}


// Include the Tabbed_Settings class.
if ( ! class_exists( 'Extendible_Tabbed_Settings' ) ) { 
	require_once( dirname( __FILE__ ) . '/class-tabbed-settings.php' );
}

// Create new tabbed settings object for this plugin..
// and Include additional functions that are required.
SMALL_GROUPS_Settings::get_instance()->registerHandler( new SMALL_GROUPS_Settings_Additional_Methods() );




		
?>