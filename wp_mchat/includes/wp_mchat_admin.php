<?php

/*
 * @class - wp_mchat_admin
 */

if( ! defined( 'WPMCHAT_FILE' ) ) die( 'Silence ' );

if( ! class_exists('wp_mchat_admin')):
class wp_mchat_admin
{
	var $date_format;
	var $default_colors;
	function __construct()
	{
		global $wpdb;
		$this->date_format		= 'j M Y h:i A';
		$this->default_colors		= ['headerbg' => '#000000', 'headerfg' => '#ffffff', 'chatbg' => '#ffffff', 'chatbgs' => '#ffffff', 'chatfgs' => '#000000', 
								 'chatbgr' => '#dedec4', 'chatfgr' => '#000000', 'chatbutbg' => '#0073aa', 'chatbutfg' => '#fff'];

		add_action( 'admin_enqueue_scripts'		, array( &$this, 'wpmchat_admin_style'	));
	}


	/*
	*
	**/
	function wpmchat_admin_style()
	{
		if( is_admin() && isset( $_GET['page'] ) && strpos( $_GET['page'], 'wpmchat' ) !== false )
		{
			wp_enqueue_style( 'wpmchat_style', WPMCHAT_URL. 'assets/css/admin.css' );
			wp_enqueue_script( 'jquery' );
			wp_enqueue_script( 'jquery-ui-tabs' );
			wp_enqueue_style( 'wp-color-picker' );
			wp_enqueue_script( 'wp-color-picker' );
		}
	}

	/**
	* filters colors with defaults.
	*
	*
	*
	**/
	function wpmchat_get_colors($wpmchat_settings)
	{
		extract($this->default_colors);

		$wpmchat_settings['headerbg'] 	= ( isset($wpmchat_settings['headerbg'])? $wpmchat_settings['headerbg']: $headerbg );
		$wpmchat_settings['headerfg'] 	= ( isset($wpmchat_settings['headerfg'])? $wpmchat_settings['headerfg']: $headerfg );
		$wpmchat_settings['chatbg'] 		= ( isset($wpmchat_settings['chatbg'])? $wpmchat_settings['chatbg']: $chatbg );
		$wpmchat_settings['chatbgs'] 		= ( isset($wpmchat_settings['chatbgs'])? $wpmchat_settings['chatbgs']: $chatbgs );
		$wpmchat_settings['chatfgs'] 		= ( isset($wpmchat_settings['chatfgs'])? $wpmchat_settings['chatfgs']: $chatfgs );
		$wpmchat_settings['chatbgr'] 		= ( isset($wpmchat_settings['chatbgr'])? $wpmchat_settings['chatbgr']: $chatbgr );
		$wpmchat_settings['chatfgr'] 		= ( isset($wpmchat_settings['chatfgr'])? $wpmchat_settings['chatfgr']: $chatfgr );
		$wpmchat_settings['chatbutbg'] 	= ( isset($wpmchat_settings['chatbutbg'])? $wpmchat_settings['chatbutbg']: $chatbutbg );
		$wpmchat_settings['chatbutfg'] 	= ( isset($wpmchat_settings['chatbutfg'])? $wpmchat_settings['chatbutfg']: $chatbutfg );

		return $wpmchat_settings;
	}

	function get_roles_list( $selected = array() )
	{
		$r = '';
 
		if( empty( $selected ) ) $selected = [];
		$editable_roles = get_editable_roles();
		if( ! empty( $editable_roles ) ) {
		$r .= "\n\t<label><input type='checkbox' name='roles_all' id='role-all' value='1'> <strong>Select All</strong></label><br/>";

		foreach ( $editable_roles as $role => $details ) {
			$name = translate_user_role( $details['name'] );
			// Preselect specified role.
			if ( in_array( $role, $selected ) ) {
				$r .= "\n\t<label><input type='checkbox' name='roles[]' id='role-" . esc_attr( $role ) . "' checked='checked' value='" . esc_attr( $role ) . "'> $name</label><br/>";
			} else {
				$r .= "\n\t<label><input type='checkbox' name='roles[]' id='role-" . esc_attr( $role ) . "' value='" . esc_attr( $role ) . "'> $name</label><br/>";
			}
		}}
		return $r;
	}

