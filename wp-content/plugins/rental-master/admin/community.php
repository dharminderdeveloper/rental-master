<?php

if(!defined('ABSPATH')) exit;
class CF_AR_Community{
	public static function init(){
        $class = __CLASS__;
        new $class;
    }
	public function __construct(){
		$version	=	get_option('abtrv', false);
		if(($version==1)||($version==2)){
			add_action('init',array($this,'abr_community_init'),0);
		}
		add_action('init',array($this,'create_community_taxonomies'),0);		
		add_action('admin_menu',array($this,'remove_community_meta_box'));
		add_action('cmb2_admin_init',array($this,'cf_community_register_metabox'));
		add_action('cmb2_admin_init',array($this,'cf_community_owner_term_metabox'));
		add_action('cmb2_admin_init',array($this,'cf_community_category_term_metabox'));
		# -------------------------------------------------/
		# Remove description filed from taxonomies's term /
		# -----------------------------------------------/
		add_action('admin_footer-edit-tags.php',array($this,'remove_tags_from_term'));
		add_action('admin_footer-term.php',array($this,'remove_tags_from_term'));
		add_action('manage_edit-community_owner_columns',array($this,'del_description_of_term'));
		add_action('manage_edit-community_location_columns',array($this,'del_description_of_term'));
		add_action('manage_edit-community_features_columns',array($this,'del_description_of_term'));
		add_action('manage_edit-community_categories_columns',array($this,'del_description_of_term'));
		add_action('manage_edit-community_type_columns',array($this,'del_description_of_term'));
		add_action('manage_edit-apartment_leasing_columns',array($this,'del_description_of_term'));
		add_action('manage_edit-apartment_style_columns',array($this,'del_description_of_term'));
		add_action('manage_edit-apartment_image_group_columns',array($this,'del_description_of_term'));
		add_action('manage_edit-apartment_amenity_group_columns',array($this,'del_description_of_term'));
		add_action('manage_edit-apartment_amenities_columns',array($this,'del_description_of_term'));
		add_action('manage_edit-apartment_bedrooms_columns',array($this,'del_description_of_term'));
		add_action('manage_edit-apartment_bathrooms_columns',array($this,'del_description_of_term'));
		add_action('manage_edit-apartment_area_columns',array($this,'del_description_of_term'));
		add_action('manage_edit-apartment_rental_occupancy_columns',array($this,'del_description_of_term'));
		add_action('manage_edit-apartment_utilities_columns',array($this,'del_description_of_term'));
		add_action('manage_edit-apartment_availability_options_columns',array($this,'del_description_of_term'));
		add_action('manage_edit-apartment_availibility_category_columns',array($this,'del_description_of_term'));
		add_filter('manage_community_posts_columns',array($this, 'no_of_appartments_head'));
		add_action('manage_community_posts_custom_column', array($this,'no_of_appartments_content'), 10, 2);
		add_action( 'manage_community_posts_columns', array($this,'hide_community_feature_columns') , 10, 2 );
		add_action("wp_ajax_nopriv_get_appartments_by_post_meta_ajax",array($this,'get_appartments_by_post_meta_ajax'));
		add_action("wp_ajax_get_appartments_by_post_meta_ajax",array($this,'get_appartments_by_post_meta_ajax'));
	}
	
	public static function remove_tags_from_term(){
		global $current_screen;
		$cs	=	$current_screen->id;
		if($cs=='edit-community_owner'||$cs=='edit-community_location'||$cs=='edit-community_features'
		||$cs=='edit-community_categories'||$cs=='edit-community_type'||$cs=='edit-apartment_leasing'
		||$cs=='edit-apartment_style'||$cs=='edit-apartment_image_group'||$cs=='edit-apartment_amenity_group'
		||$cs=='edit-apartment_amenities'||$cs=='edit-apartment_bedrooms'	||$cs=='edit-apartment_bathrooms'
		||$cs=='edit-apartment_area'||$cs=='edit-apartment_rental_occupancy'||$cs=='edit-apartment_utilities'
		||$cs=='edit-apartment_availability_options'||$cs=='edit-apartment_availibility_category'){
			?>
			<script>
			jQuery(document).ready(function(e){(function($){
				$('.term-parent-wrap,.term-description-wrap,.term-slug-wrap').remove();
				}(jQuery))});
			</script><?php
		}
	}
	
	public function del_description_of_term($columns){
		unset($columns['description']);
		return $columns;
	}
	
