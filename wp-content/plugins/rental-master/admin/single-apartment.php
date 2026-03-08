<?php
add_filter('the_content','abr_add_single_apartment_filter',99);

 function abr_add_single_apartment_filter($content){
	global $post;
	$apid			=	$post->ID;
	$aptAreaTAX		=	wp_get_post_terms($apid,'apartment_area');
	$aptStyleTAX	=	wp_get_post_terms($apid,'apartment_style');	
	$aptLeasingTAX	=	wp_get_post_terms($apid,'apartment_leasing');
	$aptBedroomTAX	=	wp_get_post_terms($apid,'apartment_bedrooms');
	$aptBathroomTAX =	wp_get_post_terms($apid,'apartment_bathrooms');	
	$aptAmenitieTAX =	wp_get_post_terms($apid,'apartment_amenities');
	$aptRentOcupTAX =	wp_get_post_terms($apid,'apartment_rental_occupancy');
	$aptAvltyOptTAX =	wp_get_post_terms($apid,'apartment_availability_options');	
	$apgallery   	=	get_post_meta($apid,'cf_apartment_gallery',true);
	$cmid			=	get_post_meta($apid,'cf_apartment_community',true);
	$apt_rentMnth	=	get_post_meta($apid,'cf_apartment_rent_month',true);
	$apUT			=	get_post_meta($apid,'cf_apartment_utilities_tenant',true);
	$apUL			=	get_post_meta($apid,'cf_apartment_utilities_landlord',true);		
	$map			=	get_post_meta($cmid,'cf_community_map',true);
	$aptPic			=	get_post_meta($apid,'cf_apartment_pic',true);
	$cmCity			=	get_post_meta($cmid,'cf_community_city',true);
	$cmState		=	get_post_meta($cmid,'cf_community_state',true);
	$vrIDs			=	get_post_meta($apid,'cf_apartment_vrids',true);
	$mlatlng 		=	get_post_meta($apid,"martygeocoderlatlng",true);
	$cmAdress		=	get_post_meta($cmid,'cf_community_address',true);
	$mAd	 		=	get_post_meta($apid,"martygeocoderaddress",true);
	$cmZip			=	get_post_meta($cmid,'cf_community_zip_code',true);
	$aptFeatures	=	get_post_meta($apid,'cf_apartment_features',true);
	$video			=	get_post_meta($apid,'cf_apartment_youtube_video',true);
	$latlang		=	Cf_Abr_Apartment::lat_long_explode($mlatlng);
	$emailfavid 	=	About_rental_cf_exe::get_id_by_slug('email-favorites');
	if($aptStyleTAX){$apt_style=$aptStyleTAX[0]->name;}else{$apt_style = '';}
	if($aptAreaTAX){$apt_area	=$aptAreaTAX[0]->name;}else{$apt_area =	'';}
	if($aptBedroomTAX){$apt_bedroom=$aptBedroomTAX[0]->name;}else{$apt_bedroom= '';}
	if($aptBathroomTAX){$apt_bathroom=$aptBathroomTAX[0]->name;}else{	$apt_bathroom= '';}
	if($aptRentOcupTAX){$apt_rentalOc=$aptRentOcupTAX[0]->name;}else{	$apt_rentalOc= '';}
	if($aptAvltyOptTAX){$apt_avlty_opt=$aptAvltyOptTAX[0]->name;}else{$apt_avlty_opt= '';}
	ob_start();
	if($post->post_type=='apartment'&&!is_page()){
		?>
		<style type="text/css">
			.post-navigation{
				display:none !important;
			}
		</style>
		<?php
		if($mlatlng){
			?>
			<script type= "text/javascript">
			var homeimg = '<?php echo ABR_PLUGIN_DIR_URL.'img/home2-16.png'; ?>';
			var bedroom = '<?php echo ABR_PLUGIN_DIR_URL.'img/bed39-16.png'; ?>';
			var bathroom= '<?php echo ABR_PLUGIN_DIR_URL.'img/sex-male-16.png'; ?>';
			var sqfeet  = '<?php echo ABR_PLUGIN_DIR_URL.'img/sqfeet.png'; ?>';
			function asingleinitialize(){
				var markerImage	=	'<?php echo ABR_PLUGIN_DIR_URL.'img/mapmarker.png'; ?>';
				var myCenter		=	new google.maps.LatLng(<?php echo $latlang[0]; ?>,<?php echo $latlang[1]; ?>);
				var mapProp 		=	{center:myCenter,zoom:12,mapTypeId:google.maps.MapTypeId.ROADMAP};
				var map		=	new google.maps.Map(document.getElementById("googleMap"),mapProp);
				var marker	=	new google.maps.Marker({position:myCenter,icon:markerImage,});
				var infowindow=	new google.maps.InfoWindow();
				marker.setMap(map);
				google.maps.event.addListener(marker, 'click',function(){
					var htm = '<div class="info_content">';
					if('<?php echo $aptPic; ?>' != ""){
						htm+='<div class="image"><img class="mapimage" src="'+'<?php echo $aptPic;?>'+'"></div>';
					}
					htm+='<ul class="details">';
					if('<?php echo $apt_style; ?>' !=""){
						htm+='<li><img class="property-icon" src='+homeimg+'>&nbsp;<?php echo $apt_style; ?>'+'</li>'
					}
					if('<?php echo $apt_area; ?>'!=""){
						htm+='<li><img class="property-icon" src='+sqfeet+'>&nbsp;<?php echo $apt_area; ?>'+'Sq Ft</li>'
					}
					if('<?php echo $apt_bedroom; ?>' != ""){
						htm+='<li><img class="property-icon" src='+bedroom+'>&nbsp;<?php echo $apt_bedroom; ?>'+'</li>'
					}
					if('<?php echo $apt_bathroom; ?>' != ""){
						htm+='<li><img class="property-icon" src='+bathroom+'>&nbsp;<?php echo $apt_bathroom; ?>'+'</li>'
					}
					if('<?php echo $apt_rentMnth; ?>' != ""){
						htm+='<li>$'+'<?php echo $apt_rentMnth; ?>'+'</li>'
					}
					htm+='<li><a class="btn-green pad_small pull-right" href="">View details</a></li></ul></div>';
					infowindow.setContent(htm);infowindow.open(map,marker);
				});
			}
			google.maps.event.addDomListener(window,'load',asingleinitialize);			
			</script><?php
		}
		if($vrIDs){
			echo abr_display_virtual_tour($vrIDs);
		} ?>
	<div class="aptSect1">
	
		<div class="aptSecA">
			<?php
			echo'<h4>'.__('UNIT INFORMATION:','ar').'</h4><b>';
			
			$apt_style ? $apt_style : $apt_style='NA';
			echo __('Type:','ar').'</b> &nbsp;'.__( $apt_style,'ar').'<br>';
			
			$apt_area ? $apt_area : $apt_area='NA';
			echo'<b>'.__('Total Area:','ar').'</b> &nbsp;'. __($apt_area. 'sq ft','ar').'<br>';
			
			$apt_bedroom ? $apt_bedroom : $apt_bedroom='NA';
			echo'<b>'.__('Available Bed Room:','ar').'</b>&nbsp; '. __($apt_bedroom,'ar').'<br>';
			
			$apt_bathroom ? $apt_bathroom : $apt_bathroom='NA';
			echo'<b>'.__('Available Bath Room:','ar').'</b> &nbsp;'. __( $apt_bathroom,'ar').'<br>';
			
			$apt_rentalOc ? $apt_rentalOc : $apt_rentalOc='NA';
			echo'<b>'.__('Max # tenant:','ar').'</b> &nbsp;'. __( $apt_rentalOc,'ar').'<br>';
			
			$apt_rentMnth ? $apt_rentMnth : $apt_rentMnth='NA';
			echo'<b>'.__('Rent Starting At:','ar').'</b> &nbsp;$'. __( $apt_rentMnth.'/ month','ar');
			?>
		</div> <!--aptSecA Ends-->
		
		<div class="aptSecB">
		<?php
			echo '<h4>'.__('LOCATION:','ar').'</h4>';
			if($cmAdress){
				echo __($cmAdress,'ar').'<br>'.__($cmCity,'ar').', '.__($cmState,'ar').', '.__($cmZip,'ar');
			}
			else{
				echo __('NA','ar');
			}
			 ?> 
		</div>
	</div> <!--aptSect1 Ends-->
	 <div  style="clear:both;"></div>
	 <hr>	
	<div class="aptPic"><?php
		if(empty($apgallery)){ 
			$fullWidth="style='width:100% !important;'"; 
		}else{
			$fullWidth="";
		}
		
		if($aptPic){
			echo '<div class="abr-gallery" '.$fullWidth.'>';
			echo '<a href="'.$aptPic.'" rel="darkbox">';
				echo '<img src="'.$aptPic.'">';
			echo '</a>';
			echo '</div>';
		}
		?>
		<div class="abr-photo-section">
		<?php if(!empty($apgallery)){ echo '<ul class="abr-photo-section-block">'; ?>
		
		<?php	foreach($apgallery as $apgal){
					echo '<li class="abr-photo-image"><img class="alignleft size-medium" width="" src="'.$apgal.'"  data-darkbox="'.$apgal.'" data-darkbox-group="one"></li>';
				}
			echo '<ul>'; 
			} 
		?>
		</div>
		<div  style="clear:both;"></div>
		<div class="abrLinks">
		<?php		
			$url	=	get_option('abr_applicationFormInput', false);
			$url?$url:$url='/applications/';			
		?>
		  <ul class="arsCtrl">
		  <?php
				echo '<li><a href="'.$url.'" class="arBtn">'.__('Apply Now','ar').'&nbsp;|&nbsp;</a></li>';
				echo '<li><a href="'.get_permalink($emailfavid).'" >'.__('Email My Favorites','ar').'&nbsp;|&nbsp;</a></li>';
				echo '<li><a href="javascript:void(0)" class="viewMap" id="'.$cmid.'">'.__(' Click to view map','ar').'&nbsp;|&nbsp;</a></li>';
				echo '<li><a href="javascript:void(0)"  onclick="cf_print_page()">'.__('Print this page','ar').'&nbsp;|&nbsp;</a></li>';
				echo '<li class="addfav_remofav">';
				if(CF_AR_Favorites::abr_is_in_favorites($post->ID) == false){
					echo'<a id="'.$post->ID.'" href="javascript:void(0)" class="apFVT" status="1" >'.__('Add To Favorites','ar').'</a>';
				}
				else{
					echo'<a id="'.$post->ID.'" href="javascript:void(0)" class="apFVT" status="0" >'.__(' Remove From Favorites','ar').'</a>';
				}
				echo'</li>';
			?>
			</ul>
			<?php 
		?>
		</div>
		<div  style="clear:both;"></div>
		<hr>
	</div>
	
	<?php
		/*
		if(!empty($apgallery)){
			echo abr_apartment_gallery($apgallery);
		}
		*/
	?>
	<div class="apLoc">
	<?php
		/*
		echo '<h4>'.__('LOCATION:','ar').'</h4>';
		if($cmAdress){
			echo __($cmAdress,'ar').'<br>'.__($cmCity,'ar').', '.__($cmState,'ar').', '.__($cmZip,'ar');
		}
		else{
			echo __('NA','ar');
		}
		*/
	?>
	</div>
	
	<div class="apMap"><?php  echo $map;?><hr></div>
	<input type="hidden" value="<?php echo get_the_title($cmid);?>" id="community_name" />
	<input type="hidden" value='<?php echo $map; ?>' id="community_map" />
	<script type="text/javascript">
		jQuery(document).ready(function(e){(function($){
			//$('a[rel=darkbox]').darkbox();
		}(jQuery))});
		 jQuery(function(){
			// SyntaxHighlighter.all(); 
			 });
		   jQuery(window).load(function(){
			 jQuery('.flexslider').flexslider({animation: "slide",animationLoop: false,itemWidth: 210,itemMargin: 5,
				pausePlay: true,prevText: "<", nextText: ">",
				start: function(slider){jQuery('body').removeClass('loading');}
			  });
			 });
	</script>
	
	<div class="aptSect2">
		<div>
			<h4><?php echo __('AVAILABILITY:','ar');?></h4>
			<?php
			if(!empty($apt_avlty_opt)){
				echo __($apt_avlty_opt,'ar').'<hr>';
			}
			else{echo __('NA','ar').'<hr>';}
			?>
		</div>
		 <div  style="clear:both;"></div>
		<div class="apt-features">
			<h4><?php echo __('APARTMENT FEATURES:','ar');?></h4>
			<?php
			if(!empty($aptFeatures)){
				echo __($aptFeatures,'ar').'<hr>';
			}
			else{
				echo __('NA','ar').'<hr>';
			}
			?>
		</div>
		 <div  style="clear:both;"></div>
	   <div class="amenities">
			<h4><?php echo __('AMENITIES','ar');?></h4>
			<ul>
			<?php
				if(!empty($aptAmenitieTAX)){
					foreach($aptAmenitieTAX as $amenit){
						echo '<li>'.__($amenit->name,'ar').'</li>';
					}
				}
				else{
					echo '<li>'.__('NA','ar').'</li>';
				}
			?>
			</ul>
	   </div>
	   <div  style="clear:both;"></div>
	   <hr>
	     <div class="leasing">
			<h4><?php echo __('LEASING','ar');?></h4>
			<ul>
			<?php
				if(!empty($aptLeasingTAX)){
					foreach($aptLeasingTAX as $leasing){
						echo '<li>'.__($leasing->name,'ar').'</li>';
					}
				}
				else{
					echo '<li>'.__('NA','ar').'</li>';
				}
			?>
			</ul>
	   </div>
		 <div  style="clear:both;"></div>
		  <hr>
	   <div class="utilities-by-landloard">
			<h4><?php echo __('UTILITIES PAID BY LANDLORD:','ar');?></h4>
			<ul>
			<?php
				if(!empty($apUL)){
					foreach($apUL as $apULTID){
						echo '<li>'.__(About_rental_cf_exe::get_term_name($apULTID),'ar').'</li>';
					}
				}
				else{
					echo '<li>'.__('NA','ar').'</li>';
				}
			?>
			</ul>			
	   </div>
		 <div  style="clear:both;"></div>
		  <hr>
	   <div class="utilities-by-tenant">
			<h4><?php echo __('UTILITIES PAID BY TENANT:','ar');?></h4>
			<ul>
			<?php
			if(!empty($apUT)){
				foreach($apUT as $apUTTID){
					echo '<li>'.__(About_rental_cf_exe::get_term_name($apUTTID),'ar').'</li>';
				}
			}
			else{
				echo '<li>'.__('NA','ar').'</li>';
			}
			?>
			</ul>
	   </div>
	    <div style="clear:both;"></div>
		<hr>
		<div class="aptVideo">
			<?php echo abr_display_youtube_video($video);?>
		</div>
		
	</div>
	<?php
		$content=ob_get_contents();
		ob_end_clean();
	}
	return $content;
}

