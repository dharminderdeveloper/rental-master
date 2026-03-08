<?php
if(!defined('ABSPATH')) exit;

class Cf_Abr_RealEstate{
	public static function init(){
        $class = __CLASS__;
        new $class;
    }
	
	public function __construct(){
		#abtrv= about rental version
		 $version	=	get_option('abtrv', false);
		if(($version==1)||($version==2)){
			add_action('init',array($this,'abr_realestate_init'));
		}
		add_action('init',array($this,'abr_create_realestate_taxonomies'),0);		
		add_action('cmb2_admin_init',array($this,'cf_realestate_register_metabox'));
		add_action('admin_menu',array($this,'abr_remove_realestate_meta_box'));
		/*
		add_action('admin_menu',array($this,'abr_remove_apartment_meta_box'));
		add_action('cmb2_admin_init',array($this,'cf_apartment_availability_options_term_metabox'));
		add_action('cmb2_admin_init',array($this,'cf_apartment_amenities_term_metabox'));
		add_action('manage_posts_columns',array($this,'abr_manage_columns' ),10,2 );
		add_filter('manage_apartment_posts_columns',array($this,'craigslist_columns_head'));
		add_action('manage_apartment_posts_custom_column', array($this,'craigslist_columns_content'), 10, 2);
		*/
	}	

	function abr_remove_realestate_meta_box(){
		remove_meta_box('realestate_categorydiv','realestate','normal');
	   	remove_meta_box('listing_amenity_editordiv','realestate','normal');
	   	remove_meta_box('construction_type_editordiv','realestate','normal');
	   	remove_meta_box('listing_bathroomsdiv','realestate','normal');
	   	remove_meta_box('listing_bedroomsdiv','realestate','normal');
	   	remove_meta_box('style_editordiv','realestate','normal');
	   	remove_meta_box('listing_typediv','realestate','normal');	   	
	}

	function abr_realestate_init(){
		$labels = array(
			'name'                  => _x( 'My Real Estates', 'Post Type General Name','ar'),
			'singular_name'         => _x( 'Real Estate', 'Post Type Singular Name','ar'),
			'menu_name'             => __( 'Manage Real Estate for Sale','ar'),
			'name_admin_bar'        => __( 'Real Estate for Sale','ar'),
			'archives'              => __( 'Real Estate Archives','ar'),
			'parent_item_colon'     => __( 'Parent Real Estate:','ar'),			
			'add_new_item'          => __( 'Add New Real Estate','ar'),
			'add_new'               => __( 'Add New','ar'),
			'new_item'              => __( 'New Real Estate','ar'),
			'edit_item'             => __( 'Edit Real Estate','ar'),
			'update_item'           => __( 'Update Real Estate','ar'),
			'view_item'             => __( 'View Real Estate','ar'),
			'search_items'          => __( 'Search Real Estate','ar'),
			'not_found'             => __( 'Not found','ar'),
			'not_found_in_trash'    => __( 'Not found in Trash','ar'),
			'featured_image'        => __( 'Featured Image','ar'),
			'set_featured_image'    => __( 'Set featured image','ar'),
			'remove_featured_image' => __( 'Remove featured image','ar'),
			'use_featured_image'    => __( 'Use as featured image','ar'),
			'insert_into_item'      => __( 'Insert into Real Estate','ar'),
			'uploaded_to_this_item' => __( 'Uploaded to this Real Estate','ar'),
			'items_list'            => __( 'Real Estates list','ar'),
			'items_list_navigation' => __( 'Real Estates list navigation','ar'),
			'filter_items_list'     => __( 'Filter Real Estates list','ar' ),
		);
		$args = array(
			'label'                 => __( 'Real Estate','ar'),
			'description'           => __( 'Real Estates','ar'),
			'labels'                => $labels,
			'supports'              => array('title'),
			'taxonomies'            => array('realestate_category','listing_amenity_editor','construction_type_editor','style_editor','listing_type'),				
			'hierarchical'          => true,
			'public'                => true,
			'show_ui'               => true,
			'show_in_menu'          => true,
			'menu_icon'             => ABR_PLUGIN_DIR_URL.'img/apartment.png',
			'show_in_admin_bar'     => true,
			'show_in_nav_menus'     => true,
			'can_export'            => true,
			'has_archive'           =>  false,
			'exclude_from_search'   => false,
			'publicly_queryable'    => true,
			'capability_type'       => 'page',
		);
		register_post_type('realestate',$args);
	}

	# ----------------------/
	# Real Estate Taxonomies /
	# --------------------/
	
