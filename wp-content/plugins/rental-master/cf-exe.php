<?php
if(!defined('ABSPATH')) exit;
class About_rental_cf_exe{
	public static function init(){
        $class = __CLASS__;
        new $class;
    }
	
	public function __construct(){
		add_action('wp_enqueue_scripts', array($this,'about_rentals_darkbox'));
		add_action('wp_enqueue_scripts',array($this,'about_rentals_common_css'));
		add_action( 'wp_enqueue_scripts',array($this,'cf_jqueryvalidation'));
		add_action('admin_enqueue_scripts', array($this,'about_rental_admin_js'));
		add_action('wp_enqueue_scripts', array($this,'about_rental_admin_js'));
		add_action('wp_footer',array($this,'ajax_url_for_js'));
		add_action('admin_footer',array($this,'ajax_url_for_js'));
		add_filter('custom_menu_order',array($this,'custom_menu_order') );
		add_filter('menu_order', array($this,'custom_menu_order'),99);
		add_filter( 'query_vars',array($this,'custom_query_variable') );
	}

	public function ajax_url_for_js(){
	 ?><script>wp_ajax_url = function(){ return '<?php echo admin_url('admin-ajax.php'); ?>'; }</script><?php
	}

	# Common css
	public function about_rentals_common_css(){
		wp_register_style('about-rentals-common',ABR_PLUGIN_DIR_URL.'/css/ar.css',false,false);
		wp_enqueue_style('about-rentals-common');
		wp_register_style('css-flexslider',ABR_PLUGIN_DIR_URL.'/css/flexslider.css');
		wp_enqueue_style('css-flexslider');
	}
	
	public function cf_jqueryvalidation() {
		wp_register_script('jq-validate-jquery', ABR_PLUGIN_DIR_URL.'/js/jquery.validate.min.js', array('jquery'), '1.15.0',false);
		wp_enqueue_script('jq-validate-jquery');
		$mapAPI='AIzaSyCO57Oj_U92hCIFW_Eb-VI4CCarxNH4AC8';
		wp_register_script('jq-google-map-api','https://maps.googleapis.com/maps/api/js?key='.$mapAPI, array('jquery'),'1.15.0',false);
		wp_enqueue_script('jq-google-map-api');
		wp_register_script('jq-marker-clusterer',ABR_PLUGIN_DIR_URL.'/js/markerclusterer.js',array('jquery'),'4.5.2',false);
		wp_enqueue_script('jq-marker-clusterer');
	}
	
	# darkbox js
	public function about_rentals_darkbox(){
		wp_register_script('popup_darkbox', ABR_PLUGIN_DIR_URL.'/js/jquery.darkbox.js',array('jquery'),false);
		wp_enqueue_script('popup_darkbox');
		wp_register_script('flexslider-js', ABR_PLUGIN_DIR_URL.'/js/jquery.flexslider.js',array('jquery'),false);
		wp_enqueue_script('flexslider-js');
		wp_register_script('modernizr-js', ABR_PLUGIN_DIR_URL.'/js/modernizr.js',array('jquery'),false);
		wp_enqueue_script('modernizr-js');
	}

	#	Add  js to admin
	public function about_rental_admin_js(){
		wp_register_script('clipboard', ABR_PLUGIN_DIR_URL.'/js/clipboard.min.js',array('jquery'),false);
		wp_enqueue_script('clipboard');
		wp_register_script('about-rental-admin', ABR_PLUGIN_DIR_URL.'/js/about-rental-admin.js',array('jquery'),false);
		wp_enqueue_script('about-rental-admin');
	}

	# GET TERMS ID AND TITLE IN ARRAY
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
		$vars[] .= 'paged';		
		return $vars;
	}
}

add_action('plugins_loaded',array('About_rental_cf_exe','init'));
#-----------------------------------------------/
# Add CLASSES and supoortted files into plugin /
#---------------------------------------------/

/**--------	Admin	---------**/
include(ABR_PLUGIN_DIR_PATH.'admin/email.php');
include(ABR_PLUGIN_DIR_PATH.'admin/favorites.php');
include(ABR_PLUGIN_DIR_PATH.'admin/apartment.php');
include(ABR_PLUGIN_DIR_PATH.'admin/community.php');
include(ABR_PLUGIN_DIR_PATH.'admin/leasing-special.php');
include(ABR_PLUGIN_DIR_PATH.'admin/single-apartment.php');
include(ABR_PLUGIN_DIR_PATH.'admin/single-community.php');
include(ABR_PLUGIN_DIR_PATH.'admin/ar-virtual-tours.php');
include(ABR_PLUGIN_DIR_PATH.'add-on/sweetalert/index.php');
include(ABR_PLUGIN_DIR_PATH.'admin/apartment-listing.php');
include(ABR_PLUGIN_DIR_PATH.'admin/community-listing.php');
include(ABR_PLUGIN_DIR_PATH.'admin/featured-apartments.php');
include(ABR_PLUGIN_DIR_PATH.'admin/apartment-application.php');
include(ABR_PLUGIN_DIR_PATH.'admin/apartment-of-spec-comm.php');
include(ABR_PLUGIN_DIR_PATH.'admin/leasing-special-shortcode.php');

/**---------	Widget	----------**/
include(ABR_PLUGIN_DIR_PATH.'widget/apartment-widgets.php');

/**---------	Add On	----------**/
require_once('add-on/session/wp-session-manager.php');

/**----------	Cmb2	----------**/
include(ABR_PLUGIN_DIR_PATH.'cmb2/init.php');
include(ABR_PLUGIN_DIR_PATH.'cmb2/add-on/select2-field/cmb-field-select2.php');
include(ABR_PLUGIN_DIR_PATH.'cmb2/add-on/date-range-field/wds-cmb2-date-range-field.php');
include(ABR_PLUGIN_DIR_PATH.'cmb2/add-on/cmb_field_map/cmb-field-map.php');

/**----------	Includes	----------**/
include(ABR_PLUGIN_DIR_PATH.'includes/application-form.php');
include(ABR_PLUGIN_DIR_PATH.'includes/shortcodes.php');
include(ABR_PLUGIN_DIR_PATH.'includes/pagination.php');

/**----------	Premium Feature	----------**/
include(ABR_PLUGIN_DIR_PATH.'admin/premium/premium-init.php');