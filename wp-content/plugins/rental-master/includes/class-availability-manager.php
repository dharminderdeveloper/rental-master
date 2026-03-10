<?php
/**
 * Availability manager admin page.
 *
 * @package RentalMaster
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class RM_Availability_Manager {
	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'register_menu' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_assets' ) );
		add_action( 'wp_ajax_update_availability_manager_ajax', array( $this, 'handle_update' ) );
		add_action( 'wp_ajax_abr_update_apartment_featured', array( $this, 'handle_featured_update' ) );
	}

	/**
	 * Register submenu under the plugin dashboard.
	 *
	 * @return void
	 */
	public function register_menu() {
		add_submenu_page(
			'rm-dashboard',
			__( 'Availability Manager', 'ar' ),
			__( 'Availability Manager', 'ar' ),
			'manage_options',
			'availability-manager',
			array( $this, 'render_page' )
		);
	}

	/**
	 * Enqueue admin assets on the availability manager page.
	 *
	 * @param string $hook_suffix Current page hook.
	 *
	 * @return void
	 */
	public function enqueue_assets( $hook_suffix ) {
		if ( false === strpos( $hook_suffix, 'availability-manager' ) ) {
			return;
		}

		$css_file = RM_PLUGIN_DIR_PATH . 'admin/premium/css/abr-premium.css';
		if ( file_exists( $css_file ) ) {
			wp_enqueue_style(
				'rm-availability',
				RM_PLUGIN_DIR_URL . 'admin/premium/css/abr-premium.css',
				array(),
				(string) filemtime( $css_file )
			);
		}

		$js_file = RM_PLUGIN_DIR_PATH . 'admin/js/availability-manager.js';
		if ( file_exists( $js_file ) ) {
			wp_enqueue_script(
				'rm-availability',
				RM_PLUGIN_DIR_URL . 'admin/js/availability-manager.js',
				array( 'jquery' ),
				(string) filemtime( $js_file ),
				true
			);
			wp_localize_script(
				'rm-availability',
				'RMAvailability',
				array(
					'ajaxUrl' => admin_url( 'admin-ajax.php' ),
					'nonce'   => wp_create_nonce( 'rm_availability_nonce' ),
				)
			);
		}
	}

	/**
	 * Render availability manager page.
	 *
	 * @return void
	 */
	public function render_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You do not have permission to access this page.', 'ar' ) );
		}
		?>
		<div class="wrap">
			<h1><?php echo esc_html__( 'Availability Manager', 'ar' ); ?></h1>
			<div class="availability-manager">
				<?php $this->render_page_content(); ?>
			</div>
		</div>
		<?php
	}

	/**
	 * Render availability manager listing.
	 *
	 * @return void
	 */
	private function render_page_content() {
		$ids = $this->get_apartment_ids();
		?>
		<ul class="availHead">
			<li class="welcome-panel">
				<span><?php echo esc_html__( 'Title', 'ar' ); ?></span>
				<span><?php echo esc_html__( 'Rent', 'ar' ); ?></span>
				<span><?php echo esc_html__( 'Monthly Rent Range', 'ar' ); ?></span>
				<span><?php echo esc_html__( 'No of Units Available', 'ar' ); ?></span>
				<span><?php echo esc_html__( 'No of Units', 'ar' ); ?></span>
				<span><?php echo esc_html__( 'Date Available', 'ar' ); ?></span>
				<span><?php echo esc_html__( 'Featured', 'ar' ); ?></span>
				<span><?php echo esc_html__( 'Action', 'ar' ); ?></span>
			</li>
			<?php foreach ( $ids as $id ) : ?>
				<?php $this->render_apartment_row( $id ); ?>
			<?php endforeach; ?>
		</ul>
		<?php
	}

	/**
	 * Fetch apartment IDs.
	 *
	 * @return int[]
	 */
	private function get_apartment_ids() {
		$args = array(
			'orderby'        => 'date',
			'order'          => 'DESC',
			'post_type'      => 'apartment',
			'post_status'    => 'publish',
			'fields'         => 'ids',
			'posts_per_page' => -1,
		);
		return array_map( 'absint', (array) get_posts( $args ) );
	}

	/**
	 * Render apartment row.
	 *
	 * @param int $id Apartment ID.
	 *
	 * @return void
	 */
	private function render_apartment_row( $id ) {
		$title          = get_the_title( $id );
		$selected_range = wp_get_post_terms( $id, 'apartment_monthly_rent' );
		$featured       = get_post_meta( $id, 'rm_apartment_featured', true );
		$rent           = get_post_meta( $id, 'rm_apartment_rent_month', true );
		$units          = get_post_meta( $id, 'rm_apartment_no_of_units', true );
		$date           = get_post_meta( $id, 'rm_apartment_date_available', true );
		$units_avail    = get_post_meta( $id, 'rm_apartment_no_of_units_available', true );
		?>
		<li pid="<?php echo esc_attr( $id ); ?>">
			<span><a href="<?php echo esc_url( get_permalink( $id ) ); ?>"><?php echo esc_html( $title ); ?></a></span>
			<span><input type="text" value="<?php echo esc_attr( $rent ); ?>" id="Rent"></span>
			<span><?php echo $this->render_rent_range_select_box( $selected_range ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
			<span><input type="text" value="<?php echo esc_attr( $units_avail ); ?>" id="unitAvail"></span>
			<span><input type="text" value="<?php echo esc_attr( $units ); ?>" id="Units"></span>
			<span><input type="date" value="<?php echo esc_attr( $date ); ?>" class="datepicker" id="availDate"></span>
			<span><input class="checkbox" type="checkbox" <?php checked( $featured, 'on' ); ?> id="isfeatured" value="<?php echo esc_attr( $featured ); ?>"/></span>
			<span><a href="javascript:void(0)" class="button button-primary button-large updateApartment"><?php echo esc_html__( 'Update', 'ar' ); ?></a></span>
		</li>
		<?php
	}

	/**
	 * Render rent range select box.
	 *
	 * @param array $selected_range Selected term.
	 *
	 * @return string
	 */
	private function render_rent_range_select_box( $selected_range ) {
		$available_ranges = get_terms(
			array(
				'taxonomy'   => 'apartment_monthly_rent',
				'hide_empty' => false,
			)
		);
		$selected_id = isset( $selected_range[0]->term_id ) ? (int) $selected_range[0]->term_id : 0;

		ob_start();
		?>
		<select id="rentRange">
			<?php foreach ( (array) $available_ranges as $range ) : ?>
				<option value="<?php echo esc_attr( $range->term_id ); ?>" <?php selected( $selected_id, (int) $range->term_id ); ?>><?php echo esc_html( $range->name ); ?></option>
			<?php endforeach; ?>
		</select>
		<?php
		return (string) ob_get_clean();
	}

	/**
	 * Handle availability manager updates.
	 *
	 * @return void
	 */
	public function handle_update() {
		check_ajax_referer( 'rm_availability_nonce', 'nonce' );

		$id = isset( $_POST['id'] ) ? absint( $_POST['id'] ) : 0;
		if ( ! $id || ! current_user_can( 'edit_post', $id ) ) {
			wp_send_json_error( array( 'message' => __( 'Unauthorized request.', 'ar' ) ), 403 );
		}

		$rent      = isset( $_POST['Rent'] ) ? sanitize_text_field( wp_unslash( $_POST['Rent'] ) ) : '';
		$units     = isset( $_POST['Units'] ) ? sanitize_text_field( wp_unslash( $_POST['Units'] ) ) : '';
		$rentRange = isset( $_POST['rentRange'] ) ? absint( $_POST['rentRange'] ) : 0;
		$unitAvail = isset( $_POST['unitAvail'] ) ? sanitize_text_field( wp_unslash( $_POST['unitAvail'] ) ) : '';
		$availDate = isset( $_POST['availDate'] ) ? sanitize_text_field( wp_unslash( $_POST['availDate'] ) ) : '';
		$isFeatured = isset( $_POST['isFeatured'] ) ? sanitize_text_field( wp_unslash( $_POST['isFeatured'] ) ) : '';

		update_post_meta( $id, 'rm_apartment_rent_month', $rent, false );
		update_post_meta( $id, 'rm_apartment_no_of_units', $units, false );
		if ( $rentRange ) {
			wp_set_post_terms( $id, $rentRange, 'apartment_monthly_rent', false );
		}
		update_post_meta( $id, 'rm_apartment_featured', $isFeatured, false );
		update_post_meta( $id, 'rm_apartment_date_available', $availDate, false );
		update_post_meta( $id, 'rm_apartment_no_of_units_available', $unitAvail, false );

		wp_send_json_success( array( 'message' => __( 'Updated Successfully!', 'ar' ) ) );
	}

	/**
	 * Handle featured toggle updates.
	 *
	 * @return void
	 */
	public function handle_featured_update() {
		check_ajax_referer( 'rm_availability_nonce', 'nonce' );

		$id          = isset( $_POST['id'] ) ? absint( $_POST['id'] ) : 0;
		$is_featured = isset( $_POST['is_featured'] ) ? sanitize_text_field( wp_unslash( $_POST['is_featured'] ) ) : '';

		if ( ! $id || ! current_user_can( 'edit_post', $id ) ) {
			wp_send_json_error( array( 'message' => __( 'Unauthorized request.', 'ar' ) ), 403 );
		}

		$updated = update_post_meta( $id, 'rm_apartment_featured', $is_featured, false );
		if ( $updated ) {
			wp_send_json_success( array( 'message' => __( 'Updated Successfully!', 'ar' ) ) );
		}

		wp_send_json_error( array( 'message' => __( 'Could not update.', 'ar' ) ) );
	}
}
