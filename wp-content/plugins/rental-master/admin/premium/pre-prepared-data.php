<?php

/**
*	Function:	abr_insert_predefined_data
*
*	Descripton: Contains all function related to predefined data
*/
function abr_insert_predefined_data(){
	
	$array=abr_retrieve_JSON_DATA();

	foreach($array as $key => $values){
		foreach($values as $val){
			abr_on_activation_insert_terms($val,$key);
		}
	}
	abr_ADD_Scholars_Community();
	abr_ADD_Village_Grove_Community();
	abr_ADD_The_Heather_Apartment();
	abr_ADD_The_Commodore_Apartment();
}

/**
*	Function:	abr_on_activation_insert_terms
*
*	@param:		$term_name,$taxonomy_name
*	@return:
*/
function abr_on_activation_insert_terms($term_name,$taxonomy_name){
	$term_name		=	trim($term_name);
	$taxonomy_name	=	trim($taxonomy_name);
	$term = term_exists($term_name, $taxonomy_name);
	if ($term == 0 && $term == null) {
		wp_insert_term( $term_name, $taxonomy_name);
	}
}

/**
*	Function:	abr_retrieve_JSON_DATA
*
*	Descripton: retrive content from json file
*	@return: $array
*/
function abr_retrieve_JSON_DATA(){
	$json_file = file_get_contents(ABR_PLUGIN_DIR_URL.'/json/About-Rental.json');
	$array 		= json_decode($json_file, true);	
	return $array;
}

/**
*	Function:	abr_ADD_Scholars_Community
*
*	Descripton: Creates Community [Scholars Community] and insert all releveant data
*/
function abr_ADD_Scholars_Community(){
	if(!post_exists('Scholars Community')){
		$my_post = array('post_type'	=> 'community','post_title'	=> 'Scholars Community',
		'post_status'	=> 'publish');
		$post_id	=	wp_insert_post( $my_post );
		$image_path=	ABR_PLUGIN_DIR_URL.'/img/download.jpg';
		$src=abr_update_featured_image($image_path,$post_id);
		update_post_meta( $post_id, 'cf_community_pic',$src); // Community Pic		
		$site_plan		=	ABR_PLUGIN_DIR_URL.'/img/complex-layout2.jpg';
		$site_plan_src	=	abr_update_featured_image($site_plan,$post_id);
		update_post_meta( $post_id, 'cf_community_site_plan',$site_plan_src); // Site paln		
		/*$img_path=ABR_PLUGIN_DIR_URL.'/img/';
		$gallery_imgs=	array($img_path.'1.jpg',$img_path.'2.jpg',$img_path.'3.jpg',$img_path.'4.jpg',$img_path.'5.jpg',$img_path.'6.jpg',$img_path.'7.jpg');		
		$src_arr=array();
		foreach($gallery_imgs as $img){
			$gallery_src	=	abr_update_featured_image($img,$post_id);
			array_push($src_arr,$gallery_src);
		}
		update_post_meta($post_id,'cf_community_gallery',$src_arr); //Gallery*/		
		$terms1		=	array("Generic Rentals, Inc.");
		$values1	=	abr_get_taxonomy_ids_ARRAY($terms1,'community_owner');	//community_owner
		wp_set_post_terms( $post_id, $values1,'community_owner',false );		
		$terms2		=	array("Quadplex");
		$values2	=	abr_get_taxonomy_ids_ARRAY($terms2,'community_type');	//community_type
		wp_set_post_terms( $post_id, $values2,'community_type',false );		
		$terms3		=	array("Condominium Community");
		$values3	=	abr_get_taxonomy_ids_ARRAY($terms3,'community_categories');	//community_categories
		wp_set_post_terms( $post_id, $values3,'community_categories',false );		
		$terms4		=	array("MIami Florida – East Miami");
		$values4	=	abr_get_taxonomy_ids_ARRAY($terms4,'community_location');	//community_location
		wp_set_post_terms( $post_id, $values4,'community_location',false );		
		$terms5		=	array("On Site Parking","Clubhouse","Guest Parking","Hot Tub","Kid’s Art Room","Rooftop Balcony");
		$values5		=	abr_get_taxonomy_ids_ARRAY($terms5,'community_features');	//community_features
		wp_set_post_terms( $post_id, $values5,'community_features',false );		
		update_post_meta( $post_id, 'cf_community_address','29392 E. 10th St. Miami Fl 47401');
		update_post_meta( $post_id, 'cf_community_map','<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3092.843958042047!2d-86.53551358409257!3d39.17826797952841!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x886c66d0e26f2b15%3A0xfe051e9b7bea614a!2sScholar&#39;s+Rock+Studio+Apartments!5e0!3m2!1sen!2sus!4v1509123092135" width="600" height="450" frameborder="0" style="border:0" allowfullscreen></iframe>');
		update_post_meta( $post_id, 'cf_community_city','Miami');
		update_post_meta( $post_id, 'cf_community_state','Florida');
		update_post_meta( $post_id, 'cf_community_zip_code','19392');
		update_post_meta( $post_id, 'cf_community_youtube_video','https://www.youtube.com/watch?v=XYV0qATsyts');
		update_post_meta( $post_id, 'cf_community_description','Lorem ipsum dolor sit amet, consectetur adipiscing elit. In mollis non nulla sit amet volutpat. Aenean et scelerisque diam. Integer vitae mi orci. Nunc fringilla tortor in lorem scelerisque, sit amet tempor odio tristique. Praesent id efficitur lorem. Sed ac rhoncus mi, quis vehicula leo. Etiam laoreet nibh ac mattis sodales. In consequat massa non tortor molestie elementum. Aenean quis augue cursus purus porttitor commodo quis ac felis. Cras eget condimentum lectus. Pellentesque ultricies id purus et congue. Mauris condimentum molestie metus, nec hendrerit eros blandit hendrerit. Proin et accumsan ipsum. Fusce sed velit elit. Aliquam at arcu posuere, iaculis leo nec, bibendum est. Fusce eros libero, rhoncus ut eleifend nec, facilisis in mauris. Quisque rhoncus magna finibus lectus ultricies, in pharetra lorem sollicitudin. Sed ligula libero, porta ultricies erat sed, mollis faucibus ante. Phasellus mollis accumsan vehicula. Etiam ut metus suscipit, fermentum nisl blandit, posuere diam. Vestibulum ac tortor dignissim, ornare arcu at, semper dui.');
	}
}

