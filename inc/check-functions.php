<?php

/**
 * Returns the main option which we use to store data related to 
 * settings
 * 
 * @return array Settings option
 */
if ( ! function_exists('wg_ue_settings_option') ) {
	function wg_ue_settings_option() {
		return (array) get_option( 'wg_ue_settings' );
	}
}

/**
 * Check whether the main option is selected or not.
 * 
 * @return bool Return true if set else false
 */

if ( ! function_exists('wg_ue_is_enabled') ) {
	function wg_ue_is_enabled() {
		$checkboxConfirm = ! empty( wg_ue_settings_option()['ue_checkbox_confirm'] ) ? wg_ue_settings_option()['ue_checkbox_confirm'] : '';
		// Check whether to show username editor field in profiles or not
		if ( $checkboxConfirm == 1 ) {
			return true;
		}

		return false;
	}
}

/**
 * Check whther the email option is selected or not.
 * 
 * @return bool Return true if selected else false
 */
if ( ! function_exists('wg_ue_send_email_to_user') ) {
	function wg_ue_send_email_to_user() {
		// Check if the administrator has enabled email functionality or not.
		if ( wg_ue_settings_option()['ue_email_confirm'] == 1 ) {
			return true;
		}

		return false;
	}
}

/**
 * Check which roles are allowed to edit username
 * 
 * @return bool Return true if the current user has role else false
 */
if ( ! function_exists('wg_ue_allowed_roles') ) {
	function wg_ue_allowed_roles() {

		$user = wp_get_current_user();
		$currentUserRole = $user->roles;
		$allowedUserRoles = (array) wg_ue_settings_option()['ue_roles_confirm'];

		foreach ( $allowedUserRoles as $role) {
			$allowedRole = strtolower( $role );
		}

		if ( in_array($allowedRole, $currentUserRole) ) {
			return true;
		} else {
			return false;
		}
		
	}
}

/**
 * Check whether the Ajax field option is selected or not
 * 
 * @return bool Return true if 'ajax-field' is set else false
 */
if ( ! function_exists('wg_ue_is_ajax_enabled') ) {
	function wg_ue_is_ajax_enabled() {
		if (wg_ue_settings_option()['ue_field_type'] == 'ajax-field') {
			return true;
		}

		return false;
	}
}

/**
 * Check whether the Ajax field with password check option is selected or not
 * 
 * @return bool Return true if 'ajax-field-password' is set else false
 */
if ( ! function_exists('wg_ue_is_password_check') ) {
	function wg_ue_is_password_check() {
		if (wg_ue_settings_option()['ue_field_type'] == 'ajax-field-password') {
			return true;
		}

		return false;
	}
}

/**
 * Check the length of admin defined username limit.
 * 
 * @return int Return username limit length value as integer
 */
if ( !function_exists('wg_ue_get_username_limit') ) {
	function wg_ue_get_username_limit() {

		return intval( wg_ue_settings_option()['ue_username_length'] );

	}
}

/**
 * Check whether the Ajax feild option is selected or not
 * 
 * @return bool Return true if 
 */
if ( ! function_exists('wg_ue_check_username_limit') ) {
	function wg_ue_check_username_limit( $username ) {
		if ( strlen( $username ) < wg_ue_get_username_limit() ) {
			return true;
		}
		return;
	}
}

