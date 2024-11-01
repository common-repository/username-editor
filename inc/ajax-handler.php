<?php

add_action( "wp_ajax_wg_ue_ajax_enabled", 'wg_ue_ajax_enabled' );
add_action( "wp_ajax_wg_ue_ajax_enabled_password_check", 'wg_ue_ajax_enabled_password_check' );

if( ! function_exists( 'wg_ue_ajax_enabled' ) ) {
	function wg_ue_ajax_enabled() {

		$currentUser = wp_get_current_user();
		$user_id = $currentUser->ID;
		$userEmail = $currentUser->user_email;
		$oldUsername = $currentUser->user_login;
		$requestedUsername = $_REQUEST['ue_new_username'];

		if ( is_user_logged_in() && wp_verify_nonce( $_REQUEST['nonce'], 'wg_ue_new_username_nonce' ) && ! empty( $requestedUsername )) {

			$newUsername = wg_ue_username_check( $requestedUsername );

			if ( wg_ue_check_username_limit( $requestedUsername ) == true ) {
				wp_send_json( array(
					'username_limit' => true,
					'username_number' 	=> wg_ue_get_username_limit(),
				) );
			}

			$result = wg_ue_sql_query($user_id, esc_sql( $newUsername ) );

			if ( $result ) {
				wp_send_json( array('update' => true) );
			} else {
				wp_send_json( array('update' => false) );
			}

			wg_ue_send_email($user_id, $userEmail, $oldUsername, $newUsername);
		}
		wp_die();
	}
}

if ( ! function_exists('wg_ue_ajax_enabled_password_check') ) {
	function wg_ue_ajax_enabled_password_check() {

		$currentUser = wp_get_current_user();
		$user_id = $currentUser->ID;
		$userEmail = $currentUser->user_email;
		$oldUsername = $currentUser->user_login;
		$userPassword = $currentUser->user_pass;
		$requestedUsername = $_REQUEST['ue_new_username'];

		if (  is_user_logged_in() && wp_verify_nonce( $_REQUEST['nonce'], 'wg_ue_new_username_nonce' ) && ! empty( $requestedUsername ) ) { 

			if ( wp_check_password( $_REQUEST['password'], $userPassword, $user_id ) ) {

				$newUsername = wg_ue_username_check( $requestedUsername );

				if ( wg_ue_check_username_limit( $requestedUsername ) === true ) {
					wp_send_json( array(
						'username_limit' => true,
						'username_number' 	=> wg_ue_get_username_limit(),
					) );
				}

				$result = wg_ue_sql_query($user_id, esc_sql( $newUsername ) );

				if ( $result ) {
					wp_send_json( array('update' => true) );
				} else {
					wp_send_json( array('update' => false) );
				}

				wg_ue_send_email($user_id, $userEmail, $oldUsername, $newUsername);
			} else {
				wp_send_json( array('password_wrong' => true) );
			}
			wp_die();
		}
	}
}