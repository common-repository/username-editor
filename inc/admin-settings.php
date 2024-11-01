<?php

if( ! function_exists('wg_ue_register_menu_pages') ) {
    function wg_ue_register_menu_pages() {

        add_menu_page( __('Username Editor', 'username-editor'), __('Username Editor', 'username-editor'), 'manage_options', 'wg_ue', 'wg_ue_main_page', 'dashicons-edit-page' );
        add_submenu_page('wg_ue', __('User Settings', 'username-editor'), __('Users', 'username-editor'), 'manage_options', 'wg_ue_user_list', 'wg_ue_change_user_list_handler');

        add_submenu_page( null, __('Change Username', 'username-editor'), __('Change Username', 'username-editor'), 'manage_options', 'wg_ue_change_username', 'wg_ue_change_username_handler' );

    }
}

if ( ! function_exists('wg_ue_settings_field_register') ) {
    function wg_ue_settings_field_register() {

            register_setting( 'wg_username_editor', 'wg_ue_settings' );
            add_settings_section( 'wg_ue_confirmation', false, false, 'wg_username_editor' );

            add_settings_field( 
                'wg_ue_checkboxes', 
                __( 'Enable Username Editor', 'username_editor' ), 
                'wg_ue_checkbox_callback',
                'wg_username_editor',
                'wg_ue_confirmation'
            );

            add_settings_section( 'wg_ue_roles', 'Roles', false, 'wg_username_editor' );

            add_settings_field( 
                'wg_ue_roles_settings', 
                __( 'Select roles which can edit usernames', 'username_editor' ), 
                'wg_ue_roles_callback',
                'wg_username_editor',
                'wg_ue_roles',
                array('class' => 'ue_user_roles')
            );

            add_settings_section( 'wg_ue_email', 'Email', false, 'wg_username_editor' );

            add_settings_field( 
                'wg_ue_email_settings', 
                __( 'Send Email on username change', 'username_editor' ), 
                'wg_ue_email_callback',
                'wg_username_editor',
                'wg_ue_email',
                array('class' => 'ue_email_settings')
            );

            add_settings_section( 'wg_ue_field_options', 'Field Options', false, 'wg_username_editor' );

            add_settings_field( 
                'wg_ue_user_field_type_settings', 
                __( 'Select how you want the field to behave', 'username_editor' ), 
                'wg_ue_user_field_type_callback',
                'wg_username_editor',
                'wg_ue_field_options',
                array('class' => 'ue_user_field_type_settings')
            );

            add_settings_field( 
                'wg_ue_length_settings', 
                __( 'Minimum Username Length', 'username_editor' ), 
                'wg_ue_length_settings_callback',
                'wg_username_editor',
                'wg_ue_field_options',
                array('class' => 'ue_user_field_type_settings')
            );
    }
}

add_action( 'admin_init', 'wg_ue_username_has_changed_handler' );
if ( ! function_exists( 'wg_ue_username_has_changed_handler' ) ) {
    function wg_ue_username_has_changed_handler() {
        if ( isset( $_POST['wg_ue_new_username_submit'] ) && check_admin_referer('wg_ue_new_username_nonce_action', '_wpnonce') ) {

            $userId = absint($_GET['user_id']);

            if ( ! empty( $_POST['wg_ue_new_username'] ) ) {
                $newUsername = wg_ue_username_sanitize( $_POST['wg_ue_new_username'] );

                if ( wg_ue_username_check( $newUsername ) == false ) {
                    echo '<script>alert("Username exists");</script>';
                } else if( wg_ue_check_username_limit( $newUsername ) == true ) {
                    echo '<script>alert("Username must be atleast ' . wg_ue_get_username_limit() .' alphanumeric");</script>';
                } else {
                    $sqlQuery = wg_ue_sql_query( $userId, esc_sql( $newUsername ) );
                }

                if ( $sqlQuery ) {
                    $successArgs = add_query_arg( array( 'username_update' => 'true' ), wg_ue_change_admin_page());
                    wp_safe_redirect( $successArgs );
                    exit;
                }
            } else {
                echo '<script>alert("Username field can not be empty.");</script>';
            }
        }
    }
}