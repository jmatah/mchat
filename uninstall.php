<?php
/**
 *	Uninstall Front End PM
 *
 *	Deletes all the plugin data
 */

// Exit if accessed directly.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

global $wpdb;

if( ! defined( "WPMCHAT_CHAT_TABLE" ) ) {
	define( "WPMCHAT_CHAT_TABLE"			, $wpdb->prefix . "wpmchat_chat"		);
	define( "WPMCHAT_CHAT_MESG_TABLE"		, $wpdb->prefix . "wpmchat_chatmesg"	);
}
$wpmchat_settings = get_option( 'wpmchat_settings' );

if ( is_array( $wpmchat_settings ) && $wpmchat_settings['uninstall'] == 1 ) {

	/** Delete all the Plugin Options */
	delete_option( 'wpmchat_settings' );
	delete_option( 'wpmchat_ver' );

	$wpdb->query( "DROP TABLE IF EXISTS `".WPMCHAT_CHAT_TABLE."`" );
	$wpdb->query( "DROP TABLE IF EXISTS `".WPMCHAT_CHAT_MESG_TABLE."`" );

	// Remove any transients we've left behind
	$wpdb->query( "DELETE FROM $wpdb->options WHERE option_name LIKE '\_transient\_wpmchat\_user\_online%'" );
}
