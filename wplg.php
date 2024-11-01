<?php
/*
Plugin Name: WPLG Default Mail From 
Plugin URI: http://www.wpletsgo.com/wplg-default-mail/
Description: Changes the default email address and email name.
Author: wpletsgo
Version: 1.0.0
Author URI: http://wpletsgo.com/
License: GPLv2 or later
Text domain: wplg-default-mail
Domain path: /languages/
*/
?>
<?php
$wplg_opt = get_option('mail_from_options');
register_activation_hook(__FILE__,'wdmf_setup_options');
function wdmf_setup_options()
{
	global $wplg_opt;
	$wplg_version = get_option('mail_from_version'); //Mail From Version Number
	$wplg_this_version = '1.0.0';
	$domain = preg_replace('#^www\.#', '', strtolower($_SERVER['SERVER_NAME']));
	$sitename = get_bloginfo('name');
	if (empty($wplg_version))
	{
		add_option('mail_from_version', $wplg_this_version);
	} 
	elseif ($wplg_version != $wplg_this_version)
	{
		update_option('mail_from_version', $wplg_this_version);
	}
	$optionarray_def = array(
		'username' => 'noreply',
		'domainname' => $domain,
		'sendername' => $sitename
		);
	if (empty($wplg_opt)){ //If there aren't already options for Mail From
		add_option('mail_from_options', $optionarray_def);
	}	
}
$wpversion_full = get_bloginfo('version');
$wpversion = preg_replace('/([0-9].[0-9])(.*)/', '$1', $wpversion_full);
function wdmf_add_options_page() 
{
    if (function_exists('add_options_page')) 
    {
		add_options_page( __('WPLG Default Mail From Settings','wplg-default-mail-from'), __('WPLG Default Mail From Settings','wplg-default-mail-from'), 'activate_plugins', 'wplg.php', 'wdmf_options_page');
    }
}
function wdmf_from() 
{
global $wplg_opt;
	if (empty($wplg_opt['username'])) : $username = "wordpress"; else : $username = $wplg_opt['username']; endif;
	if (empty($wplg_opt['domainname'])) : $domainname = strtolower($_SERVER['SERVER_NAME']); else : $domainname = $wplg_opt['domainname']; endif;
	$emailaddress = $username.'@'.$domainname;
	return $emailaddress;
}
function wdmf_from_name() 
{
global $wplg_opt;
	if (empty($wplg_opt['sendername'])) : $sendername = "WordPress"; else : $sendername = stripslashes($wplg_opt['sendername']); endif;
	return $sendername;
}
function wplg_enqueue() {
wp_register_style('wplg_css', plugins_url('assets/wplg.css',__FILE__ ));
wp_enqueue_style('wplg_css');
wp_register_script( 'wplg_js', plugins_url('assets/wplg.js',__FILE__ ));
wp_enqueue_script('wplg_js');
}

add_action( 'admin_init','wplg_enqueue' );

function wplg_load_textdomain() {
	//load_plugin_textdomain( 'wplg-plugin', false,  basename( dirname( __FILE__ ) ) . '/languages');
	$domain = 'wplg-default-mail-from';
	$locale = apply_filters( 'plugin_locale', get_locale(), $domain );
	// wp-content/languages/plugin-name/plugin-name-de_DE.mo
	load_textdomain( $domain, trailingslashit( WP_LANG_DIR ) . $domain . '/' . $domain . '-' . $locale . '.mo' );
	// wp-content/plugins/plugin-name/languages/plugin-name-de_DE.mo
	load_plugin_textdomain( $domain, FALSE, basename( dirname( __FILE__ ) ) . '/languages/' );
}
add_action( 'init', 'wplg_load_textdomain' );

