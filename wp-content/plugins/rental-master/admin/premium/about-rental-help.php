<?php

/******************************************************
*	Page: about-rentals-help
*
* 	Description: (About Rentals Help) menu for user help
*
******************************************************/

	/**
	* Function: About Rentals Help Menu
	*
	* Desscription: Creates a menu to display user help page
	*/
	add_action( 'admin_menu', 'abr_about_rental_help_menu' );
	function abr_about_rental_help_menu(){
		add_menu_page(__('About Rentals Help','ar'),__('About Rentals Help','ar'),'administrator','abr-user-help','abr_help_page',ABR_PLUGIN_DIR_URL.'/img/help-icon.png',0);
	}
	
	function abr_help_page(){
		$content	 ='';
		$content	.=	'<h1>About Rentals User Help Section</h1>';
		$content	.=	'<div class="welcome-panel">';
		$content 	.=	abr_is_about_end();
		$content 	.=	abr_premium_package_notification();
		$content	.=	'<div class=" postbox-container">';
		$content	.=	abr_retrieve_content();
		$content	.=	'</div>';/*postbox-container Ends*/
		$content	.=	'</div>';/*welcome-panel Ends*/
		echo $content;
	}

	/**	
	** About rental premium package notifiation
	*
	* Displays notification html
	*/
	function abr_premium_package_notification(){
		$version	=	get_option('abtrv', false);
		$content	=	'';
		$content	.=	'<div class="trial-notification">';
		# Hide if  premium version activated and show if trial version activated or licence expired
		if($version==1||$version==''){
			$content	.=	'<h2 class="blink">Get Premium Version</h2>';
			$content	.=	'<p>';
			$content	.=	'	<span>Please insert your licence key <b>&nbsp;:&nbsp;</b></span>';
			$content	.=	'	<input type="text" name="licence-key" id="licence-key" value="'.get_option('abr_key', false).'">&nbsp;';
			$content	.=	'	<a href="#" id="get-premium" class="button button-primary">Submit</a>';
			$content	.=	'</p>';
		}
		# Hide if trial version or premium version activated
		if($version==''){
			$content	.=	'<p><strong>or</strong></p>';
			$content	.=	'<p>';
			$content	.=	'<a href="javascript:void(0)" class="button button-primary" name="trial-version" id="trial-version" title="Continue free trial">Continue Free Trial</a>';
			$content	.=	'</p>';
			$content	.=	'<hr>';
		}
		$content	.=	'</div>';
		return $content;
	}
	
	/*
	* Function:	abr_is_expire
	*
	* Description: compares current date and expiry date of premium package
	*
	* @return Remaining Days
	*/
	function abr_check_expiry_date(){
		$current_date	= new DateTime(date("Y/m/d"));
		//$start date	= get_option('abtre_date', false);
		$end_date 		= new DateTime(get_option('abtre_date', false));
		
		$interval	=	$current_date->diff($end_date); //compare current date with expiry date
		$interval	=	$interval->format('%a'); //$interval	=	Remaining  Days
		
		if($interval<=7&&$interval>=1){
			return $interval;
		}
		else if($interval==0){
			return 'end';
		}
		else{
			return 'go';
		}
	}
	
	/**
	*	abr_is_about_end
	*
	* @return: notification
	*/
	function abr_is_about_end(){
		$status =	abr_check_expiry_date();
		if($status=='end'){
			abr_start_trial_pack();
		}
		else if($status=='go'){}
		else{
			$version	=	get_option('abtrv', false);
			if($version==2){
				return abr_expire_notification($status);
			}
		}
	}

	/**
	*	Licenec expire notification
	*	
	*	@param:	$status
	*	@return: html
	*/
	
	function abr_expire_notification($status){
		$content	=	'';
		$content	.=	'<div style="text-align:center">';
		$content	.=		'<h2 style="color:#FF0000"> Your licence will be expire in '.$status.' days	</h2>';
		$content	.=		'<h3>To Continue the Service Please Renew Your Monthly <a href="'.ABR_PLUGIN_MASTER_URL.'">Subscription</a><h3>';
		$content	.=	'</div>';
		return $content;
	}

	/**
	*	Update for trial package
	*	@return:	trial pack
	*/
	function abr_start_trial_pack(){
		return update_option('abtrv',1); // if subscription ends then  trial	version starts
	}


	function abr_retrieve_content(){
		$url = ABR_PLUGIN_MASTER_URL.'wp-admin/admin-ajax.php?action=abr_page_html&page_slug=plugin-page-html';
		$request	=	wp_remote_get($url);
		$response	=	wp_remote_retrieve_body( $request );
		$obj		=	json_decode( $response,true);
		$response	=	$obj['response'];
		$content	=	$obj['content'];
		if($content){
			return $content;
		}
		else{
			return null;
		}
	}

	/**
	*	Function:	abr_check_saved_key
	*
	*	Description:	Checks for key after activation  if key exist in database no need to enter key again
	*/
	function abr_find_saved_key(){
		$key	=	get_option('abr_key', false);
		$domain	=	get_option('abr_domain', false);

		if((!empty($key))&&(!empty($domain))){
			$url =ABR_PLUGIN_MASTER_URL.'wp-admin/admin-ajax.php?action=activation_ajax&key='.$key.'&domain='.$domain;
			$request	=	wp_remote_get($url);
			$response	=	wp_remote_retrieve_body( $request );
			$obj		=	json_decode( $response,true);
			$key_status=	$obj['key_status'];
			$key		=	get_option('abr_key');
			
			$version=get_option('abtrv'); // 1= trial / 2= premium
			
			if($key_status==1&&$key!=''&&$version!=1){
				if(get_option('abtrv')){
					update_option('abtrv',2);
				}else {
					add_option('abtrv',2);
				}
			}
		}
	}

	function abr_admin_notice() {
		?>
		<div class="notice notice-warning  is-dismissible">
			<p><?php _e( '&nbsp; Your Premium Pack has expired or temporarily disabled by your administrator. For any query please contact with plugin <a href="'.ABR_PLUGIN_MASTER_URL.'">administration</a>', 'abr' ); ?></p>
		</div>
		<?php
	}