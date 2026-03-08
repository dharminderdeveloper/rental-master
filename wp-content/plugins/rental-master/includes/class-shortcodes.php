<?php
/**
 * Shortcode router.
 *
 * @package RentalMaster
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class RM_Shortcodes {
	/**
	 * Track whether Tailwind config was already injected.
	 *
	 * @var bool
	 */
	private $tailwind_config_injected = false;

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_shortcode( 'rental_listings', array( $this, 'render_rental_listings' ) );
		add_shortcode( 'rental_search', array( $this, 'render_rental_search' ) );
		add_shortcode( 'rental_map', array( $this, 'render_rental_map' ) );
		add_shortcode( 'rental_sidebar_filters', array( $this, 'render_rental_sidebar_filters' ) );
	}

	/**
	 * Render rental listings shortcode.
	 *
	 * @param array<string,mixed> $atts Shortcode attributes.
	 *
	 * @return string
	 */
	public function render_rental_listings( $atts ) {
		$this->enqueue_modern_ui_assets( false );

		$defaults = array(
			'posts_per_page'  => (string) get_option( 'abr_searchPaginationNumber', 10 ),
			'orderby'         => 'date',
			'order'           => 'DESC',
			'location'        => '',
			'property_type'   => '',
			'rental_category' => '',
			'show_pagination' => 'yes',
		);

		$atts = shortcode_atts( $defaults, (array) $atts, 'rental_listings' );
		$atts = apply_filters( 'rm_shortcode_atts_rental_listings', $atts );

		$paged = max( 1, (int) get_query_var( 'paged', 1 ) );
		$show_pagination = ( 'yes' === strtolower( (string) $atts['show_pagination'] ) );

		$query_args = array(
			'post_type'      => 'rental_listing',
			'post_status'    => 'publish',
			'posts_per_page' => max( 1, min( 200, absint( $atts['posts_per_page'] ) ) ),
			'orderby'        => sanitize_key( $atts['orderby'] ),
			'order'          => 'ASC' === strtoupper( (string) $atts['order'] ) ? 'ASC' : 'DESC',
			'paged'          => $paged,
		);

		if ( ! $show_pagination ) {
			$query_args['no_found_rows']          = true;
			$query_args['update_post_meta_cache'] = false;
			$query_args['update_post_term_cache'] = false;
		}

		$request_filters = $this->get_request_filters();
		$selected = array(
			'location'        => $request_filters['location'] ? $request_filters['location'] : sanitize_title( $atts['location'] ),
			'property_type'   => $request_filters['property_type'] ? $request_filters['property_type'] : sanitize_title( $atts['property_type'] ),
			'rental_category' => $request_filters['rental_category'] ? $request_filters['rental_category'] : sanitize_title( $atts['rental_category'] ),
		);

		$tax_query = array();
		foreach ( $selected as $taxonomy => $slug ) {
			if ( '' === $slug ) {
				continue;
			}
			$tax_query[] = array(
				'taxonomy' => $taxonomy,
				'field'    => 'slug',
				'terms'    => array( $slug ),
			);
		}

		if ( ! empty( $tax_query ) ) {
			$query_args['tax_query'] = $tax_query;
		}

		$query_args = apply_filters( 'rm_shortcode_rental_listings_query_args', $query_args, $atts, $selected );
		$query      = new WP_Query( $query_args );

		$template_data = array(
			'query'           => $query,
			'atts'            => $atts,
			'selected'        => $selected,
			'show_pagination' => $show_pagination,
		);

		$output = $this->render_template( 'shortcode-rental-listings.php', $template_data );
		wp_reset_postdata();

		return $output;
	}

	/**
	 * Render search shortcode.
	 *
	 * @param array<string,mixed> $atts Shortcode attributes.
	 *
	 * @return string
	 */
	public function render_rental_search( $atts ) {
		$this->enqueue_modern_ui_assets( true );

		$defaults = array(
			'show_button' => 'yes',
			'button_text' => __( 'Search', 'ar' ),
		);

		$atts = shortcode_atts( $defaults, (array) $atts, 'rental_search' );
		$atts = apply_filters( 'rm_shortcode_atts_rental_search', $atts );

		$template_data = array(
			'atts'     => $atts,
			'selected' => $this->get_request_filters(),
			'terms'    => $this->get_filter_terms(),
		);

		wp_localize_script(
			'rm-public',
			'RM_FastSearch',
			array(
				'ajaxUrl'      => admin_url( 'admin-ajax.php' ),
				'nonce'        => wp_create_nonce( 'rm_fast_search_nonce' ),
				'action'       => 'rm_fast_search',
				'defaultLimit' => (int) get_option( 'abr_searchPaginationNumber', 10 ),
				'loadingText'  => __( 'Searching listings...', 'ar' ),
				'emptyText'    => __( 'No listings found.', 'ar' ),
			)
		);

		return $this->render_template( 'shortcode-rental-search.php', $template_data );
	}

	/**
	 * Render map shortcode.
	 *
	 * @param array<string,mixed> $atts Shortcode attributes.
	 *
	 * @return string
	 */
	public function render_rental_map( $atts ) {
		$this->enqueue_modern_ui_assets( false );

		$defaults = array(
			'height' => '420px',
			'zoom'   => '10',
			'limit'  => '100',
		);

		$atts = shortcode_atts( $defaults, (array) $atts, 'rental_map' );
		$atts = apply_filters( 'rm_shortcode_atts_rental_map', $atts );

		$query_args = array(
			'post_type'      => 'rental_listing',
			'post_status'    => 'publish',
			'posts_per_page' => max( 1, min( 500, absint( $atts['limit'] ) ) ),
			'fields'         => 'ids',
			'no_found_rows'  => true,
		);

		$query_args = apply_filters( 'rm_shortcode_rental_map_query_args', $query_args, $atts );
		$markers = $this->get_cached_markers( $query_args );
		$api_key = (string) get_option( 'abr_google_maps_api_key', '' );
		if ( '' === $api_key && defined( 'RM_MAP_API_KEY' ) ) {
			$api_key = (string) RM_MAP_API_KEY;
		}

		if ( '' !== $api_key ) {
			wp_enqueue_script(
				'rm-google-maps',
				'https://maps.googleapis.com/maps/api/js?key=' . rawurlencode( $api_key ),
				array(),
				null,
				true
			);
		}

		$template_data = array(
			'atts'    => $atts,
			'markers' => apply_filters( 'rm_shortcode_rental_map_markers', $markers, $atts ),
		);

		return $this->render_template( 'shortcode-rental-map.php', $template_data );
	}

	/**
	 * Render sidebar filters shortcode.
	 *
	 * @param array<string,mixed> $atts Shortcode attributes.
	 *
	 * @return string
	 */
	public function render_rental_sidebar_filters( $atts ) {
		$this->enqueue_modern_ui_assets( false );

		$defaults = array(
			'title' => __( 'Filter Listings', 'ar' ),
		);

		$atts = shortcode_atts( $defaults, (array) $atts, 'rental_sidebar_filters' );
		$atts = apply_filters( 'rm_shortcode_atts_rental_sidebar_filters', $atts );

		$template_data = array(
			'atts'     => $atts,
			'selected' => $this->get_request_filters(),
			'terms'    => $this->get_filter_terms(),
		);

		return $this->render_template( 'shortcode-rental-sidebar-filters.php', $template_data );
	}

	/**
	 * Load template with support for theme overrides.
	 *
	 * @param string              $template_name Template filename.
	 * @param array<string,mixed> $data Template variables.
	 *
	 * @return string
	 */
	private function render_template( $template_name, $data = array() ) {
		$theme_template = locate_template( array( 'rental-master/' . $template_name ) );
		$template_path  = $theme_template ? $theme_template : RM_PLUGIN_DIR_PATH . 'public/templates/' . $template_name;
		$template_path  = apply_filters( 'rm_shortcode_template_path', $template_path, $template_name, $data );

		if ( ! file_exists( $template_path ) ) {
			return '';
		}

		do_action( 'rm_before_shortcode_template', $template_name, $data );
		ob_start();
		include $template_path;
		$output = (string) ob_get_clean();
		do_action( 'rm_after_shortcode_template', $template_name, $data, $output );

		return $output;
	}

	/**
	 * Collect selected filters from request.
	 *
	 * @return array<string,string>
	 */
	private function get_request_filters() {
		return array(
			'location'        => isset( $_GET['location'] ) ? sanitize_title( wp_unslash( $_GET['location'] ) ) : '',
			'property_type'   => isset( $_GET['property_type'] ) ? sanitize_title( wp_unslash( $_GET['property_type'] ) ) : '',
			'rental_category' => isset( $_GET['rental_category'] ) ? sanitize_title( wp_unslash( $_GET['rental_category'] ) ) : '',
		);
	}

	/**
	 * Fetch filter terms.
	 *
	 * @return array<string,array<int,WP_Term>>
	 */
	private function get_filter_terms() {
		$transient_key = $this->get_cache_key( 'filter_terms', array( 'hide_empty' => false ), 1 );
		$terms         = get_transient( $transient_key );

		if ( ! is_array( $terms ) ) {
			$terms = array(
				'location'        => get_terms( array( 'taxonomy' => 'location', 'hide_empty' => false ) ),
				'property_type'   => get_terms( array( 'taxonomy' => 'property_type', 'hide_empty' => false ) ),
				'rental_category' => get_terms( array( 'taxonomy' => 'rental_category', 'hide_empty' => false ) ),
			);
			set_transient( $transient_key, $terms, 10 * MINUTE_IN_SECONDS );
		}

		return apply_filters( 'rm_shortcode_filter_terms', $terms );
	}

	/**
	 * Enqueue scoped Tailwind UI assets.
	 *
	 * @param bool $needs_js Whether interactive JS is required.
	 *
	 * @return void
	 */
	private function enqueue_modern_ui_assets( $needs_js = false ) {
		wp_enqueue_style( 'rm-public' );
		if ( $needs_js ) {
			wp_enqueue_script( 'rm-public' );
		}

		if ( ! wp_script_is( 'rm-tailwind-play', 'registered' ) ) {
			wp_register_script(
				'rm-tailwind-play',
				'https://cdn.tailwindcss.com',
				array(),
				null,
				false
			);
		}

		if ( ! $this->tailwind_config_injected ) {
			$config = "tailwind.config = {prefix: 'tw-', important: '.rental-plugin-wrapper'};";
			wp_add_inline_script( 'rm-tailwind-play', $config, 'before' );
			$this->tailwind_config_injected = true;
		}
		wp_enqueue_script( 'rm-tailwind-play' );
	}

	/**
	 * Build cache key that is invalidated when listing cache version changes.
	 *
	 * @param string              $type Cache type.
	 * @param array<string,mixed> $payload Cache payload.
	 * @param int                 $version Schema version.
	 *
	 * @return string
	 */
	private function get_cache_key( $type, $payload, $version ) {
		$cache_version = (int) get_option( 'rm_cache_version', 1 );
		return 'rm_' . $type . '_' . $version . '_' . $cache_version . '_' . md5( wp_json_encode( $payload ) );
	}

	/**
	 * Get cached map markers.
	 *
	 * @param array<string,mixed> $query_args Query arguments.
	 *
	 * @return array<int,array<string,mixed>>
	 */
	private function get_cached_markers( $query_args ) {
		$transient_key = $this->get_cache_key( 'map_markers', $query_args, 1 );
		$markers       = get_transient( $transient_key );
		if ( is_array( $markers ) ) {
			return $markers;
		}

		$post_ids = get_posts( $query_args );
		$markers  = array();

		foreach ( $post_ids as $post_id ) {
			$lat = get_post_meta( $post_id, 'map_lat', true );
			$lng = get_post_meta( $post_id, 'map_lng', true );

			if ( '' === (string) $lat || '' === (string) $lng ) {
				$legacy = (string) get_post_meta( $post_id, 'martygeocoderlatlng', true );
				if ( false !== strpos( $legacy, ',' ) ) {
					$parts = array_map( 'trim', explode( ',', $legacy ) );
					if ( count( $parts ) >= 2 ) {
						$lat = $parts[0];
						$lng = $parts[1];
					}
				}
			}

			if ( ! is_numeric( $lat ) || ! is_numeric( $lng ) ) {
				continue;
			}

			$price = get_post_meta( $post_id, 'price', true );
			if ( '' === (string) $price ) {
				$price = get_post_meta( $post_id, 'rm_apartment_rent_month', true );
			}

			$address = get_post_meta( $post_id, 'address', true );
			if ( '' === (string) $address ) {
				$address = get_post_meta( $post_id, 'rm_community_address', true );
			}

			$image = get_the_post_thumbnail_url( $post_id, 'medium' );
			if ( ! $image ) {
				$image = '';
			}

			$markers[] = array(
				'id'      => $post_id,
				'title'   => get_the_title( $post_id ),
				'url'     => get_permalink( $post_id ),
				'lat'     => (float) $lat,
				'lng'     => (float) $lng,
				'price'   => (string) $price,
				'address' => (string) $address,
				'image'   => (string) $image,
			);
		}

		set_transient( $transient_key, $markers, 10 * MINUTE_IN_SECONDS );
		return $markers;
	}
}
