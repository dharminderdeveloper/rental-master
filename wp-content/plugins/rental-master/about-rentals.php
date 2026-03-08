<?php
/*
Plugin Name: Rental master
Plugin URI: http://about-rentals.com
Description: This is WP plugin to manage communities and apartments. Moreover, there are other features like Leasing special, Virtual Tour, Favorites too
Version: 1.3.1
Tested up to: 4.5.2
Author:Dharminder Singh.
Author URI:#
Text Domain: ar
Domain Path: /lang/
Network:true
*/

/**-----------------	ABOUT RENTAL PLUGIN ACTIVATION REDIRECTION -----------------**/

define('ABR_PLUGIN_DIR_PATH',plugin_dir_path(__FILE__));
define("ABR_PLUGIN_DIR_URL",plugin_dir_url(__FILE__));
define("ABR_PLUGIN_MASTER_URL",'https://about-rentals.com/');
define("ABR_MAP_API_KEY",'AIzaSyCAtyGhqMW66Z4sdrzyHLHPIyUlTad6p74');

if(in_array(ABR_PLUGIN_DIR_URL.'about-rentals.php', apply_filters('active_plugins', get_option('active_plugins')))){
	wp_die('This plugin Already Exist');
}

register_activation_hook(__FILE__, 'abr_plugin_activate');
add_action('admin_init', 'abr_plugin_redirect');

function abr_plugin_activate() {
	add_option('abr_do_activation_redirect', true);
}

function abr_plugin_redirect() {
    if (get_option('abr_do_activation_redirect', false)) {
        delete_option('abr_do_activation_redirect');
        if(!isset($_GET['activate-multi'])){
            wp_redirect("admin.php?page=abr-user-help");
        }
		/**Add Predefined Data*/
		abr_insert_predefined_data();
    }
}

$is_price_updated = get_option( 'community_price_updated' );

if( !empty( $is_price_updated )){
	$priceLimitArray = $wpdb->get_results('SELECT * FROM '.$wpdb->prefix.'postmeta AS pm WHERE pm.meta_key="cf_apartment_rent_month" ORDER BY pm.meta_value DESC');
	
	foreach( $priceLimitArray as $cPRice){
		$mValue =	(int)str_replace( ',','',$cPRice->meta_value);		
		update_post_meta( $cPRice->post_id,'cf_apartment_rent_month', $mValue );
	}
	
	update_option( 'community_price_updated',1);
}
	
/**		exe File	**/
include(ABR_PLUGIN_DIR_PATH.'cf-exe.php');