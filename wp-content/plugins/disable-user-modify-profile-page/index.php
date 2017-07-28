<?php
/*
Plugin Name: Disable user modify profile page
Description: Go to "Settings->Profile Access", and Disable users, to access(modify) his profile page.  (P.S.  OTHER MUST-HAVE PLUGINS FOR EVERYONE: http://bitly.com/MWPLUGINS  )
Authors: tazotodua 
Original Author: ( giuseppemazzapica, Based on https://gist.github.com/Giuseppe-Mazzapica/11070063 and http://wordpress.stackexchange.com/a/29087 )
Author URI: http://www.protectpages.com/
@license free
Version: 1.1
*/
if ( ! defined( 'ABSPATH' ) ) exit; //Exit if accessed directly
define('pluginpage__DUMPD',    'my-disable-userprof-access');
define('plugin_settings_page__DUMPD', 	(is_multisite() ? network_admin_url('settings.php') : admin_url( 'options-general.php') ). '?page='.pluginpage__DUMPD  );
									
								//===========  links in Plugins list ==========//
								add_filter( "plugin_action_links_".plugin_basename( __FILE__ ), function ( $links ) {   $links[] = '<a href="'.plugin_settings_page__DUMPD.'">Settings</a>'; $links[] = '<a href="http://paypal.me/tazotodua">Donate</a>';  return $links; } );
								//REDIRECT SETTINGS PAGE (after activation)
								add_action( 'activated_plugin', function($plugin ) { if( $plugin == plugin_basename( __FILE__ ) ) { exit( wp_redirect( plugin_settings_page__DUMPD ) ); } } );
								
								
								
								
								
								
add_action( 'admin_init', 'stop_access_profile__DUMPD'); function stop_access_profile__DUMPD() {
	if(!current_user_can( 'create_users')){
		if (get_site_option('AllOrInd__DUMPP') == 'all') {	$disabled =true;	}
		if (get_site_option('AllOrInd__DUMPP') != 'all') {	$ids = explode( ',', get_site_option('DisabledIds__DUMPP'));
			if ($GLOBALS['current_user']->ID == 0 || in_array( $GLOBALS['current_user']->ID, $ids)){  $disabled =true;  }
		}
					if (isset($disabled)) { ///////////////START DISABLINGG/////////////////
		remove_menu_page('profile.php');	remove_submenu_page( 'users.php', 'profile.php' );
		if( (defined( 'IS_PROFILE_PAGE' ) && IS_PROFILE_PAGE && IS_PROFILE_PAGE === true)) {
			wp_redirect( admin_url() );	wp_die( 'You are not permitted to change your own profile information. Please contact a member of HR to have your profile information changed. <a href="'.admin_url() .'">Dashboard url</a>' );
}}}}

//add_action('wp_before_admin_bar_render', 'remove_adminbar_completely__DUMPD' ); function remove_adminbar_completely__DUMPD(){
//	if (!current_user_can( 'create_users' )) {  //&& 'wp_before_admin_bar_render' === current_filter()
//		return $GLOBALS['wp_admin_bar']->remove_menu( 'edit-profile', 'user-actions' );}}


//==================================================== ACTIVATION command ===============================
register_activation_hook( __FILE__,  function () {
    if ( is_multisite() && ! strpos( $_SERVER['REQUEST_URI'], 'wp-admin/network/plugins.php' ) ) {
		die ( __( '<script>alert("Activate this plugin only from the NETWORK DASHBOARD.");</script>') );
    }
	add_site_option('AllOrInd__DUMPP',	'all');	add_site_option('DisabledIds__DUMPP',	'0,99999998,99999999');	
});

//==================================================== ADMIN DASHBOARD PAGE  ===============================
// START 
add_action( (is_multisite() ? 'network_admin_menu' : 'admin_menu') , function() {add_submenu_page(   (is_multisite() ?  'settings.php' : 'options-general.php'), 'Disable Profile Access', 'Disable Profile Access', 'create_users', pluginpage__DUMPD, 'my_submenu1__DUMPP' ); 	} );

function my_submenu1__DUMPP() { 
		if (isset($_POST['inp_SecureNonce'])){	if(wp_verify_nonce($_POST['inp_SecureNonce'],'fupd__DUMPP')) { 
			update_site_option('AllOrInd__DUMPP',$_POST['AllOrInd']);	update_site_option('DisabledIds__DUMPP',	$_POST['disabledIds']);
		}}	$chosen_method = get_site_option('AllOrInd__DUMPP');
	?> 
	<form action="" method="POST"><h1>==profile page access==</h1>	<br/>Disable for everyone(except admins): <input type="radio" name="AllOrInd" value="all" <?php echo (($chosen_method=='all')? 'checked="checked"':'');?> />
	<br/>Disable them individually : <input type="radio" name="AllOrInd" value="individual" <?php echo (($chosen_method=='individual')? 'checked="checked"':'');?> /> (if this checkbox is chosen, then input user IDs here, separated with comma: <input type="text" style="width:90%;" name="disabledIds" value="<?php echo get_site_option('DisabledIds__DUMPP');?>" /> 
	<br/><br/><input type="submit" value="SAVE" /><input type="hidden" name="inp_SecureNonce" value="<?php echo wp_create_nonce('fupd__DUMPP');?>" /></form>
	<?php 
}

?>
