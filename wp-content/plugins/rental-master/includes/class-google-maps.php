<?php
/**
 * Google Maps service.
 *
 * @package RentalMaster
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class RM_Google_Maps {

	/**
	 * Constructor.
	 */
	public function __construct() {
	}

	/**
	 * Return maps API key with compatibility fallback.
	 *
	 * @return string
	 */
	public function get_api_key() {
		$key = (string) get_option( 'abr_google_maps_api_key', '' );

		if ( '' !== $key ) {
			return $key;
		}

		if ( defined( 'RM_MAP_API_KEY' ) ) {
			return (string) RM_MAP_API_KEY;
		}

		return '';
	}
}