	function remove_community_meta_box(){ 
	   remove_meta_box('community_typediv','community','normal');
	   remove_meta_box('community_featuresdiv','community','normal');
	   remove_meta_box('community_locationdiv','community','normal');
	   remove_meta_box('community_categoriesdiv','community','normal');
	   remove_meta_box('community_ownerdiv','community','normal');
	}
	
	function abr_community_init(){
		$labels = array(
			'name'                  => _x( 'My Community', 'Post Type General Name', 'sdthm' ),
			'singular_name'         => _x( 'Community', 'Post Type Singular Name', 'sdthm' ),
			'menu_name'             => __( 'Manage Communities', 'sdthm' ),
			'name_admin_bar'        => __( 'Community', 'sdthm' ),
			'archives'              => __( 'Community Archives', 'sdthm' ),
			'parent_item_colon'     => __( 'Parent Community:', 'sdthm' ),
			'add_new_item'          => __( 'Add New Community', 'sdthm' ),
			'add_new'               => __( 'Add New', 'sdthm' ),
			'new_item'              => __( 'New Community', 'sdthm' ),
			'edit_item'             => __( 'Edit Community', 'sdthm' ),
			'update_item'           => __( 'Update Community', 'sdthm' ),
			'view_item'             => __( 'View Community', 'sdthm' ),
			'search_items'          => __( 'Search Community', 'sdthm' ),
			'not_found'             => __( 'Not found', 'sdthm' ),
			'not_found_in_trash'    => __( 'Not found in Trash', 'sdthm' ),
			'featured_image'        => __( 'Featured Image', 'sdthm' ),
			'set_featured_image'    => __( 'Set featured image', 'sdthm' ),
			'remove_featured_image' => __( 'Remove featured image', 'sdthm' ),
			'use_featured_image'    => __( 'Use as featured image', 'sdthm' ),
			'insert_into_item'      => __( 'Insert into Community', 'sdthm' ),
			'uploaded_to_this_item' => __( 'Uploaded to this Community', 'sdthm' ),
			'items_list'            => __( 'Communitys list', 'sdthm' ),
			'items_list_navigation' => __( 'Communitys list navigation', 'sdthm' ),
			'filter_items_list'     => __( 'Filter Communitys list', 'sdthm' ),
		);
		$args = array(
			'label'                 => __( 'Community', 'sdthm' ),
			'description'           => __( 'Community', 'sdthm' ),
			'labels'                => $labels,
			'supports'              => array( 'title', ),
			'taxonomies'            => array( 'community_type', 'community_categories', 'community_features', 'community_location', 'community_owner' ),
			'hierarchical'          => true,
			'public'                => true,
			'show_ui'               => true,
			'show_in_menu'          => true,		
			'menu_icon'             => ABR_PLUGIN_DIR_URL.'img/community.png',
			'show_in_admin_bar'     => true,
			'show_in_nav_menus'     => true,
			'can_export'            => true,
			'has_archive'           => true,
			'exclude_from_search'   => false,
			'publicly_queryable'    => true,
			'capability_type'       => 'post',
		);
		register_post_type( 'community', $args );
	}
	# ----------------------/
	# Community Taxonomies /
	# --------------------/
	function create_community_taxonomies(){
		# Community Type
		$labels = array(
			'name'              => _x( 'Community Type', 'taxonomy general name' ),
			'singular_name'     => _x( 'Community Type', 'taxonomy singular name' ),
			'search_items'      => __( 'Search Community Type' ),
			'all_items'         => __( 'Communities Type' ),
			'parent_item'       => __( 'Parent Community Type' ),
			'parent_item_colon' => __( 'Parent Community Type:' ),
			'edit_item'         => __( 'Edit Community Type' ),
			'update_item'       => __( 'Update Community Type' ),
			'add_new_item'      => __( 'Add New Community Type' ),
			'new_item_name'     => __( 'New Community Type Name' ),
			'menu_name'         => __( 'Type' ),
		);
		$args = array(
			'hierarchical'      => true,
			'labels'            => $labels,
			'show_ui'           => true,
			'show_admin_column' => true,
			'query_var'         => true,
			'rewrite'           => array('slug'=>'community_type'),
		);
		register_taxonomy( 'community_type',array('community'),$args);
		# Community Category
		$labels = array(
			'name'              => _x('Community Category','taxonomy general name'),
			'singular_name'     => _x('Community Category','taxonomy singular name'),
			'search_items'      => __('Search Community Category'),
			'all_items'         => __('Communities Category'),
			'parent_item'       => __('Parent Community Category'),
			'parent_item_colon' => __('Parent Community Category:'),
			'edit_item'         => __('Edit Community Category'),
			'update_item'       => __('Update Community Category'),
			'add_new_item'      => __('Add New Community Category'),
			'new_item_name'     => __('New Community Category Name'),
			'menu_name'         => __('Category'),
		);
		$args = array(
			'hierarchical'      => true,
			'labels'            => $labels,
			'show_ui'           => true,
			'show_admin_column' => true,
			'query_var'         => true,
			'rewrite'           => array('slug'=>'community_categories'),
		);
		register_taxonomy('community_categories',array('community'),$args);
		# Community Feature
		$labels = array(
			'name'              => _x( 'Community Feature', 'taxonomy general name' ),
			'singular_name'     => _x( 'Community Feature', 'taxonomy singular name' ),
			'search_items'      => __( 'Search Community Features' ),
			'all_items'         => __( 'Communities Features' ),
			'parent_item'       => __( 'Parent Community Feature' ),
			'parent_item_colon' => __( 'Parent Community Feature:' ),
			'edit_item'         => __( 'Edit Community Feature' ),
			'update_item'       => __( 'Update Community Feature' ),
			'add_new_item'      => __( 'Add New Community Feature' ),
			'new_item_name'     => __( 'New Community Feature' ),
			'menu_name'         => __( 'Feature' ),
		);
		$args = array(
			'hierarchical'      => true,
			'labels'            => $labels,
			'show_ui'           => true,
			'show_admin_column' => true,
			'query_var'         => true,
			'rewrite'           => array('slug'=>'community_features'),
		);
		register_taxonomy( 'community_features',array('community'),$args);
		# Community Locatioin
		$labels = array(
			'name'              => _x( 'Community Location', 'taxonomy general name' ),
			'singular_name'     => _x( 'Community Location', 'taxonomy singular name' ),
			'search_items'      => __( 'Search Community Locations' ),
			'all_items'         => __( 'Communities Locations' ),
			'parent_item'       => __( 'Parent Community Location' ),
			'parent_item_colon' => __( 'Parent Community Location:' ),
			'edit_item'         => __( 'Edit Community Location' ),
			'update_item'       => __( 'Update Community Location' ),
			'add_new_item'      => __( 'Add New Community Location' ),
			'new_item_name'     => __( 'New Community Type Location' ),
			'menu_name'         => __( 'Location' ),
		);
		$args = array(
			'hierarchical'      => true,
			'labels'            => $labels,
			'show_ui'           => true,
			'show_admin_column' => true,
			'query_var'         => true,
			'rewrite'           => array('slug'=>'community_location'),
		);
		register_taxonomy('community_location',array('community','apartment'),$args);
		
		# Community Owner
		$labels = array(
			'name'              => _x('Community Owner','taxonomy general name'),
			'singular_name'     => _x('Community Owner','taxonomy singular name'),
			'search_items'      => __('Search Community Owner'),
			'all_items'         => __('Communities Owner'),
			'parent_item'       => __('Parent Community Owner'),
			'parent_item_colon' => __('Parent Community Owner:'),
			'edit_item'         => __('Edit Community Owner'),
			'update_item'       => __('Update Community Owner'),
			'add_new_item'      => __('Add New Community Owner'),
			'new_item_name'     => __('New Community Owner Name'),
			'menu_name'         => __('Owner'),
		);
		$args = array(
			'hierarchical'      => true,
			'labels'            => $labels,
			'description'			=>	false,
			'show_ui'           => true,
			'show_admin_column' => true,
			'query_var'         => true,
			'rewrite'           => array('slug'=>'community_owner'),
		);
		register_taxonomy('community_owner',array('community'),$args);
	}
	
