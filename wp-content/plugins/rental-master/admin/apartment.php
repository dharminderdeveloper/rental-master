<?php
if(!defined('ABSPATH')) exit;

class Cf_Abr_Apartment{
	public static function init(){
        $class = __CLASS__;
        new $class;
    }
	
	public function __construct(){
		#abtrv= about rental version
		 $version	=	get_option('abtrv', false);
		if(($version==1)||($version==2)){
			add_action('init',array($this,'abr_apartment_init'));
		}
		add_action('init',array($this,'abr_create_apartment_taxonomies'),0);
		add_action('cmb2_admin_init',array($this,'cf_apartment_register_metabox'));
		add_action('admin_menu',array($this,'abr_remove_apartment_meta_box'));
		add_action('cmb2_admin_init',array($this,'cf_apartment_availability_options_term_metabox'));
		add_action('cmb2_admin_init',array($this,'cf_apartment_amenities_term_metabox'));
		add_action('manage_posts_columns',array($this,'abr_manage_columns' ),10,2 );
		add_filter('manage_apartment_posts_columns',array($this,'craigslist_columns_head'));
		add_action('manage_apartment_posts_custom_column', array($this,'craigslist_columns_content'), 10, 2);
	}
	
	public static function get_apartment_id_title_ARR(){
		$args = array(
			'posts_per_page'   => -1,
			'orderby'          => 'date',
			'order'            => 'DESC',
			'post_type'        => 'apartment',
			'post_status'      => 'publish'
		);
		$newARR	=	array();
		$posts	=	get_posts($args);
		foreach($posts as $post){
			$id		=	$post->ID;
			$title	=	$post->post_title;
			$newARR[$id]=$title;
		}
		if($newARR){return $newARR;}
	}
	
	/* Hide some option from wp talbel list screen */
	public function abr_manage_columns( $columns, $post_type = 'page' ){
		if (in_array($post_type,array('apartment'))){
			unset($columns['taxonomy-apartment_availibility_category'] );
			unset($columns['taxonomy-apartment_area']);
			unset($columns['taxonomy-apartment_amenity_group']);
			unset($columns['taxonomy-apartment_amenities']);
			unset($columns['taxonomy-apartment_style']);
			unset($columns['taxonomy-apartment_image_group']);
			unset($columns['taxonomy-community_location']);
			unset($columns['riv_post_thumbs']);
		}
		return $columns;
	}
	
	function abr_remove_apartment_meta_box(){
		remove_meta_box('apartment_stylediv','apartment','normal');
	   	remove_meta_box('apartment_bedroomsdiv','apartment','normal');
	   	remove_meta_box('apartment_areadiv','apartment','normal');
	   	remove_meta_box('apartment_utilitiesdiv','apartment','normal');
	   	remove_meta_box('apartment_amenitiesdiv','apartment','normal');
	   	remove_meta_box('apartment_leasingdiv','apartment','normal');
	   	remove_meta_box('apartment_image_groupdiv','apartment','normal');
	   	remove_meta_box('apartment_amenity_groupdiv','apartment','normal');
	   	remove_meta_box('apartment_bathroomsdiv','apartment','normal');
	   	remove_meta_box('apartment_monthly_rentdiv','apartment','normal');
	   	remove_meta_box('apartment_rental_occupancydiv','apartment','normal');
	   	remove_meta_box('apartment_availability_optionsdiv','apartment','normal');
	   	remove_meta_box('apartment_availibility_categorydiv','apartment','normal');
	  	remove_meta_box('community_locationdiv','apartment','normal');
	}
	
