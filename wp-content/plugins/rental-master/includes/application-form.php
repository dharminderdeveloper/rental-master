<?php

/******************************************************
*	Page: Application Form
*
* 	Description: (Application Form) menu to enter applicaton from link
*
******************************************************/

/**
* Function: abr_application_menu
*
* Desscription: Creates a menu to display Application form input
*/
function abr_application_menu(){
	add_menu_page(__('Application Form','ar'),__('Application Form','ar'),'administrator','abr-application-form','abr_application_form_page',ABR_PLUGIN_DIR_URL.'/img/application-from.png',0);
}
add_action( 'admin_menu', 'abr_application_menu' );
	
function abr_application_form_page(){
	$content	 ='';
	$content	.=	'<h1>'.__('Application Form','ar').'</h1>';
	$content	.=	'<div class="wrap">';
	$content	.=	'<table class="form-table">';
	$content	.=	'<tbody>';
	$content	.=	'<tr>';
	$content	.=	'<th scope="row"><label for="applicationFormInput">'.__('Application Form Link:','ar').'</label></th>';
	$content	.=	'<td><input name="applicationFormInput" id="applicationFormInput"class="regular-text" type="text" value="'.get_option('abr_applicationFormInput', false) .'">';
	$content	.=	'&nbsp; <a href="javascript:void(0)" class="button button-primary SaveUrl" id="SaveUrl">Save</a>';
	$content	.=	'</td>';
	$content	.=	'</tr>';
	$content	.=	'</tbody>';
	$content	.=	'</table>';
	$content	.=	'</div>'; //wrap Ends
	echo $content;
}

/**
*	Function:		bar_update_application_form_url
*	Description:	Update applicationFormInput value
*/
add_action("wp_ajax_nopriv_abr_update_application_form_url",'abr_update_application_form_url');
add_action("wp_ajax_abr_update_application_form_url",'abr_update_application_form_url');
function abr_update_application_form_url() {
	if($_REQUEST['value']){
		$value	=	sanitize_text_field($_REQUEST['value']);
		update_option('abr_applicationFormInput',$value);
		$response=array(
			'response'	=>	1,
			'message'=>'Url Updated Successfully',
			'value'	=>	$value
			);
	}
	echo json_encode($response);
	die;
}