/**
*	Function:	abr_ADD_Village_Grove_Community
*
*	Descripton: Creates Community [Village Grove Community] and insert all releveant data
*/
function abr_ADD_Village_Grove_Community(){
	if(!post_exists('Village Grove Community')){
		$my_post = array('post_type' => 'community','post_title' => 'Village Grove Community','post_status'  => 'publish');
		$post_id	= wp_insert_post($my_post);
		$image_path=	ABR_PLUGIN_DIR_URL.'/img/536bd1f719cf1718.jpg';
		$src		=abr_update_featured_image($image_path,$post_id);
		update_post_meta( $post_id, 'cf_community_pic',$src); // Community Pic		
		$site_plan		=	ABR_PLUGIN_DIR_URL.'/img/complex-layout2.jpg';
		$site_plan_src	=abr_update_featured_image($site_plan,$post_id);
		update_post_meta( $post_id, 'cf_community_site_plan',$site_plan_src); // Site paln		
		/*$img_path		=	ABR_PLUGIN_DIR_URL.'/img/';
		$gallery_imgs	=	array($img_path.'1.jpg',$img_path.'2.jpg',$img_path.'3.jpg',$img_path.'4.jpg',$img_path.'5.jpg',$img_path.'6.jpg',$img_path.'7.jpg');
		$src_arr		=	array();
		foreach($gallery_imgs as $img){
			$gallery_src	=	abr_update_featured_image($img,$post_id);
			array_push($src_arr,$gallery_src);
		}
		update_post_meta($post_id,'cf_community_gallery',$src_arr);
		*/		
		$terms1		=	array("Generic Rentals, Inc.");
		$values1	=	abr_get_taxonomy_ids_ARRAY($terms1,'community_owner');	//community_owner
		wp_set_post_terms( $post_id, $values1,'community_owner',false );
		
		$terms2		=	array("Residential Home");
		$values2	=	abr_get_taxonomy_ids_ARRAY($terms2,'community_type');	//community_type
		wp_set_post_terms( $post_id, $values2,'community_type',false );
		
		$terms3		=	array("Apartment Community");
		$values3	=	abr_get_taxonomy_ids_ARRAY($terms3,'community_categories');	//community_categories
		wp_set_post_terms( $post_id, $values3,'community_categories',false );
		
		$terms4		=	array("Bloomington Indiana – North");
		$values4	=	abr_get_taxonomy_ids_ARRAY($terms4,'community_location');	//community_location
		wp_set_post_terms( $post_id, $values4,'community_location',false );
		
		$terms5		=	array("On Site Parking","Clubhouse","Guest Parking","Hot Tub","Kid’s Art Room","Rooftop Balcony");
		$values5	=	abr_get_taxonomy_ids_ARRAY($terms5,'community_features');	//community_features
		wp_set_post_terms( $post_id, $values5,'community_features',false );
		
		update_post_meta( $post_id, 'cf_community_address','1292 E. 10th St.Miami FL, 392039');
		update_post_meta( $post_id, 'cf_community_map','<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3467.4812426993312!2d-95.1404848852493!3d29.64780658202839!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x86409f2be0d3cca5%3A0x1d087996057072fb!2sVillage+Grove+Community!5e0!3m2!1sen!2sin!4v1510295784167" width="600" height="450" frameborder="0" style="border:0" allowfullscreen></iframe>');
		update_post_meta( $post_id, 'cf_community_city','Miami');
		update_post_meta( $post_id, 'cf_community_state','Florida');
		update_post_meta( $post_id, 'cf_community_zip_code','19201');
		update_post_meta( $post_id, 'cf_community_youtube_video','https://youtu.be/yAoLSRbwxL8');
		update_post_meta( $post_id, 'cf_community_description','Lorem ipsum dolor sit amet, consectetur adipiscing elit. In mollis non nulla sit amet volutpat. Aenean et scelerisque diam. Integer vitae mi orci. Nunc fringilla tortor in lorem scelerisque, sit amet tempor odio tristique. Praesent id efficitur lorem. Sed ac rhoncus mi, quis vehicula leo. Etiam laoreet nibh ac mattis sodales. In consequat massa non tortor molestie elementum. Aenean quis augue cursus purus porttitor commodo quis ac felis. Cras eget condimentum lectus. Pellentesque ultricies id purus et congue. Mauris condimentum molestie metus, nec hendrerit eros blandit hendrerit. Proin et accumsan ipsum. Fusce sed velit elit. Aliquam at arcu posuere, iaculis leo nec, bibendum est. Fusce eros libero, rhoncus ut eleifend nec, facilisis in mauris.');
	}
}

