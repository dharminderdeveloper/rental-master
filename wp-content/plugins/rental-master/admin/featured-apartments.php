<?php
add_shortcode('ar_featured_apartments','abr_featured_apartments');

/**
**	Function:		abr_featured_apartments
*
*	Description:	Display featured apartments shortcode
*/
function abr_featured_apartments(){
	$posts	=	abr_get_featured_apartments();
	if($posts){
		$newPostArr	=	array();
		foreach($posts as $rpost){
			array_push($newPostArr,$rpost->ID);
		}
		global $wp_query;
		$nowpage	=	get_query_var( 'paged', 1 );
		$data		=	$newPostArr;
		$page		=	$nowpage;
		$total		=	count($data);
		$limit		=	5;
		if(!$limit)
		$limit		=	5;
		$totalPages=	ceil( $total/ $limit );
		$mpage 		=	max($page, 1);
		$page 		=	min($mpage, $totalPages);
		$offset 	=	($page - 1) * $limit;
		if( $offset < 0 ) $offset = 0;
		$myposts = array_slice($data,$offset,$limit );
		$range=  (int) $totalPages+1;
		$out='';
		$out.='<div id="aptListing">';
		$out.='<ul class="aptul aptulB">';
		foreach($myposts as $mpost){
			$out.=abr_apartment_post($mpost);
		}
		$out.='</ul>';
		$out.=abr_cf_num_pagination($totalPages);
		$out.='</div>';
	}
	return $out;
}


/**
**	Function:	abr_get_featured_apartments
*
*	@return:	array
*/
function abr_get_featured_apartments(){
	$args = array(
		'orderby'			=>	'date',
		'order'				=>	'DESC',
		'post_type'		=>	'apartment',
		'post_status'		=>	'publish',
		'suppress_filters'=>	true,
		'numberposts'		=>	-1,
		'meta_query' 		=>	array(
			   array(
				   'key' => 'cf_apartment_featured',
				   'value' => 'on',
				   'compare' => '=',
			   )
		)
	);
	$posts	=	get_posts($args);
	return $posts;
}