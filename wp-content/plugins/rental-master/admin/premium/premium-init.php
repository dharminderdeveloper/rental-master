<?php
	/**
	** About Renatl Premium Version style
	*
	*/
	function abr_register_premium_style(){
		wp_register_style('abr-premium-css',ABR_PLUGIN_DIR_URL.'/admin/premium/css/abr-premium.css',false,'0.0.1');
		wp_enqueue_style('abr-premium-css');
	}
	add_action( 'admin_enqueue_scripts','abr_register_premium_style');
	
	/*
	* Register Script for About Rentals
	*/
	function abr_register_premium_js() {
		wp_register_script('abr-premium-js',ABR_PLUGIN_DIR_URL.'/admin/premium/js/abr-premium.js',array('jquery'),'1.0',false);
		wp_enqueue_script('abr-premium-js');
		
		wp_register_script('sweet-alert',ABR_PLUGIN_DIR_URL.'/admin/premium/js/sa.min.js',array('jquery'),'1.0',false);
		wp_enqueue_script('sweet-alert');
	}
	add_action( 'admin_enqueue_scripts','abr_register_premium_js');

	/*--------------------------------------*/
			/*	ALL SUPPORTED FILES	*/
	/*--------------------------------------*/

	include('limit-post.php');
	include('about-rental-help.php');
	include('about-rental-ajax.php');
	include('pre-prepared-data.php');
	include('availability-manager.php');
	
	
	/**
	 **************************************************
	 ***** New Features of Version 2*******************
	 **************************************************
	 */
	 
	include('previous-leasing-special.php');
	include('realestate.php');
	include('single-realestate.php');
	include('realestate-listing.php');
	
	/**
	 **************************************************
	 ***** New Features of Version 1.3.2*******************
	 **************************************************
	 */
	include('search-feature.php');
	include('search-settings.php');

	/**---------	Widget	----------**/
	include(ABR_PLUGIN_DIR_PATH.'widget/realestate-widgets.php');
	

	/**
	* Function: Track the status of key
	*
	*/
	function abr_check_key_status(){
		
		$key	=	get_option('abr_key', false);
		$domain	=	get_option('abr_domain', false);

		if((!empty($key))&&(!empty($domain))){

			$url =ABR_PLUGIN_MASTER_URL.'wp-admin/admin-ajax.php?action=activation_ajax&key='.$key.'&domain='.$domain;
			$request	=	wp_remote_get($url);
			$response	=	wp_remote_retrieve_body( $request );
			$obj		=	json_decode( $response,true);
			
			$response	=	$obj['response'];
			$key_status=	$obj['key_status'];

			 // if version is premium and key is not outdated or disabled
			if(get_option('abtrv')==2){
				if($key_status==0){
					abr_start_trial_pack(); //trial pack starts
					add_action( 'admin_notices', 'abr_admin_notice' );
				}
			}else if(get_option('abtrv')==1){
				if($key_status==1){
					update_option('abtrv',2); // premium
				}
				if($key_status==0){
					add_action( 'admin_notices', 'abr_admin_notice' );
				}
			}
		}
	}

	add_action('admin_init','abr_check_key_status');
	
	
	/** 
     *	Get the latitude and longitude of an address 
	 */
	
	function getLatLong($address){
		if(!empty($address)){
			//Formatted address
			$formattedAddr = str_replace(' ','+',$address);
			//Send request and receive json data by address
			$geocodeFromAddr = file_get_contents('https://maps.googleapis.com/maps/api/geocode/json?address='.$formattedAddr.'&sensor=true_or_false&key='.ABR_MAP_API_KEY); 

			$output = json_decode($geocodeFromAddr);
			//	echo '<pre>';print_r( $output );exit; 

			//Get latitude and longitute from json data
			$data['latitude']  = $output->results[0]->geometry->location->lat; 
			$data['longitude'] = $output->results[0]->geometry->location->lng;
			//Return latitude and longitude of the given address
			if(!empty($data)){
				return $data;
			}else{
				return false;
			}
		}else{
			return false;   
		}
	}