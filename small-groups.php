<?php
/*
Plugin Name: Small Groups
Plugin URI: http://justinandco.com/plugins/small-groups
Description: Allow for maintaining a list of group members ( uses the "Posts 2 Posts" plugin )
Version: 1.0
Author: Justin Fletcher
Author URI: http://justinandco.com
Text Domain: small-groups
Domain Path: /languages/
License: GPLv2 or later
*/


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * small-groups class.
 */
class SMALL_GROUPS {

	// Refers to a single instance of this class.
    private static $instance = null;
	
    public	 $plugin_full_path;
    public   $plugin_file = 'small-groups/small-groups.php';
	
	// Settings page slug	
    public	 $menu = 'small-groups-settings';
	
	// Settings Admin Menu Title
    public	 $menu_title = 'Small Groups';
	
	// Settings Page Title
    public	 $page_title = 'Small Groups';
    
    /**
	 * __construct function.
	 *
	 * @access public
	 * @return void
	 */
	public function __construct() {

		// On plugin activation
		//register_activation_hook( __FILE__ , array( $this, 'on_activation' ));
                
		$this->plugin_full_path = plugin_dir_path(__FILE__) . 'small-groups.php' ;

		/* Set the constants needed by the plugin. */
		add_action( 'after_setup_theme', array( $this, 'constants' ), 9 );
 
		/* Load the resources */
		 add_action( 'after_setup_theme', array( $this, 'includes' ), 10 );

		// Attached to after_setup_theme. Loads the plugin installer CLASS after themes are set-up to stop duplication of the CLASS.
		// this should remain the hook until TGM-Plugin-Activation version 2.4.0 has had time to roll out to the majority of themes and plugins.
		add_action( 'after_setup_theme', array( $this, 'after_setup_theme' ));
		
		// register admin side - upgrade routine and menu item.
		add_action( 'admin_init', array( $this, 'admin_init' ));
		// Load the textdomain.
		add_action( 'after_setup_theme', array( $this, 'i18n' ));
		
		
		
		// Load admin error messages	
		add_action( 'admin_init', array( $this, 'deactivation_notice' ));
		add_action( 'admin_notices', array( $this, 'action_admin_notices' ));

	}
	
	/**
	 * Defines constants used by the plugin.
	 *
	 * @return void
	 */
	public function constants() {

		// Define constants
		define( 'SMALL_GROUPS_MYPLUGINNAME_PATH', plugin_dir_path( __FILE__ ) );
		define( 'SMALL_GROUPS_MYPLUGINNAME_FULL_PATH', SMALL_GROUPS_MYPLUGINNAME_PATH . 'small-groups.php' );
		define( 'SMALL_GROUPS_PLUGIN_DIR', trailingslashit( plugin_dir_path( SMALL_GROUPS_MYPLUGINNAME_PATH )));
		define( 'SMALL_GROUPS_PLUGIN_URI', plugins_url('', __FILE__) );
		
		// admin prompt constants
		define( 'SMALL_GROUPS_PROMPT_DELAY_IN_DAYS', 30);
		define( 'SMALL_GROUPS_PROMPT_ARGUMENT', 'SMALL_GROUPS_hide_notice');
		
	}

	/**
	 * Loads the initial files needed by the plugin.
	 *
	 * @return void
	 */
	public function includes() {

            // settings 
            require_once( SMALL_GROUPS_MYPLUGINNAME_PATH . 'includes/settings.php' );  

            // include the main class 
            require_once( SMALL_GROUPS_MYPLUGINNAME_PATH . 'includes/class-small-groups.php' );

            // custom post type capabilities
            require_once( SMALL_GROUPS_MYPLUGINNAME_PATH . 'includes/class-capabilities.php' );                  
	}
	
	/**
	 * Initialise the plugin installs
	 *
	 * @return void
	 */
	public function after_setup_theme() {

		// install the plugins and force activation if they are selected within the plugin settings
		require_once( SMALL_GROUPS_MYPLUGINNAME_PATH . 'includes/plugin-install.php' );
		
	}

        
    /**
	 * Initialise the plugin menu. 
	 *
	 * @return void
	 */
	public function admin_menu() {

	}
    
