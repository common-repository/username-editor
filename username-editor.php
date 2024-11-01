<?php

/**
 * Plugin Name: Username Editor
 * Description: Change WordPress username easily
 * Plugin URI: https://websiteguider.com
 * Author: WebsiteGuider
 * Author URI: https://websiteguider.com
 * Version: 1.2
 * License: GPL2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: username-editor
 */

defined( 'ABSPATH' ) || exit;

define( 'WG_UE_VERSION', '1.2' );

define( 'WG_UE_BASENAME', plugin_basename( __FILE__ ) );

define( 'WG_UE_DIRPATH', plugin_dir_path( __FILE__ ) );


register_activation_hook( __FILE__, 'wg_ue_username_editor_activation' );
register_deactivation_hook( __FILE__, 'wg_ue_username_editor_deactivation' );

if( ! function_exists('wg_ue_username_editor_activation') ) {
	function wg_ue_username_editor_activation() {
		set_transient( 'wg_ue_plugin_activated', true, 5 );
	}
}

if( ! function_exists('wg_ue_username_editor_deactivation') ) {
	function wg_ue_username_editor_deactivation() {
		set_transient( 'wg_ue_plugin_deactivated', true, 5 );
	}
}

require_once WG_UE_DIRPATH . 'inc/check-functions.php';
require_once WG_UE_DIRPATH . 'inc/common-functions.php';
require_once WG_UE_DIRPATH . 'inc/admin-settings.php';
require_once WG_UE_DIRPATH . 'inc/callback-functions.php';
require_once WG_UE_DIRPATH . 'inc/username-editor.php';
require_once WG_UE_DIRPATH . 'inc/ajax-handler.php';

if ( !function_exists('wg_ue')) {
	function wg_ue() {
		add_action( 'admin_menu', 'wg_ue_register_menu_pages' );
		add_action( 'admin_init', 'wg_ue_settings_field_register' );
	}	
}

// Call the function to start running code
wg_ue();

add_action( 'admin_enqueue_scripts', 'wg_ue_enqueue_scripts' );
add_action( 'plugins_loaded', 'wg_ue_initialize_fields');
// Admin notices on plugin activation/deactivation
add_action( 'admin_notices', 'wg_ue_plugin_activation_notice' );
add_action( 'admin_notices', 'wg_ue_plugin_deactivation_notice' );

if( ! function_exists('wg_ue_enqueue_scripts') ) {
	function wg_ue_enqueue_scripts() {
		wp_enqueue_style( 'wg-ue-main-style', plugin_dir_url( __FILE__ ) . 'css/style.css');
		wp_enqueue_script( 'wg_ue_js', plugin_dir_url( __FILE__ ) . 'js/username-editor.js', array('jquery') );

		wp_localize_script( 'wg_ue_js', 'ue_ajax', array(
			'ajax_url' 			=> admin_url('admin-ajax.php'),
			'id'				=> get_current_user_id(),
			'redirectUrl'		=> get_home_url() . '/wp-admin',
			'beforeMessage' 	=> __('Checking...', 'username-editor'),
			'successMessage'	=> __('Username succesfully changed, you will be redirected shortly', 'username-editor'),
			'failureMessage'	=> __('We were unable to change, please try again...', 'username-editor'),
			'usernameExists'	=> __('Username already exists', 'username-editor'),
			'passwordWrong'		=> __('Password mismatch, please try again...', 'username-editor'),
		) );
	}
}

if ( ! function_exists('wg_ue_initialize_fields') ) {
	function wg_ue_initialize_fields(){
		wg_ue_show_field();
	}
}

if ( ! function_exists('wg_ue_plugin_activation_notice') ) {
	function wg_ue_plugin_activation_notice() {
		if ( get_transient( 'wg_ue_plugin_activated' ) ) {
			?>
			<div class="updated notice is-dismissible">
				<p><?php echo __('Thank you for using this plugin! <strong>You are awesome</strong>.', 'username-editor'); ?></p>
			</div>
			<?php
			/* Delete transient, only display this notice once. */
			delete_transient( 'wg_ue_plugin_activated' );
		}
	}
}

if( ! function_exists('wg_ue_plugin_deactivation_notice') ) {
	function wg_ue_plugin_deactivation_notice() {
		if ( get_transient( 'wg_ue_plugin_deactivated' ) ) {
			?>
			<div class="updated notice is-dismissible">
				<p><?php echo __('Username Editor has been successfully uninstalled.', 'username-editor'); ?></p>
			</div>
			<?php
			/* Delete transient, only display this notice once. */
			delete_transient( 'wg_ue_plugin_deactivated' );
		}
	}
}