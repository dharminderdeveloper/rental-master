<?php
/**
 * Activation handler.
 *
 * @package RentalMaster
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class RM_Activator {

	/**
	 * Plugin activation callback.
	 *
	 * @return void
	 */
	public static function activate() {
		add_option( 'abr_do_activation_redirect', true );

		if ( false === get_option( 'abtrv', false ) ) {
			add_option( 'abtrv', 1 );
		}
	}
}
