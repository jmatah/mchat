<?php

/*
 * @class - wp_mchat_front
 */

if( ! defined( 'WPMCHAT_FILE' ) ) die( 'Silence ' );

if( ! class_exists('wp_mchat_front')):
class wp_mchat_front
{
	var $date_format;
	var $requested_page;

	function __construct()
	{
		global $wpdb;
		$this->date_format				= 'j M Y h:i A';
		$this->requested_page				= '';

		add_action( 'init'				, array( &$this, 'wpmchat_session_start'		), 0 );
		add_action( 'wp_footer'				, array( &$this, 'wpmchat_enqueue_scripts'		), 1 );

		add_action( 'delete_user'			, array( &$this, 'wpmchat_delete_user'		), 10, 3 );

		add_action( 'wp_ajax_wpmchat_chat_action'		, array( &$this, 'wpmchat_ajax_chat_action'	));
		add_action( 'wp_ajax_nopriv_wpmchat_chat_action', array( &$this, 'wpmchat_ajax_chat_action'	));

		add_shortcode( 'MCHAT'				, array( &$this, 'wpmchat_chat_button'		) );

	}

	function wpmchat_session_start()
	{
		if( ! session_id() ) 
			session_start();
	}

	/*
	*
	**/
	function wpmchat_enqueue_scripts()
	{
		global $wp_mchat_admin;
		$wpmchat_settings = get_option( 'wpmchat_settings' );

		$wpmchat_settings = $wp_mchat_admin->wpmchat_get_colors($wpmchat_settings);

		$wpmchat_settings = array_map( 'esc_attr', $wpmchat_settings );

		extract( $wpmchat_settings );

		wp_enqueue_style( 'dashicons' );
		wp_enqueue_style( 'wpmchat-css'		, WPMCHAT_URL.'assets/css/common.css' );

		$custom_css = <<<HTML
#wpmchat_wrapper_inbox h3, #wpmchat_wrapper_message h3{ background-color:{$headerbg}; color:{$headerfg};}
#wpmchat_wrapper_inbox #wpmchat_messages, #wpmchat_wrapper_message #wpmchat_message_list{background-color:{$chatbg};}
#wpmchat_wrapper_message .wpmchat_chat_msg.reply{ background-color: {$chatbgr}; color:{$chatfgr};}
#wpmchat_wrapper_message .wpmchat_chat_msg.author{ background-color: {$chatbgs}; color:{$chatfgs};}
#wpmchat_wrapper_message #wpmchat_message_list #wpmchat_write_submit{background-color: {$chatbutbg}; color: {$chatbutfg};}
#wpmchat_wrapper_message #wpmchat_message_list #wpmchat_write_submit:hover {background-color: {$chatbutbg}; color: {$chatbutfg};}
HTML;
		wp_add_inline_style( 'wpmchat-css'		, $custom_css );

		wp_enqueue_script( 'wpmchat-common'		, WPMCHAT_URL.'assets/js/common.js'	, array('jquery') );
		$params = array(
			'wpmchat_url' => WPMCHAT_URL,
			'ajaxurl' => admin_url('admin-ajax.php'),
			'ajax_nonce' => wp_create_nonce( 'wpmchat_ajax' ),
		);
		wp_localize_script( 'wpmchat-common', 'wpmchat_ajax_object', $params );
	}

	function wpmchat_delete_user( $user_id, $reassign, $user )
	{
		global $wpdb;
		$chats = $wpdb->get_results("SELECT id FROM `". WPMCHAT_CHAT_TABLE . "` WHERE ( user_id1 = ".$user_id." OR user_id2 = ".$user_id.")");
		$all_chats = [];
		foreach( $chats as $chat ) {
			$all_chats[] = $chat->id;
		}

		$wpdb->query("DELETE FROM `". WPMCHAT_CHAT_MESG_TABLE . "` WHERE chat_id IN ( ".implode(",", $all_chats ) ." ) " );
		$wpdb->query("DELETE FROM `". WPMCHAT_CHAT_TABLE . "` WHERE id IN ( ".implode(",", $all_chats ) ." ) " );
	}

