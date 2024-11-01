<?php
if ( ! function_exists('wg_ue_main_page') ) {
	function wg_ue_main_page() {
		wg_ue_settings_header()
		?>
			<div class="wg-ue-main">
				<div class="wg-ue-left">
					<div class="wg-ue-left-inner">
						<form action="options.php" method="POST">
							<div class="wg-ue-plugin-header">
							</div>
							<?php
							settings_fields( 'wg_username_editor' );
							do_settings_sections( 'wg_username_editor' );
							submit_button('Save Settings');
							?>
						</form>
					</div>
				</div>
				<div class="wg-ue-right">
					<div class="wg-ue-right-inner">
						<div class="sidebar-item sidebar-item-1">
							<div class="sidebar-item-inner">
								<div class="wg-ue-sidebar-header">
									<h3>WordPress Resources</h3>
								</div>
								<div class="wg-ue-sidebar-content">
									<ul class="wg-ue-side-content">
										<li class="wg-ue-sc-1">
											<a target="blank" rel="nofollow" href="https://websiteguider.com/blog">WebsiteGuider's Blog</a>
										</li>
									</ul>
								</div>
							</div>
						</div>			
						<div class="sidebar-item sidebar-item-2">
							<div class="sidebar-item-inner">
								<div class="wg-ue-sidebar-header">
									<h3>WordPress Tools We Recommend</h3>
								</div>
								<div class="wg-ue-sidebar-content">
									<ul class="wg-ue-side-content">
										<li class="wg-ue-sc wg-ue-sc-1">
											<span>Hosting:</span><a rel="nofollow" target="_blank" href="https://www.greengeeks.com/track/raadin8/cp-websiteguider" class="button button-primary">Green Geeks</a>
										</li>						
										<li class="wg-ue-sc wg-ue-sc-3">
											<span>Theme:</span><a rel="nofollow" target="_blank" href="https://shareasale.com/r.cfm?b=1320631&u=2323235&m=41388&urllink=&afftrack=" class="button button-primary">Genesis Framework</a>
										</li>			
										<li class="wg-ue-sc wg-ue-sc-5">
											<span>SEO:</span><a rel="nofollow" target="_blank" href="https://shareasale.com/r.cfm?b=1537039&u=2323235&m=97231&urllink=&afftrack=" class="button button-primary">SEMrush</a>
										</li>
									</ul>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<?php    	
	}
}

if ( ! function_exists('wg_ue_checkbox_callback') ) {
	function wg_ue_checkbox_callback() {
		$checkboxConfirm = ! empty( wg_ue_settings_option()["ue_checkbox_confirm"] ) ? wg_ue_settings_option()["ue_checkbox_confirm"] : '';
		// Show checkbox
		$username_editor_settings = array();
		$html = '<input type="checkbox" id="checkbox_selected" name="wg_ue_settings[ue_checkbox_confirm]"' . checked( 1, $checkboxConfirm, false) . ' value="1"/>';
		echo $html;
	}
}

if ( ! function_exists('wg_ue_roles_callback') ) {
	function wg_ue_roles_callback() {
		global $wp_roles;
		$roles = $wp_roles->roles;
		$roleConfirm = ! empty( wg_ue_settings_option()["ue_roles_confirm"] ) ? wg_ue_settings_option()["ue_roles_confirm"] : '';

		foreach ($roles as $role) {
			$roleName = $role['name'];
			$output = sprintf('<input type="checkbox" id="ue_roles_checkbox" name="wg_ue_settings[ue_roles_confirm][]" value="%1$s" %2$s><label for="ue_roles_checkbox">%1$s</label><br>', 
				$roleName,
				checked( in_array($roleName, (array) $roleConfirm), 1, false )
			);
			echo $output;
		}
	}
}

if ( ! function_exists('wg_ue_email_callback') ) {
	function wg_ue_email_callback() {
		$emailConfirm = ! empty( wg_ue_settings_option()["ue_email_confirm"] ) ? wg_ue_settings_option()["ue_email_confirm"] : '';
		// Show checkbox
		$html = '<input type="checkbox" id="email_selected" name="wg_ue_settings[ue_email_confirm]"' . checked( 1, $emailConfirm, false) . ' value="1"/>';
		echo $html;
	}
}

