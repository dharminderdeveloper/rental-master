<?php
if(!defined('ABSPATH')) exit;
class About_rental_rm_exe{
	public static function init(){
        $class = __CLASS__;
        new $class;
    }
	
	public function __construct(){
		add_action('wp_enqueue_scripts', array($this,'about_rentals_darkbox'));
		add_action('wp_enqueue_scripts',array($this,'about_rentals_common_css'));
		add_action( 'wp_enqueue_scripts',array($this,'rm_jqueryvalidation'));
		add_action('admin_enqueue_scripts', array($this,'about_rental_admin_js'));
		add_action('wp_footer',array($this,'ajax_url_for_js'));
		add_action('admin_footer',array($this,'ajax_url_for_js'));
		add_filter('custom_menu_order',array($this,'custom_menu_order') );
		add_filter('menu_order', array($this,'custom_menu_order'),99);
		add_filter( 'query_vars',array($this,'custom_query_variable') );
	}

	public function ajax_url_for_js(){
		if ( ! $this->should_enqueue_public_assets() && ! is_admin() ) {
			return;
		}
	 ?><script>wp_ajax_url = function(){ return '<?php echo admin_url('admin-ajax.php'); ?>'; }</script><?php
	}

	# Common css
	public function about_rentals_common_css(){
		if ( ! $this->should_enqueue_public_assets() ) {
			return;
		}
		wp_register_style('rental-masters-common',RM_PLUGIN_DIR_URL.'/css/ar.css',false,false);
		wp_enqueue_style('rental-masters-common');
		wp_register_style('css-flexslider',RM_PLUGIN_DIR_URL.'/css/flexslider.css');
		wp_enqueue_style('css-flexslider');
	}
	
	public function rm_jqueryvalidation() {
		if ( ! $this->should_enqueue_public_assets() ) {
			return;
		}
		wp_register_script('jq-validate-jquery', RM_PLUGIN_DIR_URL.'/js/jquery.validate.min.js', array('jquery'), '1.15.0',false);
		wp_enqueue_script('jq-validate-jquery');
		$mapAPI = (string) get_option( 'abr_google_maps_api_key', '' );
		if ( '' === $mapAPI && defined( 'RM_MAP_API_KEY' ) ) {
			$mapAPI = (string) RM_MAP_API_KEY;
		}
		if ( '' !== $mapAPI ) {
			wp_register_script('jq-google-map-api','https://maps.googleapis.com/maps/api/js?key='.$mapAPI, array('jquery'),'1.15.0',false);
			wp_enqueue_script('jq-google-map-api');
		}
		wp_register_script('jq-marker-clusterer',RM_PLUGIN_DIR_URL.'/js/markerclusterer.js',array('jquery'),'4.5.2',false);
		wp_enqueue_script('jq-marker-clusterer');
	}
	
	# darkbox js
	public function about_rentals_darkbox(){
		if ( ! $this->should_enqueue_public_assets() ) {
			return;
		}
		wp_register_script('popup_darkbox', RM_PLUGIN_DIR_URL.'/js/jquery.darkbox.js',array('jquery'),false);
		wp_enqueue_script('popup_darkbox');
		wp_register_script('flexslider-js', RM_PLUGIN_DIR_URL.'/js/jquery.flexslider.js',array('jquery'),false);
		wp_enqueue_script('flexslider-js');
		wp_register_script('modernizr-js', RM_PLUGIN_DIR_URL.'/js/modernizr.js',array('jquery'),false);
		wp_enqueue_script('modernizr-js');
	}

	#	Add  js to admin
	public function about_rental_admin_js(){
		wp_register_script('clipboard', RM_PLUGIN_DIR_URL.'/js/clipboard.min.js',array('jquery'),false);
		wp_enqueue_script('clipboard');
		wp_register_script('rental-admin', RM_PLUGIN_DIR_URL.'/js/rental-admin.js',array('jquery'),false);
		wp_enqueue_script('rental-admin');
	}

	# GET TERM_S ID AND TITLE IN ARRAY
	public static function get_terms_id_title_ARR($taxName){
		$taxonomy	=	get_terms(array('taxonomy'=>$taxName,'hide_empty'=>false));
		$newARR		=	array();
		foreach($taxonomy as $term){
			$tid	=	$term->term_id;
			$name	=	$term->name;
			$newARR[$tid]=$name;
		}
		if($newARR){
			return $newARR;
		}
	}
	
	# GET TERM NAME USING ID
	public static function get_term_name($tid){
		if(get_term($tid)){
			$term	=	get_term($tid);
			return $term->name;
		}
	}
	
