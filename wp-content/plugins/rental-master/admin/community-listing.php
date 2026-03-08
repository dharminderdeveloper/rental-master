<?php

add_shortcode('ar_community_listing_default','abr_community_listing_default');

/**
*	ar_community_listing_default
*
*	# Community Listing shortcode function
*/
function abr_community_listing_default(){
	$html='';
	$args = array(
	'numberposts'	=> -1,
	'orderby'		=> 'date',
	'order' 		=> 'DESC',
	'post_type' 	=> 'community',
	'post_status'	=> 'publish');
	$posts	=	get_posts($args);
	if($posts){
		$html.='<section>';
			$html.='<ul class="cmdlist">';
			foreach($posts as $post){
				$cmid	=	$post->ID;
				$title	=	$post->post_title;
				$link	=	get_permalink($cmid);
				$pic	=	get_post_meta($cmid,'cf_community_pic',true);
				$owners	=	wp_get_post_terms($cmid,'community_owner');
				$types	=	wp_get_post_terms($cmid,'community_type');
				$cats	=	wp_get_post_terms($cmid,'community_categories');
				$locs	=	wp_get_post_terms($cmid,'community_location');
				$html.='<li>';
				if($pic){
					$html.='<a href="'.$link.'">';
					$html.='<img class="alignleft size-medium wp-image-43" width="300" height="193" src="'.$pic.'">';
					$html.='</a>';
				}
				$html.='<a href="'.$link.'" class="aptttl">'.__($title,'ar').'</a><br>';
				foreach($owners as $owner){
					$html.='<b>'.__('Owner:','ar').' </b>'.__($owner->name,'ar');
				}
				$html.='<br>';
				foreach($types as $type){
					$html.='<b>'.__('Type:','ar').'  </b>'.__($type->name,'ar');
				}
				$html.='<br>';
				foreach($cats as $cat){
					$html.='<b>'.__('Category:','ar').' </b>'.__($cat->name,'ar');
				}
				$html.='<br>';
				foreach($locs as $loc){
					$html.='<b>'.__('Location:','ar').' </b>'.__($loc->name,'ar');
				}
				$html.='</li>';
			}
			$html.='</ul>';
		$html.='</section>';
	}
	return $html;
}