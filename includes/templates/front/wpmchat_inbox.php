<?php
/*
 * 
 * 
 * @class - wp_mchat_front
 * @template - wp_mchat_front::wpinas_inbox()
 * 
 * 
 * 
 */
if( ! defined( 'WPMCHAT_FILE' ) ) die( 'Silence ' );

$wpmchat_settings = get_option( 'wpmchat_settings' );

$branding = '<div id="wpmchat_branding">'.sprintf( __('Powered by %sMChat Plugin%s', 'wpmchat_lang' ), '<a href="https://www.m-solutions.co.in/" target="_blank">', '</a>' ).'</div>';
if( $wpmchat_settings['branding'] != 1 )
	$branding = '';

ob_start();
?>
<!-- WordPress MChat one to one chat -->
<div class="wpmchat_wrapper" id="wpmchat_wrapper_inbox">
	<h3><?php _e('Messages','wpmchat_lang');?><i class="dashicons dashicons-arrow-down-alt2" id="wpmchat_inbox_collapse"></i></h3>
	<div id="wpmchat_messages">
	<ul id="wpmchat_inbox_list">
	</ul>
	<input type="hidden" name="wpmchat_inbox_updated" id="wpmchat_inbox_updated" value=""/>
	</div>
	<?php echo wp_kses_post( $branding );?>
</div>
<div class="wpmchat_wrapper" id="wpmchat_wrapper_message" style="display:none">
	<h3>
		<span id="wpmchat_user_name">&nbsp;</span>
		<span id="wpmchat_user_icons"><i class="dashicons dashicons-flag" title="<?php _e('Block User','wpmchat_lang');?>" id="wpmchat_message_block"></i><i class="dashicons dashicons-no-alt"  title="<?php _e('Close','wpmchat_lang');?>" id="wpmchat_message_close"></i></span>
	</h3>
	<div id="wpmchat_message_list">
	<ul id="wpmchat_mesg_list">
		
	</ul>
	<div id="wpmchat_write_wrapper">
	<textarea rows="2" cols="40" id="wpmchat_write" name="wpmchat_write" autofocus></textarea>
	<input type="button" name="wpmchat_write_submit" id="wpmchat_write_submit" value="Send"/>
	<input type="hidden" name="wpmchat_write_chatid" id="wpmchat_write_chatid" value=""/>
	<input type="hidden" name="wpmchat_write_userid" id="wpmchat_write_userid" value=""/>
	</div>
</div>
<?php
$html = ob_get_contents();
ob_end_clean();