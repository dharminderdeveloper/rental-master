<?php

/**
*	Function:	abr_auto_generate_page
*
*	@param:	$name, $slug, $content, $parent
*/
	function abr_auto_generate_page($name,$slug,$content,$parent){
		if(!abr_is_slug_exists($slug)){
			$page_defin=array($slug=>array('title'=>$name,'content'=>$content));
			foreach($page_defin as $sl=>$pg){
				$query=new WP_Query('pagename='.$sl);
				if(!$query->have_posts()){
					wp_insert_post(
					array('post_content'=>$pg['content'],'post_name'=>$sl,'post_title'=>$pg['title'],
					'page_template'=>'template-dashboard.php','post_status'=>'publish','post_type'=>'page',
					'ping_status'=>'closed','comment_status'=>'closed','post_parent'=>$parent)
					);
				}
			}
		}
	}
	add_action('after_setup_theme','abr_create_defualt_pages');

/**
*	Function:	abr_is_slug_exists
*
*	@param:		$post_name
*	@return:	boolean
*/
	function abr_is_slug_exists($post_name){
		global $wpdb;
		$pfx=$wpdb->base_prefix;
		if($wpdb->get_row("SELECT post_name FROM ".$pfx."posts WHERE post_name='".$post_name."'",'ARRAY_A')){ return true; }else{ return false; }
	}

/**
*	Function:	abr_create_defualt_pages
*
*	Description: Create default pages with shortcode
*/
	function abr_create_defualt_pages(){
		abr_auto_generate_page('Apartment Listing','apartments-listing','[ar_apartment_listing]','');
		// on hold from client side
		//abr_auto_generate_page('Apartment Application','apartment-application','[ar_apartment_application_form]',''); 
		
		abr_auto_generate_page('Email Favorites','email-favorites','[ar_email_favorites_to_friends]','');
		abr_auto_generate_page('My Favorites','my-favorites','[ar_my_favorites_apartment]','');
		abr_auto_generate_page('Apartments Of Community','community-apartments','[ar_apartments_Of_Specific_Community]','');
		abr_auto_generate_page('Community Listing','community-listing','[ar_community_listing_default]','');
		abr_auto_generate_page('Leasing Special','leasing-special','[ar_leasing_specials]','');
		abr_auto_generate_page('Featured Apartments','featured-apartments','[ar_featured_apartments]','');

		// Real Estate Listing Page	
		abr_auto_generate_page('ReaEstate Listing','realestate-listing','[ar_realestate_listing]','');

		// Search Listing Page	
		abr_auto_generate_page('Search Listing','search-listing','[ar_search_listing]','');
	}