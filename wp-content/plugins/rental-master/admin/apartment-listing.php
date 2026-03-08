<?php

add_shortcode('ar_apartment_listing','abr_apartment_listing');
/**
*	abr_apartment_listing
*
*	# Apartment Listing shortcode function
*/
 function abr_apartment_listing(){
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
			'order'			=>	'DESC',
			'post_type'		=>	'apartment',
			'post_status'		=>	'publish',
			'suppress_filters'=>	true,
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
				$out.=abr_apartment_post($mpost);
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

 function abr_apartment_post($aid){
	$o				=	'';
	$apartment		=	get_post($aid);
	$alink			=	get_permalink($aid);
	@$title			=	$apartment->post_title;	
	$bedRoomARR		=	wp_get_post_terms($aid,'apartment_bedrooms');
	$bathRoomARR		=	wp_get_post_terms($aid,'apartment_bathrooms');
	$aAvailableOpARR	=	wp_get_post_terms($aid,'apartment_availability_options');
	$aAreaARR			=	wp_get_post_terms($aid,'apartment_area');
	$aPic				=	get_post_meta($aid,'cf_apartment_pic',true);
	$monthlyRent		=	get_post_meta($aid,'cf_apartment_rent_month',true);	
	$cmid				=	get_post_meta($aid,'cf_apartment_community',true);
	$cDetail 			=	get_post($cmid);
	?>
		<script>
			jQuery( '.entry-title' ).text( '<?php echo $cDetail->post_title; ?>' );
		</script>
	<?php
	//entry-title
	$location			=	get_post_meta($cmid,'cf_community_address',true);
	$o.='<li>';
		$o.='<a class="aptttl" href="'.$alink.'"><img class="alignleft size-medium wp-image-43" src="'.$aPic.'" alt="apartment" width="300" height="193" /></a>';
		$o.='<div>';
			$o.='<a class="aptttl" href="'.$alink.'">'.__($title,'ar').'</a><br><b>';
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
			$o.='<br><b>'.__('Monthly Rent:','ar').'</b> &nbsp; $'.$monthlyRent;
			$o.='</b><br><b>'.__('Availability:','ar').'</b>  &nbsp;';
			foreach($aAvailableOpARR as $aAvailable){
				$o.=__($aAvailable->name,'ar').' ';
				}
			
			$o.='<br><b>'.__('Location:','ar').'</b>  &nbsp;'.$location;
		$o.='</div>';
	$o.='</li>';
	return $o;
}

 function abr_map_area($myposts){
	ob_start();
	$newarr	=	array();
	foreach($myposts as $apid){
		$apic    		=	get_post_meta($apid,'cf_apartment_pic',true);
		$mlatlng 		=	get_post_meta($apid,"martygeocoderlatlng",true);
		$agallery  	=	get_post_meta($apid,'cf_apartment_gallery',true);
		$mAd	 		=	get_post_meta($apid,"martygeocoderaddress",true);
		$arent   		=	get_post_meta($apid,'cf_apartment_rent_month',true);
		$bedRoomARR	=	wp_get_post_terms($apid,'apartment_bedrooms');
		$bathRoomARR 	=	wp_get_post_terms($apid,'apartment_bathrooms');
		$aptStyleTAX 	=	wp_get_post_terms($apid,'apartment_style');
		$apartment_area =	wp_get_post_terms($apid,'apartment_area');
		$post_perma   =   get_post_permalink($apid);
		foreach($bedRoomARR as $bedroom){ $bedroomname = $bedroom->name; }
		foreach($bathRoomARR as $bathroom){ $bathroomname = $bathroom->name;}
		foreach($apartment_area as $aArea){ $aAreaname = $aArea->name;}
		foreach($aptStyleTAX as $astyle){ $astylename = $astyle->name;}
		if(!empty($mlatlng)){
			$ll	=	Cf_Abr_Apartment::lat_long_explode($mlatlng);
			array_push($newarr,array($apic,$ll[0],$ll[1],$astylename,$aAreaname,$bathroomname,$bedroomname,$arent,$post_perma));
		}
	} ?>
	<script type="text/javascript">
	var homeimg='<?php echo ABR_PLUGIN_DIR_URL.'img/home2-16.png'; ?>',
	bedroom='<?php echo ABR_PLUGIN_DIR_URL.'img/bed39-16.png'; ?>',
	bathroom='<?php echo ABR_PLUGIN_DIR_URL.'img/sex-male-16.png'; ?>',
	sqfeet='<?php echo ABR_PLUGIN_DIR_URL.'img/sqfeet.png';  ?>';
	function initialize(){
		var markerImage='<?php echo ABR_PLUGIN_DIR_URL.'img/mapmarker.png'; ?>';
		var clusterMarkerImage='<?php echo ABR_PLUGIN_DIR_URL.'img/mapmaker.png'; ?>';
		var jsondata=JSON.parse('<?php  echo json_encode($newarr); ?>');
		var map=new google.maps.Map(document.getElementById('map'),{
			zoom:10,center:new google.maps.LatLng(39.170496,-86.5978595),
			mapTypeId:google.maps.MapTypeId.ROADMAP});
			var infowindow = new google.maps.InfoWindow();
			var marker,i,markers=[];
			for(i=0;i<jsondata.length;i++){
				marker=new google.maps.Marker({
					position: new google.maps.LatLng(jsondata[i][1],jsondata[i][2]),map: map,icon:markerImage});google.maps.event.addListener(marker,'click',(function(marker,i){
						return function(){
							var htm='<div class="info_content">';
							if(jsondata[i][0]!=""){
								htm+='<div class="image"><img class="mapimage" src="'+jsondata[i][0]+'"></div>'
							}
							htm+='<ul class="details">';
							if(jsondata[i][3]!=""){
								htm+='<li><img class="property-icon" src='+homeimg+'>&nbsp;'+jsondata[i][3]+'</li>'
							}
							if(jsondata[i][3]!=""){
								htm+='<li><img class="property-icon" src='+sqfeet+'>&nbsp;'+jsondata[i][4]+'Sq Ft</li>'
							}
							if(jsondata[i][5]!=""){
								htm+='<li><img class="property-icon" src='+bedroom+'>&nbsp;'+jsondata[i][5]+'</li>'
							}
							if(jsondata[i][4]!=""){
								htm+='<li><img class="property-icon" src='+bathroom+'>&nbsp;'+jsondata[i][6]+'</li>'
							}
							if(jsondata[i][5]!=""){
								htm+='<li>$'+jsondata[i][7]+'</li>'
							}
							htm+='<li><a class="btn-green pad_small pull-right" href="'+jsondata[i][8]+'">View details</a></li></ul></div>'
							infowindow.setContent(htm);
							infowindow.open(map,marker)}})(marker,i));
							markers.push(marker)
						};
						var markerCluster = new MarkerClusterer(map, markers, {imagePath: clusterMarkerImage});
		}google.maps.event.addDomListener(window,'load',initialize);
		</script>
	<div id="map" style="height:400px;width:100%"></div><?php
	$content=ob_get_contents();
	ob_end_clean();
	return $content;
}