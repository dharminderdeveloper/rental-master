<?php
/**
 * Deactivation handler.
 *
 * @package RentalMaster
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class RM_Deactivator {

	/**
	 * Plugin deactivation callback.
	 *
	 * @return void
	 */
	public static function deactivate() {
		wp_clear_scheduled_hook( 'wp_session_garbage_collection' );
	}
}
