<?php
/**
 * Admin layer.
 *
 * @package RentalMaster
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class RM_Admin {

	/**
	 * Admin capability.
	 *
	 * @var string
	 */
	private $capability = 'manage_options';

	/**
	 * Known plugin shortcodes.
	 *
	 * @var array<string,string>
	 */
	private $shortcodes = array(
		'ar_apartment_listing'           => 'Apartment listing',
		'ar_community_listing_default'   => 'Community listing',
		'ar_featured_apartments'         => 'Featured apartments',
		'ar_leasing_specials'            => 'Leasing specials',
		'ar_apartment_application_form'  => 'Apartment application form',
		'ar_email_favorites_to_friends'  => 'Email favorites form',
		'ar_my_favorites_apartment'      => 'My favorites',
		'ar_apartments_Of_Specific_Community' => 'Apartments by community',
		'ar_search_listing'              => 'Advanced search',
		'ar_realestate_listing'          => 'Real estate listing',
	);

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'admin_init', array( $this, 'maybe_activation_redirect' ) );
		add_action( 'admin_init', array( $this, 'register_settings' ) );
		add_action( 'admin_menu', array( $this, 'register_admin_menu' ), 20 );
		add_action( 'admin_menu', array( $this, 'cleanup_legacy_menus' ), 999 );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_assets' ) );
		add_action( 'admin_post_rm_run_dev_tool', array( $this, 'handle_dev_tool_action' ) );
	}

	/**
	 * Handle one-time redirect on activation.
	 *
	 * @return void
	 */
	public function maybe_activation_redirect() {
		$should_redirect = get_option( 'abr_do_activation_redirect', false );

		if ( ! $should_redirect ) {
			return;
		}

		delete_option( 'abr_do_activation_redirect' );

		if ( function_exists( 'abr_insert_predefined_data' ) ) {
			abr_insert_predefined_data();
		}

		if ( isset( $_GET['activate-multi'] ) ) {
			return;
		}

		wp_safe_redirect( admin_url( 'admin.php?page=abr-user-help' ) );
		exit;
	}

	/**
	 * Register admin menu tree.
	 *
	 * @return void
	 */
	public function register_admin_menu() {
		add_menu_page(
			__( 'Rental master', 'ar' ),
			__( 'Rental master', 'ar' ),
			$this->capability,
			'rm-dashboard',
			array( $this, 'render_dashboard_page' ),
			'dashicons-admin-home',
			25
		);

		add_submenu_page(
			'rm-dashboard',
			__( 'Dashboard', 'ar' ),
			__( 'Dashboard', 'ar' ),
			$this->capability,
			'rm-dashboard',
			array( $this, 'render_dashboard_page' )
		);

		add_submenu_page(
			'rm-dashboard',
			__( 'Listings', 'ar' ),
			__( 'Listings', 'ar' ),
			'edit_posts',
			'edit.php?post_type=rental_listing'
		);

		add_submenu_page(
			'rm-dashboard',
			__( 'Search Settings', 'ar' ),
			__( 'Search Settings', 'ar' ),
			$this->capability,
			'rm-search-settings',
			array( $this, 'render_search_settings_page' )
		);

		add_submenu_page(
			'rm-dashboard',
			__( 'Map Settings', 'ar' ),
			__( 'Map Settings', 'ar' ),
			$this->capability,
			'rm-map-settings',
			array( $this, 'render_map_settings_page' )
		);

		add_submenu_page(
			'rm-dashboard',
			__( 'UI Settings', 'ar' ),
			__( 'UI Settings', 'ar' ),
			$this->capability,
			'rm-ui-settings',
			array( $this, 'render_ui_settings_page' )
		);

		add_submenu_page(
			'rm-dashboard',
			__( 'Shortcodes', 'ar' ),
			__( 'Shortcodes', 'ar' ),
			$this->capability,
			'rm-shortcodes',
			array( $this, 'render_shortcodes_page' )
		);

		add_submenu_page(
			'rm-dashboard',
			__( 'Developer Hooks', 'ar' ),
			__( 'Developer Hooks', 'ar' ),
			$this->capability,
			'rm-developer-hooks',
			array( $this, 'render_developer_hooks_page' )
		);
	}

	/**
	 * Remove legacy pages replaced by modern dashboard.
	 *
	 * @return void
	 */
	public function cleanup_legacy_menus() {
		if ( ! current_user_can( $this->capability ) ) {
			return;
		}

		remove_menu_page( 'abr-search-settings' );
		remove_menu_page( 'availability-manager' );
	}

	/**
	 * Register settings using Settings API.
	 *
	 * @return void
	 */
	public function register_settings() {
		register_setting(
			'rm_search_settings_group',
			'abr_searchPaginationNumber',
			array(
				'type'              => 'integer',
				'sanitize_callback' => array( $this, 'sanitize_pagination_number' ),
				'default'           => 10,
			)
		);

		add_settings_section(
			'rm_search_main',
			__( 'Search Behavior', 'ar' ),
			array( $this, 'render_search_section_description' ),
			'rm-search-settings'
		);

		add_settings_field(
			'abr_searchPaginationNumber',
			__( 'Records Per Page', 'ar' ),
			array( $this, 'render_search_records_field' ),
			'rm-search-settings',
			'rm_search_main'
		);

		register_setting(
			'rm_map_settings_group',
			'abr_google_maps_api_key',
			array(
				'type'              => 'string',
				'sanitize_callback' => 'sanitize_text_field',
				'default'           => '',
			)
		);

		add_settings_section(
			'rm_map_main',
			__( 'Google Maps Configuration', 'ar' ),
			array( $this, 'render_map_section_description' ),
			'rm-map-settings'
		);

		add_settings_field(
			'abr_google_maps_api_key',
			__( 'Google Maps API Key', 'ar' ),
			array( $this, 'render_map_api_field' ),
			'rm-map-settings',
			'rm_map_main'
		);

		register_setting(
			'rm_ui_settings_group',
			'rm_ui_settings',
			array(
				'type'              => 'array',
				'sanitize_callback' => array( $this, 'sanitize_ui_settings' ),
				'default'           => array(
					'card_style'      => 'modern',
					'show_map'        => '1',
					'enable_animations' => '0',
				),
			)
		);

		add_settings_section(
			'rm_ui_main',
			__( 'Frontend UI Defaults', 'ar' ),
			array( $this, 'render_ui_section_description' ),
			'rm-ui-settings'
		);

		add_settings_field(
			'rm_ui_card_style',
			__( 'Listing Card Style', 'ar' ),
			array( $this, 'render_ui_card_style_field' ),
			'rm-ui-settings',
			'rm_ui_main'
		);

		add_settings_field(
			'rm_ui_show_map',
			__( 'Show Map on Search', 'ar' ),
			array( $this, 'render_ui_show_map_field' ),
			'rm-ui-settings',
			'rm_ui_main'
		);

		add_settings_field(
			'rm_ui_enable_animations',
			__( 'Enable Animations', 'ar' ),
			array( $this, 'render_ui_animation_field' ),
			'rm-ui-settings',
			'rm_ui_main'
		);
	}

	/**
	 * Enqueue admin assets for plugin pages.
	 *
	 * @param string $hook_suffix Current page hook.
	 *
	 * @return void
	 */
	public function enqueue_assets( $hook_suffix ) {
		if ( false === strpos( $hook_suffix, 'rm-' ) ) {
			return;
		}

		$css_file = RM_PLUGIN_DIR_PATH . 'admin/css/admin.css';
		$js_file  = RM_PLUGIN_DIR_PATH . 'admin/js/admin.js';

		if ( file_exists( $css_file ) ) {
			wp_enqueue_style(
				'rm-admin',
				RM_PLUGIN_DIR_URL . 'admin/css/admin.css',
				array(),
				(string) filemtime( $css_file )
			);
		}

		if ( file_exists( $js_file ) ) {
			wp_enqueue_script(
				'rm-admin',
				RM_PLUGIN_DIR_URL . 'admin/js/admin.js',
				array( 'jquery', 'wp-util' ),
				(string) filemtime( $js_file ),
				true
			);
		}
	}

	/**
	 * Handle developer page action with nonce verification.
	 *
	 * @return void
	 */
	public function handle_dev_tool_action() {
		if ( ! current_user_can( $this->capability ) ) {
			wp_die( esc_html__( 'You do not have permission to perform this action.', 'ar' ) );
		}

		check_admin_referer( 'rm_dev_tool_action', 'rm_dev_tool_nonce' );

		do_action( 'rm_before_dev_tool_action' );
		update_option( 'rm_last_dev_action', current_time( 'mysql' ) );
		do_action( 'rm_after_dev_tool_action' );

		wp_safe_redirect(
			add_query_arg(
				array(
					'page'    => 'rm-developer-hooks',
					'updated' => 'true',
				),
				admin_url( 'admin.php' )
			)
		);
		exit;
	}

	/**
	 * Render dashboard page.
	 *
	 * @return void
	 */
	public function render_dashboard_page() {
		$this->assert_capability();
		$this->render_page_header(
			__( 'Dashboard', 'ar' ),
			__( 'Centralized controls for listings, search, maps, and UI behavior.', 'ar' )
		);
		?>
		<div class="rm-admin-grid">
			<div class="rm-admin-card">
				<h2><?php esc_html_e( 'Quick Actions', 'ar' ); ?></h2>
				<p><?php esc_html_e( 'Manage your inventory and plugin settings from one place.', 'ar' ); ?></p>
				<p><a class="button button-primary" href="<?php echo esc_url( admin_url( 'edit.php?post_type=rental_listing' ) ); ?>"><?php esc_html_e( 'Manage Listings', 'ar' ); ?></a></p>
				<p><a class="button" href="<?php echo esc_url( admin_url( 'admin.php?page=rm-search-settings' ) ); ?>"><?php esc_html_e( 'Configure Search', 'ar' ); ?></a></p>
			</div>
			<div class="rm-admin-card">
				<h2><?php esc_html_e( 'Status', 'ar' ); ?></h2>
				<ul>
					<li><strong><?php esc_html_e( 'Search Records/Page:', 'ar' ); ?></strong> <?php echo esc_html( (string) get_option( 'abr_searchPaginationNumber', 10 ) ); ?></li>
					<li><strong><?php esc_html_e( 'Maps API Key:', 'ar' ); ?></strong> <?php echo get_option( 'abr_google_maps_api_key', '' ) ? esc_html__( 'Configured', 'ar' ) : esc_html__( 'Missing', 'ar' ); ?></li>
					<li><strong><?php esc_html_e( 'Last Dev Action:', 'ar' ); ?></strong> <?php echo esc_html( (string) get_option( 'rm_last_dev_action', __( 'Never', 'ar' ) ) ); ?></li>
				</ul>
			</div>
		</div>
		<?php
		do_action( 'rm_after_dashboard_render' );
		echo '</div>';
	}

	/**
	 * Render search settings page.
	 *
	 * @return void
	 */
	public function render_search_settings_page() {
		$this->assert_capability();
		$this->render_page_header(
			__( 'Search Settings', 'ar' ),
			__( 'Tune listing search performance and pagination behavior.', 'ar' )
		);
		?>
		<form method="post" action="options.php" class="rm-admin-card">
			<?php
			settings_fields( 'rm_search_settings_group' );
			do_settings_sections( 'rm-search-settings' );
			submit_button( __( 'Save Search Settings', 'ar' ) );
			?>
		</form>
		<?php
		echo '</div>';
	}

	/**
	 * Render map settings page.
	 *
	 * @return void
	 */
	public function render_map_settings_page() {
		$this->assert_capability();
		$this->render_page_header(
			__( 'Map Settings', 'ar' ),
			__( 'Configure map integrations and geolocation behavior.', 'ar' )
		);
		?>
		<form method="post" action="options.php" class="rm-admin-card">
			<?php
			settings_fields( 'rm_map_settings_group' );
			do_settings_sections( 'rm-map-settings' );
			submit_button( __( 'Save Map Settings', 'ar' ) );
			?>
		</form>
		<?php
		echo '</div>';
	}

	/**
	 * Render UI settings page.
	 *
	 * @return void
	 */
	public function render_ui_settings_page() {
		$this->assert_capability();
		$this->render_page_header(
			__( 'UI Settings', 'ar' ),
			__( 'Set frontend display defaults for the rental experience.', 'ar' )
		);
		?>
		<form method="post" action="options.php" class="rm-admin-card">
			<?php
			settings_fields( 'rm_ui_settings_group' );
			do_settings_sections( 'rm-ui-settings' );
			submit_button( __( 'Save UI Settings', 'ar' ) );
			?>
		</form>
		<?php
		echo '</div>';
	}

	/**
	 * Render shortcode reference page.
	 *
	 * @return void
	 */
	public function render_shortcodes_page() {
		$this->assert_capability();
		$this->render_page_header(
			__( 'Shortcodes', 'ar' ),
			__( 'Copy and paste these shortcodes into pages, posts, or widget blocks.', 'ar' )
		);
		?>
		<div class="rm-admin-card">
			<table class="widefat striped">
				<thead>
					<tr>
						<th><?php esc_html_e( 'Shortcode', 'ar' ); ?></th>
						<th><?php esc_html_e( 'Description', 'ar' ); ?></th>
					</tr>
				</thead>
				<tbody>
				<?php foreach ( $this->shortcodes as $shortcode => $description ) : ?>
					<tr>
						<td><code>[<?php echo esc_html( $shortcode ); ?>]</code></td>
						<td><?php echo esc_html( $description ); ?></td>
					</tr>
				<?php endforeach; ?>
				</tbody>
			</table>
		</div>
		<?php
		echo '</div>';
	}

	/**
	 * Render developer hooks page.
	 *
	 * @return void
	 */
	public function render_developer_hooks_page() {
		$this->assert_capability();
		$updated = isset( $_GET['updated'] ) ? sanitize_text_field( wp_unslash( $_GET['updated'] ) ) : '';
		$this->render_page_header(
			__( 'Developer Hooks', 'ar' ),
			__( 'Extend plugin behavior using these filters and actions.', 'ar' )
		);
		?>
		<div class="rm-admin-grid">
			<div class="rm-admin-card">
				<h2><?php esc_html_e( 'Available Hooks', 'ar' ); ?></h2>
				<ul>
					<li><code>rm_load_legacy_stack</code> (filter)</li>
					<li><code>rm_after_dashboard_render</code> (action)</li>
					<li><code>rm_before_dev_tool_action</code> (action)</li>
					<li><code>rm_after_dev_tool_action</code> (action)</li>
				</ul>
				<p><?php esc_html_e( 'Use `add_action()` and `add_filter()` inside a must-use plugin or custom module.', 'ar' ); ?></p>
			</div>
			<div class="rm-admin-card">
				<h2><?php esc_html_e( 'Developer Tool', 'ar' ); ?></h2>
				<p><?php esc_html_e( 'Run a safe developer action protected by capability and nonce checks.', 'ar' ); ?></p>
				<?php if ( 'true' === $updated ) : ?>
					<div class="notice notice-success inline"><p><?php esc_html_e( 'Developer action executed successfully.', 'ar' ); ?></p></div>
				<?php endif; ?>
				<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
					<input type="hidden" name="action" value="rm_run_dev_tool" />
					<?php wp_nonce_field( 'rm_dev_tool_action', 'rm_dev_tool_nonce' ); ?>
					<?php submit_button( __( 'Run Developer Action', 'ar' ), 'secondary', 'submit', false ); ?>
				</form>
			</div>
		</div>
		<?php
		echo '</div>';
	}

	/**
	 * Render page header wrapper.
	 *
	 * @param string $title Title text.
	 * @param string $description Description text.
	 *
	 * @return void
	 */
	private function render_page_header( $title, $description ) {
		?>
		<div class="wrap rm-admin-wrap">
			<h1><?php echo esc_html( $title ); ?></h1>
			<p class="description"><?php echo esc_html( $description ); ?></p>
		<?php
	}

	/**
	 * Ensure current user has access to settings pages.
	 *
	 * @return void
	 */
	private function assert_capability() {
		if ( current_user_can( $this->capability ) ) {
			return;
		}

		wp_die( esc_html__( 'You do not have permission to access this page.', 'ar' ) );
	}

	/**
	 * Sanitize pagination number.
	 *
	 * @param mixed $value Raw input.
	 *
	 * @return int
	 */
	public function sanitize_pagination_number( $value ) {
		$number = absint( $value );
		if ( $number < 1 ) {
			return 10;
		}
		if ( $number > 200 ) {
			return 200;
		}
		return $number;
	}

	/**
	 * Sanitize UI settings array.
	 *
	 * @param mixed $value Raw option value.
	 *
	 * @return array<string,string>
	 */
	public function sanitize_ui_settings( $value ) {
		$value = is_array( $value ) ? $value : array();

		$card_style = isset( $value['card_style'] ) ? sanitize_key( $value['card_style'] ) : 'modern';
		$allowed    = array( 'modern', 'compact', 'classic' );
		if ( ! in_array( $card_style, $allowed, true ) ) {
			$card_style = 'modern';
		}

		return array(
			'card_style'         => $card_style,
			'show_map'           => ! empty( $value['show_map'] ) ? '1' : '0',
			'enable_animations'  => ! empty( $value['enable_animations'] ) ? '1' : '0',
		);
	}

	/**
	 * Render search section description.
	 *
	 * @return void
	 */
	public function render_search_section_description() {
		echo '<p>' . esc_html__( 'Control search result limits. Lower values improve load time on large datasets.', 'ar' ) . '</p>';
	}

	/**
	 * Render search field.
	 *
	 * @return void
	 */
	public function render_search_records_field() {
		$value = (int) get_option( 'abr_searchPaginationNumber', 10 );
		printf(
			'<input type="number" class="small-text" min="1" max="200" name="abr_searchPaginationNumber" value="%1$d" /> <p class="description">%2$s</p>',
			$value,
			esc_html__( 'Recommended: 10 to 24 records per page.', 'ar' )
		);
	}

	/**
	 * Render map section description.
	 *
	 * @return void
	 */
	public function render_map_section_description() {
		echo '<p>' . esc_html__( 'Provide a valid API key with Maps JavaScript and Geocoding enabled.', 'ar' ) . '</p>';
	}

	/**
	 * Render map key field.
	 *
	 * @return void
	 */
	public function render_map_api_field() {
		$key = (string) get_option( 'abr_google_maps_api_key', '' );
		printf(
			'<input type="text" class="regular-text code" name="abr_google_maps_api_key" value="%1$s" autocomplete="off" /> <p class="description">%2$s</p>',
			esc_attr( $key ),
			esc_html__( 'Key is stored in wp_options and used by map/search services.', 'ar' )
		);
	}

	/**
	 * Render UI section description.
	 *
	 * @return void
	 */
	public function render_ui_section_description() {
		echo '<p>' . esc_html__( 'These defaults are used by modern templates and can be overridden by themes.', 'ar' ) . '</p>';
	}

	/**
	 * Render UI card style field.
	 *
	 * @return void
	 */
	public function render_ui_card_style_field() {
		$settings = get_option( 'rm_ui_settings', array() );
		$current  = isset( $settings['card_style'] ) ? $settings['card_style'] : 'modern';
		?>
		<select name="rm_ui_settings[card_style]">
			<option value="modern" <?php selected( $current, 'modern' ); ?>><?php esc_html_e( 'Modern', 'ar' ); ?></option>
			<option value="compact" <?php selected( $current, 'compact' ); ?>><?php esc_html_e( 'Compact', 'ar' ); ?></option>
			<option value="classic" <?php selected( $current, 'classic' ); ?>><?php esc_html_e( 'Classic', 'ar' ); ?></option>
		</select>
		<?php
	}

	/**
	 * Render show map toggle.
	 *
	 * @return void
	 */
	public function render_ui_show_map_field() {
		$settings = get_option( 'rm_ui_settings', array() );
		$checked  = ! empty( $settings['show_map'] ) ? '1' : '0';
		?>
		<label>
			<input type="checkbox" name="rm_ui_settings[show_map]" value="1" <?php checked( $checked, '1' ); ?> />
			<?php esc_html_e( 'Display map on search and listing pages', 'ar' ); ?>
		</label>
		<?php
	}

	/**
	 * Render animation toggle.
	 *
	 * @return void
	 */
	public function render_ui_animation_field() {
		$settings = get_option( 'rm_ui_settings', array() );
		$checked  = ! empty( $settings['enable_animations'] ) ? '1' : '0';
		?>
		<label>
			<input type="checkbox" name="rm_ui_settings[enable_animations]" value="1" <?php checked( $checked, '1' ); ?> />
			<?php esc_html_e( 'Enable subtle frontend animations', 'ar' ); ?>
		</label>
		<?php
	}
}