	function abr_apartment_init(){
		$labels = array(
			'name'                  => _x( 'My Apartments', 'Post Type General Name','ar'),
			'singular_name'         => _x( 'Apartment', 'Post Type Singular Name','ar'),
			'menu_name'             => __( 'Manage Apartments','ar'),
			'name_admin_bar'        => __( 'Apartment','ar'),
			'archives'              => __( 'Apartment Archives','ar'),
			'parent_item_colon'     => __( 'Parent Apartment:','ar'),			
			'add_new_item'          => __( 'Add New Apartment','ar'),
			'add_new'               => __( 'Add New','ar'),
			'new_item'              => __( 'New Apartment','ar'),
			'edit_item'             => __( 'Edit Apartment','ar'),
			'update_item'           => __( 'Update Apartment','ar'),
			'view_item'             => __( 'View Apartment','ar'),
			'search_items'          => __( 'Search Apartment','ar'),
			'not_found'             => __( 'Not found','ar'),
			'not_found_in_trash'    => __( 'Not found in Trash','ar'),
			'featured_image'        => __( 'Featured Image','ar'),
			'set_featured_image'    => __( 'Set featured image','ar'),
			'remove_featured_image' => __( 'Remove featured image','ar'),
			'use_featured_image'    => __( 'Use as featured image','ar'),
			'insert_into_item'      => __( 'Insert into Apartment','ar'),
			'uploaded_to_this_item' => __( 'Uploaded to this Apartment','ar'),
			'items_list'            => __( 'Apartments list','ar'),
			'items_list_navigation' => __( 'Apartments list navigation','ar'),
			'filter_items_list'     => __( 'Filter Apartments list','ar' ),
		);
		$args = array(
			'label'                 => __( 'Apartment','ar'),
			'description'           => __( 'Apartments','ar'),
			'labels'                => $labels,
			'supports'              => array('title'),
			'taxonomies'            => array('community_location','apartment_leasing','apartment_style','apartment_image_group','partment_amenity_group', 'apartment_amenities','apartment_bedrooms','apartment_bathrooms','apartment_area','apartment_rental_occupancy','apartment_utilities','apartment_availability_options','apartment_availibility_category','apartment_monthly_rent'),
			'hierarchical'          => true,
			'public'                => true,
			'show_ui'               => true,
			'show_in_menu'          => true,
			'menu_icon'             => ABR_PLUGIN_DIR_URL.'img/apartment.png',
			'show_in_admin_bar'     => true,
			'show_in_nav_menus'     => true,
			'can_export'            => true,
			'has_archive'           => true,
			'exclude_from_search'   => false,
			'publicly_queryable'    => true,
			'capability_type'       => 'page',
		);
		register_post_type('apartment',$args);
	}
	
	function cf_apartment_availability_options_term_metabox(){
		$cf_term_c_owner = new_cmb2_box( array(
			'id'               => 'cf_term_a_options',
			'title'            => __('Priority','cmb2'),
			'object_types'     => array('term'),
			'taxonomies'       => array('apartment_availability_options'),
		));
		$cf_term_c_owner->add_field(array(
			'name'       => __('Priority','ar'),
			'id'         => 'cf_ao_priority',
			'type'       => 'text'
		));
	}
	
	function cf_apartment_amenities_term_metabox(){
		$cf_term_c_owner = new_cmb2_box( array(
			'id'               => 'cf_term_a_amenities',
			'title'            => __('Amenity Group','cmb2'),
			'object_types'     => array('term'),
			'taxonomies'       => array('apartment_amenities'),
		));
		$cf_term_c_owner->add_field(array(
			'name'      => __('Amenity Group','ar'),
			'id'        => 'cf_oc_address',
			'type'		=>'taxonomy_select',
			'taxonomy'	=>'apartment_amenity_group',
			'show_option_none' =>true
		));
	}
	# ----------------------/
	# Apartment Taxonomies /
	# --------------------/
	
