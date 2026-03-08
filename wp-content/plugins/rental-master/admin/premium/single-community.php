<?php
add_filter('the_content','abr_add_single_community_filter',99);

/**
*	Single Community Page 
*/
function abr_add_single_community_filter($content){
	$CF_AR_Community	= new CF_AR_Community();
	global $post;
	if($post->post_type=='community'&&!is_page()){
		$cmid				=	$post->ID;
		$cmPic				=	get_post_meta($cmid,'cf_community_pic',true);
		$map				=	get_post_meta($cmid,'cf_community_map',true);
		$cmCity			=	get_post_meta($cmid,'cf_community_city',true);
		$cmState			=	get_post_meta($cmid,'cf_community_state',true);
		$vrIDs				=	get_post_meta($cmid,'cf_community_vrids',true);
		$cmgallery		=	get_post_meta($cmid,'cf_community_gallery',true);
		$cmAdress			=	get_post_meta($cmid,'cf_community_address',true);
		$cmZip				=	get_post_meta($cmid,'cf_community_zip_code',true);
		$desc				=	get_post_meta($cmid,'cf_community_description',true);
		$video				=	get_post_meta($cmid,'cf_community_youtube_video',true);
		$apid 				=	About_rental_cf_exe::get_id_by_slug('apartment-application');
		$alistid 			=	About_rental_cf_exe::get_id_by_slug('community-apartments');
		$emailfavid		=	About_rental_cf_exe::get_id_by_slug('email-favorites');
		$comshortcodetid =	About_rental_cf_exe::get_id_by_slug('community-shortcode');
		$cmAmenitieTAX	=	wp_get_post_terms($cmid,'community_features');
		$apartmentsARR	=	$CF_AR_Community->abr_get_apartments($cmid);
		ob_start(); ?>
		<div class="cmSecA">
			<?php
				if($vrIDs){
					echo '<div id="vrCont" class="vrcont">';
					for($i=0;$i<count($vrIDs);$i++){
						$vrID		=	$vrIDs[$i];
						$zipURL	=	get_post_meta($vrID,'podcast_file',true);
						$zipID		=	AR_Virtual_Tours::abr_get_image_id($zipURL);
						$uploads 	=	wp_upload_dir();
						$zipName	=	basename($zipURL);
						$htmName	=	str_replace(".zip",".html",basename($zipURL));
						$zipDIR 	= 	preg_replace('/\\.[^.\\s]{3,4}$/', '',$zipName);
						$zDIRUR	=	preg_replace('/\\.[^.\\s]{3,4}$/', '',$zipURL);
						if($zDIRUR){
							echo '<iframe id="frameVR" src="'.$zDIRUR.'/_html5/'.$htmName.'" width="400" height="400">
							</iframe>';
						}
					}
					echo '</div>';
				}  ?>
		  <?php
			if($cmPic){
				echo '<div class="gallery">';
					echo '<div>';
					echo '<a href="'.$cmPic.'" rel="darkbox">';
						echo '<img class="alignleft size-medium" src="'.$cmPic.'">';
					echo '</a>';
					echo '</div>';
				echo '</div>';
			}
			?>
			<div class="amnt-and-favlink"><?php
					echo '<div="cmtDesc">'.__($desc,'ar').'</div><div  style="clear:both;"></div>
				<hr>';
					echo '<div class="cmtAmenties">';
						echo '<h4>Community Amenities:</h4>';
						echo'<ul>';
						if($cmAmenitieTAX){
							foreach($cmAmenitieTAX as $amenit){
								echo '<li>'.__($amenit->name,'ar').'</li>';
							}
						}
						else{
							echo 'NA';
						}
						echo '</ul>';
					echo '</div>';
				
				?>
				<div class="favlinks">
					<ul class="arsCtrl">
					<?php
						$url	=	get_option('abr_applicationFormInput', false);
						$url?$url : $url='/applications/';
						echo '<li><a href="'.$url.'" class="arBtn">'.__('Apply Now','ar').'</a></li>';
						
						echo '<li><a href="'.get_permalink($alistid).'?cmid='.$cmid.'" class="arBtn">'.__('View Apartments','ar').'</a></li>';
						
						echo '<li><a href="'.get_permalink($emailfavid).'">'.__('Email My Favorites','ar').'</a></li>';
						
						echo '<li><a href="javascript:void(0)" onclick="cf_print_page()">'.__('Print this page','ar').'</a></li>';
						
						echo'<li class="addfav_remofav">';
						if(CF_AR_Favorites::abr_is_in_favorites($post->ID) == false){
							echo'<a id="'.$post->ID.'" href="javascript:void(0)" class="cmFVT" status="1" >'.__('Add To Favorites','ar').'</a>';
						}
						else{
							echo'<a id="'.$post->ID.'" href="javascript:void(0)" class="cmFVT" status="0" >'.__(' Remove From Favorites','ar').'</a>';
						}
					echo'</li>';
					?>
					</ul>
				</div>				
				<div  style="clear:both;"></div><hr><?php
				if(!empty($cmAdress)){
					echo '<h4>'.__('LOCATION:','ar').'</h4>';
					echo __($cmAdress,'ar').'<br>'.__($cmCity,'ar').',<br>'.__($cmState,'ar').','.__($cmZip,'ar').'<br>';
				}
				
				echo '<div class="cmtyMap">'.$map.'</div>';
				?>
				 <div  style="clear:both;"></div>
				<hr>
				<?php
				if(!empty($cmgallery)){
					echo '<div class="flexslider carousel ">';
						echo '<ul class="slides ">';
					foreach($cmgallery as $cmgal){
						echo '<li>';							
								echo '<img class="alignleft size-medium" width="165" src="'.$cmgal.'"  data-darkbox="'.$cmgal.'" data-darkbox-group="one">';
						echo '</li>';
						}
						echo '</ul>';
						echo '</div>';
					}
			?>
			<div  style="clear:both;"></div>
			 <hr>
			<div class="cmtApartment"><?php
				echo'<h3>'.__('Apartments','ar').'</h3>';
					echo '<ul>';
					if(!empty($apartmentsARR)){
						for($i=0;$i<count($apartmentsARR);$i++){
							$apartmentID 	=	$apartmentsARR[$i];
							$src	=	get_post_meta($apartmentID,'cf_apartment_pic',true);
							echo '<li>';
							echo '<span class="cmAptsA">';
							echo '<a href="'.get_permalink($apartmentID).'">';
							echo '<img src="'.$src.'">';
							echo '<small>'.get_the_title($apartmentID).'</small></a></span>';
							echo '</li>';
						}
					}
					else{
						echo 'NA';
					}
					echo '</ul>';
				?>
			</div>
			 <div  style="clear:both;"></div>
			 <hr>
			<div class="aptVideo">
			<?php echo abr_display_youtube_video($video);?>
			</div>
        </div><!--	cmSecA	Ends-->
		
         <script type="text/javascript">
			jQuery(document).ready(function(e){(function($){$('a[rel=darkbox]').darkbox();}(jQuery))});
			// jQuery(function(){SyntaxHighlighter.all(); });
			   jQuery(window).load(function(){
				 jQuery('.flexslider').flexslider({animation: "slide",animationLoop: false,itemWidth: 210,itemMargin: 5,
					pausePlay: true,prevText: "<", nextText: ">",
					start: function(slider){jQuery('body').removeClass('loading');}
				  });
				});
		</script><?php
		$content=ob_get_contents();
		ob_end_clean();
	}
	return $content;
}