	/**
	* 
	*
	**/
	function get_init_html()
	{
		$current_user = wp_get_current_user();
		require_once apply_filters( 'wpmchat_get_init_html', __DIR__.'/templates/front/wpmchat_inbox.php' );
		return $html;
	}

	/**
	*
	*
	**/
	function get_latest_inbox()
	{
		global $wpdb;

		$arr = [];

		if( ! is_user_logged_in() ) {
			$arr[] = ['id'=>"", 'display_name'=>'Please log in to see your Inbox', 'is_read' => 'is_unread', 'date'=>''];
			return json_encode( ['inbox'=>$arr,'updated'=>current_time('timestamp')] );
		}

	/*
		$arr[] = ['id'=>2, 'display_name'=>'4rahul2435', 'is_read' => 'is_read', 'date'=>'2021-12-22 08:10:00'];
	*/
		$chats = $wpdb->get_results("SELECT c.id, user_id1, user_id2, seen1, seen2, status, m.posted, m.sender_id FROM `". WPMCHAT_CHAT_TABLE . "` c
								LEFT JOIN `". WPMCHAT_CHAT_MESG_TABLE . "` m
								ON c.id = m.chat_id
								INNER JOIN ( SELECT chat_id, MAX(posted) as posted FROM `wp_wpmchat_chatmesg`
									WHERE 
										1
									GROUP BY chat_id ) im
								ON
									m.posted = im.posted AND 
									m.chat_id = im.chat_id
								WHERE
									( user_id1 = ".get_current_user_id()."  OR user_id2 = ".get_current_user_id().")
								ORDER BY m.posted	DESC 
								LIMIT 50");

		$play_a_sound = 'no';
		foreach( $chats as $chat ) {
			$arr2 = [];
			$arr2['id'] = $chat->id;
			if( $chat->user_id1 == get_current_user_id() ) {
				$seen = 'seen1';
				$arr2['display_name'] = get_userdata( $chat->user_id2 )->display_name;
				$arr2['is_online'] = ( $this->is_user_online( $chat->user_id2 ) !== false? 'is_online': 'is_offline' );
			}
			else {
				$seen = 'seen2';
				$arr2['display_name'] = get_userdata( $chat->user_id1 )->display_name;
				$arr2['is_online'] = ( $this->is_user_online( $chat->user_id1 ) !== false? 'is_online': 'is_offline' );
			}
			$last_mesg = $chat->posted;

			if( strtotime( $last_mesg ) > strtotime( $chat->$seen ) ) {
				$arr2['is_read'] = ' is_unread ';
				if( $chat->sender_id != get_current_user_id() )
					$play_a_sound = 'yes';
			}
			else {
				$arr2['is_read'] = 'is_read ';
			}

			$arr2['date'] = date( 'Y-m-d H:i:s', strtotime( $last_mesg ) );

			//blocked
			if( $chat->status > 0 ) {
				if( ( $chat->user_id1 == get_current_user_id() && $chat->status == 1 ) || ( $chat->user_id2 == get_current_user_id() && $chat->status == 2 ) ) {
					$arr2['blocked'] = 'blocked';
				}
				else if( ( $chat->user_id1 == get_current_user_id() && $chat->status == 2 ) || ( $chat->user_id2 == get_current_user_id() && $chat->status == 1 ) ) {
					$arr2['blocked'] = '';
				}
			}

			$arr[] = $arr2;
		}

		return json_encode( ['inbox'=>$arr,'play_a_sound'=>$play_a_sound,'updated'=>current_time('timestamp')] );
	}

	function get_latest_chat( $chat_id, $user_id )
	{
		global $wpdb;
	/*
		$arr2 = [];
		$arr2[] = ['id'=>1, 'by_user'=>'reply', 'display_name'=>'1rahul2435', 'is_read' => 'is_read', 'chattxt'=>'Hello', 'date'=>'2021-12-24 08:10:00'];
	*/

		if( ! empty( $user_id ) && $user_id == get_current_user_id() ) {
			return json_encode( array( 'err'=> sprintf( __('Error %d: Something went wrong, plesase refresh page and try again = ','wpmchat_lang' ), __LINE__ ) ) );
		}

		if( empty( $chat_id ) && ! empty( $user_id ) ) {
			$chat = $wpdb->get_row("SELECT id, user_id1, user_id2, seen1, seen2 FROM `". WPMCHAT_CHAT_TABLE . "` WHERE 
								( user_id1 = ".get_current_user_id()." AND user_id2 = ".$user_id.") OR (user_id1 = ".$user_id." AND user_id2 = ".get_current_user_id().")");
		}
		else if( ! empty( $chat_id ) ) {
			$chat = $wpdb->get_row("SELECT id, user_id1, user_id2, seen1, seen2 FROM `". WPMCHAT_CHAT_TABLE . "` WHERE id=".$chat_id );
		}

		if( ! empty( $chat ) )
			$chat_id = $chat->id;

		if( ! empty( $chat ) ) {
			$seen = '';
			$from = '';

			if( $chat->user_id1 == get_current_user_id() ) {
				$seen = 'seen1';
				$from = get_userdata( $chat->user_id2 )->display_name;
			}
			else {
				$seen = 'seen2';
				$from = get_userdata( $chat->user_id1 )->display_name;
			}
			$time_seen = strtotime( $chat->$seen );

			$chat_msgs = $wpdb->get_results("SELECT * FROM `". WPMCHAT_CHAT_MESG_TABLE . "` WHERE chat_id=".$chat_id." ORDER BY posted DESC LIMIT ".WPMCHAT_FRONT_PER_PAGE );

			$arr2 = [];
			if( ! empty( $chat_msgs ) ) {
			foreach( $chat_msgs as $msgs ) {
				$arr = [];	
				$arr['id'] 			= $msgs->id;
				$arr['by_user'] 		= ( $msgs->sender_id == get_current_user_id()? 'author': 'reply' );
				$arr['display_name'] 	= get_userdata( $arr['by_user'] )->display_name;
				$arr['is_read'] 		= ( $time_seen < strtotime( $msgs->posted )? 'is_unread': 'is_read' );
				$arr['chattxt'] 		= wp_kses_post( $msgs->message );
				$arr['date'] 		= date( 'Y-m-d H:i:s', strtotime( $msgs->posted ) );

				$arr2[] = $arr;
			}}

			$seen_arr = [];
			$seen_arr[$seen] = date('Y-m-d H:i:s', current_time('timestamp'));
			$wpdb->update( WPMCHAT_CHAT_TABLE, $seen_arr, array( 'id'=> $chat_id) );
		}
		else {
			$from = get_userdata( $user_id )->display_name;
		}

		if( ! empty( $arr2 ) )
			$arr2 = array_reverse( $arr2 );

		$arr = [];
		$arr['chatid'] 	= (int)$chat_id;
		$arr['userid'] 	= (int)$user_id;
		$arr['from'] 	= esc_attr( $from );
		$arr['msgs'] 	= $arr2;
		$arr['updated'] 	= current_time('timestamp');
		return json_encode( $arr );
	}

	/**
	* @ user_id = the message has been sent to;
	*
	* send one email per 5 minutes, .... dont want an email for evevry chat message!
	*
	*
	**/
	function maybe_send_notification_email( $user_id )
	{
		if( get_transient( 'wpmchat_email-'.$user_id ) !== false )
			return;

		// for the next 5 mintes no emails;//
		set_transient( 'wpmchat_email-'.$user_id, 'sent', (5 * 60 ) );

		$wpmchat_settings = get_option( 'wpmchat_settings' );

		$to = get_userdata( $user_id );
		$to = $to->display_name.' <'.$to->user_email .'>';

		$from = esc_attr( $wpmchat_settings['email_from'] ) .'<'.esc_attr( $wpmchat_settings['email_email'] ).'>';

		$subject = esc_attr( $wpmchat_settings['email_subject'] );

		$body = wp_kses_post( wpautop( $wpmchat_settings['email_body'] ) );
		$headers = array('Content-Type: text/html; charset=UTF-8', 'From: '.$from, 'Reply-To: '.$from );

		if( apply_filters( 'wpmchat_maybe_send_notification_email', true, $user_id ) ) {
			$z = wp_mail( $to, $subject, $body, $headers );
		}
		return $z;
	}

	function wpmchat_user_can( $user_id )
	{
		$wpmchat_settings = get_option( 'wpmchat_settings' );

		$roles = get_userdata( $user_id )->roles;

		$ret = array_intersect( $wpmchat_settings['role_list'], $roles );

		$ret = apply_filters( 'wpmchat_user_can', (!! $ret), $user_id );

		return $ret;
	}

	/**
	*
	* @ chat_id - chat id
	* @ user_id - the other user on one to one chat.
	* @ msg - message posted.
	*
	*
	**/
	function update_chat( $chat_id, $user_id, $msg )
	{
		global $wpdb;

		if( ! is_user_logged_in() ) {
			return json_encode( array( 'err'=> sprintf( __('Error %d: Please login to message this user.','wpmchat_lang' ), __LINE__ ) ) );
		}

		if( ! empty( $user_id ) && $user_id == get_current_user_id() ) {
			return json_encode( array( 'err'=> sprintf( __('Error %d: Something went wrong, plesase refresh page and try again = ','wpmchat_lang' ), __LINE__ ) ) );
		}

		if( empty( $chat_id ) && ! empty( $user_id ) ) {
			$chat = $wpdb->get_row("SELECT id, user_id1, user_id2, status FROM `". WPMCHAT_CHAT_TABLE . "` WHERE 
								( user_id1 = ".get_current_user_id()." AND user_id2 = ".$user_id.") OR (user_id1 = ".$user_id." AND user_id2 = ".get_current_user_id().")");
		}
		else if( ! empty( $chat_id ) ) {
			$chat = $wpdb->get_row("SELECT id, user_id1, user_id2, status FROM `". WPMCHAT_CHAT_TABLE . "` WHERE id=".$chat_id );
		}

		if( ! empty( $chat ) && empty( $user_id ) ) {
			$chat_id = $chat->id;
			if( $chat->user_id1 == get_current_user_id() )
				$user_id = $chat->user_id2;
			else
				$user_id = $chat->user_id1;
		}

		//blocked
		if( ! empty( $chat ) && $chat->status > 0 ) {
			if( ( $chat->user_id1 == get_current_user_id() && $chat->status == 1 ) || ( $chat->user_id2 == get_current_user_id() && $chat->status == 2 ) ) {
				return json_encode( array( 'err'=> sprintf( __('Error %d: You have blocked this user from chatting with you.','wpmchat_lang' ), __LINE__ ) ) );
			}
			else if( ( $chat->user_id1 == get_current_user_id() && $chat->status == 2 ) || ( $chat->user_id2 == get_current_user_id() && $chat->status == 1 ) ) {
				return json_encode( array( 'err'=> sprintf( __('Error %d: User has left this chat room.','wpmchat_lang' ), __LINE__ ) ) );
			}
		}

		if( empty( $chat_id ) ) {
			$arr = [];
			$arr['user_id1'] = get_current_user_id();
			$arr['user_id2'] = $user_id;
			$arr['seen1'] = date('Y-m-d H:i:s', current_time('timestamp'));
			$arr['seen2'] = date('Y-m-d H:i:s', current_time('timestamp'));
			$arr['status'] = 0;
			$wpdb->insert( WPMCHAT_CHAT_TABLE, $arr );
			$chat_id = $wpdb->insert_id;
		}

		$arr = [];
		$arr['chat_id'] = $chat_id;
		$arr['message'] = wp_kses_post( $msg );
		$arr['sender_id'] = get_current_user_id();
		$arr['posted'] = date('Y-m-d H:i:s', current_time('timestamp'));
		$wpdb->insert( WPMCHAT_CHAT_MESG_TABLE, $arr );

		$this->maybe_send_notification_email( $user_id );

		$arr = [];
		$arr['chatid'] = $chat_id;
		$arr['userid'] = $user_id;
		$arr['posted'] = 1;
		return json_encode( $arr );
	}

	function set_user_online()
	{
		if( is_user_logged_in() ){
			set_transient('wpmchat_user_online-'.get_current_user_id(), 'active', 15);
		}
	}

	function is_user_online( $user_id )
	{
		return ( get_transient('wpmchat_user_online-'.$user_id ) !== false ? true: false );
	}

	function wpmchat_heartbeat( $chat_id, $user_id, $inbox_updated )
	{
		global $wpdb;

		$ret = [];
		$ret['inbox'] = [];
		$ret['msgs'] = [];

		//last_message for this user//
		$last_message = $wpdb->get_var("SELECT MAX(posted) as posted FROM `". WPMCHAT_CHAT_TABLE . "` c, `". WPMCHAT_CHAT_MESG_TABLE . "` m 
							WHERE 
								c.id = m.chat_id AND 
								( user_id1 = ".get_current_user_id()."  OR user_id2 = ".get_current_user_id().")
								/* AND sender_id != ".get_current_user_id()." */
							GROUP BY m.chat_id
							ORDER BY posted DESC 
							LIMIT 1");

		//get inbox - whole of it//
		if( strtotime( $last_message ) > $inbox_updated ){
			$xx = $this->get_latest_inbox();
			$xx = json_decode( $xx, true );
			$ret['inbox'] = $xx['inbox'];
			$ret['play_a_sound'] = $xx['play_a_sound'];
		}

		// get messages, only latest //
		if( $chat_id > 0 && strtotime( $last_message ) > $inbox_updated ){
			$chat_msgs = $wpdb->get_results("SELECT * FROM `". WPMCHAT_CHAT_MESG_TABLE . "` WHERE chat_id=".$chat_id." AND posted > '".date("Y-m-d H:i:s", $inbox_updated )."' ORDER BY posted LIMIT ".WPMCHAT_FRONT_PER_PAGE );
			$arr2 = [];

			if( ! empty( $chat_msgs ) ) {
			foreach( $chat_msgs as $msgs ) {
				//dont add users own messages to the chat; already there
				if( $msgs->sender_id == get_current_user_id() ) 
					continue;

				$arr = [];	
				$arr['id'] 			= $msgs->id;
				$arr['by_user'] 		= ( $msgs->sender_id == get_current_user_id()? 'author': 'reply' );
				$arr['display_name'] 	= get_userdata( $arr['by_user'] )->display_name;
				$arr['is_read'] 		= ( $time_seen < strtotime( $msgs->posted )? 'is_unread': 'is_read' );
				$arr['chattxt'] 		= wp_kses_post( $msgs->message );
				$arr['date'] 		= $msgs->posted;
	
				$arr2[] = $arr;
			}}
			$ret['msgs'] = $arr2;
		}

		$ret['chatid'] = $chat_id;
		$ret['userid'] = $user_id;
		$ret['updated'] = current_time('timestamp');
		return json_encode( $ret );
	}

	function block_user( $chat_id, $user_id )
	{
		global $wpdb;

		if( empty( $chat_id ) && ! empty( $user_id ) ) {
			$chat = $wpdb->get_row("SELECT id, user_id1, user_id2, status FROM `". WPMCHAT_CHAT_TABLE . "` WHERE 
								( user_id1 = ".get_current_user_id()." AND user_id2 = ".$user_id.") OR (user_id1 = ".$user_id." AND user_id2 = ".get_current_user_id().")");
			if( ! empty( $chat ) )
				$chat_id = $chat->id;
		}
		else if( ! empty( $chat_id ) ) {
			$chat = $wpdb->get_row("SELECT id, user_id1, user_id2, status FROM `". WPMCHAT_CHAT_TABLE . "` WHERE id=".$chat_id );
		}

		$arr = [];
		if( $chat->user_id1 == get_current_user_id() && $chat->status == 0 ) {
			$arr['status'] = 1;
		}
		else if( $chat->user_id1 == get_current_user_id() && $chat->status == 1 ) {
			$arr['status'] = 0;
		}
		else if( $chat->user_id2 == get_current_user_id() && $chat->status == 0 ) {
			$arr['status'] = 2;
		}
		else if( $chat->user_id2 == get_current_user_id() && $chat->status == 2 ) {
			$arr['status'] = 0;
		}

		//preventing counter blocking by the other user//
		if( ! empty( $arr ) ) { 
			$wpdb->update( WPMCHAT_CHAT_TABLE, $arr, array( 'id'=>$chat->id ) );
		}

		$arr2 = [];
		$arr2['blocked'] = ( ! empty( $arr['status'] )? 1: 0 );
		$arr2['chatid'] = $chat_id;
		return json_encode( $arr2 );
	}

	function wpmchat_ajax_chat_action()
	{
		global $wpdb;
		$current_user = wp_get_current_user();

		$this->set_user_online();

		$out = array();
		check_ajax_referer( "wpmchat_ajax" );

		if(!defined('DOING_AJAX')) define('DOING_AJAX', 1);
		set_time_limit(60);

		$html = "";
		$action = ( isset( $_POST['subaction'] )? trim( sanitize_text_field( $_POST['subaction'] )):'' );

		if( ! in_array( $action, array('init', 'init_inbox', 'load_mchat_mesgs', 'post_mchat_mesg', 'mchat_user_block', 'mchat_heartbeat' )) ) {
			$out = array();
			$out['msg'] = __('Invalid Call.','wpmchat_lang');
			$out['err'] = __LINE__;
			header( "Content-Type: application/json" );
			echo json_encode( $out );
			die();
		}

		//if the current_user can do this action//
		if( ! $this->wpmchat_user_can( get_current_user_id() ) )
			return false;

		$html = '';
		$msg  = '';
		switch( $action ) {
		case 'init':
			$html 	= $this->get_init_html();
		break;
		case 'init_inbox':
			$html		= $this->get_latest_inbox();
		break;
		case 'load_mchat_mesgs':
			$chat_id 	= ( isset( $_POST['mchat_id'] )? 	(int)trim( sanitize_text_field( $_POST['mchat_id'] ) ): 0 );
			$user_id 	= ( isset( $_POST['user_id'] )? 	(int)trim( sanitize_text_field( $_POST['user_id'] ) ): 0 );
			$html 	= $this->get_latest_chat( $chat_id, $user_id );
		break;
		case 'post_mchat_mesg':
			$chat_id 	= ( isset( $_POST['mchat_id'] )? 	(int)trim( sanitize_text_field( $_POST['mchat_id'] ) ): 0 );
			$user_id 	= ( isset( $_POST['user_id'] )? 	(int)trim( sanitize_text_field( $_POST['user_id'] ) ): 0 );
			$msg	   	= ( isset( $_POST['mchat_msg'] )? 	trim( sanitize_textarea_field( $_POST['mchat_msg'] ) ): '' );

			$html = $this->update_chat( $chat_id, $user_id, $msg );
			/*$html = 'posted';*/
		break;
		case 'mchat_user_block':
			$chat_id 	= ( isset( $_POST['mchat_id'] )? 	(int)trim( sanitize_text_field( $_POST['mchat_id'] ) ): 0 );
			$user_id 	= ( isset( $_POST['user_id'] )? 	(int)trim( sanitize_text_field( $_POST['user_id'] ) ): 0 );
			$html = $this->block_user( $chat_id, $user_id );
		break;
		case 'mchat_heartbeat':
			$chat_id 	= ( isset( $_POST['mchat_id'] )? 	(int)trim( sanitize_text_field( $_POST['mchat_id'] ) ): 0 );
			$user_id 	= ( isset( $_POST['user_id'] )? 	(int)trim( sanitize_text_field( $_POST['user_id'] ) ): 0 );
			$inbox_updated = ( isset( $_POST['inbox_updated'] )? 	trim( sanitize_text_field( $_POST['inbox_updated'] ) ): '' );

			if( empty( $inbox_updated ) )
				$inbox_updated = current_time('timestamp');

			$html = $this->wpmchat_heartbeat( $chat_id, $user_id, $inbox_updated );
		break;
		}

		$out = array();
		$out['html'] = $html;
		header( "Content-Type: application/json" );
		echo json_encode( $out );
		die();
	}

	function wpmchat_chat_button( $atts = array() )
	{
		$atts = shortcode_atts( array(
					'user_id' => 0,
				), $atts );

		extract( $atts );
		if( empty( $user_id ) )
			return false;

		if( ! $this->wpmchat_user_can( $user_id ) ) return false;

		require __DIR__.'/templates/front/wpmchat_chat_button.php';

		return wp_kses_post($html);
	}
}
endif;

global $wp_mchat_front;
if( ! $wp_mchat_front ) $wp_mchat_front = new wp_mchat_front();