	function abr_create_apartment_taxonomies(){
		# Leasing Options
		$labels = array(
			'name'              => _x('Leasing','ar'),
			'singular_name'     => _x('Leasing','ar'),
			'search_items'      => __('Search Leasing'),
			'all_items'         => __('All Leasing'),
			'parent_item'       => __('Parent Leasing'),
			'parent_item_colon' => __('Parent Leasing:'),
			'edit_item'         => __('Edit Leasing'),
			'update_item'       => __('Update Leasing'),
			'add_new_item'      => __('Add New Leasing'),
			'new_item_name'     => __('New Leasing Name'),
			'menu_name'         => __('Leasing Options'),
		);
		$args = array(
			'hierarchical'      => true,
			'labels'            => $labels,
			'show_ui'           => true,
			'show_admin_column' => true,
			'query_var'         => true,
			'rewrite'           => array('slug'=>'apartment_leasing'),
		);
		register_taxonomy('apartment_leasing',array('apartment'),$args);
		
		# Apartment Styles
		$labels = array(
			'name'              => _x('Style', 'taxonomy general name'),
			'singular_name'     => _x('Apartment Style', 'taxonomy singular name'),
			'search_items'      => __('Search Apartment Style'),
			'all_items'         => __('All Apartment Style'),
			'parent_item'       => __('Parent Apartment Style'),
			'parent_item_colon' => __('Parent Apartment Style:'),
			'edit_item'         => __('Edit Apartment Style'),
			'update_item'       => __('Update Apartment Style'),
			'add_new_item'      => __('Add New Apartment Style'),
			'new_item_name'     => __('New Apartment Style Name'),
			'menu_name'         => __('Apartment Styles'),
		);
		$args = array(
			'hierarchical'      => true,
			'labels'            => $labels,
			'show_ui'           => true,
			'show_admin_column' => true,
			'query_var'         => true,
			'rewrite'           => array('slug'=>'apartment_style'),
		);
		register_taxonomy('apartment_style',array('apartment'),$args);
		# Apartment Image Groups
		$labels = array(
			'name'              => _x('Image', 'taxonomy general name'),
			'singular_name'     => _x('Apartment Image Groups', 'taxonomy singular name'),
			'search_items'      => __('Search Apartment Image Groups'),
			'all_items'         => __('All Apartment Image Groups'),
			'parent_item'       => __('Parent Apartment Image Groups'),
			'parent_item_colon' => __('Parent Apartment Image Groups:'),
			'edit_item'         => __('Edit Apartment Image Groups'),
			'update_item'       => __('Update Apartment Image Groups'),
			'add_new_item'      => __('Add New Apartment Image Groups'),
			'new_item_name'     => __('New Apartment Image Groups Name'),
			'menu_name'         => __('Image Groups'),
		);
		$args = array(
			'hierarchical'      => true,
			'labels'            => $labels,
			'show_ui'           => true,
			'show_admin_column' => true,
			'query_var'         => true,
			'rewrite'           => array('slug'=>'apartment_image_group'),
		);
		register_taxonomy('apartment_image_group',array('apartment'),$args);
		# Apartment Amenity Groups
		$labels = array(
			'name'              => _x('Amenity Groups','ar'),
			'singular_name'     => _x('Amenity Groups','ar'),
			'search_items'      => __('Search Amenity Groups'),
			'all_items'         => __('All Amenity Groups'),
			'parent_item'       => __('Parent Amenity Groups'),
			'parent_item_colon' => __('Parent Amenity Groups:'),
			'edit_item'         => __('Edit Amenity Groups'),
			'update_item'       => __('Update Amenity Groups'),
			'add_new_item'      => __('Add New Amenity Groups'),
			'new_item_name'     => __('New Amenity Groups Name'),
			'menu_name'         => __('Amenity Groups'),
		);
		$args = array(
			'hierarchical'      => true,
			'labels'            => $labels,
			'show_ui'           => true,
			'show_admin_column' => true,
			'query_var'         => true,
			'rewrite'           => array('slug'=>'apartment_amenity_group'),
		);
		register_taxonomy('apartment_amenity_group',array('apartment'),$args);
		# Apartment Amenity
		$labels = array(
			'name'              => _x('Amenities',''),
			'singular_name'     => _x('Amenities',''),
			'search_items'      => __('Search Amenities'),
			'all_items'         => __('All Amenities'),
			'parent_item'       => __('Parent Amenities'),
			'parent_item_colon' => __('Parent Amenities:'),
			'edit_item'         => __('Edit Amenities'),
			'update_item'       => __('Update Amenities'),
			'add_new_item'      => __('Add New Amenities'),
			'new_item_name'     => __('New Amenities Name'),
			'menu_name'         => __('Amenities'),
		);
		$args = array(
			'hierarchical'      => true,
			'labels'            => $labels,
			'show_ui'           => true,
			'show_admin_column' => true,
			'query_var'         => true,
			'rewrite'           => array('slug'=>'apartment_amenities'),
		);
		register_taxonomy('apartment_amenities',array('apartment'),$args);
		# Bedroom
		$labels = array(
			'name'              => _x('Bedrooms', 'taxonomy general name'),
			'singular_name'     => _x('Apartment  Bedrooms', 'taxonomy singular name'),
			'search_items'      => __('Search Apartment  Bedrooms'),
			'all_items'         => __('All Apartment  Bedrooms'),
			'parent_item'       => __('Parent Apartment  Bedrooms'),
			'parent_item_colon' => __('Parent Apartment  Bedrooms:'),
			'edit_item'         => __('Edit Apartment  Bedrooms'),
			'update_item'       => __('Update Apartment  Bedrooms'),
			'add_new_item'      => __('Add New Apartment  Bedrooms'),
			'new_item_name'     => __('New Apartment  Bedrooms Name'),
			'menu_name'         => __('Bedrooms'),
		);
		$args = array(
			'hierarchical'      => true,
			'labels'            => $labels,
			'show_ui'           => true,
			'show_admin_column' => true,
			'query_var'         => true,
			'rewrite'           => array('slug'=>'apartment_bedrooms'),
		);
		register_taxonomy('apartment_bedrooms',array('apartment'),$args);
		# Bathrooms
		$labels = array(
			'name'              => _x('Bathrooms', 'taxonomy general name'),
			'singular_name'     => _x('Apartment  Bathrooms', 'taxonomy singular name'),
			'search_items'      => __('Search Apartment  Bathrooms'),
			'all_items'         => __('All Apartment  Bathrooms'),
			'parent_item'       => __('Parent Apartment  Bathrooms'),
			'parent_item_colon' => __('Parent Apartment  Bathrooms:'),
			'edit_item'         => __('Edit Apartment  Bathrooms'),
			'update_item'       => __('Update Apartment  Bathrooms'),
			'add_new_item'      => __('Add New Apartment  Bathrooms'),
			'new_item_name'     => __('New Apartment  Bathrooms Name'),
			'menu_name'         => __('Bathrooms'),
		);
		$args = array(
			'hierarchical'      => true,
			'labels'            => $labels,
			'show_ui'           => true,
			'show_admin_column' => true,
			'query_var'         => true,
			'rewrite'           => array('slug'=>'apartment_bathrooms'),
		);
		register_taxonomy('apartment_bathrooms',array('apartment'),$args);
		# Area
		$labels = array(
			'name'              => _x('Area', 'taxonomy general name'),
			'singular_name'     => _x('Apartment  Area', 'taxonomy singular name'),
			'search_items'      => __('Search Apartment  Area'),
			'all_items'         => __('All Apartment  Area'),
			'parent_item'       => __('Parent Apartment  Area'),
			'parent_item_colon' => __('Parent Apartment  Area:'),
			'edit_item'         => __('Edit Apartment  Area'),
			'update_item'       => __('Update Apartment  Area'),
			'add_new_item'      => __('Add New Apartment  Area (Square Footage)'),
			'new_item_name'     => __('New Apartment  Area Name'),
			'menu_name'         => __('Area'),
		);
		$args = array(
			'hierarchical'      => true,
			'labels'            => $labels,
			'show_ui'           => true,
			'show_admin_column' => true,
			'query_var'         => true,
			'rewrite'           => array('slug'=>'apartment_area'),
		);
		register_taxonomy('apartment_area',array('apartment'),$args);
		# Rental Occupancy 
		$labels = array(
			'name'              => _x('Rental Occupancy',''),
			'singular_name'     => _x('Rental Occupancy',''),
			'search_items'      => __('Search Rental Occupancy'),
			'all_items'         => __('All Rental Occupancy'),
			'parent_item'       => __('Parent Rental Occupancy'),
			'parent_item_colon' => __('Parent Rental Occupancy:'),
			'edit_item'         => __('Edit Rental Occupancy'),
			'update_item'       => __('Update Rental Occupancy'),
			'add_new_item'      => __('Add New Rental Occupancy'),
			'new_item_name'     => __('New Rental Occupancy Name'),
			'menu_name'         => __('Rental Occupancy'),
		);
		$args = array(
			'hierarchical'      => true,
			'labels'            => $labels,
			'show_ui'           => true,
			'show_admin_column' => true,
			'query_var'         => true,
			'rewrite'           => array('slug'=>'apartment_rental_occupancy'),
		);
		register_taxonomy('apartment_rental_occupancy',array('apartment'),$args);	
		# Utilities 
		$labels = array(
			'name'              => _x('Utilities',''),
			'singular_name'     => _x('Utilities',''),
			'search_items'      => __('Search Utilities'),
			'all_items'         => __('All Utilities'),
			'parent_item'       => __('Parent Utilities'),
			'parent_item_colon' => __('Parent Utilities:'),
			'edit_item'         => __('Edit Utilities'),
			'update_item'       => __('Update Utilities'),
			'add_new_item'      => __('Add New Utilities'),
			'new_item_name'     => __('New Utilities Name'),
			'menu_name'         => __('Utilities'),
		);
		$args = array(
			'hierarchical'      => true,
			'labels'            => $labels,
			'show_ui'           => true,
			'show_admin_column' => true,
			'query_var'         => true,
			'rewrite'           => array('slug'=>'apartment_utilities'),
		);
		register_taxonomy('apartment_utilities',array('apartment'),$args);		
		# Availability Options
		$labels = array(
			'name'              => _x('Availability Options',''),
			'singular_name'     => _x('Availability Options',''),
			'search_items'      => __('Search Availability Options'),
			'all_items'         => __('All Availability Options'),
			'parent_item'       => __('Parent Availability Options'),
			'parent_item_colon' => __('Parent Availability Options:'),
			'edit_item'         => __('Edit Availability Options'),
			'update_item'       => __('Update Availability Options'),
			'add_new_item'      => __('Add New Availability Options'),
			'new_item_name'     => __('New Availability Options Name'),
			'menu_name'         => __('Availability Options'),
		);
		$args = array(
			'hierarchical'      => true,
			'labels'            => $labels,
			'show_ui'           => true,
			'show_admin_column' => true,
			'query_var'         => true,
			'rewrite'           => array('slug'=>'apartment_availability_options'),
		);
		register_taxonomy('apartment_availability_options',array('apartment'),$args);
		
		# Availibility  Category
		$labels = array(
			'name'              => _x('Availibility Category',''),
			'singular_name'     => _x('Availibility Category',''),
			'search_items'      => __('Search Availibility Category'),
			'all_items'         => __('All Availibility Category'),
			'parent_item'       => __('Parent Availibility Category'),
			'parent_item_colon' => __('Parent Availibility Category:'),
			'edit_item'         => __('Edit Availibility Category'),
			'update_item'       => __('Update Availibility Category'),
			'add_new_item'      => __('Add New Availibility Category'),
			'new_item_name'     => __('New Availibility Category Name'),
			'menu_name'         => __('Availibility Category'),
		);
		$args = array(
			'hierarchical'      => true,
			'labels'            => $labels,
			'show_ui'           => true,
			'show_admin_column' => true,
			'query_var'         => true,
			'rewrite'           => array('slug'=>'apartment_availibility_category'),
		);
		register_taxonomy('apartment_availibility_category',array('apartment'),$args);
		# Monthly Rent
		$labels = array(
			'name'              => _x('Rent (M)',''),
			'singular_name'     => _x('Monthly Rent',''),
			'search_items'      => __('Search Monthly Rent'),
			'all_items'         => __('All Monthly Rent'),
			'parent_item'       => __('Parent Monthly Rent'),
			'parent_item_colon' => __('Parent Monthly Rent:'),
			'edit_item'         => __('Edit Monthly Rent'),
			'update_item'       => __('Update Monthly Rent'),
			'add_new_item'      => __('Add New Monthly Rent'),
			'new_item_name'     => __('New Monthly Rent Name'),
			'menu_name'         => __('Monthly Rent'),
		);
		$args = array(
			'hierarchical'      => true,
			'labels'            => $labels,
			'show_ui'           => true,
			'show_admin_column' => true,
			'query_var'         => true,
			'rewrite'           => array('slug'=>'apartment_monthly_rent'),
		);
		register_taxonomy('apartment_monthly_rent',array('apartment'),$args);
		
	}
	