	function abr_create_realestate_taxonomies(){
		# Real Estate Category
		$labels = array(
			'name'              => _x('Category', 'taxonomy general name' ),
			'singular_name'     => _x('Real Estate Category', 'taxonomy singular name' ),
			'search_items'      => __('Search Real Estate Category'),
			'all_items'         => __('All Real Estate Category'),
			'parent_item'       => __('Parent Real Estate Category'),
			'parent_item_colon' => __('Parent Real Estate Category:'),
			'edit_item'         => __('Edit Real Estate Category'),
			'update_item'       => __('Update Real Estate Category'),
			'add_new_item'      => __('Add New Real Estate Category'),
			'new_item_name'     => __('New Real Estate Category Name'),
			'menu_name'         => __('Real Estate Category'),
		);
		$args = array(
			'hierarchical'      => true,
			'labels'            => $labels,
			'show_ui'           => true,
			'show_admin_column' => true,
			'query_var'         => true,
			'rewrite'           => array('slug'=>'realestate_category'),
		);

		register_taxonomy('realestate_category',array('realestate'),$args);
		
		# Listing Amenities Editor
		$labels = array(
			'name'              => _x('Amenity Editor', 'taxonomy general name'),
			'singular_name'     => _x('Amenity Editor', 'taxonomy singular name'),
			'search_items'      => __('Search Amenity Editor'),
			'all_items'         => __('All Amenity Editor'),
			'parent_item'       => __('Parent Amenity Editor'),
			'parent_item_colon' => __('Parent Amenity Editor:'),
			'edit_item'         => __('Edit Amenity Editor'),
			'update_item'       => __('Update Amenity Editor'),
			'add_new_item'      => __('Add New Amenity Editor'),
			'new_item_name'     => __('New Amenity Editor Name'),
			'menu_name'         => __('Listing Amenity Editors'),
		);
		$args = array(
			'hierarchical'      => true,
			'labels'            => $labels,
			'show_ui'           => true,
			'show_admin_column' => true,
			'query_var'         => true,
			'rewrite'           => array('slug'=>'listing_amenity_editor'),
		);
		register_taxonomy('listing_amenity_editor',array('realestate'),$args);		

		# Construction Type Editor
		$labels = array(
			'name'              => _x('Type Editor', 'taxonomy general name'),
			'singular_name'     => _x('Type Editors', 'taxonomy singular name'),
			'search_items'      => __('Search Construction Type Editor'),
			'all_items'         => __('All Construction Type Editors'),
			'parent_item'       => __('Parent Construction Type Editors'),
			'parent_item_colon' => __('Parent Construction Type Editors:'),
			'edit_item'         => __('Edit Construction Type Editor'),
			'update_item'       => __('Update Construction Type Editor'),
			'add_new_item'      => __('Add New Construction Type Editor'),
			'new_item_name'     => __('New Construction Type Editor Name'),
			'menu_name'         => __('Construction Type Editor'),
		);
		$args = array(
			'hierarchical'      => true,
			'labels'            => $labels,
			'show_ui'           => true,
			'show_admin_column' => true,
			'query_var'         => true,
			'rewrite'           => array('slug'=>'construction_type_editor'),
		);
		register_taxonomy('construction_type_editor',array('realestate'),$args);

		# Listing Bathroom
		$labels = array(
			'name'              => _x('Listing Bathroom', 'taxonomy general name'),
			'singular_name'     => _x('Listing Bathrooms', 'taxonomy singular name'),
			'search_items'      => __('Search Listing Bathroom'),
			'all_items'         => __('All Listing Bathrooms'),
			'parent_item'       => __('Parent Listing Bathrooms'),
			'parent_item_colon' => __('Parent Listing Bathrooms:'),
			'edit_item'         => __('Edit Listing Bathroom'),
			'update_item'       => __('Update Listing Bathroom'),
			'add_new_item'      => __('Add New Listing Bathroom'),
			'new_item_name'     => __('New Listing Bathroom Name'),
			'menu_name'         => __('Listing Bathroom'),
		);
		$args = array(
			'hierarchical'      => true,
			'labels'            => $labels,
			'show_ui'           => true,
			'show_admin_column' => true,
			'query_var'         => true,
			'rewrite'           => array('slug'=>'listing-bathroom'),
		);
		register_taxonomy('listing_bathrooms',array('realestate'),$args);

		# Listing Bedroom
		$labels = array(
			'name'              => _x('Listing Bedroom', 'taxonomy general name'),
			'singular_name'     => _x('Listing Bedrooms', 'taxonomy singular name'),
			'search_items'      => __('Search Listing Bedroom'),
			'all_items'         => __('All Listing Bedrooms'),
			'parent_item'       => __('Parent Listing Bedrooms'),
			'parent_item_colon' => __('Parent Listing Bedrooms:'),
			'edit_item'         => __('Edit Listing Bedroom'),
			'update_item'       => __('Update Listing Bedroom'),
			'add_new_item'      => __('Add New Listing Bedroom'),
			'new_item_name'     => __('New Listing Bedroom Name'),
			'menu_name'         => __('Listing Bedroom'),
		);
		$args = array(
			'hierarchical'      => true,
			'labels'            => $labels,
			'show_ui'           => true,
			'show_admin_column' => true,
			'query_var'         => true,
			'rewrite'           => array('slug'=>'listing-bedroom'),
		);
		register_taxonomy('listing_bedrooms',array('realestate'),$args);

		# Style Editor
		$labels = array(
			'name'              => _x('Style Editor','ar'),
			'singular_name'     => _x('Style Editor','ar'),
			'search_items'      => __('Search Style Editor'),
			'all_items'         => __('All Style Editor'),
			'parent_item'       => __('Parent Style Editor'),
			'parent_item_colon' => __('Parent Style Editor:'),
			'edit_item'         => __('Edit Style Editor'),
			'update_item'       => __('Update Style Editor'),
			'add_new_item'      => __('Add New Style Editor'),
			'new_item_name'     => __('New Style Editor Name'),
			'menu_name'         => __('Style Editor'),
		);
		$args = array(
			'hierarchical'      => true,
			'labels'            => $labels,
			'show_ui'           => true,
			'show_admin_column' => true,
			'query_var'         => true,
			'rewrite'           => array('slug'=>'style_editor'),
		);
		register_taxonomy('style_editor',array('realestate'),$args);		
		/*
		# Type of Listing
		$labels = array(
			'name'              => _x('Listing Type',''),
			'singular_name'     => _x('Listing Type',''),
			'search_items'      => __('Search Listing Type'),
			'all_items'         => __('All Listing Types'),
			'parent_item'       => __('Parent Listing Types'),
			'parent_item_colon' => __('Parent Listing Types:'),
			'edit_item'         => __('Edit Listing Type'),
			'update_item'       => __('Update Listing Type'),
			'add_new_item'      => __('Add New Listing Type'),
			'new_item_name'     => __('New Listing Type Name'),
			'menu_name'         => __('Listing Type'),
		);
		$args = array(
			'hierarchical'      => true,
			'labels'            => $labels,
			'show_ui'           => true,
			'show_admin_column' => true,
			'query_var'         => true,
			'rewrite'           => array('slug'=>'listing_type'),
		);
		register_taxonomy('listing_type',array('realestate'),$args);		
		*/
	}
	
