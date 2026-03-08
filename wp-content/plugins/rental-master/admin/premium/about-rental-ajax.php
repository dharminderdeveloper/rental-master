<?php
/**
**	File : about-renatal-ajax.php	
*/
	/*
	* Function:	abr_version_activation_ajax
	*
	* Description: get ajax request to activate Particular vesrion
	*/
	function abr_version_activation_ajax(){
		if($_REQUEST){
			$act	=		sanitize_text_field($_REQUEST['act']);
			 $key	=		sanitize_text_field($_REQUEST['key']);
			 $domain	=	sanitize_text_field(abr_pure_Domain_name($_POST['domain']));			
			if($act=='trial-version'){
				abr_trial_version_activation();
			}
			if($act=='premium-version'){
				abr_premium_version_activation($key,$domain);
			}
		}
	}

	add_action( 'wp_ajax_abr_version_activation_ajax', 'abr_version_activation_ajax' );
	add_action( 'wp_ajax_nopriv_abr_version_activation_ajax', 'abr_version_activation_ajax' );

	/*
	* Function:	abr_trial_version_activation
	*
	* Description: Activates TRIAL Version of plugin and sends notification emails to both user and   
	plugin administration
	*/
	function abr_trial_version_activation(){

		if(get_option('abtrv')){
			update_option('abtrv',1);
		}
		else {
			add_option('abtrv',1);
		}
		abr_mail_to_plugin_support();
		abr_send_mail_to_user();

		$response	=	array(
					'response'=>1,
					'message'=>'Trial version is activated !'
		);

		echo json_encode( $response );
		die();
	}

	/*
	* Function:	abr_premium_version_activation
	*
	* Description: Activates PREMIUM Version of plugin and sends notification emails to both  user as
	*				well as plugin administration
	*/
	function abr_premium_version_activation($key,$domain){
	
		$url =ABR_PLUGIN_MASTER_URL.'wp-admin/admin-ajax.php?action=activation_ajax&key='.$key.'&domain='.$domain;
		$request	=	wp_remote_get($url);
		$response	=	wp_remote_retrieve_body($request);
		$obj		=	json_decode($response,true);
		$response	=	$obj['response'];
		$key_status=	$obj['key_status'];

		if($response==1) { // if response is ==1
						
			if($key_status==0){ ## if key_status ==0

				update_option('abtrv',1); // trial Version activate
				$response	=	array(
							'response'=>0,
							'message'=>'Your Premium pack is Disabled. Please contact with plugin administration '
				);
			}
			else{
				$start_date	=	$obj['start_date']; // Package starting date just after publish/update post
				$newdate 		=	strtotime ( '30 day' , strtotime ( $start_date ) ) ;
				$end_date		=	date ( 'Y-m-d' , $newdate );

				update_option('abr_key',$key); // save key
				update_option('abr_domain',$domain); // save Domain
				update_option('abtrs_date',$start_date);// start_date
				update_option('abtre_date',$end_date);// end_date

				if(get_option('abtrv')){
					update_option('abtrv',2);
				}else {
					add_option('abtrv',2);
				}
				/**	Send notification mail to user and plugin support**/
				abr_mail_to_plugin_support();
				abr_send_mail_to_user();

				$response	=	array(
							'response'=>1,
							'message'=>'Premium version is activated !'
				);
			}
		}
		if($response==0) {
			$response	=	array(
							'response'=>0,
							'message'=>'Key Does not match Please try again !'
			);
		}
		echo json_encode( $response );
		die();
	}

	/**
	*	Mail to Plugin Support
	* 	@param
	*/
	 function abr_mail_to_plugin_support(){

		$user_info		=	get_userdata(get_current_user_id());
		$user_email	=	$user_info->user_email;
		$display_name	=	$user_info->display_name;
		$to 			=	'info@whiteirondata.com'; //Plugin administration eamil
		$subject 		=	'New Subscription';
		
		$body 			=	'<div style="border: 1px solid #ccc;border-radius: 4px;padding: 30px 0;text-align:center;width:100%;"><img src="'.plugin_dir_url(__FILE__).'../img/master-logo.png" width="186" height="45" style="display: block;margin: 0 auto;">
			<p style="color: #333;font-size: 18px;margin: 20px 0;"><strong>Congratulations! </strong><b>'.$display_name.'</b> has Successfuly activated About Rentals Plugin.<br><b>User Email:</b> &nbsp;'. $user_email.'<br><b>User Website:</b>&nbsp; <a href="'.get_site_url().' ">'.get_site_url().'</a></p></div>';

		$headers = array('Content-Type: text/html; charset=UTF-8','From: About Rentals: <info@whiteirondata.com');
		wp_mail( $to, $subject, $body, $headers );
	}

	/**
	*	Function Name: abr_send_mail_to_user
	*
	*	Sends activation notification by email to User
	*/
	 function abr_send_mail_to_user(){
		
		$to 		=	get_bloginfo('admin_email');
		$subject 	=	'About Rentals';
		$body 		=	'
			<div style="border: 1px solid #ccc;border-radius: 4px;padding: 30px 0;text-align:center;width: 100%;">
			<img src="'.ABR_PLUGIN_DIR_URL.'img/master-logo.png" width="186" height="45" style="display: block;margin: 0 auto;">
			<p style="color: #333;font-size: 18px;margin: 20px 0;">
				<strong>Congratulations! </strong> you have successfuly activated About Rentals
			</p>
			<p>For any further query please visit at:
				<a href="'.ABR_PLUGIN_MASTER_URL.'contact/">'.ABR_PLUGIN_MASTER_URL.'</a>
			</p>
			</div>';
		$headers = array('Content-Type: text/html; charset=UTF-8','From: About Rentals: <info@whiteirondata.com');
		
		 wp_mail( $to, $subject, $body, $headers );
	}

	function abr_pure_Domain_name($url) {
		$urlArray  = explode( 'www.', $url );
		if( count( $urlArray ) > 1 ){
			$updateUrl = $urlArray[1];
		}else{
			$updateUrl = $urlArray[0];
		}
		return $updateUrl;
	}
	
	/*
	* Function:	update_availability_manager_ajax
	*
	* Description: update apartment info
	*/
	function update_availability_manager_ajax(){
		if($_REQUEST){
			$id			=	sanitize_text_field($_REQUEST['id']);
			$Rent		=	sanitize_text_field($_REQUEST['Rent']);
			$Units		=	sanitize_text_field($_REQUEST['Units']);
			$rentRange	=	sanitize_text_field($_REQUEST['rentRange']);
			$unitAvail	=	sanitize_text_field($_REQUEST['unitAvail']);
			$availDate	=	sanitize_text_field($_REQUEST['availDate']);
			$isFeatured=	sanitize_text_field($_REQUEST['isFeatured']);
			
			update_post_meta( $id, 'cf_apartment_rent_month', $Rent, false );
			update_post_meta( $id, 'cf_apartment_no_of_units', $Units, false );
			wp_set_post_terms( $id, $rentRange,'apartment_monthly_rent',false );
			update_post_meta( $id, 'cf_apartment_featured', $isFeatured, false );
			update_post_meta( $id, 'cf_apartment_date_available', $availDate, false);
			update_post_meta( $id, 'cf_apartment_no_of_units_available', $unitAvail, false );
			$response	=	array(
							'response'=>1,
							'message'=>'Updated Successfully !'
			);
		}
		else{
			$response	=	array(
							'response'=>1,
							'message'=>'Could not Update !'
			);
		}
		echo json_encode( $response );
		die();
	}
	
	add_action( 'wp_ajax_update_availability_manager_ajax', 'update_availability_manager_ajax');
	add_action( 'wp_ajax_nopriv_update_availability_manager_ajax', 'update_availability_manager_ajax');
	
	/*
	* Function:	abr_update_apartment_featured
	*
	* Description: (update) is apartment featured or not
	*/
	function abr_update_apartment_featured(){
		if($_REQUEST){
			$id				=	sanitize_text_field($_REQUEST['id']);
			$is_featured	=	sanitize_text_field($_REQUEST['is_featured']);
			if(update_post_meta( $id, 'cf_apartment_featured', $is_featured, false )){
				$response	=	array(
							'response'=>1,
							'message'=>'Updated Successfully !'
				);
			}
			else{
				$response	=	array(
							'response'=>0,
							'message'=>'Could not Update !'
				);
			}
			echo json_encode( $response );
			die();
		}
	}

	add_action( 'wp_ajax_abr_update_apartment_featured', 'abr_update_apartment_featured');
	add_action( 'wp_ajax_nopriv_abr_update_apartment_featured', 'abr_update_apartment_featured');