/**
*	Function:	abr_ADD_The_Heather_Apartment
*
*	Descripton: Creates apartment [The Heather - 5 Bedroom 3 Bath Style House] and insert all releveant data
*/
function abr_ADD_The_Heather_Apartment(){
	if(!post_exists('The Heather - 5 Bedroom 3 Bath Style House')){
		$my_post = array('post_type' =>'apartment','post_title'	=>	'The Heather - 5 Bedroom 3 Bath Style House',
		'post_status'	=>	'publish');
		$post_id	=	wp_insert_post( $my_post );
		$image_path=	ABR_PLUGIN_DIR_URL.'/img/ogdubory3io79bjqvmnc.jpg';
		$src		=	abr_update_featured_image($image_path,$post_id);
		update_post_meta( $post_id, 'cf_apartment_pic',$src); // Apartment Pic	
		
		/*$img_path		=	ABR_PLUGIN_DIR_URL.'/img/';
		$gallery_imgs	=	array($img_path.'1.jpg',$img_path.'2.jpg',$img_path.'3.jpg',$img_path.'4.jpg',$img_path.'5.jpg',$img_path.'6.jpg',$img_path.'7.jpg');
		$src_arr	=	array();
		foreach($gallery_imgs as $img){
			$gallery_src	=	abr_update_featured_image($img,$post_id);
			array_push($src_arr,$gallery_src);
		}
		update_post_meta($post_id,'cf_apartment_gallery',$src_arr); //Gallery Pic*/		
		$posttitle = 'Scholars Community';
		$mypost 	= get_page_by_title($posttitle, OBJECT, 'community');
		update_post_meta( $post_id, 'cf_apartment_community',$mypost->ID);
		update_post_meta( $post_id, 'cf_apartment_location','bloomington-indiana-north');
				$taxonomy_name	='apartment_utilities';
		$terms1		=	array("All Utilities Included","Cable TV","Electricity Included");
		$values1	=	abr_get_taxonomy_ids_ARRAY($terms1,$taxonomy_name); //cf_apartment_utilities_landlord
		update_post_meta( $post_id, 'cf_apartment_utilities_landlord',$values1);		
		$terms2		=	array("Heat","Cable TV","Water Included");
		$values2	=	abr_get_taxonomy_ids_ARRAY($terms2,$taxonomy_name); //cf_apartment_utilities_tenant
		update_post_meta( $post_id, 'cf_apartment_utilities_tenant',$values2);
		
		$terms3		=	array("24-Hour Emergency Maintenance","Built-in Microwave Oven","Central Air Conditioning and Heating","Energy-efficient appliances","Balcony","Ceiling Fan","Dishwasher In Unit","Gym");
		$values3	=	abr_get_taxonomy_ids_ARRAY($terms3,'apartment_amenities'); //apartment_amenities
		 wp_set_post_terms( $post_id, $values3, 'apartment_amenities',false );
		 
		$terms4		=	array("Monthly","Fixed Term Lease");
		$values4	=	abr_get_taxonomy_ids_ARRAY($terms4,'apartment_leasing');	//apartment_leasing
		wp_set_post_terms( $post_id, $values4, 'apartment_leasing',false );
		
		$terms5	=	array("Apartment");
		$values5	=	abr_get_taxonomy_ids_ARRAY($terms5,'apartment_style');	//apartment_style
		wp_set_post_terms( $post_id, $values5,'apartment_style',false );
		
		$terms6=	array("1 Bedroom");
		$values6	=	abr_get_taxonomy_ids_ARRAY($terms6,'apartment_bedrooms');	//cf_apartment_bedrooms
		wp_set_post_terms( $post_id, $values6,'apartment_bedrooms',false );
		
		$terms7=	array("5 Baths");
		$values7	=	abr_get_taxonomy_ids_ARRAY($terms7,'apartment_bathrooms');	//apartment_bathrooms
		wp_set_post_terms( $post_id, $values7,'apartment_bathrooms',false );
		
		$terms8=	array("1300 Sq. Ft.");
		$values8	=	abr_get_taxonomy_ids_ARRAY($terms8,'apartment_area');	//apartment_area
		wp_set_post_terms( $post_id, $values8,'apartment_area',false );
		
		$terms8=	array("1 Person");
		$values8	=	abr_get_taxonomy_ids_ARRAY($terms8,'apartment_rental_occupancy');	//apartment_rental_occupancy
		wp_set_post_terms( $post_id, $values8,'apartment_rental_occupancy',false );
		
		$terms9=	array("Available August 2018","Month to Month","Vacation Rental");
		$values9	=	abr_get_taxonomy_ids_ARRAY($terms9,'apartment_availability_options');	//apartment_availability_options
		wp_set_post_terms( $post_id, $values9,'apartment_availability_options',false );
				
		update_post_meta( $post_id, 'cf_apartment_rent_month','1,238.00');
		update_post_meta( $post_id, 'cf_apartment_rent_month_range','1200-per-month');
		update_post_meta( $post_id, 'cf_apartment_date_available','23-03-2018');
		update_post_meta( $post_id, 'cf_apartment_features','Lorem ipsum dolor sit amet, consectetur adipiscing elit. In mollis non nulla sit amet volutpat. Aenean et scelerisque diam. Integer vitae mi orci. Nunc fringilla tortor in lorem scelerisque, sit amet tempor odio tristique. Praesent id efficitur lorem. Sed ac rhoncus mi, quis vehicula leo. Etiam laoreet nibh ac mattis sodales. In consequat massa non tortor molestie elementum. Aenean quis augue cursus purus porttitor commodo quis ac felis. Cras eget condimentum lectus. Pellentesque ultricies id purus et congue. Mauris condimentum molestie metus, nec hendrerit eros blandit hendrerit. Proin et accumsan ipsum. Fusce sed velit elit.');
		update_post_meta( $post_id, 'cf_apartment_no_of_units','12');
		update_post_meta( $post_id, 'cf_apartment_featured','on');
		update_post_meta( $post_id, 'cf_apartment_youtube_video','https://www.youtube.com/watch?v=C9LRCwOYZGo');
	}
}