if ( ! function_exists('wg_ue_user_field_type_callback') ) {
	function wg_ue_user_field_type_callback() {
		$fieldType = ! empty(wg_ue_settings_option()['ue_field_type'] ) ? wg_ue_settings_option()['ue_field_type'] : '';
		?>
		<select name='wg_ue_settings[ue_field_type]'>
			<option value='simple-input' <?php selected( $fieldType, 'simple-input' ); ?>><?php echo __('Simple Input', 'username-editor'); ?></option>
			<option value='ajax-field' <?php selected( $fieldType, 'ajax-field' ); ?>><?php echo __('Ajax Field', 'username-editor'); ?></option>
			<option value='ajax-field-password' <?php selected( $fieldType, 'ajax-field-password' ); ?>><?php echo __('Ajax Field With Password Check', 'username-editor'); ?></option>
	
		</select>
		<?php
	}
}

if ( ! function_exists('wg_ue_length_settings_callback') ) {
	function wg_ue_length_settings_callback() {
		$usernameLength = ! empty(wg_ue_settings_option()['ue_username_length']) ? wg_ue_settings_option()['ue_username_length'] : '';
		?>
		<input type="number" name="wg_ue_settings[ue_username_length]" value="<?php echo $usernameLength; ?>" min="3" max="18">
		<p><?php __("Mininum length can be '3' and maximum '18' by default", "username-editor");  ?></p>
		<?php
	}
}

if ( ! function_exists('wg_ue_change_user_list_handler') ) {
	function wg_ue_change_user_list_handler() {

		$fields = array( 'ID', 'user_login', 'display_name' );
		$args = array( 'fields' => $fields );
		$userList = get_users( $args );
		$currentUser = wp_get_current_user();
		?>
		<div class="wg-ue-user-list wrap">
			<h2>Users</h3>
			<table class="wp-list-table widefat table-view-list">
				<thead>
					<tr>
						<th><?php echo __( 'ID', 'wg-ue' ); ?></th>
						<th><?php echo __( 'Username', 'wg-ue' ); ?></th>
						<th><?php echo __( 'Display Name', 'wg-ue' ); ?></th>
						<th><?php echo __( 'Change Username', 'wg-ue' ); ?></th>
					</tr>
				</thead>
				<tbody>
					<?php
					foreach ( $userList as $user ) {
						if ( $currentUser->ID == $user->ID ) {
							continue;
						}

						$redirectUser = add_query_arg( 
							array(
								'user_id' => $user->ID
							), 
							wg_ue_change_admin_page() 
						);

						$nonceUrl = wp_nonce_url( $redirectUser, 'wg_ue_new_username_nonce_action' );
						?>
						<tr>

							<th><?php echo absint( $user->ID ); ?></th>
							<th><?php esc_html_e( $user->user_login ); ?></th>
							<th><?php esc_html_e( $user->display_name ); ?></th>
							<th><a href="<?php echo esc_url( $nonceUrl ); ?>">Change</a></th>
						</tr>
						<?php
					}
					?>
				</tbody>
			</table>
		</div>
		<?php
	}
}

if ( ! function_exists( 'wg_ue_change_username_handler' ) ) {
	function wg_ue_change_username_handler() {			
		if ( isset( $_GET['page'] ) && $_GET['page'] == 'wg_ue_change_username' && isset( $_GET['user_id'] )  ) {
			?>
			<form action="" method="POST">
				<div class="wg_ue_username_update">
					<p class="new-username-heading">Please enter the new username you wish to set.</p>
					<div class="wg_ue_username_update_inner">
						<input class="widefat" type="text" name="wg_ue_new_username" placeholder="Enter new username">
						<input type="submit" class="wg-tk-button" name="wg_ue_new_username_submit">
					</div>
				</div>
			</form>
			<?php 
		} else {
			if ( isset( $_GET['page'] ) && $_GET['page'] == 'wg_ue_change_username' && $_GET['username_update'] == 'true' ) {
				?>
				<div class="wg-ue-updated">
					<span class="dashicons dashicons-edit-page wg-ue-username-success"></span>
					<p>Username updated successfully.</p>
				</div>
				<?php
			} else {
				?>
				<div class="wg-ue-updated">
					<span class="dashicons dashicons-edit-page wg-ue-username-failed"></span>
					<p>Username not updated.</p>
				</div>
				<?php 
			}
		}
	}
}

