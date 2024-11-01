<?php
/**
 * Description
 * @param string $nonce 
 * @return bool 
 */

if ( ! function_exists('wg_ue_security_check') ) {
	function wg_ue_security_check( $nonce ) {
		$security = is_user_logged_in() && wp_verify_nonce( $nonce, 'ue_new_username_nonce' );
		return $security;
	}
}

if ( ! function_exists('wg_ue_username_sanitize') ) {
	function wg_ue_username_sanitize( $username ) {
		return sanitize_text_field( $username );
	}
}

if ( ! function_exists('wg_ue_username_check') ) {
	function wg_ue_username_check( $username ) {
		
		if ( wg_ue_settings_option()['ue_field_type'] == 'ajax-field' || wg_ue_settings_option()['ue_field_type'] == 'ajax-field-password' ) {

			return username_exists( wg_ue_username_sanitize( $username ) ) ? wp_send_json( array('username_exists' => true) ) : $username;

		} else {
				return username_exists( wg_ue_username_sanitize( $username ) ) ? false : $username;
		}
	}
}

if ( ! function_exists('wg_ue_sql_query') ) {
	function wg_ue_sql_query( $userId, $newUsername ) {

		global $wpdb;

		// Get name of our users table
		$tableName = $wpdb->prefix . 'users';

		// Data to change
		$dataToChange = array('user_login' => $newUsername);

		// Where to Change
		$whereToChange = array('ID' => $userId); 

		// Change the data inside the table
		$sql_update = $wpdb->update( $tableName, $dataToChange, $whereToChange, array('%s'), array('%d') );

		return $sql_update;

	}
}

if( ! function_exists( 'wg_ue_send_email' ) ) {
	function wg_ue_send_email( $userId, $userEmail, $oldUsername, $newUsername ) {
		if ( ! wg_ue_send_email_to_user() ) {
			return;
		}

		$subject = 'Username Changed Successfully ID:' . $userId;
		$message = "<p>You username has been successfully changed. Your new details are given below.</p>";
		$message .= "<strong>Previous Username:</strong><span>{$oldUsername}</span>";
		$message .= "<strong>New Username</strong><span>{$newUsername}</span>";
		$from = "From: " . get_bloginfo('name');

		wp_mail( array(wg_ue_get_administrators_email(), $userEmail), $subject, $message, $from );

	}
}

if ( ! function_exists( 'wg_ue_get_administrators_email' ) ) {
	function wg_ue_get_administrators_email() {
		$administrators = get_users('role=Administrator');
		foreach ($administrators as $user) {
			return $user->user_email;
		} 
	}
}

if ( ! function_exists( 'wg_ue_admin_page' ) ) {
	function wg_ue_change_admin_page() {
		 return admin_url('admin.php?page=wg_ue_change_username');
	}
}

if ( ! function_exists( 'wg_ue_settings_header' ) ) {
	function wg_ue_settings_header () {
		?>
		<div class="wg-ue-settings">
			<div class="wg-ue-header">
				<div class="wg-ue-header-inner">
					<div class="wg-ue-header-left">
					<span class="dashicons dashicons-edit-page"></span><span class="plugin-title"><strong>Username Editor</strong></span>
					</div>
					<div class="wg-ue-header-right">
						<span class="wg-ue-version">version <sup><?php echo WG_UE_VERSION ?></sup></span>
						<span class="wg-logo"><img src=""></span>
					</div>
				</div>
			</div>
		<?php
	}
}