	function cf_community_category_term_metabox(){
		$cf_term_c_owner = new_cmb2_box( array(
		'id'               => 'cf_category_t_mb',
		'title'            => __('Priority','cmb2'),
		'object_types'     => array('term'),
		'taxonomies'       => array('community_categories'),
		));	
		$cf_term_c_owner->add_field(array(
			'name'       => __('Priority','ar'),
			'id'         => 'cf_cc_priority',
			'type'       => 'text'
		));
	}
	
	function cf_community_owner_term_metabox(){
		$cf_term_c_owner = new_cmb2_box( array(
		'id'               => 'cf_term_c_owner',
		'title'            => __('Owner','cmb2'),
		'object_types'     => array('term'),
		'taxonomies'       => array('community_owner'),
		));
		$cf_term_c_owner->add_field(array(
			'name'       => __('Company City','ar'),
			'id'         => 'cf_oc_city',
			'type'       => 'text'
		));
		$cf_term_c_owner->add_field(array(
			'name'       => __('Company State','ar'),
			'id'         => 'cf_oc_state',
			'type'       => 'text'
		));
		$cf_term_c_owner->add_field(array(
			'name'       => __('Company Zip','ar'),
			'id'         => 'cf_oc_zip',
			'type'       => 'text'
		));
		$cf_term_c_owner->add_field(array(
			'name'       => __('Company Address','ar'),
			'id'         => 'cf_oc_address',
			'type' => 'textarea_small'
		));
		$cf_term_c_owner->add_field(array(
			'name'       => __('Company Contact Person','ar'),
			'id'         => 'cf_oc_contact_person',
			'type'       => 'text'
		));
		$cf_term_c_owner->add_field(array(
			'name'       => __('Company Contact Email','ar'),
			'id'         => 'cf_oc_cp_email',
			'type'       => 'text_email'
		));
	}
	