	/*
	*
	**/
	function wpmchat_main()
	{
		global $wpdb;

		if (!current_user_can('manage_options')) wp_die(__('Sorry, but you have no permissions to change settings.'));

		$wpmchat_settings = get_option( 'wpmchat_settings' );
		if( isset( $_POST['call'] ) && $_POST['call'] == 'save' )
		{
			extract($this->default_colors);
			$wpmchat_settings['branding'] 	= ( isset($_POST['wpmchat_branding'])? 1 : 0 );
			$wpmchat_settings['uninstall'] 	= ( isset($_POST['wpmchat_uninstall'])? 1 : 0 );

			$wpmchat_settings['headerbg'] 	= ( isset($_POST['wpmchat_headerbg'])? trim( sanitize_text_field( $_POST['wpmchat_headerbg'] ) ) : $headerbg );
			$wpmchat_settings['headerfg'] 	= ( isset($_POST['wpmchat_headerfg'])? trim( sanitize_text_field( $_POST['wpmchat_headerfg'] ) ) : $headerfg );
			$wpmchat_settings['chatbg'] 		= ( isset($_POST['wpmchat_chatbg'])? trim( sanitize_text_field( $_POST['wpmchat_chatbg'] ) ) : $chatbg );
			$wpmchat_settings['chatbgs'] 		= ( isset($_POST['wpmchat_chatbgs'])? trim( sanitize_text_field( $_POST['wpmchat_chatbgs'] ) ) : $chatbgs );
			$wpmchat_settings['chatfgs'] 		= ( isset($_POST['wpmchat_chatfgs'])? trim( sanitize_text_field( $_POST['wpmchat_chatfgs'] ) ) : $chatfgs );
			$wpmchat_settings['chatbgr'] 		= ( isset($_POST['wpmchat_chatbgr'])? trim( sanitize_text_field( $_POST['wpmchat_chatbgr'] ) ) : $chatbgr );
			$wpmchat_settings['chatfgr'] 		= ( isset($_POST['wpmchat_chatfgr'])? trim( sanitize_text_field( $_POST['wpmchat_chatfgr'] ) ) : $chatfgr );
			$wpmchat_settings['chatbutbg'] 	= ( isset($_POST['wpmchat_chatbutbg'])? trim( sanitize_text_field( $_POST['wpmchat_chatbutbg'])): $chatbutbg );
			$wpmchat_settings['chatbutfg'] 	= ( isset($_POST['wpmchat_chatbutfg'])? trim( sanitize_text_field( $_POST['wpmchat_chatbutfg'])): $chatbutfg );

			$wpmchat_settings['email_send']	= ( isset($_POST['wpmchat_email_send'])? 1 : 0 );
			$wpmchat_settings['email_from']	= ( isset($_POST['wpmchat_email_from'])? trim( sanitize_text_field( $_POST['wpmchat_email_from'] ) ) : '' );
			$wpmchat_settings['email_email']	= ( isset($_POST['wpmchat_email_email'])? trim( sanitize_text_field( $_POST['wpmchat_email_email'] ) ) : '' );
			$wpmchat_settings['email_subject']	= ( isset($_POST['wpmchat_email_subject'])? trim( sanitize_text_field( $_POST['wpmchat_email_subject'] ) ) : '' );
			$wpmchat_settings['email_body']	= ( isset($_POST['wpmchat_email_body'])? trim( sanitize_textarea_field( $_POST['wpmchat_email_body'] ) ) : '' );

			$roles 					= $_POST['roles'];

			$editable_roles = get_editable_roles();
			$editable_roles = array_keys( $editable_roles );

			$new_roles = [];
			foreach( $roles as $role ) {
				$role = trim( sanitize_text_field( $role ) );
				if( in_array( $role, $editable_roles ) )
					$new_roles[] = $role;
			}
			$wpmchat_settings['role_list']	= $new_roles;

			$wpmchat_settings = apply_filters( 'wpmchat_save_settings', $wpmchat_settings );

			update_option( 'wpmchat_settings', $wpmchat_settings );
			$result1 = __('Settings have been saved','wpmchat_lang');

		}

		require_once __DIR__.'/templates/admin/wpmchat_main.php';

		wp_mchat::wpmchat_page_footer();
	} 
}
endif;

global $wp_mchat_admin;
if( ! $wp_mchat_admin ) $wp_mchat_admin = new wp_mchat_admin();