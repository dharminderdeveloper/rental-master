<?php
/**
 * Uninstall routine.
 *
 * @package RentalMaster
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

$option_keys = array(
	'abr_do_activation_redirect',
	'abr_searchPaginationNumber',
	'abr_applicationFormInput',
	'abr_key',
	'abr_domain',
	'abtrs_date',
	'abtre_date',
	'abtrv',
	'community_price_updated',
	'leasing_special_updated',
);

foreach ( $option_keys as $option_key ) {
	delete_option( $option_key );
}
