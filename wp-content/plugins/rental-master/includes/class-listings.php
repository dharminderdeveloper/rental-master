<?php
/**
 * Listings module.
 *
 * @package RentalMaster
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class RM_Listings {

	/**
	 * Meta field keys.
	 *
	 * @var string[]
	 */
	private $meta_keys = array(
		'price',
		'bedrooms',
		'bathrooms',
		'availability',
		'address',
		'map_lat',
		'map_lng',
	);

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'register_post_type' ) );
		add_action( 'init', array( $this, 'register_taxonomies' ) );
		add_action( 'init', array( $this, 'register_meta' ) );
		add_action( 'add_meta_boxes', array( $this, 'add_meta_box' ) );
		add_action( 'save_post_rental_listing', array( $this, 'save_meta_box' ) );
		add_action( 'save_post_rental_listing', array( $this, 'bump_cache_version' ), 99 );
		add_action( 'deleted_post', array( $this, 'maybe_bump_cache_version_on_delete' ), 99 );
		add_action( 'created_term', array( $this, 'maybe_bump_cache_version_on_term' ), 99, 3 );
		add_action( 'edited_terms', array( $this, 'maybe_bump_cache_version_on_term' ), 99, 2 );
		add_action( 'delete_term', array( $this, 'maybe_bump_cache_version_on_delete_term' ), 99, 4 );
	}

	/**
	 * Bump cache version used by transient keys.
	 *
	 * @return void
	 */
	public function bump_cache_version() {
		$current = (int) get_option( 'rm_cache_version', 1 );
		update_option( 'rm_cache_version', $current + 1 );
	}

	/**
	 * Bump cache version when rental listing is deleted.
	 *
	 * @param int $post_id Deleted post ID.
	 *
	 * @return void
	 */
	public function maybe_bump_cache_version_on_delete( $post_id ) {
		if ( 'rental_listing' !== get_post_type( $post_id ) ) {
			return;
		}

		$this->bump_cache_version();
	}

	/**
	 * Bump cache version for relevant taxonomy term events.
	 *
	 * @param int    $term_id Term ID.
	 * @param int    $tt_id Term taxonomy ID.
	 * @param string $taxonomy Taxonomy name.
	 *
	 * @return void
	 */
	public function maybe_bump_cache_version_on_term( $term_id, $tt_id = 0, $taxonomy = '' ) { // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.FoundAfterLastUsed
		if ( ! in_array( $taxonomy, array( 'location', 'property_type', 'rental_category' ), true ) ) {
			return;
		}

		$this->bump_cache_version();
	}

	/**
	 * Bump cache version on taxonomy term deletion.
	 *
	 * @param int    $term Term ID.
	 * @param int    $tt_id Term taxonomy ID.
	 * @param string $taxonomy Taxonomy name.
	 * @param mixed  $deleted_term Deleted term object.
	 *
	 * @return void
	 */
	public function maybe_bump_cache_version_on_delete_term( $term, $tt_id, $taxonomy, $deleted_term ) { // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.FoundAfterLastUsed
		if ( ! in_array( $taxonomy, array( 'location', 'property_type', 'rental_category' ), true ) ) {
			return;
		}

		$this->bump_cache_version();
	}

	/**
	 * Register rental listing post type.
	 *
	 * @return void
	 */
	public function register_post_type() {
		$labels = array(
			'name'               => __( 'Rental Listings', 'ar' ),
			'singular_name'      => __( 'Rental Listing', 'ar' ),
			'menu_name'          => __( 'Rental Listings', 'ar' ),
			'name_admin_bar'     => __( 'Rental Listing', 'ar' ),
			'add_new'            => __( 'Add New', 'ar' ),
			'add_new_item'       => __( 'Add New Rental Listing', 'ar' ),
			'edit_item'          => __( 'Edit Rental Listing', 'ar' ),
			'new_item'           => __( 'New Rental Listing', 'ar' ),
			'view_item'          => __( 'View Rental Listing', 'ar' ),
			'search_items'       => __( 'Search Rental Listings', 'ar' ),
			'not_found'          => __( 'No rental listings found', 'ar' ),
			'not_found_in_trash' => __( 'No rental listings found in Trash', 'ar' ),
		);

		$args = array(
			'labels'             => $labels,
			'public'             => true,
			'show_ui'            => true,
			'show_in_menu'       => true,
			'show_in_rest'       => true,
			'has_archive'        => true,
			'rewrite'            => array( 'slug' => 'rental-listing' ),
			'supports'           => array( 'title', 'editor', 'thumbnail' ),
			'menu_icon'          => 'dashicons-building',
			'publicly_queryable' => true,
			'capability_type'    => 'post',
		);

		register_post_type( 'rental_listing', $args );
	}

	/**
	 * Register listing taxonomies.
	 *
	 * @return void
	 */
	public function register_taxonomies() {
		$this->register_taxonomy(
			'location',
			__( 'Locations', 'ar' ),
			__( 'Location', 'ar' ),
			true
		);

		$this->register_taxonomy(
			'property_type',
			__( 'Property Types', 'ar' ),
			__( 'Property Type', 'ar' ),
			true
		);

		$this->register_taxonomy(
			'rental_category',
			__( 'Rental Categories', 'ar' ),
			__( 'Rental Category', 'ar' ),
			true
		);
	}

	/**
	 * Helper for taxonomy registration.
	 *
	 * @param string $taxonomy Taxonomy key.
	 * @param string $name Plural label.
	 * @param string $singular Singular label.
	 * @param bool   $hierarchical Hierarchical flag.
	 *
	 * @return void
	 */
	private function register_taxonomy( $taxonomy, $name, $singular, $hierarchical ) {
		$labels = array(
			'name'          => $name,
			'singular_name' => $singular,
			'search_items'  => sprintf( __( 'Search %s', 'ar' ), $name ),
			'all_items'     => sprintf( __( 'All %s', 'ar' ), $name ),
			'edit_item'     => sprintf( __( 'Edit %s', 'ar' ), $singular ),
			'update_item'   => sprintf( __( 'Update %s', 'ar' ), $singular ),
			'add_new_item'  => sprintf( __( 'Add New %s', 'ar' ), $singular ),
			'menu_name'     => $name,
		);

		$args = array(
			'hierarchical'      => $hierarchical,
			'labels'            => $labels,
			'show_ui'           => true,
			'show_admin_column' => true,
			'show_in_rest'      => true,
			'query_var'         => true,
			'rewrite'           => array( 'slug' => $taxonomy ),
		);

		register_taxonomy( $taxonomy, array( 'rental_listing' ), $args );
	}

	/**
	 * Register post meta with sanitization.
	 *
	 * @return void
	 */
	public function register_meta() {
		register_post_meta(
			'rental_listing',
			'price',
			array(
				'type'              => 'number',
				'single'            => true,
				'show_in_rest'      => true,
				'sanitize_callback' => array( $this, 'sanitize_price' ),
				'auth_callback'     => array( $this, 'can_edit_listing' ),
			)
		);

		register_post_meta(
			'rental_listing',
			'bedrooms',
			array(
				'type'              => 'integer',
				'single'            => true,
				'show_in_rest'      => true,
				'sanitize_callback' => array( $this, 'sanitize_bedrooms' ),
				'auth_callback'     => array( $this, 'can_edit_listing' ),
			)
		);

		register_post_meta(
			'rental_listing',
			'bathrooms',
			array(
				'type'              => 'integer',
				'single'            => true,
				'show_in_rest'      => true,
				'sanitize_callback' => array( $this, 'sanitize_bathrooms' ),
				'auth_callback'     => array( $this, 'can_edit_listing' ),
			)
		);

		register_post_meta(
			'rental_listing',
			'availability',
			array(
				'type'              => 'string',
				'single'            => true,
				'show_in_rest'      => true,
				'sanitize_callback' => array( $this, 'sanitize_availability' ),
				'auth_callback'     => array( $this, 'can_edit_listing' ),
			)
		);

		register_post_meta(
			'rental_listing',
			'address',
			array(
				'type'              => 'string',
				'single'            => true,
				'show_in_rest'      => true,
				'sanitize_callback' => array( $this, 'sanitize_address' ),
				'auth_callback'     => array( $this, 'can_edit_listing' ),
			)
		);

		register_post_meta(
			'rental_listing',
			'map_lat',
			array(
				'type'              => 'number',
				'single'            => true,
				'show_in_rest'      => true,
				'sanitize_callback' => array( $this, 'sanitize_latitude' ),
				'auth_callback'     => array( $this, 'can_edit_listing' ),
			)
		);

		register_post_meta(
			'rental_listing',
			'map_lng',
			array(
				'type'              => 'number',
				'single'            => true,
				'show_in_rest'      => true,
				'sanitize_callback' => array( $this, 'sanitize_longitude' ),
				'auth_callback'     => array( $this, 'can_edit_listing' ),
			)
		);
	}

	/**
	 * Add meta box.
	 *
	 * @return void
	 */
	public function add_meta_box() {
		add_meta_box(
			'rm-listing-fields',
			__( 'Listing Details', 'ar' ),
			array( $this, 'render_meta_box' ),
			'rental_listing',
			'normal',
			'default'
		);
	}

	/**
	 * Render listing meta box.
	 *
	 * @param WP_Post $post Current post.
	 *
	 * @return void
	 */
	public function render_meta_box( $post ) {
		wp_nonce_field( 'rm_save_listing_meta', 'rm_listing_meta_nonce' );

		$values = array();
		foreach ( $this->meta_keys as $meta_key ) {
			$values[ $meta_key ] = $this->get_meta_with_legacy_fallback( $post->ID, $meta_key );
		}
		?>
		<table class="form-table" role="presentation">
			<tbody>
			<tr>
				<th scope="row"><label for="rm-price"><?php esc_html_e( 'Price', 'ar' ); ?></label></th>
				<td><input id="rm-price" name="price" type="number" step="0.01" min="0" class="regular-text" value="<?php echo esc_attr( $values['price'] ); ?>" /></td>
			</tr>
			<tr>
				<th scope="row"><label for="rm-bedrooms"><?php esc_html_e( 'Bedrooms', 'ar' ); ?></label></th>
				<td><input id="rm-bedrooms" name="bedrooms" type="number" min="0" max="100" class="regular-text" value="<?php echo esc_attr( $values['bedrooms'] ); ?>" /></td>
			</tr>
			<tr>
				<th scope="row"><label for="rm-bathrooms"><?php esc_html_e( 'Bathrooms', 'ar' ); ?></label></th>
				<td><input id="rm-bathrooms" name="bathrooms" type="number" min="0" max="100" class="regular-text" value="<?php echo esc_attr( $values['bathrooms'] ); ?>" /></td>
			</tr>
			<tr>
				<th scope="row"><label for="rm-availability"><?php esc_html_e( 'Availability', 'ar' ); ?></label></th>
				<td>
					<select id="rm-availability" name="availability">
						<?php
						$options = array(
							'available'    => __( 'Available', 'ar' ),
							'unavailable'  => __( 'Unavailable', 'ar' ),
							'coming_soon'  => __( 'Coming Soon', 'ar' ),
						);
						foreach ( $options as $key => $label ) :
							?>
							<option value="<?php echo esc_attr( $key ); ?>" <?php selected( $values['availability'], $key ); ?>><?php echo esc_html( $label ); ?></option>
						<?php endforeach; ?>
					</select>
				</td>
			</tr>
			<tr>
				<th scope="row"><label for="rm-address"><?php esc_html_e( 'Address', 'ar' ); ?></label></th>
				<td><input id="rm-address" name="address" type="text" class="regular-text" value="<?php echo esc_attr( $values['address'] ); ?>" /></td>
			</tr>
			<tr>
				<th scope="row"><label for="rm-map-lat"><?php esc_html_e( 'Map Latitude', 'ar' ); ?></label></th>
				<td><input id="rm-map-lat" name="map_lat" type="number" step="0.000001" min="-90" max="90" class="regular-text" value="<?php echo esc_attr( $values['map_lat'] ); ?>" /></td>
			</tr>
			<tr>
				<th scope="row"><label for="rm-map-lng"><?php esc_html_e( 'Map Longitude', 'ar' ); ?></label></th>
				<td><input id="rm-map-lng" name="map_lng" type="number" step="0.000001" min="-180" max="180" class="regular-text" value="<?php echo esc_attr( $values['map_lng'] ); ?>" /></td>
			</tr>
			</tbody>
		</table>
		<?php
	}

	/**
	 * Save listing metadata.
	 *
	 * @param int $post_id Listing ID.
	 *
	 * @return void
	 */
	public function save_meta_box( $post_id ) {
		if ( ! isset( $_POST['rm_listing_meta_nonce'] ) ) {
			return;
		}

		if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['rm_listing_meta_nonce'] ) ), 'rm_save_listing_meta' ) ) {
			return;
		}

		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		$sanitized = array(
			'price'        => $this->sanitize_price( $this->post_value( 'price' ) ),
			'bedrooms'     => $this->sanitize_bedrooms( $this->post_value( 'bedrooms' ) ),
			'bathrooms'    => $this->sanitize_bathrooms( $this->post_value( 'bathrooms' ) ),
			'availability' => $this->sanitize_availability( $this->post_value( 'availability' ) ),
			'address'      => $this->sanitize_address( $this->post_value( 'address' ) ),
			'map_lat'      => $this->sanitize_latitude( $this->post_value( 'map_lat' ) ),
			'map_lng'      => $this->sanitize_longitude( $this->post_value( 'map_lng' ) ),
		);

		foreach ( $sanitized as $meta_key => $value ) {
			if ( '' === $value || null === $value ) {
				delete_post_meta( $post_id, $meta_key );
				continue;
			}

			update_post_meta( $post_id, $meta_key, $value );
		}

		$this->sync_legacy_meta( $post_id, $sanitized );
	}

	/**
	 * Sync key metadata to legacy keys for compatibility.
	 *
	 * @param int                  $post_id Listing ID.
	 * @param array<string,string> $sanitized Sanitized data.
	 *
	 * @return void
	 */
	private function sync_legacy_meta( $post_id, $sanitized ) {
		if ( '' !== $sanitized['price'] ) {
			update_post_meta( $post_id, 'rm_apartment_rent_month', $sanitized['price'] );
		}

		if ( '' !== $sanitized['address'] ) {
			update_post_meta( $post_id, 'rm_community_address', $sanitized['address'] );
		}

		if ( '' !== $sanitized['availability'] ) {
			update_post_meta( $post_id, 'rm_apartment_date_available', $sanitized['availability'] );
		}

		if ( '' !== $sanitized['map_lat'] && '' !== $sanitized['map_lng'] ) {
			update_post_meta( $post_id, 'martygeocoderlatlng', $sanitized['map_lat'] . ',' . $sanitized['map_lng'] );
		}
	}

	/**
	 * Get a post value safely.
	 *
	 * @param string $key Input key.
	 *
	 * @return string
	 */
	private function post_value( $key ) {
		if ( ! isset( $_POST[ $key ] ) ) {
			return '';
		}

		return (string) wp_unslash( $_POST[ $key ] );
	}

	/**
	 * Get a listing field with fallback to legacy key map.
	 *
	 * @param int    $post_id Post ID.
	 * @param string $meta_key New meta key.
	 *
	 * @return string
	 */
	private function get_meta_with_legacy_fallback( $post_id, $meta_key ) {
		$value = get_post_meta( $post_id, $meta_key, true );
		if ( '' !== (string) $value ) {
			return (string) $value;
		}

		switch ( $meta_key ) {
			case 'price':
				return (string) get_post_meta( $post_id, 'rm_apartment_rent_month', true );

			case 'bedrooms':
				$terms = wp_get_post_terms( $post_id, 'apartment_bedrooms', array( 'fields' => 'names' ) );
				return ! empty( $terms ) ? (string) absint( preg_replace( '/\D+/', '', $terms[0] ) ) : '';

			case 'bathrooms':
				$terms = wp_get_post_terms( $post_id, 'apartment_bathrooms', array( 'fields' => 'names' ) );
				return ! empty( $terms ) ? (string) absint( preg_replace( '/\D+/', '', $terms[0] ) ) : '';

			case 'availability':
				$legacy = (string) get_post_meta( $post_id, 'rm_apartment_date_available', true );
				if ( '' !== $legacy ) {
					return $legacy;
				}
				$terms = wp_get_post_terms( $post_id, 'apartment_availability_options', array( 'fields' => 'names' ) );
				return ! empty( $terms ) ? sanitize_key( $terms[0] ) : 'available';

			case 'address':
				$legacy_address = (string) get_post_meta( $post_id, 'rm_community_address', true );
				if ( '' !== $legacy_address ) {
					return $legacy_address;
				}
				$community_id = (int) get_post_meta( $post_id, 'rm_apartment_community', true );
				if ( $community_id > 0 ) {
					return (string) get_post_meta( $community_id, 'rm_community_address', true );
				}
				return '';

			case 'map_lat':
			case 'map_lng':
				$lat_lng = (string) get_post_meta( $post_id, 'martygeocoderlatlng', true );
				if ( '' === $lat_lng || false === strpos( $lat_lng, ',' ) ) {
					return '';
				}
				$parts = array_map( 'trim', explode( ',', $lat_lng ) );
				if ( count( $parts ) < 2 ) {
					return '';
				}
				return 'map_lat' === $meta_key ? $parts[0] : $parts[1];
		}

		return '';
	}

	/**
	 * Permission check callback for register_post_meta.
	 *
	 * @return bool
	 */
	public function can_edit_listing() {
		return current_user_can( 'edit_posts' );
	}

	/**
	 * Sanitize price value.
	 *
	 * @param string $value Raw input value.
	 *
	 * @return string
	 */
	public function sanitize_price( $value ) {
		$clean = preg_replace( '/[^0-9.]/', '', (string) $value );
		if ( '' === $clean || ! is_numeric( $clean ) ) {
			return '';
		}

		$price = (float) $clean;
		if ( $price < 0 ) {
			return '';
		}

		return (string) round( $price, 2 );
	}

	/**
	 * Sanitize bedrooms value.
	 *
	 * @param string $value Raw input value.
	 *
	 * @return string
	 */
	public function sanitize_bedrooms( $value ) {
		$number = absint( $value );
		if ( $number > 100 ) {
			return '100';
		}
		return (string) $number;
	}

	/**
	 * Sanitize bathrooms value.
	 *
	 * @param string $value Raw input value.
	 *
	 * @return string
	 */
	public function sanitize_bathrooms( $value ) {
		$number = absint( $value );
		if ( $number > 100 ) {
			return '100';
		}
		return (string) $number;
	}

	/**
	 * Sanitize availability value.
	 *
	 * @param string $value Raw input value.
	 *
	 * @return string
	 */
	public function sanitize_availability( $value ) {
		$allowed = array( 'available', 'unavailable', 'coming_soon' );
		$clean   = sanitize_key( $value );
		if ( in_array( $clean, $allowed, true ) ) {
			return $clean;
		}
		return 'available';
	}

	/**
	 * Sanitize address value.
	 *
	 * @param string $value Raw input value.
	 *
	 * @return string
	 */
	public function sanitize_address( $value ) {
		return sanitize_text_field( $value );
	}

	/**
	 * Sanitize latitude value.
	 *
	 * @param string $value Raw input value.
	 *
	 * @return string
	 */
	public function sanitize_latitude( $value ) {
		if ( '' === (string) $value ) {
			return '';
		}

		if ( ! is_numeric( $value ) ) {
			return '';
		}

		$lat = (float) $value;
		if ( $lat < -90 || $lat > 90 ) {
			return '';
		}

		return (string) round( $lat, 6 );
	}

	/**
	 * Sanitize longitude value.
	 *
	 * @param string $value Raw input value.
	 *
	 * @return string
	 */
	public function sanitize_longitude( $value ) {
		if ( '' === (string) $value ) {
			return '';
		}

		if ( ! is_numeric( $value ) ) {
			return '';
		}

		$lng = (float) $value;
		if ( $lng < -180 || $lng > 180 ) {
			return '';
		}

		return (string) round( $lng, 6 );
	}
}
