<?php


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * SG_Capabilities class.
 */
class SG_Capabilities {

	/**
	 * __construct function.
	 *
	 * @access public
	 * @return void
	 */
	public function __construct( ) {
	
		// Add Meta Capability Handling 
		add_filter( 'map_meta_cap', array( $this, 'sg_map_meta_cap' ), 10, 4);
	}
		
	/**
	 * sg_add_role_caps function.
	 *
	 * @access static
	 * @return void
	 */
	static function sg_add_role_caps( ) {
            
            // gets the new Help Note active role
            $role = get_role( 'administrator' );
           // $capability_type = $active_posttype;
            $capability_type = 'small_group';

            $role->add_cap( "edit_{$capability_type}s" );
            $role->add_cap( "edit_others_{$capability_type}s" );
            $role->add_cap( "publish_{$capability_type}s" );
            $role->add_cap( "read_private_{$capability_type}s" );
            $role->add_cap( "delete_{$capability_type}s" );
            $role->add_cap( "delete_private_{$capability_type}s" );
            $role->add_cap( "delete_published_{$capability_type}s" );
            $role->add_cap( "delete_others_{$capability_type}s" );
            $role->add_cap( "edit_private_{$capability_type}s" );
            $role->add_cap( "edit_published_{$capability_type}s" );
            $role->add_cap( "create_{$capability_type}s" );
            $role->add_cap( "manage_categories_{$capability_type}" );

					

	}


	/**
	 * sg_delete_role_caps function.
	 *
	 * @access public
	 * @return void
	 */
	static function sg_delete_role_caps( ) {
            
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

            $users = get_users( );
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

        
        
        
        
	/**
	 * sg_map_meta_cap function to add Meta Capability Handling.
	 *
	 * @access public
	 * @param mixed $caps, $cap, $user_id, $args
	 * @return void
	 */
	public function sg_map_meta_cap( $caps, $cap, $user_id, $args ) {

            $capability_type = 'small_group';

            if ( 'edit_' . $capability_type == $cap || 'delete_' . $capability_type == $cap || 'read_' . $capability_type == $cap  ) {

                    $post = get_post( $args[0] );
                    $post_type = get_post_type_object( $post->post_type );

                    /* Set an empty array for the caps. */
                    $caps = array( );
            }


            /* If editing a help note, assign the required capability. */
            if (  "edit_{$capability_type}" == $cap ) {

                    if( $user_id == $post->post_author )
                            $caps[] = $post_type->cap->edit_posts;
                    else
                            $caps[] = $post_type->cap->edit_others_posts;	
            }

            /* If deleting a help note, assign the required capability. */
            elseif( "delete_{$capability_type}" == $cap ) {

                    if( isset( $post->post_author ) && $user_id == $post->post_author  && isset( $post_type->cap->delete_posts ) )
                            $caps[] = $post_type->cap->delete_posts;
                    elseif ( isset( $post_type->cap->delete_others_posts ) )
                            $caps[] = $post_type->cap->delete_others_posts;		
            }

            /* If reading a private help note, assign the required capability. */
            elseif(  "read_{$capability_type}" == $cap ) {

                    if( 'private' != $post->post_status )
                            $caps[] = 'read';
                    elseif ( $user_id == $post->post_author )
                            $caps[] = 'read';
                    else
                            $caps[] = $post_type->cap->read_private_posts;
            }

            /* Return the capabilities required by the user. */
            return $caps;	
	}
}

new SG_Capabilities( );

?>