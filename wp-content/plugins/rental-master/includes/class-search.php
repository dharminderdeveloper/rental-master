<?php
/**
 * Search service.
 *
 * @package RentalMaster
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class RM_Search {

	/**
	 * Constructor.
	 */
	public function __construct() {
	}

	/**
	 * Build default apartment listing query args.
	 *
	 * @param array<string,mixed> $args Optional args.
	 *
	 * @return array<string,mixed>
	 */
	public function get_default_apartment_query_args( $args = array() ) {
		$defaults = array(
			'post_type'      => 'apartment',
			'post_status'    => 'publish',
			'posts_per_page' => (int) get_option( 'abr_searchPaginationNumber', -1 ),
			'orderby'        => 'date',
			'order'          => 'DESC',
		);

		return wp_parse_args( $args, $defaults );
	}
}