	function cf_apartment_register_metabox(){
		$cf_apartment = new_cmb2_box(array(
			'id'=>'cf_apartment_metabox',
			'title'=>__('New Apartment','ar'),
			'object_types'=>array('apartment')
		));
		
		
		$cf_apartment->add_field(array(
			'name'=>__('Community','ar'),
			'desc'=>__('Select one of community from drop-down ','ar'),
			'id'=>'cf_apartment_community',
			'type'=>'select',
			'show_option_none' =>false,
			'options'          =>CF_AR_Community::get_communitys_id_title_ARR()
		));	
		
		$cf_apartment->add_field(array(
			'name'     =>__('Apartment Location','ar'),
			'id'       =>'cf_apartment_location',
			'type'     =>'taxonomy_select',
			'taxonomy' =>'community_location',
			'show_option_none' =>false
		));
		$cf_apartment->add_field(array(
			'name'     =>__('Apartment Style','ar'),
			'desc'     =>__('Select one of apartment style from drop-down ','ar'),
			'id'       =>'cf_apartment_style',
			'type'     =>'taxonomy_select',
			'taxonomy' =>'apartment_style',
			'show_option_none' =>false
		));
		$cf_apartment->add_field(array(
			'name'     =>__('Bedrooms','ar'),
			'id'       =>'cf_apartment_bedrooms',
			'type'     =>'taxonomy_select',//taxonomy_multicheck_inline
			'taxonomy' =>'apartment_bedrooms',
			'show_option_none' =>false
		));
		$cf_apartment->add_field(array(
			'name'     =>__('Bathrooms','ar'),
			'id'       =>'cf_apartment_bathrooms',
			'type'     =>'taxonomy_select',
			'taxonomy' =>'apartment_bathrooms',
			'show_option_none' =>false
		));
		$cf_apartment->add_field(array(
			'name'     =>__('Area (Square Footage)','ar'),
			'id'       =>'cf_apartment_area',
			'type'     =>'taxonomy_select',
			'taxonomy' =>'apartment_area',
			'show_option_none' =>false
		));
		$cf_apartment->add_field(array(
			'name' =>__('Rental Occupancy','ar'),
			'id'   =>'cf_apartment_rental_occupancy',
			'type'     =>'taxonomy_select',
			'taxonomy' =>'apartment_rental_occupancy',
			'show_option_none' =>false
		));
		$cf_apartment->add_field(array(
			'name'		=>__('Utilities Paid by Landlord','ar'),
			'id'=>		'cf_apartment_utilities_landlord',
			'type'		=> 'multicheck',
			'options'	=> About_rental_cf_exe::get_terms_id_title_ARR('apartment_utilities')
		));
		$cf_apartment->add_field(array(
			'name'		=>__('Utilities Paid by Tenant','ar'),
			'id'		=>'cf_apartment_utilities_tenant',
			'type'		=> 'multicheck',
			'options'	=> About_rental_cf_exe::get_terms_id_title_ARR('apartment_utilities')
		));
		$cf_apartment->add_field(array(
			'name'     =>__('Apartment Amenities','ar'),
			'id'       =>'cf_apartment_amenities',
			'type'     =>'taxonomy_multicheck',
			'taxonomy' =>'apartment_amenities',
			'show_option_none' =>false
		));
		$cf_apartment->add_field(array(
			'name' =>__('Availability Options','ar'),
			'id'   =>'cf_apartment_availability',
			'type'     =>'taxonomy_multicheck_inline',
			'taxonomy' =>'apartment_availability_options',
		));
		$cf_apartment->add_field(array(
			'name'     =>__('Leasing Options ','ar'),
			'id'       =>'cf_apartment_leasing_options',
			'type'     =>'taxonomy_multicheck',
			'taxonomy' =>'apartment_leasing'
		));
		
		$cf_apartment->add_field( array(
			'name' => __('Rent/month','ar'),
			'id'   =>'cf_apartment_rent_month',
			'type' => 'text',
			'attributes' => array(
				'type' => 'number',
				'pattern' => '\d*',
			),
			'sanitization_cb' => 'absint',
			'escape_cb'       => 'absint',
		));
		$cf_apartment->add_field(array(
			'name' =>__('Monthly Rent Range','ar'),
			'id'   =>'cf_apartment_rent_month_range',
			'type'     =>'taxonomy_select',
			'taxonomy' =>'apartment_monthly_rent',
			'show_option_none' =>false
		));
		
		$cf_apartment->add_field(array(
			'name' =>__('Date Available','ar'),
			'id'   =>'cf_apartment_date_available',
			'type' =>'text_date',
			'date_format' =>'d-m-Y'
		));
		$cf_apartment->add_field(array(
			'name' => __('Apartment Features','ar'),
			'id'   => 'cf_apartment_features',
			'type' => 'wysiwyg',
		));
		$cf_apartment->add_field(array(
			'name' => __('No of units','ar'),
			'id'   => 'cf_apartment_no_of_units',
			'type' => 'text'
		));
		$cf_apartment->add_field(array(
			'name' => __('No of units available','ar'),
			'id'   => 'cf_apartment_no_of_units_available',
			'type' => 'text'
		));
		$cf_apartment->add_field(array(
			'name' => __('Page Title','ar'),
			'id'   => 'cf_apartment_page_title',
			'type' => 'text'
		));
		$cf_apartment->add_field(array(
			'name' => __('Meta Keywords','ar'),
			'id'   => 'cf_apartment_meta_keywords',
			'type' => 'textarea_small'
		));
		$cf_apartment->add_field(array(
			'name' => __('Meta Description','ar'),
			'id'   => 'cf_apartment_meta_description',
			'type' => 'textarea_small'
		));
		$cf_apartment->add_field(array(
			'name' => __('Meta Author','ar'),
			'id'   => 'cf_apartment_meta_author',
			'type' => 'text'
		));
		$cf_apartment->add_field(array(
			'name' => __('Meta Robots','ar'),
			'id'   => 'cf_apartment_meta_robots',
			'type' => 'text'
		));
		$cf_apartment->add_field( array(
			'name' => __( 'Apartment Pic','ar'),
			'id'   => 'cf_apartment_pic',
			'type' => 'file',
		));
		$cf_apartment->add_field( array(
			'name' => __( 'Apartment Gallery','ar'),
			'id'   => 'cf_apartment_gallery',
			'type' => 'file_list',
		));
		$cf_apartment->add_field(array(
			'name' => __('Apartment Pic Alt Text','ar'),
			'id'   => 'cf_apartment_pic_alt_text',
			'type' => 'text'
		));
		$cf_apartment->add_field( array(
			'name' => __( 'Apartment Pic','ar'),
			'id'   => 'cf_apartment_pic',
			'type' => 'file',
		));
		$cf_apartment->add_field(array(
			'name' => __('Apartment Floor Plan Alt Text','ar'),
			'id'   => 'cf_apartment_floor_plan_alt_text',
			'type' => 'text'
		));
		$cf_apartment->add_field( array(
			'name' => __('Featured ?','ar'),
			'id'   => 'cf_apartment_featured',
			'type' => 'checkbox',
		));
		$cf_apartment->add_field( array(
			'name' => __('Youtube video','ar'),
			'desc' => __('Enter a youtube URL','ar' ),
			'id'   => 'cf_apartment_youtube_video',
			'type' => 'oembed',
		));	
		$cf_apartment->add_field( array(
			'name'         => 'Listing Priority',
			'id'           => 'cf_apartment_listing_priority',
			'type'         => 'text'
		));
		$cf_apartment->add_field( array(
			'name'    => 'Virtual Tours',
			'id'      => 'cf_apartment_vrids',
			'desc'    => 'Select ingredients. Drag to reorder.',
			'type'    => 'pw_multiselect',
			'options' => Cf_Abr_Apartment::get_vr_post_ARR(),
		));
	}
	
