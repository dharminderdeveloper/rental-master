<?php
/**
 * AJAX router.
 *
 * @package RentalMaster
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class RM_Ajax {

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'wp_ajax_rm_health_check', array( $this, 'health_check' ) );
		add_action( 'wp_ajax_rm_fast_search', array( $this, 'fast_search' ) );
		add_action( 'wp_ajax_nopriv_rm_fast_search', array( $this, 'fast_search' ) );
	}

	/**
	 * Basic AJAX health check endpoint.
	 *
	 * @return void
	 */
	public function health_check() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error(
				array(
					'message' => __( 'Unauthorized request.', 'ar' ),
				),
				403
			);
		}

		wp_send_json_success(
			array(
				'message' => __( 'Rental master AJAX router is active.', 'ar' ),
			)
		);
	}

	/**
	 * Fast search AJAX endpoint.
	 *
	 * @return void
	 */
	public function fast_search() {
		check_ajax_referer( 'rm_fast_search_nonce', 'nonce' );

		$name          = isset( $_POST['name'] ) ? sanitize_text_field( wp_unslash( $_POST['name'] ) ) : '';
		$location      = isset( $_POST['location'] ) ? sanitize_title( wp_unslash( $_POST['location'] ) ) : '';
		$category      = isset( $_POST['rental_category'] ) ? sanitize_title( wp_unslash( $_POST['rental_category'] ) ) : '';
		$property_type = isset( $_POST['property_type'] ) ? sanitize_title( wp_unslash( $_POST['property_type'] ) ) : '';
		$limit         = isset( $_POST['limit'] ) ? absint( $_POST['limit'] ) : 12;
		$limit         = max( 1, min( 50, $limit ) );

		$query_args = array(
			'post_type'              => 'rental_listing',
			'post_status'            => 'publish',
			'posts_per_page'         => $limit,
			's'                      => $name,
			'fields'                 => 'ids',
			'no_found_rows'          => true,
			'cache_results'          => false,
			'update_post_meta_cache' => false,
			'update_post_term_cache' => false,
			'orderby'                => 'date',
			'order'                  => 'DESC',
			'suppress_filters'       => true,
		);

		$tax_query = array();
		if ( '' !== $location ) {
			$tax_query[] = array(
				'taxonomy' => 'location',
				'field'    => 'slug',
				'terms'    => array( $location ),
			);
		}

		if ( '' !== $category ) {
			$tax_query[] = array(
				'taxonomy' => 'rental_category',
				'field'    => 'slug',
				'terms'    => array( $category ),
			);
		}

		if ( '' !== $property_type ) {
			$tax_query[] = array(
				'taxonomy' => 'property_type',
				'field'    => 'slug',
				'terms'    => array( $property_type ),
			);
		}

		if ( ! empty( $tax_query ) ) {
			$query_args['tax_query'] = $tax_query;
		}

		$query_args    = apply_filters( 'rm_fast_search_query_args', $query_args );
		$cache_version = (int) get_option( 'rm_cache_version', 1 );
		$transient_key = 'rm_fast_search_' . $cache_version . '_' . md5( wp_json_encode( $query_args ) );
		$items         = get_transient( $transient_key );

		if ( ! is_array( $items ) ) {
			$query = new WP_Query( $query_args );
			$items = array();
			foreach ( $query->posts as $post_id ) {
				$price = get_post_meta( $post_id, 'price', true );
				if ( '' === (string) $price ) {
					$price = get_post_meta( $post_id, 'rm_apartment_rent_month', true );
				}

				$items[] = array(
					'id'        => $post_id,
					'title'     => get_the_title( $post_id ),
					'permalink' => get_permalink( $post_id ),
					'price'     => (string) $price,
				);
			}
			set_transient( $transient_key, $items, 3 * MINUTE_IN_SECONDS );
		}

		wp_send_json_success(
			array(
				'count'   => count( $items ),
				'results' => $items,
			)
		);
	}
}
