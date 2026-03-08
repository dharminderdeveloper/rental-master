<?php
add_filter('the_content','abr_add_single_realestate_filter',99);

 function abr_add_single_realestate_filter($content){	 
	global $post;
	$reid			=	$post->ID;
	$rePic			=	get_post_meta($reid,'cf_realestate_photo',true);	
	$reBedroomTAX	=	wp_get_post_terms($reid,'listing_bedrooms');
	$reBathroomTAX	=	wp_get_post_terms($reid,'listing_bathrooms');
	$reLotSize		=	get_post_meta($reid,'cf_realestate_lot_size',true);		
	$rePrice		=	get_post_meta($reid,'cf_realestate_listing_price',true);	
	$regallery   	=	get_post_meta($reid,'cf_realestate_gallery',true);	
	$vrIDs			=	get_post_meta($reid,'cf_apartment_vrids',true);
	$mlatlng 		=	get_post_meta($reid,"martygeocoderlatlng",true);
	//$cmAdress		=	get_post_meta($cmid,'cf_community_address',true);
	$mAd	 		=	get_post_meta($reid,"martygeocoderaddress",true);
	$cmZip			=	get_post_meta($reid,'cf_community_zip_code',true);
	$aptFeatures	=	get_post_meta($reid,'cf_apartment_features',true);
	$video			=	get_post_meta($reid,'cf_realestate_video_url',true);
	$latlang		=	Cf_Abr_Apartment::lat_long_explode($mlatlng);

	//	Get All Listing Paragraphs
	$reHeating		= 	get_post_meta($reid,'cf_realestate_heating_cooling',true);	
	$listFeatures	= 	get_post_meta($reid,'cf_realestate_listing_features',true);
	$shInformation  = 	get_post_meta($reid,'cf_realestate_school_information',true);
	$ctInformation 	=	get_post_meta($reid,'cf_realestate_community_information',true);
	$utInformation 	=	get_post_meta($reid,'cf_realestate_utilities_information',true);

	if($reBedroomTAX){$re_bedroom=$reBedroomTAX[0]->name;}else{$re_bedroom= '';}
	if($reBathroomTAX){$re_bathroom=$reBathroomTAX[0]->name;}else{	$re_bathroom= '';}
	ob_start();
	
	if($post->post_type=='realestate'&&!is_page()){				
		if($vrIDs){
			echo abr_display_virtual_tour($vrIDs);
		} ?>
	<div class="rePic"><?php
		if($rePic){
			echo '<div class="gallery">';
			echo '<a href="'.$rePic.'" rel="darkbox">';
				echo '<img src="'.$rePic.'">';
			echo '</a>';
			echo '</div>';
		 }
		 ?>
	</div>

	<div class="aptSect1">		
		<div class="quick_info_section">
<?php 
			//echo'<h4>'.__('Quick Info:','ar').'</h4>';
?>	
			<!--
			<div class="quick_info_item">
				<div class="cf_info_image">
					<span class="cf_item_icon">ssss</span>
				</div>
				<div class="cf_info_desc">ss</div>
			</div>	
			<div class="quick_info_item"></div>	
			<div class="quick_info_item"></div>	
			<div class="quick_info_item"></div>	
			<div class="quick_info_item"></div>	
			<div class="quick_info_item"></div>	
			-->
		</div>			

		<div class="aptSecA">
			<?php
			echo'<h4>'.__('Listing Information:','ar').'</h4>';

			$re_bedroom ? $re_bedroom : $re_bedroom='NA';
			echo'<b>'.__('Available Bed Room:','ar').'</b> &nbsp; '. __($re_bedroom,'ar').'<br>';
			
			$re_bathroom ? $re_bathroom : $re_bathroom='NA';
			echo'<b>'.__('Available Bath Room:','ar').'</b> &nbsp;'. __( $re_bathroom,'ar').'<br>';

			$reLotSize ? $reLotSize : $reLotSize='NA';
			echo'<b>'.__('Listing Lot Size:','ar').'</b> &nbsp;'. __($reLotSize. 'sq ft','ar').'<br>';						

			$rePrice ? $rePrice : $rePrice='NA';
			echo'<b>'.__('Listing Price:','ar').'</b> &nbsp;$'. __( $rePrice,'ar').'<br>';
			?>
		</div> <!--aptSecA Ends-->
	</div> <!--aptSect1 Ends-->
	<div  style="clear:both;"></div>
	<hr>
	<div class="listing-featues">
		<h4><?php echo __('Listing Features:','ar');?></h4>
		<?php
			if(!empty($listFeatures)){
				echo __($listFeatures,'ar');
			}
			else{
				echo __('NA','ar');
			}
		?>
	</div>
	<div style="clear:both;"></div>
	<hr> 
	<?php
		if(!empty($regallery)){
			echo abr_apartment_gallery($regallery);
		}
	?>

	<div class="apLoc">	
	<?php
		$mapLocation 	=	get_post_meta($reid,'cf_realestate_location',true);	
		//	echo '<pre>'; print_r( $mapLocation ); exit; 

		if($mapLocation){	
			echo '<div class="cmb-row table-layout" data-fieldtype="text"><div style="width: 100%" id=""><iframe width="100%" height="400" src="https://maps.google.com/maps?width=100%&height=400&hl=en&q='.$mapLocation.'&ie=UTF8&t=&z=14&iwloc=B&output=embed" frameborder="0" scrolling="no" marginheight="0" marginwidth="0"><a href="https://www.mapsdirections.info/en/custom-google-maps/">Create Google Map</a> by <a href="https://www.mapsdirections.info/en/">Measure area on map</a></iframe></div><br /></div>';		
		}
	?>
	</div>	

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
		<hr>
		<div class="community-information">
			<h4><?php echo __('Listing Community Information:','ar');?></h4>
			<?php
			if(!empty($ctInformation)){
				echo __($ctInformation,'ar');
			}
			else{
				echo __('NA','ar');
			}
			?>
			</ul>			
	    </div>
		<div  style="clear:both;"></div>
		<hr>
		<div class="school-information">
			<h4><?php echo __('Listing School Information:','ar');?></h4>
			<?php
			if(!empty($shInformation)){
				echo __($shInformation,'ar');
			}
			else{
				echo __('NA','ar');
			}
			?>
	    </div>
	    <div style="clear:both;"></div>
		<hr> 
		<div class="utilities-information">
			<h4><?php echo __('Listing Utilities Information:','ar');?></h4>
			<?php
			if(!empty($utInformation)){
				echo __($utInformation,'ar');
			}
			else{
				echo __('NA','ar');
			}
			?>
	   	</div>
	    <div style="clear:both;"></div>
		<hr> 	
	    <div class="heating-cooling">
			<h4><?php echo __('Heating and Cooling:','ar');?></h4>
			<?php
			if(!empty($reHeating)){
				echo __($reHeating,'ar');
			}
			else{
				echo __('NA','ar');
			}
			?>
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
*	Function:	abr_realestate_gallery
*
*	@param:		$apgallery
*	@return:	HTML
*/
function abr_realestate_gallery($apgallery){
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