	public static function get_vr_post_ARR(){
		$args = array('posts_per_page'=>-1,'orderby'=>'date','order'=>'DESC','post_type'=>'virtualtour','post_status'=>'publish');
		$posts		=	get_posts($args);
		$new_arr	=	array();
		foreach($posts as $val){$new_arr[$val->ID] =$val->post_title; }
		return $new_arr;
	}

	public static function lat_long_explode($mlatlng){
		$mlatlng =  str_replace(array( '(', ')' ), '', $mlatlng);
		return explode(",",$mlatlng);
	}
	
	/**
		Add column Craigslist
	*
	*	@param: $defaults
	*	@return: name
	*/
	function craigslist_columns_head($defaults) {
		$defaults['Craigslist']	 =	'Craigslist Content';
		$defaults['Featured'] 	=	'Featured';
		return $defaults;
	}

	/**
		Show Craigslist content for all custom posts
	*
	*	@param: $column_name, $post_ID
	*/
	function craigslist_columns_content($column_name, $post_ID) {
	   if ($column_name == 'Craigslist') {
		   add_thickbox();
		   echo '<a href="#TB_inline?width=600&height=550&inlineId=modal-window-id-'.$post_ID.'" class="thickbox" pid="'.$post_ID.'">View</a>';
		  	 Cf_Abr_Apartment::craigslist_content($post_ID);
	   }
	   if($column_name == 'Featured') {
		   ?>
			<input class="checkbox" type="checkbox" <?php checked(get_post_meta($post_ID,'cf_apartment_featured',true ), 'on' ) ?> id="featurediS" value="<?php echo get_post_meta($post_ID,'cf_apartment_featured',true );?>" pid="<?php echo $post_ID; ?>"/>
			<?php
	   }
	}