/**
*	Function:	abr_display_youtube_video
*
*	@param:		$video
*	@return:	HTML
*/
function abr_display_youtube_video($video){
	$str = trim($video);
	if(!empty($str)){
		preg_match('~(?:https?://)?(?:www.)?(?:youtube.com|youtu.be)/(?:watch\?v=)?([^\s]+)~', $str, $match);
		return '<iframe width="650" height="450" src="https://www.youtube.com/embed/'.$match[1].'" frameborder="0" allowfullscreen></iframe>';
	 }
}

/**
*	Function:	abr_apartment_gallery
*
*	@param:		$apgallery
*	@return:	HTML
*/
function abr_apartment_gallery($apgallery){
	echo '<div class="flexslider carousel">';
		echo '<ul class="slides ">';
			foreach($apgallery as $src){
				echo '<li>';					
						echo '<img class="alignleft size-medium" width="165" src="'.$src.'"  data-darkbox="'.$src.'" data-darkbox-group="one">';					
				echo '</li>';
			}
	echo '</ul>';
	echo '</div>';
}

/**
*	Function:	abr_display_virtual_tour
*
*	@param:		$vrIDs
*	@return:	HTML
*/
function abr_display_virtual_tour($vrIDs){
	echo'<div id="vrCont" class="vrcont">';
	foreach($vrIDs as $vrID ){
		$zipURL		=	trim(get_post_meta($vrID,'podcast_file',true));
		$zipID		=	AR_Virtual_Tours::abr_get_image_id($zipURL);
		$uploads 	=	wp_upload_dir();
		$zipName	=	basename($zipURL);
		$htmName	=	str_replace(".zip",".html",basename($zipURL));
		$zipDIR 	= 	preg_replace('/\\.[^.\\s]{3,4}$/', '',$zipName);
		$zDIRUR		=	preg_replace('/\\.[^.\\s]{3,4}$/', '',$zipURL);
		echo '<iframe id="frameVR" src="'.$zDIRUR.'/_html5/'.$htmName.'" width="400" height="400"></iframe>';
	}
	echo '</div>';
}