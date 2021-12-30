<?php
/*
 * 
 * 
 * @class - wp_mchat_front
 * @template - wp_mchat_front::wpinas_chat_button()
 * 
 * 
 * 
 */
if( ! defined( 'WPMCHAT_FILE' ) ) die( 'Silence ' );

ob_start();
?>
<!-- WordPress MChat one to one chat -->
<div class="wpmchat_wrap">
	<button id="wpmchat_new_chat" data-user_id="<?php echo esc_attr( $user_id );?>"><i class="dashicons dashicons-format-chat"></i> <?php _e('Contact Me', 'wpmchat_lang' );?></i></button>
<div>

<?php
$html = ob_get_contents();
ob_end_clean();