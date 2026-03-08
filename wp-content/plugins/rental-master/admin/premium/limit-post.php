<?php

/**
* Function: abr_appartment_get_posts
*
* Return: $no_of_posts (apartments)
*/
function abr_appartment_get_posts(){
	
	$apartmentS		=	abr_get_post_nums('apartment');
	$communities	=	abr_get_post_nums('community');
	$virtualtours	=	abr_get_post_nums('virtualtour');
	$leasings		=	abr_get_post_nums('leasing');
	$realestates	=	abr_get_post_nums('realestate');	
	$version		=	get_option('abtrv', false); # VERSION(1==TRIAL,2== PREMIUM

	$current_post_type	=	abr_current_post_type();
	if(($version==1)||($version==0)){
		
		if (($apartmentS >=5)&&($current_post_type=='apartment')){
			add_filter('wp_insert_post_data','abr_stop_post_publish',99, 2);
		}

		if (($communities >=5)&&($current_post_type=='community')){
			add_filter('wp_insert_post_data','abr_stop_post_publish',99, 2);
		}

		if (($virtualtours >=5)&&($current_post_type=='virtualtour')){
			add_filter('wp_insert_post_data','abr_stop_post_publish',99, 2);
		}

		if (($leasings >=5)&&($current_post_type=='leasing')){
			add_filter('wp_insert_post_data','abr_stop_post_publish',99, 2);
		}

		if (($realestates >=5)&&($current_post_type=='realestate')){
			add_filter('wp_insert_post_data','abr_stop_post_publish',99, 2);
		}
	}
}

add_action( 'current_screen','abr_appartment_get_posts');

/**
** Function: abr_stop_post_publish
*
*@ Return:	Post Limit Message
*/
function abr_stop_post_publish ($data,$postarr){
	if($data['post_type'] == 'apartment'){
		return abr_post_limit_msg('Apartments');
	}
	if($data['post_type'] == 'community'){
		return abr_post_limit_msg('Communities');
	}
	if($data['post_type'] == 'virtualtour'){
		return abr_post_limit_msg('Virtualtours');
	}
	if($data['post_type'] == 'leasing'){
		return abr_post_limit_msg('Leasings');
	}
	if($data['post_type'] == 'realestate'){
		return abr_post_limit_msg('Realestates');
	}
}

/*
** Function: abr_get_post_nums
*
**@Return : Nums of posts
*/
function abr_get_post_nums($post_type){
	$args = array(
		'post_type' => array($post_type),
		'post_status' => 'publish',
		);
	$posts	=	get_posts($args);
	return  count($posts);
}

/**
** Function: abr_current_post_type
*
*@ Return:Current post type
*/
function abr_current_post_type(){
	  $screen = get_current_screen();
		return  $screen->post_type;
}

/**
** Function: abr_post_limit_msg
*
*/
function abr_post_limit_msg($name){
	$msg='';
	$msg	.=	'<h1>Sorry !</h1> <p> Currently you are using trial version of <b>About Rentals</b> <p>';
	$msg	.=	'<p>To add more <b>'.$name.'</b> Please get your <a href="'.ABR_PLUGIN_MASTER_URL.'">Premium Version</a> <p>';
	$msg	.=	'<p><a href="'.admin_url().'">Back to Dashboard</a></p>';
	?><style>#error-page {margin-top: 50px;text-align: center;}</style><?php
	echo wp_die($msg);
}