	# GET PAGE ID BY PAGE SLUG
	public static function get_id_by_slug($page_slug){
		$page = get_page_by_path($page_slug);
		if ($page) {
			return $page->ID;
		} else {
			return null;
		}
	}

	public function custom_menu_order($menu_ord) {
		if (!$menu_ord) return true;
	     return array(
	    	'index.php', // Dashboard
	    	'abr-user-help',
			'availability-manager',
			'edit.php?post_type=leasing',
	    	'edit.php?post_type=apartment',
	    	'edit.php?post_type=community',
			'edit.php?post_type=realestate',
			'abr-application-form',
			'edit.php?post_type=virtualtour',
	    );
	}

	public function custom_query_variable($vars) {
		$vars[] = 'paged';		
		return $vars;
	}

	private function should_enqueue_public_assets() {
		if ( ! is_singular() ) {
			return (bool) apply_filters( 'rm_legacy_public_assets_needed', false, null );
		}
		$post = get_post();
		if ( ! $post ) {
			return (bool) apply_filters( 'rm_legacy_public_assets_needed', false, null );
		}
		$shortcodes = apply_filters(
			'rm_legacy_shortcodes',
			array(
				'ar_apartment_listing',
				'ar_community_listing_default',
				'ar_featured_apartments',
				'ar_leasing_specials',
				'ar_apartment_application_form',
				'ar_email_favorites_to_friends',
				'ar_my_favorites_apartment',
				'ar_apartments_Of_Specific_Community',
				'ar_search_listing',
				'ar_realestate_listing',
			)
		);
		foreach ( (array) $shortcodes as $shortcode ) {
			if ( has_shortcode( $post->post_content, $shortcode ) ) {
				return true;
			}
		}
		return (bool) apply_filters( 'rm_legacy_public_assets_needed', false, $post );
	}
}

add_action('plugins_loaded',array('About_rental_rm_exe','init'));
#-----------------------------------------------/
# Add CLASSES and supoortted files into plugin /
#---------------------------------------------/

/**--------	Admin	---------**/
include(RM_PLUGIN_DIR_PATH.'admin/email.php');
include(RM_PLUGIN_DIR_PATH.'admin/favorites.php');
include(RM_PLUGIN_DIR_PATH.'admin/apartment.php');
include(RM_PLUGIN_DIR_PATH.'admin/community.php');
include(RM_PLUGIN_DIR_PATH.'admin/leasing-special.php');
include(RM_PLUGIN_DIR_PATH.'admin/single-apartment.php');
include(RM_PLUGIN_DIR_PATH.'admin/single-community.php');
include(RM_PLUGIN_DIR_PATH.'admin/ar-virtual-tours.php');
include(RM_PLUGIN_DIR_PATH.'add-on/sweetalert/index.php');
include(RM_PLUGIN_DIR_PATH.'admin/apartment-listing.php');
include(RM_PLUGIN_DIR_PATH.'admin/community-listing.php');
include(RM_PLUGIN_DIR_PATH.'admin/featured-apartments.php');
include(RM_PLUGIN_DIR_PATH.'admin/apartment-application.php');
include(RM_PLUGIN_DIR_PATH.'admin/apartment-of-spec-comm.php');
include(RM_PLUGIN_DIR_PATH.'admin/leasing-special-shortcode.php');

/**---------	Widget	----------**/
include(RM_PLUGIN_DIR_PATH.'widget/apartment-widgets.php');

/**---------	Add On	----------**/
require_once('add-on/session/wp-session-manager.php');

/**----------	Cmb2	----------**/
include(RM_PLUGIN_DIR_PATH.'cmb2/init.php');
include(RM_PLUGIN_DIR_PATH.'cmb2/add-on/select2-field/cmb-field-select2.php');
include(RM_PLUGIN_DIR_PATH.'cmb2/add-on/date-range-field/wds-cmb2-date-range-field.php');
include(RM_PLUGIN_DIR_PATH.'cmb2/add-on/cmb_field_map/cmb-field-map.php');

/**----------	Includes	----------**/

/**----------	Legacy Premium Features (now free)	----------**/
include(RM_PLUGIN_DIR_PATH.'admin/premium/previous-leasing-special.php');
include(RM_PLUGIN_DIR_PATH.'admin/premium/realestate.php');
include(RM_PLUGIN_DIR_PATH.'admin/premium/single-realestate.php');
include(RM_PLUGIN_DIR_PATH.'admin/premium/realestate-listing.php');
include(RM_PLUGIN_DIR_PATH.'admin/premium/search-feature.php');
include(RM_PLUGIN_DIR_PATH.'widget/realestate-widgets.php');
