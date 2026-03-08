<?php
/**
 * Asset management.
 *
 * @package RentalMaster
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class RM_Assets {

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'wp_enqueue_scripts', array( $this, 'register_public_assets' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'register_admin_assets' ) );
	}

	/**
	 * Register public assets.
	 *
	 * @return void
	 */
	public function register_public_assets() {
		$css_file = RM_PLUGIN_DIR_PATH . 'public/css/public.css';
		$js_file  = RM_PLUGIN_DIR_PATH . 'public/js/public.js';

		if ( file_exists( $css_file ) ) {
			wp_register_style(
				'rm-public',
				RM_PLUGIN_DIR_URL . 'public/css/public.css',
				array(),
				(string) filemtime( $css_file )
			);
		}

		if ( file_exists( $js_file ) ) {
			wp_register_script(
				'rm-public',
				RM_PLUGIN_DIR_URL . 'public/js/public.js',
				array(),
				(string) filemtime( $js_file ),
				true
			);
		}
	}

	/**
	 * Register admin assets.
	 *
	 * @return void
	 */
	public function register_admin_assets() {
		$css_file = RM_PLUGIN_DIR_PATH . 'admin/css/admin.css';
		$js_file  = RM_PLUGIN_DIR_PATH . 'admin/js/admin.js';

		if ( file_exists( $css_file ) ) {
			wp_register_style(
				'rm-admin',
				RM_PLUGIN_DIR_URL . 'admin/css/admin.css',
				array(),
				(string) filemtime( $css_file )
			);
		}

		if ( file_exists( $js_file ) ) {
			wp_register_script(
				'rm-admin',
				RM_PLUGIN_DIR_URL . 'admin/js/admin.js',
				array( 'jquery' ),
				(string) filemtime( $js_file ),
				true
			);
		}
	}
}
