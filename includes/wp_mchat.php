<?php

/*
 * @class - wp_mchat
 */

if( ! defined( 'WPMCHAT_FILE' ) ) die( 'Silence ' );

if( ! class_exists('wp_mchat')):
class wp_mchat
{
	function __construct()
	{
		global $wpdb;

		//few definitions
		define( "WPMCHAT_DIR" 				, plugin_dir_path( WPMCHAT_FILE ) 		);
		define( "WPMCHAT_URL"				, esc_url( plugins_url( '', WPMCHAT_FILE ) ).'/');

		define( "WPMCHAT_VER"				, "1.0.0" 						);
		define( "WPMCHAT_DEBUG"				, false						);

		define( "WPMCHAT_ADMIN_PER_PAGE"		, 50							);
		define( "WPMCHAT_FRONT_PER_PAGE"		, 75							);

		define( "WPMCHAT_CHAT_TABLE"			, $wpdb->prefix . "wpmchat_chat"		);
		define( "WPMCHAT_CHAT_MESG_TABLE"		, $wpdb->prefix . "wpmchat_chatmesg"	);

		register_activation_hook( WPMCHAT_FILE	, array( &$this, 'wpmchat_activate'		));
		register_deactivation_hook ( WPMCHAT_FILE	, array( &$this, 'wpmchat_deactivate'	));

		add_action( 'admin_menu'			, array( &$this, 'wpmchat_options_page'	));
 
		add_filter( 'plugin_action_links'		, array( &$this, 'wpmchat_plugin_actions'	), 10, 2 );
	}

	function wpmchat_activate()
	{
		global $wpdb;

		if($wpdb->get_var("SHOW TABLES LIKE '". WPMCHAT_CHAT_TABLE ."'") != WPMCHAT_CHAT_TABLE  ) {
			require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
			/**
			*
			* Status : 0 - active, 1 - user1 has blocked user2, 2 - user2 has blocked user 1;
			*
			*
			**/
			dbDelta( 
				"CREATE TABLE `". WPMCHAT_CHAT_TABLE . "` (
				`id` bigint(11) NOT NULL auto_increment,
				`user_id1` int(11) NOT NULL DEFAULT 0,
				`user_id2` int(11) NOT NULL DEFAULT 0,
				`seen1` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
				`seen2` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
				`status` int(11) NOT NULL DEFAULT 0,
				PRIMARY KEY  (`id`),
				KEY `k_user_id` (`user_id1`, `user_id2` )
				) ENGINE=MyISAM DEFAULT CHARSET=latin1 ;"
			);

			/**
			*
			*
			* send_id - user_id of the user that posted it, do we need a receiver id?
			*
			**/
			dbDelta( 
				"CREATE TABLE `". WPMCHAT_CHAT_MESG_TABLE . "` (
				`id` bigint(11) NOT NULL auto_increment,
				`chat_id` int(11) NOT NULL DEFAULT 0,
				`sender_id` int(11) NOT NULL DEFAULT 0,
				`message` text,
				`posted` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
				PRIMARY KEY  (`id`),
				KEY `k_chat_id` (`chat_id`, `posted`)
				) ENGINE=MyISAM DEFAULT CHARSET=latin1 ;"
			);
		}

		if( ! $wpmchat_ver = get_option ("wpmchat_ver") )
			update_option ("wpmchat_ver", WPMCHAT_VER);
	}

	function wpmchat_deactivate()
	{
		//nothing here//
	}
	static function wpmchat_footer() 
	{
		$plugin_data = get_plugin_data( WPMCHAT_FILE );
		printf('%1$s plugin | Version %2$s | by %3$s<br />', $plugin_data['Title'], $plugin_data['Version'], $plugin_data['Author']); 
	}

	static function wpmchat_page_footer() {
		echo '<br/><div id="page_footer" class="postbox" style="text-align:center;padding:10px;clear:both"><em>';
		self::wpmchat_footer(); 
		echo '</em></div>';
	}

	function wpmchat_plugin_actions($links, $file)
	{
		if( strpos( $file, basename(WPMCHAT_FILE)) !== false )
		{
			$link = '<a href="'.admin_url( 'admin.php?page=wpmchatmain').'">'.__('Settings', 'wpmchat_lang').'</a>';
			array_unshift( $links, $link );
		}
		return $links;
	}

	function wpmchat_options_page()
	{
		global $wp_mchat_admin;
		add_menu_page('MChat', 'MChat', 'manage_options', 'wpmchatmain', array( &$wp_mchat_admin, 'wpmchat_main' ), 'dashicons-format-chat');
		add_submenu_page('wpmchatmain', 'MChat - Settings', 'Settings', 'manage_options', 'wpmchatmain', array( &$wp_mchat_admin, 'wpmchat_main' ) );
	}
}
endif;

require_once __DIR__.'/wp_mchat_admin.php';
require_once __DIR__.'/wp_mchat_front.php';

global $wp_mchat;
if( ! $wp_mchat ) $wp_mchat = new wp_mchat();