/**
*	Function:	abr_ADD_The_Commodore_Apartment
*
*	Descripton: Creates apartment [The Commodore 1 Bedroom Efficiency 1 Bath(Style Flat)] and insert all releveant data
*/
function abr_ADD_The_Commodore_Apartment(){
	if(!post_exists('The Commodore 1 Bedroom Efficiency 1 Bath(Style Flat)')){
		$my_post = array('post_type' => 'apartment','post_title'    => 'The Commodore 1 Bedroom Efficiency 1 Bath(Style Flat)','post_status'   => 'publish');
		$post_id	=	wp_insert_post( $my_post );
		$image_path=	ABR_PLUGIN_DIR_URL.'/img/about.jpg';
		$src		=	abr_update_featured_image($image_path,$post_id);
		update_post_meta( $post_id, 'cf_apartment_pic',$src); // Apartment Pic	
		/*
		$img_path		=	ABR_PLUGIN_DIR_URL.'/img/';
		$gallery_imgs	=	array($img_path.'1.jpg',$img_path.'2.jpg',$img_path.'3.jpg',$img_path.'4.jpg',$img_path.'5.jpg',$img_path.'6.jpg',$img_path.'7.jpg');
		$src_arr	=	array();
		foreach($gallery_imgs as $img){
			$gallery_src	=	abr_update_featured_image($img,$post_id);
			array_push($src_arr,$gallery_src);
		}
		update_post_meta($post_id,'cf_apartment_gallery',$src_arr);	//Gallery */		
		$posttitle = 'Village Grove Community';
		$mypost = get_page_by_title($posttitle, OBJECT, 'community');
		update_post_meta( $post_id, 'cf_apartment_community',$mypost->ID); //Apartment Community		
		update_post_meta( $post_id, 'cf_apartment_location','new-york-city-east-manhattan');
		
		$taxonomy_name	='apartment_utilities';
		$terms1		=	array("All Utilities Included","Cable TV","Electricity Included");
		$values1	=	abr_get_taxonomy_ids_ARRAY($terms1,$taxonomy_name); //cf_apartment_utilities_landlord
		update_post_meta( $post_id, 'cf_apartment_utilities_landlord',$values1);
		
		$terms2		=	array("Heat","Cable TV","Water Included");
		$values2	=	abr_get_taxonomy_ids_ARRAY($terms2,$taxonomy_name); //cf_apartment_utilities_tenant
		update_post_meta( $post_id, 'cf_apartment_utilities_tenant',$values2);
		
		$terms3		=	array("In Unit Laundry","Free Wifi","Central Air Conditioning and Heating","Energy-efficient appliances","Gas Cooking","Ceiling Fan","Dishwasher In Unit","Gym");
		$values3	=	abr_get_taxonomy_ids_ARRAY($terms3,'apartment_amenities'); //apartment_amenities
		 wp_set_post_terms( $post_id, $values3, 'apartment_amenities',false );
				
		$terms4		=	array("Year to Year","Fixed Term Lease");
		$values4	=	abr_get_taxonomy_ids_ARRAY($terms4,'apartment_leasing');	//apartment_leasing
		wp_set_post_terms( $post_id, $values4, 'apartment_leasing',false );
		
		$terms5	=	array("Flat");
		$values5	=	abr_get_taxonomy_ids_ARRAY($terms5,'apartment_style');	//apartment_style
		wp_set_post_terms( $post_id, $values5,'apartment_style',false );
		
		$terms6=	array("8 Bedrooms");
		$values6	=	abr_get_taxonomy_ids_ARRAY($terms6,'apartment_bedrooms');	//cf_apartment_bedrooms
		wp_set_post_terms( $post_id, $values6,'apartment_bedrooms',false );		
		
		$terms7=	array("3.5 Baths");
		$values7	=	abr_get_taxonomy_ids_ARRAY($terms7,'apartment_bathrooms');	//apartment_bathrooms
		wp_set_post_terms( $post_id, $values7,'apartment_bathrooms',false );
		
		$terms8=	array("2000 Sq. Ft.");
		$values8	=	abr_get_taxonomy_ids_ARRAY($terms8,'apartment_area');	//apartment_area
		wp_set_post_terms( $post_id, $values8,'apartment_area',false );
		
		$terms8=	array("2 People");
		$values8	=	abr_get_taxonomy_ids_ARRAY($terms8,'apartment_rental_occupancy');	//apartment_rental_occupancy
		wp_set_post_terms( $post_id, $values8,'apartment_rental_occupancy',false );
		
		$terms9=	array("Available August 2019","Month to Month","Vacation Rental");
		$values9	=	abr_get_taxonomy_ids_ARRAY($terms9,'apartment_availability_options');	//apartment_availability_options
		wp_set_post_terms( $post_id, $values9,'apartment_availability_options',false );

		update_post_meta( $post_id, 'cf_apartment_rent_month','1,238.00');
		update_post_meta( $post_id, 'cf_apartment_rent_month_range','1200-per-month');
		update_post_meta( $post_id, 'cf_apartment_date_available','23-03-2018');
		update_post_meta( $post_id, 'cf_apartment_features','Lorem ipsum dolor sit amet, consectetur adipiscing elit. In mollis non nulla sit amet volutpat. Aenean et scelerisque diam. Integer vitae mi orci. Nunc fringilla tortor in lorem scelerisque, sit amet tempor odio tristique. Praesent id efficitur lorem. Sed ac rhoncus mi, quis vehicula leo. Etiam laoreet nibh ac mattis sodales. In consequat massa non tortor molestie elementum. Aenean quis augue cursus purus porttitor commodo quis ac felis. Cras eget condimentum lectus. Pellentesque ultricies id purus et congue. Mauris condimentum molestie metus, nec hendrerit eros blandit hendrerit. Proin et accumsan ipsum. Fusce sed velit elit.');
		update_post_meta( $post_id, 'cf_apartment_no_of_units','12');
		update_post_meta( $post_id, 'cf_apartment_featured','on');
		update_post_meta( $post_id, 'cf_apartment_youtube_video','https://www.youtube.com/watch?v=ADWsdHgzXwk');
	}
}

/**
*	Function:	abr_get_taxonomy_ids_ARRAY
*
*	@param:		$terms_name_array,$taxonomy_name
*	@return:	array
*/

function abr_get_taxonomy_ids_ARRAY($terms_name_array,$taxonomy_name){
	$aptUtilitiesTAX	=	About_rental_cf_exe::get_terms_id_title_ARR($taxonomy_name);	
	$result	=array_intersect($aptUtilitiesTAX,$terms_name_array);
	$id_arr	=array();
	foreach($result as $tid=>$term){
		array_push($id_arr,$tid);
	}
	return $id_arr;
}

/**
*	Function:	abr_update_featured_image
*
*	@param:		$image,$post_id
*	@return:	$src(image source)
*/
function abr_update_featured_image($image,$post_id){
	require_once(ABSPATH.'wp-admin/includes/media.php');
	require_once(ABSPATH.'wp-admin/includes/file.php');
	require_once(ABSPATH.'wp-admin/includes/image.php');
	$src	=	media_sideload_image($image,$post_id,null,'src');
	return $src;
}