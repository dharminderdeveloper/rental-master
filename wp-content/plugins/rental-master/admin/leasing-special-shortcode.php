<?php
/**
*	retrieve all leasing ids
*	
*	@param:	$posts_per_page
*	@return: array
*/
function abr_retrieve_all_leasing_ids($posts_per_page) {
	$posts_per_page=''? $posts_per_page=	'-1':$posts_per_page;
	 $args = array(
		'posts_per_page'	=> $posts_per_page,
		'orderby'			=> 'date',
		'order' 			=> 'DESC',
		'post_type'    	=> 'leasing',
		'fields'			=> 	'ids',
		'post_status'     => 'publish',
	);

	$ids = get_posts( $args );
	return $ids;
}

function abr_leasing_html($nums){
	$nums=''? $nums='-1':$nums;
	$ids	=	abr_retrieve_all_leasing_ids($nums);
	
	if(!empty($ids)){
		$html	=	'';
		$html.='<div class="leasingSpecial main">';
		foreach((array) $ids as $id){
			$title			=	get_the_title($id);
			$publish_date 	=	get_post_meta($id,'cf_leasing_publish_date', true);
			$expire_date 	=	get_post_meta($id,'cf_leasing_expire_date', true);
			$desc 			=	get_post_meta($id,'cf_leasing_description', true);
			$aids 			=	get_post_meta($id,'cf_leasing_apartment', true);
			$aids 			=	is_array($aids) ? $aids : array($aids);
			$html.='<div class="leasingSection">';
				$html.='<h2>'.$title.'</h2>';
				$html.='<div>'.__('Publish date:','ar').$publish_date.'</div>';
				$html.='<div>'.__('Expiry date:','ar').$expire_date.'</div>';
				$html.='<div>'.$desc.'</div>';
				$html.='<div style="clear:both;"></div>';
				$html.='<hr>';
				if(!empty($aids)){
					$html.='<ul class="aptul aptulB">';
					foreach($aids as $aid){
						$html.=abr_apartment_post($aid);
					}
					$html.='</ul>';
				}
				$html.='</div>';
				$html.='<hr class="leasing-section-hr">';
				$html.='<div style="clear:both;"></div>';
		}
		$html.='</div>';
	}
	return $html;
}

add_shortcode('ar_leasing_specials','abr_leasing_special');

/**
*	leasing_special
*
*	@return html
*/
function abr_leasing_special(){
	return abr_leasing_html($nums='');
}