	function cf_community_register_metabox(){
		$cf_community = new_cmb2_box(array('id'=>'cf_community_metabox','title'=>__('New Community','ar'),'object_types'=>array('community')));
		$cf_community->add_field(array(
			'name'=>__('Community Owner','ar'),
			'id'=>'cf_community_owner',
			'type'=>'taxonomy_select',
			'taxonomy'=>'community_owner',
			'show_option_none'=>false
		));
		$cf_community->add_field(array(
			'name'=>__('Community Category','ar'),
			'id'=>'cf_community_cat',
			'type'=>'taxonomy_select',
			'taxonomy'=>'community_categories',
			'show_option_none'=>false
		));
		$cf_community->add_field( array(
			'name'     => __( 'Community Type','ar'),
			'desc'     => __( 'Select one of community type from drop-down ','ar'),
			'id'       => 'cf_community_type',
			'type'     => 'taxonomy_select',
			'taxonomy' => 'community_type',
			'show_option_none' => false
		));
		$cf_community->add_field( array(
			'name'     => __('Community Features','ar' ),
			'desc'     => __('Select number of features','ar'),
			'id'       => 'cf_community_features',
			'type'     => 'taxonomy_multicheck',
			'taxonomy' => 'community_features'
		));
		$cf_community->add_field( array(
			'name'     => __('Location','ar' ),
			'id'       => 'cf_community_location',
			'type'     => 'taxonomy_select',
			'taxonomy' => 'community_location',
			'show_option_none' => false
		));
		$cf_community->add_field(array(
			'name' => __('Community Address','ar'),
			'id'   => 'cf_community_address',
			'type' => 'textarea_small',
		));
		$cf_community->add_field(array(
			'name' => __('Community Map','ar'),
			'id'   => 'cf_community_map',
			'desc'     => __( '<a href="javascript:void(0)" id="help_map" title="Click here to get help">Help ?</a> ','ar'),
			'type' => 'textarea_code'
		));
		$cf_community->add_field( array(
			'name'	=> 'Community City',
			'id' 	=> 'cf_community_city',
			'type'	=> 'text'
		));
		$cf_community->add_field( array(
			'name'	=> 'Community State',
			'id'	=> 'cf_community_state',
			'type'	=> 'text'
		));
		$cf_community->add_field( array(
			'name'	=> 'Community ZIP Code',
			'id'	=> 'cf_community_zip_code',
			'type'	=> 'text'
		));
		$cf_community->add_field( array(
			'name' => __( 'Community Pic','ar'),
			'id'   => 'cf_community_pic',
			'type' => 'file',
		));
		$cf_community->add_field( array(
			'name' => __( 'Community Gallery','ar'),
			'id'   => 'cf_community_gallery',
			'type' => 'file_list',
		));
		$cf_community->add_field( array(
			'name'         => 'Community Pic Alt Text',
			'id'           => 'cf_community_pic_alt_text',
			'type'         => 'text'
		));
		$cf_community->add_field( array(
			'name' => __('Community Site Plan','ar'),
			'id'   => 'cf_community_site_plan',
			'type' => 'file',
		));
		$cf_community->add_field( array(
			'name'	=> 'Community Site Plan Alt Text',
			'id'	=> 'cf_community_site_plan_alt_text',
			'type'	=> 'text'
		));
		$cf_community->add_field( array(
			'name'	=> __('Community Description','ar'),
			'id' 	=> 'cf_community_description',
			'type'	=> 'wysiwyg',
			'options'	=> array('textarea_rows'=>5),
		));
		$cf_community->add_field( array(
			'name' => __('Youtube video','ar'),
			'desc' => __('Enter a youtube URL','ar' ),
			'id'   => 'cf_community_youtube_video',
			'type'=>'text',
			//'type' => 'oembed',
		));
		$cf_community->add_field( array(
			'name'	=> 'Listing Priority',
			'id'	=> 'cf_community_listing_priority',
			'type'	=> 'text'
		));
		$cf_community->add_field( array(
			'name'    => 'Virtual Tours',
			'id'      => 'cf_community_vrids',
			'desc'    => 'Select ingredients. Drag to reorder.',
			'type'    => 'pw_multiselect',
			'options' => Cf_Abr_Apartment::get_vr_post_ARR(),
		));
	}

