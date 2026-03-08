<?php
if(!defined('ABSPATH')) exit;
class CF_AR_Favorites{
	public static function init(){
        $class = __CLASS__;
        new $class;
    }
	
 	public function __construct(){
		add_shortcode('ar_my_favorites_apartment',array($this,'ar_my_favorites_apartment'));
		add_action("wp_ajax_nopriv_add_to_fav_ajax",array($this,'add_to_fav_ajax'));
		add_action("wp_ajax_add_to_fav_ajax",array($this,'add_to_fav_ajax'));
	}
	
	/**
	*	Function:	add_to_fav_ajax
	*
	*	Description: Accept ajax request for add apartment into favourite by session
	*/
	public function add_to_fav_ajax(){
		global $wp_session;
		$wp_session = WP_Session::get_instance();
		if($_REQUEST['status'] == "1"){
			$pid	=	sanitize_text_field($_REQUEST['pid']);
			if(isset($wp_session['fav_apartment_id'])){
				if(count($wp_session['fav_apartment_id']->toArray())){
					$fav_arr = array();
					$fav_arr = $wp_session['fav_apartment_id']->toArray();				
					array_push($fav_arr,$pid);
					$wp_session['fav_apartment_id'] = "";
					$wp_session['fav_apartment_id'] = $fav_arr;
					$response=array(
									'response'	=>	1,
									'message'=>'Add to favorites Successfully',
									);
				}
				else{
					$response=array(
									'response'	=>	2,
									'message'=>'Already Added',
									);
				}
			}
			else{
				$wp_session['fav_apartment_id'] = array($pid);
				$response=array(
									'response'	=>	1,
									'message'=>'Add to favorites Successfully',
									);
			}
		}
		else{
			$pid	=	sanitize_text_field($_REQUEST['pid']);
			$seeion_arr = $wp_session['fav_apartment_id']->toArray();
			$key = array_search($pid,$seeion_arr);
			unset($seeion_arr[$key]);
			$wp_session['fav_apartment_id'] = array_values($seeion_arr);
			$response=array(
									'response'	=>	0,
									'message'		=>	'Remove Successfully',
									);
		}
		echo json_encode($response);
		die;
	}
	
	/**
	*	Function:	abr_is_in_favorites
	*
	*	@param:		$id (apartment id)
	*	@return:	true/false
	*/
	public static function abr_is_in_favorites($id){
		global $wp_session;
		$wp_session = WP_Session::get_instance();
		if(isset($wp_session['fav_apartment_id'])){
			if(count($wp_session['fav_apartment_id']->toArray())>0){
				if(in_array($id,$wp_session['fav_apartment_id']->toArray())){
					return true;
				}else{return false;}
			}else{return false;}
		}
	}
	
	/**
	*	Function:		ar_my_favorites_apartment
	*
	*	Description:	displays favourite apartmemts
	*/
	public function ar_my_favorites_apartment(){
		global $wp_session;
		$wp_session = WP_Session::get_instance();
		ob_start();
		if(isset($wp_session['fav_apartment_id'])){
			$seeion_arr = $wp_session['fav_apartment_id']->toArray();
			if(!empty($seeion_arr)){
				$emailfavid =	About_rental_cf_exe::get_id_by_slug('email-favorites');
				$o='<ul class="cmdlist">';
				foreach($seeion_arr as $pid){
					$post	=	get_post($pid);
					$href	=	get_permalink($pid);
					$title	=	$post->post_title;
					
					if(get_post_type($pid)=='apartment'){
						$aAreaARR			=	wp_get_post_terms($pid,'apartment_area');
						$aPic				=	get_post_meta($pid,'cf_apartment_pic',true);
						$bedRoomARR		=	wp_get_post_terms($pid,'apartment_bedrooms');
						$bathRoomARR		=	wp_get_post_terms($pid,'apartment_bathrooms');
						$monthlyRent		=	get_post_meta($pid,'cf_apartment_rent_month',true);
						$aAvailableOpARR	=	wp_get_post_terms($pid,'apartment_availability_options');
						$o.='<li>';
							$o.='<img class="alignleft size-medium wp-image-43" src="'.$aPic.'" alt="'.$title.'" width="300" height="193" />';
							$o.='<span>';
								$o.='<a class="aptttl" href="'.$href.'">'.$title.'</a><br>';
								$o.='<b>'.__('Floorplan:','ar').'</b> &nbsp;';
								foreach($bedRoomARR as $bedroom){$o.=$bedroom->name;}
								foreach($bathRoomARR as $bathroom){$o.='&nbsp;/&nbsp;'.$bathroom->name;}
								$o.='<br><b>'.__('Dates Available:','ar').'</b> &nbsp;';
								foreach($aAvailableOpARR as $aAvailable){$o.=$aAvailable->name;}
								$o.='<br><b>'.__('Area Sq. Ft. :','ar').'</b> &nbsp;';
								foreach($aAreaARR as $aArea){$o.=$aArea->name;}
								$o.='<br><b>'.__('Monthly Rent','ar');
								$o.=__('Starting At:','ar').'</b> &nbsp;'; $o.=$monthlyRent;
							$o.='</span>';
						$o.='</li>';
					}
					if(get_post_type($pid)=='community'){
						$aAreaARR	=	wp_get_post_terms($pid,'apartment_area');
						$cmPic		=	get_post_meta($pid,'cf_community_pic',true);
						$ownerARR	=	wp_get_post_terms($pid,'community_owner');
						$catARR	=	wp_get_post_terms($pid,'community_categories');
						$typeARR	=	wp_get_post_terms($pid,'community_type',true);
						$locARR	=	wp_get_post_terms($pid,'community_location');
						$o.='<li>';
							$o.='<img class="alignleft size-medium wp-image-43" src="'.$cmPic.'" alt="'.$title.'" width="300" height="193" />';
							$o.='<span>';
								$o.='<a class="aptttl" href="'.$href.'">'.__($title,'ar').'</a><br>';
								$o.='<b>'.__('Owner:','ar').'</b> &nbsp;';
								foreach($ownerARR as $owner){$o.=$owner->name;}
								$o.='<br><b>'.__('Category:','ar').'</b> &nbsp;';
								foreach($catARR as $cat){$o.=$cat->name;}
								$o.='<br><b>'.__('Type. :','ar').'</b> &nbsp;';
								foreach($typeARR as $type){$o.=$type->name;}
								$o.='<br><b>'.__('Location','ar').'</b> &nbsp;';
								foreach($locARR as $loc){$o.=$loc->name;}
							$o.='</span>';
						$o.='</li>';
					}
				}
				$o.='</ul>';
				$o.='<div>';
				$o.='<a class="myFavrtBtn" href="'.get_permalink($emailfavid).'" >'.__('Email Me My Favorites','ar').'</a>';
				$o.='</div>';
				echo $o;
			}
		}else{
				echo '<h3>'.__("You do not have any listing in favorites","ar").'</h3>';
			}
		$content=ob_get_contents();
		ob_end_clean();
		return $content;
	}

}
add_action('plugins_loaded',array('CF_AR_Favorites','init')); # To execute class