	/**
	 * sub_menu_page: 
	 *
	 * @return void
	 */
	public function sub_menu_page() {
		// 
	}	
	
	/**
	 * Initialise the plugin by handling upgrades and loading the text domain. 
	 *
	 * @return void
	 */
	public function admin_init() {

		//Registers user installation date/time on first use
		$this->action_init_store_user_meta();
		
		$plugin_current_version = get_option( 'small_groups_plugin_version' );
		$plugin_new_version =  self::plugin_get_version();
                
		// Admin notice hide prompt notice catch
		//$this->catch_hide_notice( );
                
		//if ( empty( $plugin_current_version ) || $plugin_current_version < $plugin_new_version ) {
		if ( version_compare( $plugin_current_version, $plugin_new_version, '<' ) ) {
		
			if ( ! $plugin_current_version ) {
				$plugin_current_version = 0;
			}
			
			$this->SMALL_GROUPS_upgrade( $plugin_current_version );

			// set default options if not already set..
			$this->do_on_activation();
			
			// Update the option again after SMALL_GROUPS_upgrade() changes and set the current plugin revision	
			update_option('small_groups_plugin_version', $plugin_new_version ); 
		}
		

	}
	
	/**
	 * Loads the text domain.
	 *
	 * @return void
	 */
	public function i18n( ) {
		$ok = load_plugin_textdomain( 'small-groups', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/');
	}
		
	/**
	 * Provides an upgrade path for older versions of the plugin
	 *
	 * @param float $current_plugin_version the local plugin version prior to an update 
	 * @return void
	 */
	public function SMALL_GROUPS_upgrade( $current_plugin_version ) {
		
		/*
		// upgrade code when required.
		if ( $current_plugin_version < '1.0' ) {

			delete_option('XXXXXX');

		}
		*/
	}

	/**
	 * Flush your rewrite rules for plugin activation and initial install date.
	 *
	 * @access public
	 * @return $settings
	 */	
	public function do_on_activation() {
            
/*            
               if ( defined('P2P_PLUGIN_VERSION') ) {
                    deactivate_plugins( plugin_basename( __FILE__ ) );
                    wp_die( __('Sorry, you need to activate posts-to-posts first.', 'small-groups'));
                }
 */               
                $this->constants();
                $this->includes();
                
		//make sure post type is present before flushing the re-write rules
		SMALL_GRPS::get_instance();
		flush_rewrite_rules( );

                                
		// Record plugin activation date.
		add_option('small_groups_install_date',  time() ); 
 
                require_once( SMALL_GROUPS_MYPLUGINNAME_PATH . 'includes/class-capabilities.php' );    
        
                SG_Capabilities::sg_add_role_caps( );		
 

	}

	/**
	 * remove the reference site option setting for safety when re-activating the plugin
	 *
	 * @access public
	 * @return $settings
	 */	
	static function do_on_deactivation() {

		//delete_option('SMALL_GROUPS_reference_site' );
	}

        
	/**
	 * Returns current plugin version.
	 *
	 * @access public
	 * @return $plugin_version
	 */	
	static function plugin_get_version() {

		$plugin_data = get_plugin_data( SMALL_GROUPS_MYPLUGINNAME_FULL_PATH, false, false );	

		$plugin_version = $plugin_data['Version'];	
		return filter_var($plugin_version, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
	}
	
	/**
	 * Register Plugin Deactivation Hooks for all the currently 
	 * enforced active extension plugins.
	 *
	 * @access public
	 * @return null
	 */
	public function deactivation_notice() {

		// loop plugins forced active.
		$plugins = SMALL_GROUPS_Settings::get_instance()->selected_plugins( 'small_groups_plugin_extension' );
		$plugins = array_filter( $plugins );
		if ( ! empty( $plugins ) ) {
		
			foreach ( $plugins as $plugin ) {
				$plugin_file = SMALL_GROUPS_PLUGIN_DIR . $plugin["slug"] . '\\' . $plugin['slug'] . '.php' ;
				register_deactivation_hook( $plugin_file, array( 'small-groups', 'on_deactivation' ) );
			}
		}
	}

	/**
	 * This function is hooked into plugin deactivation for 
	 * enforced active extension plugins.
	 *
	 * @access public
	 * @return null
	 */
	public static function on_deactivation()
    {
        if ( ! current_user_can( 'activate_plugins' ) )
            return;
        $plugin = isset( $_REQUEST['plugin'] ) ? $_REQUEST['plugin'] : '';
        check_admin_referer( "deactivate-plugin_{$plugin}" );
	
		$plugin_slug = explode( "/", $plugin);
		$plugin_slug = $plugin_slug[0];
		update_option( "SMALL_GROUPS_deactivate_{$plugin_slug}", true );
    }
	
	/**
	 * Display the admin warnings.
	 *
	 * @access public
	 * @return null
	 */
	public function action_admin_notices() {

		// loop plugins forced active.
		$plugins = SMALL_GROUPS_Settings::get_instance()->selected_plugins( 'small_groups_plugin_extension' );

		// for each extension plugin enabled (forced active) add a error message for deactivation.
		foreach ( $plugins as $plugin ) {
			$this->action_admin_plugin_forced_active_notices( $plugin["slug"] );
		}
		
		// Prompt for rating
                //if ( current_user_can( 'install_plugins' ) ) {
                //    $this->action_admin_rating_prompt_notices();
                //}
	}
	
	/**
	 * Display the admin error message for plugin forced active.
	 *
	 * @access public
	 * @return null
	 */
	public function action_admin_plugin_forced_active_notices( $plugin ) {
	
		$plugin_message = get_option("SMALL_GROUPS_deactivate_{$plugin}");
		if ( ! empty( $plugin_message ) ) {
			?>
			<div class="error">
				  <p><?php esc_html_e(sprintf( __( 'Error the %1$s plugin is forced active with ', 'small-groups'), $plugin)); ?>
				  <a href="options-general.php?page=<?php echo $this->menu ; ?>&tab=small_groups_plugin_extension"> <?php echo esc_html(__( 'Small Groups Settings!', 'small-groups')); ?> </a></p>
			</div>
			<?php
			update_option("SMALL_GROUPS_deactivate_{$plugin}", false); 
		}
	}

		
	/**
	 * Store the current users start date
	 *
	 * @access public
	 * @return null
	 */
	public function action_init_store_user_meta() {
		
		// store the initial starting meta for a user
		add_user_meta( get_current_user_id(), 'SMALL_GROUPS_start_date', time(), true );
		add_user_meta( get_current_user_id(), 'SMALL_GROUPS_prompt_timeout', time() + 60*60*24*  SMALL_GROUPS_PROMPT_DELAY_IN_DAYS, true );

	}

	/**
	 * Display the admin message for plugin rating prompt.
	 *
	 * @access public
	 * @return null
	 */
	public function action_admin_rating_prompt_notices( ) {

		$user_responses =  array_filter( (array)get_user_meta( get_current_user_id(), SMALL_GROUPS_PROMPT_ARGUMENT, true ));	
		if ( in_array(  "done_now", $user_responses ) ) 
			return;

		if ( current_user_can( 'install_plugins' ) ) {
			
			$next_prompt_time = get_user_meta( get_current_user_id(), 'SMALL_GROUPS_prompt_timeout', true );
			if ( ( time() > $next_prompt_time )) {
				$plugin_user_start_date = get_user_meta( get_current_user_id(), 'SMALL_GROUPS_start_date', true );
				?>
				<div class="update-nag">
					
					<p><?php esc_html(printf( __("-You've been using <b>Small Groups</b> for more than %s.  How about giving it a review by logging in at wordpress.org ?", 'small-groups'), human_time_diff( $plugin_user_start_date) )); ?>
				
					</p>
					<p>

						<?php echo '<a href="' .  esc_url(add_query_arg( array( SMALL_GROUPS_PROMPT_ARGUMENT => 'doing_now' )))  . '">' .  esc_html__( 'Yes, please take me there.', 'small-groups' ) . '</a> '; ?>
						
						| <?php echo ' <a href="' .  esc_url(add_query_arg( array( SMALL_GROUPS_PROMPT_ARGUMENT => 'not_now' )))  . '">' .  esc_html__( 'Not right now thanks.', 'small-groups' ) . '</a> ';?>
						
						<?php
						if ( in_array(  "not_now", $user_responses ) || in_array(  "doing_now", $user_responses )) { 
							echo '| <a href="' .  esc_url(add_query_arg( array( SMALL_GROUPS_PROMPT_ARGUMENT => 'done_now' )))  . '">' .  esc_html__( "I've already done this !", 'small-groups' ) . '</a> ';
						}?>

					</p>
				</div>
				<?php
			}
		}	
	}

	/**
	 * Store the user selection from the rate the plugin prompt.
	 *
	 * @access public
	 * @return null
	 */
	public function catch_hide_notice( ) {

            if ( isset( $_GET[SMALL_GROUPS_PROMPT_ARGUMENT] ) && $_GET[SMALL_GROUPS_PROMPT_ARGUMENT] && current_user_can( 'install_plugins' ) ) {

                    $user_user_hide_message = array( sanitize_key( $_GET[SMALL_GROUPS_PROMPT_ARGUMENT] ) ) ;				
                    $user_responses =  array_filter( ( array ) get_user_meta( get_current_user_id( ), SMALL_GROUPS_PROMPT_ARGUMENT, true ) );	

                    if ( ! empty( $user_responses ) ) {
                            $response = array_unique( array_merge( $user_user_hide_message, $user_responses ) );
                    } else {
                            $response =  $user_user_hide_message;
                    }

                    check_admin_referer( );	
                    update_user_meta( get_current_user_id( ), SMALL_GROUPS_PROMPT_ARGUMENT, $response );

                    if ( in_array( "doing_now", ( array_values( ( array ) $user_user_hide_message ) ) ) ) {
                            $next_prompt_time = time( ) + ( 60*60*24*  SMALL_GROUPS_PROMPT_DELAY_IN_DAYS ) ;
                            update_user_meta( get_current_user_id( ), 'SMALL_GROUPS_prompt_timeout' , $next_prompt_time );
                            wp_redirect( 'http://wordpress.org/support/view/plugin-reviews/role-based-help-notes' );
                            exit;					
                    }

                    if ( in_array( "not_now", ( array_values( ( array )$user_user_hide_message ) ) ) ) {
                            $next_prompt_time = time( ) + ( 60*60*24*  SMALL_GROUPS_PROMPT_DELAY_IN_DAYS ) ;
                            update_user_meta( get_current_user_id( ), 'SMALL_GROUPS_prompt_timeout' , $next_prompt_time );		
                    }


                    wp_redirect( remove_query_arg( SMALL_GROUPS_PROMPT_ARGUMENT ) );
                    exit;		
            }
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
 * Init SMALL_GROUPS class
 */
 
SMALL_GROUPS::get_instance();

register_deactivation_hook( __FILE__, array( 'small-groups', 'do_on_deactivation' ) );

// Plugin Activation
function small_groups_activation( ) {
	$small_groups = SMALL_GROUPS::get_instance( );
	$small_groups->do_on_activation( );
}
register_activation_hook( __FILE__, 'small_groups_activation' );

// Plugin De-activation
function small_groups_deactivation( ) {
	$small_groups = SMALL_GROUPS::get_instance( );
        $small_group_caps = SG_Capabilities::sg_delete_role_caps( );
	
}
// only register for testing
//register_deactivation_hook( __FILE__, 'small_groups_deactivation' );

?>