function wdmf_options_page()
{
global $wpdb, $wpversion;
	$domain = preg_replace('#^www\.#', '', strtolower($_SERVER['SERVER_NAME']));
	if (isset($_POST['submit']) ) 
	{	
		$domain_input_errors = array('http://', 'https://', 'ftp://', 'www.');
		$illegal_chars_username = array('(', ')', '<', '>', ',', ';', ':', '\\', '"', '[', ']', '@', "'", ' ');
		$sendername = $_POST['sendername'];
		$username = strtolower($_POST['domainname']);
		$username = strtok($username, '@');
		$username = str_replace ($illegal_chars_username, "", $username);
		$domainname = strtolower($_POST['domainname']);
		$domainname = substr($domainname, strpos($domainname, "@") + 1);
		$domainname = str_replace ($domain_input_errors, "", $domainname);
		$domainname = preg_replace('/[^0-9a-z\-\.]/i','',$domainname);
		$optionarray_update = array (
			'username' => $username,
			'domainname' => $domainname,
			'sendername' => $sendername
		);
		update_option('mail_from_options', $optionarray_update);
		}
	$optionarray_def = get_option('mail_from_options');
?>
<script>
 function wdmf_check_form() { 
 if(document.getElementById("switch_left").checked == true)
 {
	 if(document.getElementById("sendername_inp").value=="")
	 {
		 alert("<?php _e('Please enter a From name!','wplg-default-mail-from')?>");
		 document.getElementById("sendername_inp").focus();
		 return false;
	 }
	 if(document.getElementById("domainname_inp").value=="")
	 {
		 alert("<?php _e('Please eneter a From email address!','wplg-default-mail-from')?>");
		  document.getElementById("domainname_inp").focus();
		 return false;
	 }
	 else
	 {
		 return true;
	 }
 }
 }
</script>
	<div class="wrap">
	<h2><?php _e('WPLG Default Mail From Settings','wplg-default-mail-from')?></h2>
	<form method="post" action="<?php echo $_SERVER['PHP_SELF'] . '?page=' . basename(__FILE__); ?>&amp;updated=true" onsubmit="return wdmf_check_form()">
	<fieldset class="options" style="border: none">
	<p>
	<table width="80%"<?php $wpversion >= 2.5 ? _e('class="form-table"') : _e('cellspacing="2" cellpadding="5" class="editform"'); ?> style="width:80% !important;" >
	<tr valign="center" class="wplg_tr"> 
			<th width="40%" scope="row" style="width:55%  !important;"><?php _e('Enable plugin?','wplg-default-mail-from')?> <br /><span class="description"><?php _e('Set to yes, if you want to enable this plugin.','wplg-default-mail-from')?></span></th> 
			<td width="60%">
			<div class="switch-field">
			<input type="radio" id="switch_left" name="switch_2" onclick="return wdmf_radio_yes_click()" value="yes"<?php if($optionarray_def['domainname']!=""){?>checked<?php } ?>/>
      <label for="switch_left"><?php _e('Yes','wplg-default-mail-from')?></label>
      <input type="radio" id="switch_right" name="switch_2" onclick="return wdmf_radio_no_click()" value="no"<?php if($optionarray_def['domainname']==""){?>checked<?php } ?>/>
      <label for="switch_right"><?php _e('No','wplg-default-mail-from')?></label>
	  </div>
	  </td>
		</tr>
		<tr valign="center" class="wplg_tr"> 
			<th width="60%" scope="row" style="width:55%  !important;"><?php _e('From Name','wplg-default-mail-from')?> <br /><span class="description"><?php _e('This is the name that will appear in the "From" part of your email.','wplg-default-mail-from')?></span></th> 
			<td width="40%">
			<input type="text" id="sendername_inp" name="sendername" <?php if($optionarray_def['domainname']==""){?> style="background-color:#f1f1f1;" readonly="readonly" value=""<?php }else{ ?> value="<?php echo stripslashes($optionarray_def['sendername']); ?>"<?php } ?> size="25" /><input type="hidden" id="sh" <?php if($optionarray_def['domainname']==""){?> value=""<?php }else{ ?> value="<?php echo stripslashes($optionarray_def['sendername']); ?>"<?php } ?>/></td>
		<tr valign="center" style="border-bottom:1pt solid #e6e6e6;"> 
			<th width="60%" scope="row" style="width:55%  !important;"><?php _e('From Email','wplg-default-mail-from')?> <br /><span class="description"><?php _e('This is the email address that WordPress will use to send emails.','wplg-default-mail-from')?></span></th> 
			<td width="40%">
			<input type="text" id="domainname_inp" name="domainname"<?php if($optionarray_def['domainname']==""){?> style="background-color:#f1f1f1;" readonly="readonly" value=""<?php }else{ ?> value="<?php echo $optionarray_def['username'].'@'.$optionarray_def['domainname']; ?>" <?php } ?> size="25" /><input type="hidden" id="dh"<?php if($optionarray_def['domainname']==""){?> value=""<?php }else{ ?> value="<?php echo $optionarray_def['username'].'@'.$optionarray_def['domainname']; ?>"<?php } ?>/></td>
		</tr>
		<tr valign="center" style="width:55%  !important;"> 
			<th width="40%" scope="row"></th> 
			<td width="60%"><input type="hidden" name="_submit_check" value="1" />
		<input type="submit" name="submit" id="wplg_submit" style="float:right;" class="button button-primary button-large" value="<?php _e('Save Changes','wplg-default-mail-from') ?>" /></td>
	</table>
	</fieldset>
	</p>
	</form>
	</div>
<?php
}
add_filter('wp_mail_from','wdmf_from');
add_filter('wp_mail_from_name','wdmf_from_name');
add_action('admin_menu', 'wdmf_add_options_page');