	function cf_realestate_register_metabox(){
		$cf_realestate = new_cmb2_box(array(
			'id'=>'cf_realestate_metabox',
			'title'=>__('New Realestate','ar'),
			'object_types'=>array('realestate')
		));
		
		$cf_realestate->add_field( array(
			'name' => __( 'Primary Photo','ar'),
			'id'   => 'cf_realestate_photo',
			'type' => 'file',
		));
		
		$cf_realestate->add_field( array(
			'name' => __( 'RealEstate Gallery','ar'),
			'id'   => 'cf_realestate_gallery',
			'type' => 'file_list',
		));
		
		$cf_realestate->add_field(array(
			'name'      =>	__('Realestate Category','ar'),
			'id'        =>	'cf_realestate_category',
			'type'		=>	'taxonomy_select',
			'taxonomy'	=>	'realestate_category',
			'show_option_none' =>true
		));
		/*
		$cf_realestate->add_field(array(
			'name'      =>	__('Listing Type','ar'),
			'id'        =>	'cf_realestate_listing_type',
			'type'		=>	'taxonomy_select',
			'taxonomy'	=>	'listing_type',
			'show_option_none' =>true
		));
		*/

		$cf_realestate->add_field(array(
			'name'      =>	__('Construction Type','ar'),
			'id'        =>	'cf_realestate_construction_type',
			'type'		=>	'taxonomy_select',
			'taxonomy'	=>	'construction_type_editor',
			'show_option_none' =>true
		));

		$cf_realestate->add_field(array(
			'name'		=> __('Listing Amenity','ar'),
			'id'		=> 'cf_realestate_listing_amenity',
			'type'		=> 'multicheck',
			'options'	=>  About_rental_cf_exe::get_terms_id_title_ARR('listing_amenity_editor')
		));
		
		$cf_realestate->add_field(array(
			'name'      =>	__('Listing Style','ar'),
			'id'        =>	'cf_realestate_style_editor',
			'type'		=>	'taxonomy_select',
			'taxonomy'	=>	'style_editor',
			'show_option_none' =>true
		));
		

		$cf_realestate->add_field(array(
			'name' => __(' Property Address','ar'),
			'id'   => 'cf_realestate_location',
			'type' => 'text',
			'after_row'=> 'Cf_Abr_RealEstate::custom_map_for_location'
		));						
		
		$cf_realestate->add_field(array(
			'name' => __('Listing Short Description','ar'),
			'id'   => 'cf_realestate_short_description',
			'type' => 'textarea_small'
		));
		
		for($i=1900;$i<=2020;$i++){		
			$yearbuilt[$i]	=	$i;
		}	
		
		$cf_realestate->add_field(array(
			'name'    =>	__('Year Built','ar'),
			'id'      =>	'cf_realestate_year_built',
			'desc'    => 	'Year Built',
			'type'    =>	'pw_select',
			'options' =>	$yearbuilt,
		));
		
		$cf_realestate->add_field(array(
			'name' => __(' Lot Size','ar'),
			'id'   => 'cf_realestate_lot_size',
			'type' => 'text'
		));		
		
		$cf_realestate->add_field(array(
			'name' => __('Long Long Description','ar'),
			'id'   => 'cf_realestate_long_description',
			'type' => 'wysiwyg',
		));
		
		$cf_realestate->add_field(array(
			'name' => __('Other Features','ar'),
			'id'   => 'cf_realestate_other_features',
			'type' => 'wysiwyg',
		));		
				
		$cf_realestate->add_field(array(
			'name' => __('Heating and Cooling','ar'),
			'id'   => 'cf_realestate_heating_cooling',
			'type' => 'wysiwyg',
		));
		
		$cf_realestate->add_field(array(
			'name' => __('Listing Price','ar'),
			'id'   => 'cf_realestate_listing_price',
			'type' => 'text'
		));
		
		$cf_realestate->add_field(array(
			'name' => __('Listing Features','ar'),
			'id'   => 'cf_realestate_listing_features',
			'type' => 'wysiwyg',
		));

		$cf_realestate->add_field(array(
			'name'     =>	__('Listing Bedrooms','ar'),
			'id'       =>	'cf_realestate_listing_bedrooms',
			'type'     =>	'taxonomy_select',
			'taxonomy' =>	'listing_bedrooms',
			'show_option_none' =>false
		));

		$cf_realestate->add_field(array(
			'name'     =>	__('Listing Bathrooms','ar'),
			'id'       =>	'cf_realestate_listing_bathrooms',
			'type'     =>	'taxonomy_select',
			'taxonomy' =>	'listing_bathrooms',
			'show_option_none' =>false
		));
		
		$cf_realestate->add_field( array(
			'name' => __('Listing Video Link ','ar'),
			'desc' => __('Enter a Youtube URL','ar' ),
			'id'   => 'cf_realestate_video_url',
			'type' => 'oembed',
		));

		$cf_realestate->add_field(array(
			'name' => __('Listing School Information','ar'),
			'id'   => 'cf_realestate_school_information',
			'type' => 'wysiwyg',
		));	
		
		$cf_realestate->add_field(array(
			'name' => __('Listing Community Information','ar'),
			'id'   => 'cf_realestate_community_information',
			'type' => 'wysiwyg',
		));	
		
		$cf_realestate->add_field(array(
			'name' => __('Listing Utilities Information','ar'),
			'id'   => 'cf_realestate_utilities_information',
			'type' => 'wysiwyg',
		));	

		$cf_realestate->add_field(array(
			'name' => __('MLS Link for More Information','ar'),
			'id'   => 'cf_realestate_mls_link',
			'type' => 'text'
		));
	}	
	
	function custom_map_for_location( $field_args, $field ) {
		$mapLocation 	=	urlencode(get_post_meta($field->object_id,'cf_realestate_location',true));		
		if( $mapLocation )
		return '<div class="cmb-row table-layout" data-fieldtype="text"><div style="width: 100%" id=""><iframe width="100%" height="300" src="https://maps.google.com/maps?width=100%&height=300&hl=en&q='.$mapLocation.'&ie=UTF8&t=&z=14&iwloc=B&output=embed" frameborder="0" scrolling="no" marginheight="0" marginwidth="0"><a href="https://www.mapsdirections.info/en/custom-google-maps/">Create Google Map</a> by <a href="https://www.mapsdirections.info/en/">Measure area on map</a></iframe></div><br /></div>';	
	}	
}
add_action('plugins_loaded',array('Cf_Abr_RealEstate','init'));