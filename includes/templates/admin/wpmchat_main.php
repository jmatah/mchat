<?php
/*
 * 
 * 
 * @class - wp_mchat_admin
 * @template - wp_mchat_admin::wpinas_main()
 * 
 * 
 * 
 */
if( ! defined( 'WPMCHAT_FILE' ) ) die( 'Silence ' );

if( ! isset($wpmchat_settings['branding']) ) $wpmchat_settings['branding'] = 1;

global $wp_mchat_admin;
extract( $wp_mchat_admin->default_colors );

// filter with defaults:
$wpmchat_settings = $this->wpmchat_get_colors($wpmchat_settings);

ob_start();
?>
		<div class="wrap">
		<h2><?php _e( 'MChat - Settings', 'wpmchat_lang' ); ?></h2>
<?php

if($error)
{
?>
<div class="error fade"><p><b><?php _e('Error: ', 'wpmchat_lang')?></b><?php echo esc_attr($error);?></p></div>
<?php
}

if($result1)
{
?>
<div id="message" class="updated fade"><p><?php echo esc_attr( $result1 ); ?></p></div>
<?php
}
?>
	<script type="text/javascript">
	if( typeof jQuery == "function" ) {
		jQuery(document).ready(function($){
			$("#tabs").tabs();
			$("#tabs").tabs( "option", "cache", false );
			$("#tabs").css( 'min-height', $("#tabs ul").css("height") );

			$('.my-color-field').wpColorPicker();
		});
	}
	</script>
	<style>.hl{font-style:italic; background-color:#ffff23;}</style>
	<div id="poststuff">
	<div id="post-body" class="metabox-holder columns-2">
	<div id="post-body-content">


	<form method="post" id="wpmchat_search2" name="wpmchat_search2">
	<?php  wp_nonce_field( 'wpmchat-settings' ); ?>
	<input type="hidden" name="call" value="save"/>

	<div id="wpmchat_form">
		<div id="tabs">

		<ul id="wpmchat_ul">
		<li><a href="#wpmchat_main" title="<?php _e('Settings','wpmchat_lang');?>"><?php _e('Settings','wpmchat_lang');?></a></li>
		<li><a href="#wpmchat_appearance" title="<?php _e('Appearance','wpmchat_lang');?>"><?php _e('Appearance','wpmchat_lang');?></a></li>
		<li><a href="#wpmchat_email" title="<?php _e('Email','wpmchat_lang');?>"><?php _e('Email','wpmchat_lang');?></a></li>
		<li><a href="#wpmchat_access" title="<?php _e('Access','wpmchat_lang');?>"><?php _e('User Role Access','wpmchat_lang');?></a></li>
		<?php do_action('wpmchat_admin_menu');?>
		</ul>

	    <div id="wpmchat_main" class="postbox">
		<div class="postbox-header">
	      <h2 class='hndle ui-sortable-handle'><strong><?php _e('Settings:', 'wptcm_lang'); ?></strong></h2>
		</div>
	      <div class="inside">
			<table border="0" cellpadding="3" cellspacing="2" class="form-table" width="100%">
			<tr>
			<th><label for="wpmchat_branding"><?php _e( 'Show Branding on footer: ','wpmchat_lang' );?></th>
			<td><input type="checkbox" name="wpmchat_branding" id="wpmchat_branding" value="1" <?php checked( $wpmchat_settings['branding'], '1' )?> /><br/>
			<span class="description"><?php _e('Show MChat &trade; branding on footer of the Chat Window.','wpmchat_lang');?></span>
			</tr>
			<tr>
			<th><label for="wpmchat_uninstall"><?php _e( 'Remove Data on uninstall: ','wpmchat_lang' );?></th>
			<td><input type="checkbox" name="wpmchat_uninstall" id="wpmchat_uninstall" value="1" <?php checked( $wpmchat_settings['uninstall'], '1' )?> /><br/>
			<span class="description"><?php _e('Delete all of MChats data when you delete MChat Plugin.','wpmchat_lang');?></span>
			</tr>
			<?php do_action('wpmchat_admin_main_settings');?>
			</table>
	      </div>
	    </div>

	    <div id="wpmchat_appearance" class="postbox">
		<div class="postbox-header">
	      <h2 class='hndle ui-sortable-handle'><strong><?php _e('Appearance:', 'wptcm_lang'); ?></strong></h2>
		</div>
	      <div class="inside">
			<table border="0" cellpadding="3" cellspacing="2" class="form-table" width="100%">
			<tr>
			<th><label for="wpmchat_headerbg"><?php _e( 'Header Background Color: ','wpmchat_lang' );?></th>
			<td><input type="text" name="wpmchat_headerbg" id="wpmchat_headerbg" value="<?php echo esc_attr( $wpmchat_settings['headerbg'])?>" data-default-color="<?php echo esc_attr( $headerbg)?>" class="regualr-text my-color-field headerbg" /><br/>
			<span class="description"><?php _e('Select background color of the header of the messages and chat window.','wpmchat_lang');?></span>
			</tr>
			<tr>
			<th><label for="wpmchat_headerfg"><?php _e( 'Header Text Color: ','wpmchat_lang' );?></th>
			<td><input type="text" name="wpmchat_headerfg" id="wpmchat_headerfg" value="<?php echo esc_attr( $wpmchat_settings['headerfg'])?>" data-default-color="<?php echo esc_attr( $headerfg)?>" class="regualr-text my-color-field headerfg" /><br/>
			<span class="description"><?php _e('Select text color of the header of the messages and chat window.','wpmchat_lang');?></span>
			</tr>
			<!-- s -->
			<tr>
			<th><label for="wpmchat_chatbg"><?php _e( 'Chat Window Background Color: ','wpmchat_lang' );?></th>
			<td><input type="text" name="wpmchat_chatbg" id="wpmchat_chatbg" value="<?php echo esc_attr( $wpmchat_settings['chatbg'])?>" data-default-color="<?php echo esc_attr( $chatbg)?>" class="regualr-text my-color-field chatbg" /><br/>
			<span class="description"><?php _e('Select background color of the messages and chat window.','wpmchat_lang');?></span>
			</tr>
			<!-- s -->
			<tr>
			<th><label for="wpmchat_chatbgs"><?php _e( 'Sent Chat Background Color: ','wpmchat_lang' );?></th>
			<td><input type="text" name="wpmchat_chatbgs" id="wpmchat_chatbgs" value="<?php echo esc_attr( $wpmchat_settings['chatbgs'])?>" data-default-color="<?php echo esc_attr( $chatbgs)?>" class="regualr-text my-color-field chatbgs" /><br/>
			<span class="description"><?php _e('Select Background color of the messages sent by the user.','wpmchat_lang');?></span>
			</tr>
			<tr>
			<th><label for="wpmchat_chatfgs"><?php _e( 'Sent Chat Text Color: ','wpmchat_lang' );?></th>
			<td><input type="text" name="wpmchat_chatfgs" id="wpmchat_chatfgs" value="<?php echo esc_attr( $wpmchat_settings['chatfgs'])?>" data-default-color="<?php echo esc_attr( $chatfgs)?>" class="regualr-text my-color-field chatfgs" /><br/>
			<span class="description"><?php _e('Select Text color of the messages sent by the user.','wpmchat_lang');?></span>
			</tr>
			<!-- s -->
			<tr>
			<th><label for="wpmchat_chatbgr"><?php _e( 'Reply Chat Background Color: ','wpmchat_lang' );?></th>
			<td><input type="text" name="wpmchat_chatbgr" id="wpmchat_chatbgr" value="<?php echo esc_attr( $wpmchat_settings['chatbgr'])?>" data-default-color="<?php echo esc_attr( $chatbgr)?>" class="regualr-text my-color-field chatbgr" /><br/>
			<span class="description"><?php _e('Select Background color of the message replies got by the user.','wpmchat_lang');?></span>
			</tr>
			<tr>
			<th><label for="wpmchat_chatfgr"><?php _e( 'Reply Chat Text Color: ','wpmchat_lang' );?></th>
			<td><input type="text" name="wpmchat_chatfgr" id="wpmchat_chatfgr" value="<?php echo esc_attr( $wpmchat_settings['chatfgr'])?>" data-default-color="<?php echo esc_attr( $chatfgr)?>" class="regualr-text my-color-field chatfgr" /><br/>
			<span class="description"><?php _e('Select Text color of the messages replies got by the user.','wpmchat_lang');?></span>
			</tr>
			<!-- s -->
			<tr>
			<th><label for="wpmchat_chatbutbg"><?php _e( 'Send Button Background Color: ','wpmchat_lang' );?></th>
			<td><input type="text" name="wpmchat_chatbutbg" id="wpmchat_chatbutbg" value="<?php echo esc_attr( $wpmchat_settings['chatbutbg'])?>" data-default-color="<?php echo esc_attr( $chatbutbg)?>" class="regualr-text my-color-field chatbutbg" /><br/>
			<span class="description"><?php _e('Select Background color of the Send Button.','wpmchat_lang');?></span>
			</tr>
			<tr>
			<th><label for="wpmchat_chatbutfg"><?php _e( 'Send Button Text Color: ','wpmchat_lang' );?></th>
			<td><input type="text" name="wpmchat_chatbutfg" id="wpmchat_chatbutfg" value="<?php echo esc_attr( $wpmchat_settings['chatbutfg'])?>" data-default-color="<?php echo esc_attr( $chatbutfg)?>" class="regualr-text my-color-field chatbutfg" /><br/>
			<span class="description"><?php _e('Select Text color of the messages Send Button.','wpmchat_lang');?></span>
			</tr>
			<?php do_action('wpmchat_admin_appearance_settings');?>

			</table>
	      </div>
	    </div>
		<?php
		$sitename = wp_specialchars_decode( get_bloginfo( 'name' ), ENT_QUOTES );
		$wpmchat_settings['email_from'] = ( isset( $wpmchat_settings['email_from'] )		? $wpmchat_settings['email_from']: $sitename );
		$wpmchat_settings['email_email'] = ( isset( $wpmchat_settings['email_email'] )	? $wpmchat_settings['email_email']: get_option('admin_email') );
		$wpmchat_settings['email_subject'] = ( isset( $wpmchat_settings['email_subject'] )	? $wpmchat_settings['email_subject']: sprintf( __('[%s] New chat Notification','wpmchat_lang'), parse_url( home_url(), PHP_URL_HOST ) ) );
		$wpmchat_settings['email_body'] = ( isset( $wpmchat_settings['email_body'] )		? $wpmchat_settings['email_body']: "Hey,\nYou have a new message, please login and check.\nAdmin." );
		?>
	    <div id="wpmchat_email" class="postbox">
		<div class="postbox-header">
	      <h2 class='hndle ui-sortable-handle'><strong><?php _e('Email Settings:', 'wptcm_lang'); ?></strong></h2>
		</div>
	      <div class="inside">
			<table border="0" cellpadding="3" cellspacing="2" class="form-table" width="100%">
			<tr>
			<th><label for="wpmchat_email_send"><?php _e( 'Send Emails: ','wpmchat_lang' );?></th>
			<td><input type="checkbox" name="wpmchat_email_send" id="wpmchat_email_send" value="1" <?php checked( $wpmchat_settings['email_send'], '1' )?>  /><br/>
			<span class="description"><?php _e('Send Email to users when they get a chat message.','wpmchat_lang');?></span>
			</tr>
			<tr>
			<th><label for="wpmchat_email_from"><?php _e( 'Email From Name: ','wpmchat_lang' );?></th>
			<td><input type="text" name="wpmchat_email_from" id="wpmchat_email_from" class="regular-text" value="<?php echo esc_attr( $wpmchat_settings['email_from'])?>" /><br/>
			<span class="description"><?php _e('Enter Email From Name.','wpmchat_lang');?></span>
			</tr>
			<tr>
			<th><label for="wpmchat_email_email"><?php _e( 'From Email address: ','wpmchat_lang' );?></th>
			<td><input type="text" name="wpmchat_email_email" id="wpmchat_email_email" class="regular-text" value="<?php echo esc_attr( $wpmchat_settings['email_email'])?>" /><br/>
			<span class="description"><?php _e('Enter Email address.','wpmchat_lang');?></span>
			</tr>
			<tr>
			<th><label for="wpmchat_email_subject"><?php _e( 'Email Subject: ','wpmchat_lang' );?></th>
			<td><input type="text" name="wpmchat_email_subject" id="wpmchat_email_subject" class="regular-text" value="<?php echo esc_attr( $wpmchat_settings['email_subject'])?>" /><br/>
			<span class="description"><?php _e('Enter Email Subject for new chat notification.','wpmchat_lang');?></span>
			</tr>
			<tr>
			<th><label for="wpmchat_email_body"><?php _e( 'Email Body: ','wpmchat_lang' );?></th>
			<td><textarea cols="80" rows="5" name="wpmchat_email_body" id="wpmchat_email_body" class="regular-text"><?php echo esc_attr( $wpmchat_settings['email_body'])?></textarea><br/>
			<span class="description"><?php _e('Enter Email Body for new chat notification.','wpmchat_lang');?></span>
			</tr>
			<tr>
			<?php do_action('wpmchat_admin_email_settings');?>
			</table>
	      </div>
	    </div>

	    <div id="wpmchat_access" class="postbox">
		<div class="postbox-header">
	      <h2 class='hndle ui-sortable-handle'><strong><?php _e('Role Access Settings:', 'wptcm_lang'); ?></strong></h2>
		</div>
	      <div class="inside">
			<table border="0" cellpadding="3" cellspacing="2" class="form-table" width="100%">
			<tr>
			<th><label for="roles"><?php _e( 'Which roles are allowed to access MChat: ','wpmchat_lang' );?></th>
			<td><?php echo $this->get_roles_list( $wpmchat_settings['role_list'] );?><br/>
			<span class="description"><?php _e('Check which roles have access to the MChat system.','wpmchat_lang');?></span>
			</tr>
			<?php do_action('wpmchat_admin_role_settings');?>
			</table>
	      </div>
	    </div>
		<?php do_action('wpmchat_admin_settings_bottom');?>
		<p>
			<input type="submit" name="wpmchat_save" id="wpmchat_save" value="<?php _e( ' Save ', 'wpmchat_lang' ); ?>" class="button button-primary" />
		<img src="<?php echo admin_url('/images/wpspin_light.gif');?>" style="display:none" id="wpmchat_ajax" border="0"/>
		</p>

	      </div><!-- tab -->
	    </div><!-- wrapper -->

	  </form>


	    <div id="settingdiv" class="postbox">
		<div class="postbox-header">
	      <h2 class='hndle ui-sortable-handle'><strong><span><?php  _e('Shortcodes', 'wpinas_lang'); ?></span></strong></h2>
		</div>
	      <div class="inside">
			<table border="0" cellpadding="3" cellspacing="2" class="form-table" width="100%">
			<tr>
			<th><label for="wpinas_sc_one2one"><?php _e('Direct Chat Shortcode','wpmchat_lang');?></th>
			<td><input type="text" readonly="readonly" class="regular-text" onclick="this.select()" name="wpinas_sc_one2one" id="wpinas_sc_one2one" value="[MCHAT user_id=1]"/><br/>
			<span class="description"><?php _e('Direct Chat shortcode, place this shortcode in any post or page. <br/>Where 1 is the User ID of the user, a logged in user wishes to chat with.','wpmchat_lang')?></span><br/>
			<span class="description"><?php _e("You could use this shortcode in the templates with <code>echo do_shortcode('MCHAT user_id='.\$user_id.']');</code>.",'wpmchat_lang')?></span>
			</tr>
			</table>
	      </div>
	    </div>

	  </form>
	  <hr class="clear" />


	</div><!-- /post-body-content -->
	<div id="postbox-container-1" class="postbox-container">
		<div class="postbox">
			<h3 class="hndle"><span>MChat Plugin by M-Solutions India</span></h3>
			<div class="inside">
			<p><i class="dashicons dashicons-wordpress"></i> WordPress Plugin developers with over a decade of experience.</p>
			<p>Custom WordPress Plugins, WooCommerce, REST APIs, SEO Plugins and more... </p>
			<p>Hire me for <a href="https://bit.ly/msolutionfl" target="_blank">Custom WordPress Plugins</a></p>
			</div>
		</div>
		<div class="postbox">
			<h3 class="hndle"><span><i class="dashicons dashicons-format-chat"></i> Support MChat Plugin</span></h3>
			<div class="inside">
			<p><?php printf( __( 'Support us by giving a review on WordPress.org with a %s rating.', 'wpmchat_lang' ), str_repeat( '<i style="font-size:14px;" class="dashicons dashicons-star-filled"></i>', 5 )); ?><br/></p>
			<p><a href="https://wordpress.org/support/plugin/mchat/reviews/?filter=5#new-post" class="button button-primary" target="_blank" rel="noopener">Yes I Love it!</a></p>
			<p>For Suggestions and bugs please use WordPress.org Support forums.</p>
			</div>
		</div>
		<div class="postbox">
			<h3 class="hndle"><span><i class="dashicons dashicons-format-chat"></i> MChat Pro</span></h3>
			<div class="inside">
			<p> WordPress MChat Pro Coming Soon</p>
			</div>
		</div>
	</div>
	</div><!-- /post-body -->
	<br class="clear" />
<script>
if( typeof jQuery == "function" ) {
jQuery(document).ready(function($){
	$("#role-all").on('click', function(event) {
		var checkedStatus = this.checked;
		$('input[name="roles[]"]').each(function(i){
			this.checked = checkedStatus;
		});
	});
});
}
</script>
	</div><!-- /poststuff -->
		</div><!-- /wrap --><br/>