<?php
/*
Plugin Name: Rental master
Plugin URI: #
Description: Manage rental listings with filters, fast search, and map-based discovery.
Version: 1.3.1
Requires at least: 6.0
Requires PHP: 7.4
Tested up to: 6.5
Author: Dharminder Singh
Author URI: #
License: GPL-2.0-or-later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Text Domain: ar
Domain Path: /languages/
Network: true
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! defined( 'RM_PLUGIN_FILE' ) ) {
	define( 'RM_PLUGIN_FILE', __FILE__ );
}

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! defined( 'RM_PLUGIN_FILE' ) ) {
	define( 'RM_PLUGIN_FILE', __FILE__ );
}

if ( ! defined( 'RM_PLUGIN_DIR_PATH' ) ) {
	define( 'RM_PLUGIN_DIR_PATH', plugin_dir_path( RM_PLUGIN_FILE ) );
}

if ( ! defined( 'RM_PLUGIN_DIR_URL' ) ) {
	define( 'RM_PLUGIN_DIR_URL', plugin_dir_url( RM_PLUGIN_FILE ) );
}

if ( ! defined( 'RM_PLUGIN_MASTER_URL' ) ) {
	define( 'RM_PLUGIN_MASTER_URL', 'https://rental-masters.com/' );
}

if ( ! defined( 'RM_MAP_API_KEY' ) ) {
	define( 'RM_MAP_API_KEY', (string) get_option( 'abr_google_maps_api_key', '' ) );
}

require_once RM_PLUGIN_DIR_PATH . 'includes/class-activator.php';
require_once RM_PLUGIN_DIR_PATH . 'includes/class-deactivator.php';
require_once RM_PLUGIN_DIR_PATH . 'includes/class-assets.php';
require_once RM_PLUGIN_DIR_PATH . 'includes/class-admin.php';
require_once RM_PLUGIN_DIR_PATH . 'includes/class-frontend.php';
require_once RM_PLUGIN_DIR_PATH . 'includes/class-shortcodes.php';
require_once RM_PLUGIN_DIR_PATH . 'includes/class-ajax.php';
require_once RM_PLUGIN_DIR_PATH . 'includes/class-search.php';
require_once RM_PLUGIN_DIR_PATH . 'includes/class-google-maps.php';
require_once RM_PLUGIN_DIR_PATH . 'includes/class-listings.php';
require_once RM_PLUGIN_DIR_PATH . 'includes/class-plugin.php';

register_activation_hook( RM_PLUGIN_FILE, array( 'RM_Activator', 'activate' ) );
register_deactivation_hook( RM_PLUGIN_FILE, array( 'RM_Deactivator', 'deactivate' ) );

RM_Plugin::get_instance()->run();
