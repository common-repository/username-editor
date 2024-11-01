<?php
if( ! function_exists('wg_ue_show_field') ) {
	function wg_ue_show_field() {
		if ( ! wg_ue_is_enabled() ) {
			return;
		}

		if (  ! wg_ue_allowed_roles() ) {
			return;
		}

		add_action( 'show_user_profile', 'wg_ue_add_new_username_field' );
		add_action( 'edit_user_profile', 'wg_ue_add_new_username_field' );
		add_action( 'personal_options_update', 'wg_ue_update_username_fields' );
		add_action( 'edit_user_profile_update', 'wg_ue_update_username_fields' );
		add_action( 'admin_notices', 'edd_sample_admin_notices' );

	}
}

function edd_sample_admin_notices() {
	if ( isset( $_GET['username_update'] ) && ! empty( $_GET['message'] ) ) {
		switch( $_GET['username_update'] ) {
			case 'failed':
				$message = urldecode( $_GET['message'] );
				?>
				<div class="notice notice-error is-dismissible">
					<p><?php echo __( $message, 'username-editor' ); ?></p>
				</div>
				<?php
				break;
			default:
				// Developers can put a custom success message here for when activation is successful if they way.
				break;
		}
	}
}


if( ! function_exists('wg_ue_add_new_username_field') ) {
	function wg_ue_add_new_username_field() {
		$userData = get_userdata( get_current_user_id() );
		$username = $userData->user_login;
		?>
		<h3><?php esc_html_e( 'Change Username', 'username-editor' ); ?></h3>
		<table class="form-table" role="presentation">
			<tbody>
				<tr class="user-email-wrap">
					<th>
						<label for="new_username"><?php esc_html_e( 'Enter New Username', 'base' ); ?></label>
					</th>
					<td>
						<?php
						$input = "<input id='wg_ue_new_username' class='regular-text ltr' type='text' name='wg_ue_new_username' value='" . $username . "'>";
						if ( wg_ue_is_ajax_enabled() ) {
							echo $input;
							?>
							<span name="ue_ajax_enabled" class="button-primary ue_ajax_enabled"><?php echo __( 'Change', 'username-editor' ); ?></span>
							<div class="ue-error-success">
								<div class="ue-list-error">
									<p class="ue-show-error"></p>
									<p class="ue-show-success"></p>
								</div>
							</div>
							<?php
						} else if ( wg_ue_is_password_check() ) {
							echo $input;
							?>
							<span name="ue_ajax_enabled_password_check" class="button-primary ue_ajax_enabled_password_check"><?php echo __( 'Change', 'username-editor' ); ?></span>
							<div class="ue-popup close">
								<div class="ue-popup-inner">
									<h3><?php echo __('Confirm Password', 'username-editor'); ?></h3>
									<p><?php echo __( 'If your password is correct, then your username will be changed else not.', 'username-editor'); ?></p>
									<input type="password" class="ue_password_check_field" name="ue_password_check_field" placeholder="Enter Your Password here">
									<div class="ue-error-success">
										<div class="ue-list-error">
											<p class="ue-show-error"></p>
											<p class="ue-show-success"></p>
										</div>
									</div>
									<div class="ue_pc_buttons">
										<span class="button-primary ue_ajax_enabled_password_check_pop" name="ue_password_check_submit"><?php echo __('Change Username', 'username-editor'); ?></span>
										<span class="button-secondary cancel-password-check"><?php echo __('Cancel', 'username-editor'); ?></span>
									</div>
								</div>
							</div>
							<?php
						} 
						else {
							echo $input;
						}
						wp_nonce_field('wg_ue_new_username_nonce', 'wg_ue_new_username_nonce_check'); 
						?>
						<p>
							<?php
							echo __("Any whitespace will be changed to underscore. If you type 'root 1', the generated username will be 'root_1'", "username-editor");
							?>
						</p>
					</td>
				</tr>
			</tbody>
		</table>
		<?php
	}
}

if ( ! function_exists('wg_ue_update_username_fields') ) {
	function wg_ue_update_username_fields(  ) {

		if ( ! wg_ue_settings_option()['ue_field_type'] == 'simple-input' ) {
			return;
		}

		$currentUser = wp_get_current_user();
		$userId = $currentUser->ID;
		$userEmail = $currentUser->user_email;
		$oldUsername = $currentUser->user_login;
		$baseUrl = admin_url( 'profile.php' );
		$requestedUsername = $_POST['wg_ue_new_username'];

		if (  is_user_logged_in() && wp_verify_nonce( $_POST['wg_ue_new_username_nonce_check'], 'wg_ue_new_username_nonce' ) && ! empty($requestedUsername)) {

			$newUsername = wg_ue_username_check( $requestedUsername );

			if ( wg_ue_check_username_limit( $requestedUsername ) === true ) {

				$queryArg = add_query_arg( array(
					'username_update'	=> 'failed',
					'message'			=> urlencode("Username can not be less than " . wg_ue_get_username_limit() . " alphabets"),
					), $baseUrl );
				wp_redirect( $queryArg );
				exit();
			}

			if ( $newUsername != false ) {

				remove_query_arg( 'username_update' );

				$result = wg_ue_sql_query($userId, esc_sql( $newUsername ) );
				wg_ue_send_email($userId, $userEmail, $oldUsername, $newUsername);

			} else {
				$queryArg = add_query_arg( array(
					'username_update'	=> 'failed',
					'message'			=> urlencode( 'Username exists.'),
					), $baseUrl );
				wp_redirect( $queryArg );
				exit();
			}

		}
	}
}