	function craigslist_content($pid){
		$aptPic		=	get_post_meta($pid,'cf_apartment_pic',true);
		$cmgallery   	=	get_post_meta($pid,'cf_apartment_gallery',true);
		$location		=	get_post_meta($pid,'cf_apartment_location',true);
		$aptFeatures	=	get_post_meta($pid,'cf_apartment_features',true);
		$community_id	=	get_post_meta($pid,'cf_apartment_community',true);
		$bedroom		=	wp_get_post_terms($pid,'apartment_bedrooms',true);
		
		$html='';
		$html.='<div id="modal-window-id-'.$pid.'" style="display:none;padding:5px 10px" class="crag-content">';
		$html.='<div style="width:100%;text-align:center"><img src="'.ABR_PLUGIN_DIR_URL.'img/master-logo.png"></div>';
		$html.='<h2 style="width:100%;text-align:center">'.get_the_title($community_id).'</h2><hr>';
			
			$html.='<div style="width:100%; text-align:left">';
				$html.='<p><b>Location:</b> '.$location.' &nbsp; <b>Bedrooms: </b>'.count($bedroom).'</p>';
				$html.='<a href="'.$aptPic.'"class="square-pic"><img style="width:200px" src="'.$aptPic.'"></a>';
				$html.='<p>'.$aptFeatures.'</p>';
				if(!empty($cmgallery)){
					$html.='<div style="width:100%">';
					foreach($cmgallery as $cmgal){
						$html.='<a href="'.$cmgal.'">
						<img style="float:none" width="165" src="'.$cmgal.'"></a>';
					}
					$html.='<div>';
				}
				$html.='</div>';
				$html.='<hr>';
				$html.='<div id="content_'.$pid.'" style="min-height:200px">';				
				$html.='<xmp><div><h2>'.get_the_title($community_id).'</h2></xmp>';
				$html.='<xmp><p><b>Location:</b> '.$location.' &nbsp; <b>Bedrooms: </b>'.count($bedroom).'</p></xmp>';
				$html.='<xmp><a href="'.$aptPic.'" class="square-pic"><img src="'.$aptPic.'"></a></xmp>';
				$html.='<xmp><p>'.$aptFeatures.'</p></xmp>';
				if(!empty($cmgallery)){
					foreach($cmgallery as $cmgal){
						$html.='<xmp><a href="'.$cmgal.'"><img class="alignleft size-medium" width="165" src="'.$cmgal.'"></a></xmp>';
					}
				}
				$html.='<xmp><style>div {width:100%;text-align:left;}.square-pic img{width:700px;}div img{margin:5px}</style></div></xmp>';
				
			$html.='</div>';
			$html.='<a class="copy-text button-primary" data-clipboard-target="#content_'.$pid.'" href="javascript:void(0)" style="position: absolute;right: 25px;bottom:25px;">Copy</a>';
		$html.='</div>';
		echo $html;
		?><script> $(function(){new Clipboard('.copy-text');});</script><?php
	}
	
}
add_action('plugins_loaded',array('Cf_Abr_Apartment','init'));