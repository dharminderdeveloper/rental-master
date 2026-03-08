<?php
/**
 * Main plugin loader.
 *
 * @package RentalMaster
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class RM_Plugin {

	/**
	 * Singleton instance.
	 *
	 * @var RM_Plugin|null
	 */
	private static $instance = null;

	/**
	 * Service instances.
	 *
	 * @var array<string,object>
	 */
	private $services = array();

	/**
	 * Get singleton instance.
	 *
	 * @return RM_Plugin
	 */
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Prevent direct construction.
	 */
	private function __construct() {
	}

	/**
	 * Boot plugin services.
	 *
	 * @return void
	 */
	public function run() {
		$this->register_services();
		$this->register_hooks();
		$this->load_legacy_stack();
	}

	/**
	 * Register modern service classes.
	 *
	 * @return void
	 */
	private function register_services() {
		$this->services['assets']      = new RM_Assets();
		$this->services['admin']       = new RM_Admin();
		$this->services['frontend']    = new RM_Frontend();
		$this->services['shortcodes']  = new RM_Shortcodes();
		$this->services['ajax']        = new RM_Ajax();
		$this->services['search']      = new RM_Search();
		$this->services['google_maps'] = new RM_Google_Maps();
		$this->services['listings']    = new RM_Listings();
	}

	/**
	 * Register global plugin hooks.
	 *
	 * @return void
	 */
	private function register_hooks() {
		add_action( 'plugins_loaded', array( $this, 'load_textdomain' ) );
		add_action( 'init', array( $this, 'maybe_normalize_legacy_prices' ), 5 );
	}

	/**
	 * Load plugin translation files.
	 *
	 * @return void
	 */
	public function load_textdomain() {
		load_plugin_textdomain( 'ar', false, dirname( plugin_basename( RM_PLUGIN_FILE ) ) . '/languages/' );
	}

	/**
	 * Keep legacy price data normalization in place for backward compatibility.
	 *
	 * @return void
	 */
	public function maybe_normalize_legacy_prices() {
		global $wpdb;

		if ( empty( $wpdb ) ) {
			return;
		}

		$is_price_updated = get_option( 'community_price_updated' );

		if ( empty( $is_price_updated ) ) {
			return;
		}

		$results = $wpdb->get_results(
			"SELECT post_id, meta_value FROM {$wpdb->postmeta} WHERE meta_key='rm_apartment_rent_month' ORDER BY meta_value DESC"
		);

		if ( empty( $results ) ) {
			return;
		}

		foreach ( $results as $row ) {
			$value = (int) str_replace( ',', '', (string) $row->meta_value );
			update_post_meta( (int) $row->post_id, 'rm_apartment_rent_month', $value );
		}

		update_option( 'community_price_updated', 1 );
	}

	/**
	 * Load legacy stack as compatibility layer.
	 *
	 * @return void
	 */
	private function load_legacy_stack() {
		$should_load_legacy = apply_filters( 'rm_load_legacy_stack', true );

		if ( ! $should_load_legacy ) {
			return;
		}

		require_once RM_PLUGIN_DIR_PATH . 'rm-exe.php';
	}
}
