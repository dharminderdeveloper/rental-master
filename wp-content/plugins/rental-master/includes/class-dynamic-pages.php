<?php
/**
 * Dynamic page creator for legacy shortcodes.
 *
 * @package RentalMaster
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class RM_Dynamic_Pages {
	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'after_setup_theme', array( $this, 'maybe_create_pages' ) );
	}

	/**
	 * Create default pages for legacy shortcodes.
	 *
	 * @return void
	 */
	public function maybe_create_pages() {
		$pages = array(
			'apartments-listing'   => array( 'title' => 'Apartment Listing', 'content' => '[ar_apartment_listing]' ),
			'email-favorites'      => array( 'title' => 'Email Favorites', 'content' => '[ar_email_favorites_to_friends]' ),
			'my-favorites'         => array( 'title' => 'My Favorites', 'content' => '[ar_my_favorites_apartment]' ),
			'community-apartments' => array( 'title' => 'Apartments Of Community', 'content' => '[ar_apartments_Of_Specific_Community]' ),
			'community-listing'    => array( 'title' => 'Community Listing', 'content' => '[ar_community_listing_default]' ),
			'leasing-special'      => array( 'title' => 'Leasing Special', 'content' => '[ar_leasing_specials]' ),
			'featured-apartments'  => array( 'title' => 'Featured Apartments', 'content' => '[ar_featured_apartments]' ),
			'realestate-listing'   => array( 'title' => 'Real Estate Listing', 'content' => '[ar_realestate_listing]' ),
			'search-listing'       => array( 'title' => 'Search Listing', 'content' => '[ar_search_listing]' ),
		);

		$pages = apply_filters( 'rm_default_pages', $pages );
		if ( empty( $pages ) || ! is_array( $pages ) ) {
			return;
		}

		foreach ( $pages as $slug => $page ) {
			$slug = sanitize_title( $slug );
			if ( '' === $slug ) {
				continue;
			}

			$existing = get_page_by_path( $slug );
			if ( $existing instanceof WP_Post ) {
				continue;
			}

			$title   = isset( $page['title'] ) ? sanitize_text_field( $page['title'] ) : '';
			$content = isset( $page['content'] ) ? (string) $page['content'] : '';

			wp_insert_post(
				array(
					'post_content'   => $content,
					'post_name'      => $slug,
					'post_title'     => $title,
					'page_template'  => 'template-dashboard.php',
					'post_status'    => 'publish',
					'post_type'      => 'page',
					'ping_status'    => 'closed',
					'comment_status' => 'closed',
					'post_parent'    => 0,
				)
			);
		}
	}
}
