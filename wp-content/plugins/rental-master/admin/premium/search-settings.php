<?php

/******************************************************
*	Page: Search Settings
*
* 	Description: (Search Setings) menu to enter applicaton from link
*
******************************************************/

/**
* Function: abr_search_settings
*
* Desscription: Creates a menu to manage search settings
*/
function abr_search_settings(){
	add_menu_page(__('Search Settings','ar'),__('Search Settings','ar'),'administrator','abr-search-settings','abr_search_settings_page',ABR_PLUGIN_DIR_URL.'/img/application-from.png',0);
}
add_action( 'admin_menu', 'abr_search_settings' );
	
function abr_search_settings_page(){
	$content	 ='';
	$content	.=	'<h1>'.__('Search Settings','ar').'</h1>';
	$content	.=	'<div class="wrap">';
	$content	.=	'<table class="form-table">';
	$content	.=	'<tbody>';
	$content	.=	'<tr>';
	$content	.=	'<th scope="row"><label for="searchSettingsInput">'.__('Records Per Page:','ar').'</label></th>';
	$content	.=	'<td><input name="paginationNumber" id="paginationNumber"class="regular-text" type="text" value="'.get_option('abr_searchPaginationNumber', false) .'">';
	$content	.=	'&nbsp; <a href="javascript:void(0)" class="button button-primary" id="paginationSettings">Save</a>';
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
add_action("wp_ajax_nopriv_abr_update_search_settings",'abr_update_search_settings');
add_action("wp_ajax_abr_update_search_settings",'abr_update_search_settings');
function abr_update_search_settings() {
	if($_REQUEST['value']){
		$value	=	sanitize_text_field($_REQUEST['value']);
		update_option('abr_searchPaginationNumber',$value);
		$response=array(
			'response'	=>	1,
			'message'=>'Pagination Number Updated Successfully',
			'value'	=>	$value
			);
			
	}
	echo json_encode($response);
	die;
}