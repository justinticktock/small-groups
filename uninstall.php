<?php

//if uninstall not called from WordPress exit
if ( !defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit ( );
}
	
if ( is_multisite( ) ) {

    $blogs = wp_list_pluck( wp_get_sites(), 'blog_id' );

    if ( $blogs ) {
        foreach( $blogs as $blog ) {
            switch_to_blog( $blog );
            small_groups_clean_database( );
            sg_delete_role_caps();
        }
        restore_current_blog( );
    }
} else {
	small_groups_clean_database( );
        sg_delete_role_caps();
        
}


// remove all database entries for currently active blog on uninstall.
function small_groups_clean_database( ) {
		
		delete_option( 'small_groups_plugin_version' );
		delete_option( 'small_groups_install_date' );

		// plugin specific database entries
		delete_option( 'SMALL_GROUPS_posts_to_posts_plugin' );
		
		delete_option( 'SMALL_GROUPS_deactivate_posts_to_posts' );
		
		// user specific database entries
		delete_user_meta( get_current_user_id( ), 'SMALL_GROUPS_prompt_timeout', $meta_value );
		delete_user_meta( get_current_user_id( ), 'SMALL_GROUPS_start_date', $meta_value );
		delete_user_meta( get_current_user_id( ), 'SMALL_GROUPS_hide_notice', $meta_value );

}


/**
 * sg_delete_role_caps function.
 *
 * @access public
 * @return void
 */
function sg_delete_role_caps( ) {


    $capability_type = 'small_group';


    $delete_caps = array(
                    "edit_{$capability_type}",
                    "read_{$capability_type}",
                    "delete_{$capability_type}",
                    "edit_{$capability_type}s",
                    "edit_others_{$capability_type}s",
                    "publish_{$capability_type}s",
                    "read_private_{$capability_type}s",
                    "delete_{$capability_type}s",
                    "delete_private_{$capability_type}s",
                    "delete_published_{$capability_type}s",
                    "delete_others_{$capability_type}s",
                    "edit_private_{$capability_type}s",
                    "edit_published_{$capability_type}s",
                    "create_{$capability_type}s",
                    "manage_categories_{$capability_type}"		
                    );

    global $wp_roles;

    if ( ! isset( $wp_roles ) ) {
    $wp_roles = new WP_Roles( );
    }

    $users              = get_users( );
    $administrator      = get_role( 'administrator' );

    // loop through the capability list.
    foreach ( $delete_caps as $cap ) {

            // Clean-up Capability from WordPress Roles
            foreach ( array_keys( $wp_roles->roles ) as $role ) {
                    $wp_roles->remove_cap( $role, $cap );
            }

            // Clean-up Capability from WordPress Users where explicitly allocated 
            foreach ( $users as $user ) {
                    $user->remove_cap( $cap );
            }

            // Clean-up Capability from the Administrator Role
            $administrator->remove_cap( $cap );		
    }
    unset( $wp_roles );


}



?>