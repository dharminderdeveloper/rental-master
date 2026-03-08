<?php

add_shortcode('ar_realestate_listing','abr_realestate_listing');
/**
*	abr_realestate_listing
*
*	# RealEstate Listing shortcode function
*/
 function abr_realestate_listing(){
	 $listing_array=array();
	
	if( isset($_GET['lease'])){
		$lease	=	array_map( 'sanitize_text_field', wp_unslash( $_GET['lease'] ) );
		$taxA	=	array('taxonomy'=>'apartment_leasing','field'=>'id','terms'=>$lease);
		$taxA 	=	$taxA ? $taxA : $taxA='';
		$listing_array[]=$taxA;
	}
	if( isset($_GET['styles'])){
		$styles=	array_map( 'sanitize_text_field', wp_unslash( $_GET['styles'] ) );
		$taxB	=	array('taxonomy'=>'apartment_style','field'=>'id','terms'=>$styles);
		$taxB	=	$taxB ? $taxB : $taxB='';
		$listing_array[]=$taxB;
	 }
	if( isset($_GET['amenties'])){
		$amenties	=	array_map( 'sanitize_text_field', wp_unslash( $_GET['amenties'] ) );
		$taxC		=	array('taxonomy'=>'apartment_amenities','field'=>'id','terms'=>$amenties);
		$taxC		=	$taxC ? $taxC : $taxC='';
		$listing_array[]=$taxC;
	}
	if( isset($_GET['bedroom'])){
		$bedroom	=	array_map( 'sanitize_text_field', wp_unslash( $_GET['bedroom'] ) );
		$taxD		=	array('taxonomy'=>'apartment_bedrooms','field'=>'id','terms'=>$bedroom);
		$taxD 		=	$taxD ? $taxD : $taxD='';
		$listing_array[]=$taxD;
	}
	 if( isset($_GET['bathroom'])){
		 $bathroom=	array_map( 'sanitize_text_field', wp_unslash( $_GET['bathroom'] ) );
		 $taxE		=	array('taxonomy'=>'apartment_bathrooms','field'=>'id','terms'=>$bathroom);
		 $taxE 	=	$taxE ? $taxE : $taxE='';
		 $listing_array[]=$taxE;
	 }
	 if( isset($_GET['area'])){
		 $area	=	array_map( 'sanitize_text_field', wp_unslash( $_GET['area'] ) );
		 $taxF	=	array('taxonomy'=>'apartment_area','field'=>'id','terms'=>$area);
		 $taxF	=	$taxF ? $taxF : $taxF='';
		 $listing_array[]=$taxF;
	 }
	if( isset($_GET['utilities'])){
		$utilities=	array_map( 'sanitize_text_field', wp_unslash( $_GET['utilities'] ) );
		$taxG		=	array('taxonomy'=>'apartment_utilities','field'=>'id','terms'=>$utilities);
		$taxG 		=	$taxG ? $taxG : $taxG='';
		$listing_array[]=$taxG;
	 }
	if( isset($_GET['options'])){
		$options	=	array_map( 'sanitize_text_field', wp_unslash( $_GET['options'] ) );
		$taxH		=	array('taxonomy'=>'apartment_availability_options','field'=>'id','terms'=>$options);
		$taxH 		=	$taxH ? $taxH : $taxH='';
		$listing_array[]=$taxH;
	}
	if( isset($_GET['prices'])){
		$prices	=	array_map( 'sanitize_text_field', wp_unslash( $_GET['prices'] ) );
		$taxI		=	array('taxonomy'=>'apartment_monthly_rent','field'=>'id','terms'=>$prices);
		$taxI 		=	$taxI ? $taxI : $taxI='';
		$listing_array[]=$taxI;
	 }
	if( isset($_GET['location'])){
		$location	=	array_map( 'sanitize_text_field', wp_unslash( $_GET['location'] ) );
		$taxJ		=	array('taxonomy'=>'community_location','field'=>'id','terms'=>$location);
		$taxJ 		=	$taxJ ? $taxJ : $taxJ='';
		$listing_array[]=$taxJ;
	 }
	 if(!empty($listing_array)){
		 $args = array(
			'tax_query'		=>	array('relation'=>'AND',$listing_array),
			'orderby'			=>	'date',
			'order'			=>	'DESC',
			'post_type'		=>	'apartment',
			'post_status'		=>	'publish',
			'suppress_filters'=>	true,
			'numberposts'		=>	-1
		);
	}
	else{
		$args = array(
			'orderby'			=>	'date',
			'order'				=>	'DESC',
			'post_type'			=>	'realestate',
			'post_status'		=>	'publish',
			'suppress_filters'	=>	true,
			'numberposts'		=>	-1
		);
	}
	
	$posts    =	get_posts($args);
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
		$mpage 	=	max($page, 1);
		$page 		=	min($mpage, $totalPages);
		$offset 	=	($page - 1) * $limit;
		if( $offset < 0 ) $offset = 0;
		$myposts 	= array_slice($data,$offset,$limit );
		$range		=  (int) $totalPages+1;
		$out='';
		$out.='<div id="aptListing">';
		//$out.=abr_map_area($post_ids); map removed by client 28/10/2017
		if(!empty($myposts)){
			$out.='<ul class="aptul aptulB">';
			foreach($myposts as $mpost){
				$out.=abr_realestate_post($mpost);
			}
			$out.='</ul>';
		}
		$out.=abr_cf_num_pagination($totalPages);
		$out.='</div>';
	}
	else{
		$out='';
		$out.='<br><h2 style="text-align:center">Sorry No Result Found !</h2>';
		}
	return $out;
}

 function abr_realestate_post($aid){ 	
	$o			=	'';
	$realestate	=	get_post($aid);
	
	//	echo '<pre>';print_r( $realestate );exit; 
	$alink				=	get_permalink($aid);
	@$title			=	$realestate->post_title;	
	$aPic				=	get_post_meta($aid,'cf_realestate_photo',true);
	/*
	$bedRoomARR		=	wp_get_post_terms($aid,'apartment_bedrooms');
	$bathRoomARR		=	wp_get_post_terms($aid,'apartment_bathrooms');
	$aAvailableOpARR	=	wp_get_post_terms($aid,'apartment_availability_options');
	$aAreaARR			=	wp_get_post_terms($aid,'apartment_area');	
	$monthlyRent		=	get_post_meta($aid,'cf_apartment_rent_month',true);	
	$cmid				=	get_post_meta($aid,'cf_apartment_community',true);
	$location			=	get_post_meta($cmid,'cf_community_address',true);
	*/
	$o.='<li>';
		$o.='<a class="aptttl" href="'.$alink.'"><img class="alignleft size-medium wp-image-43" src="'.$aPic.'" alt="apartment" width="300" height="193" /></a>';
		$o.='<div>';
			$o.='<a class="aptttl" href="'.$alink.'">'.__($title,'ar').'</a><br>';
			/*
			foreach($bedRoomARR as $bedroom)	{
				$o.=__($bedroom->name,'ar').' - ';
			}
			foreach($bathRoomARR as $bathroom){
				$o.=__($bathroom->name,'ar').' - ';
			}
			foreach($aAreaARR as $aArea){
				$o.=__($aArea->name,'ar');
			}
			$o.=' Sq. Ft.';

			$o.='<br><b>'.__('Availability:','ar').'</b>  &nbsp;';
			foreach($aAvailableOpARR as $aAvailable){
				$o.=__($aAvailable->name,'ar').' ';
				}
			$o.='<br><b>'.__('Listing Price:','ar').'</b> &nbsp; $'.$monthlyRent;
			$o.='<br><b>'.__('Location:','ar').'</b>  &nbsp;'.$location;
			*/
		$o.='</div>';
	$o.='</li>';
	return $o;
}