	public static function get_communitys_id_title_ARR(){
		
		$args = array(
			'posts_per_page'   => -1,
			'orderby'          => 'date',
			'order'            => 'DESC',
			'post_type'        => 'community',
			'post_status'      => 'publish'
		);
		$newARR	=	array();
		$posts		=	get_posts($args);
		foreach($posts as $post){
			$id		=	$post->ID;
			$title	=	$post->post_title;
			$newARR[$id]=$title;
		}
		if($newARR){return $newARR;}
	}

	/**
	*	hide_community_feature_columns
	*
	*	@return: removeable column name
	*/
	function hide_community_feature_columns( $columns, $post_type = 'community' ) {
		if ( in_array( $post_type, array( 'post', 'community' ) ) ) {
		   unset( $columns['taxonomy-community_features'] );
		}
		return $columns;
	  }

	/**
		Add column Apartments
	*
	*	@param: $defaults
	*	@return: name
	*/
	function no_of_appartments_head($defaults) {
		$defaults['no_of_apartments'] = 'Apartments';
		return $defaults;
	}

	/**
		Show appartment content for all custom posts
	*
	*	@param: $column_name, $post_ID
	*/
	function no_of_appartments_content($column_name, $post_ID){
	   if ($column_name == 'no_of_apartments') {
	   		$count=CF_AR_Community::count_apartments($post_ID);
		  	if($count==0){	echo '('.$count.')'; 	}
	  		else{
	  			echo '<a class="cfwpTHICK" href="javascript:void(0)"  cid="'.$post_ID.'">('.$count.')</a>';
	  		}
	   }
	}

	function count_apartments($cid){
		$args = array('posts_per_page'=> -1,'post_type'=>'apartment','fields'=>'ids','meta_query' => array(
					array('key' => 'cf_apartment_community','value' => $cid,'compare' => '=')));
		$ids =  get_posts($args);
		return count($ids);
	}

	/**
	*	get_appartments_by_post_meta_ajax
	*
	*/
	function get_appartments_by_post_meta_ajax(){
		if($_REQUEST){
			$cid	=	sanitize_text_field($_REQUEST['cid']);
			$args = array('posts_per_page'=> -1,'post_type'=>'apartment','fields'=>'ids','meta_query' => array(
					array('key' => 'cf_apartment_community','value' => $cid,'compare' => '=')));
			$ids =  get_posts($args);
			$this->community_appartments_content($ids);
		}
		die();
	}

	function community_appartments_content($ids){
		echo '<ul class="cmApts">';
		foreach($ids as $id){
			if(!empty($id)){
				$src	=	get_post_meta($id,'cf_apartment_pic',true);
				echo '<li>';
						echo '<span class="cmAptsA">';
						echo '<a href="'.get_edit_post_link($id).'">';
						echo '<img src="'.$src.'">';
						echo '</a>';
						echo '</span>';
						echo '<span class="cmAptsB">';
						echo '<a href="'.get_edit_post_link($id).'">';
						echo get_the_title($id).'</a></span>';
				echo '</li>';
			}
			else{echo '<h3>No Apartment</h3>';}
		}
		echo '</ul>';
	}

	function abr_get_apartments($cid){
		$args	=	array(
			'posts_per_page'	=>	-1,
			'post_type'		=>	'apartment',
			'fields'			=>	'ids',
			'meta_query' 		=>	array(
					array('key'	=> 'cf_apartment_community',
					'value'		=> $cid,
					'compare' 	=> '=')));
		$ids 	=	get_posts($args);
		if($ids){return $ids;}
	}

}
add_action('plugins_loaded',array('CF_AR_Community','init'));