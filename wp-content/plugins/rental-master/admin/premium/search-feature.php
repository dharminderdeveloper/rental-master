<?php
add_shortcode('ar_search_listing','abr_search_listing');
/**
*	abr_search_listing
*
*	# Search Listing shortcode function
*/
function abr_search_listing(){
	global $post,$wpdb;

	$args 	=	array();
	//	Get all search parameters
	$search_string  = '';
	$selected_styles  	= '';
	$selected_styles 	= '';
	$selected_locations	= '';
	$selected_bathrooms = '';
	$abr_price_range  	= '';
	$order_by_value	=	'';
	
	if( isset( $post->guid ) ){
		$form_action =	$post->guid; 
	}else{
		$form_action =	''; 
	}	
	//MIN(pm.meta_value) as minimum,MAX(pm.meta_value) as maximum
	/*
	$priceLimitArray = $wpdb->get_results('SELECT pm.meta_value FROM '.$wpdb->prefix.'postmeta AS pm WHERE pm.meta_key="cf_apartment_rent_month" AND pm.meta_value>="1000" ORDER BY pm.meta_value DESC');
	echo '<pre>'; print_r( $priceLimitArray );echo '</pre>';exit; 
	*/

	$priceLimitArray = $wpdb->get_results('SELECT GROUP_CONCAT(pm.meta_value) as minimum FROM '.$wpdb->prefix.'postmeta AS pm WHERE pm.meta_key="cf_apartment_rent_month"');

	if( !empty( $priceLimitArray )){		
		$priceArray 	=	explode(',',$priceLimitArray[0]->minimum);
		//	echo '<pre>'; print_r( $priceArray );echo '</pre>';exit; 
		$priceMin 	=	min( $priceArray );
		$priceMax 	=	max( $priceArray );				
	}else{
		$priceMin 	=	1000;
		$priceMax 	=	5000;		
	}

	$priceStartSlot  = 	$priceMin;	
	$priceEndSlot    = 	$priceMin+100;	
	$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;

	if(isset($_REQUEST[ 'keyword' ])){
		//	echo '<pre>'; print_r( $_POST );echo '</pre>';exit;
		//	Get all search parameters
		$search_string  = (isset( $_REQUEST['keyword']))?$_REQUEST['keyword']:'';
		$selected_bedrooms  = (isset( $_REQUEST['bedrooms']))?$_REQUEST['bedrooms']:'';
		$selected_styles	= (isset( $_REQUEST['style']))?$_REQUEST['style']:'';
		$selected_locations  = (isset( $_REQUEST['location']))?$_REQUEST['location']:'';
		$selected_bathrooms  = (isset( $_REQUEST['bathrooms']))?$_REQUEST['bathrooms']:'';
		$order_by_value	=	(isset( $_REQUEST['order_by_value']))?$_REQUEST[ 'order_by_value']:'';	
		$abr_price_range = (isset( $_REQUEST['abr_price_range']))?$_REQUEST[ 'abr_price_range']:'';			
		$metaQueryArray[ 'relation' ] 	=	'AND';
		$taxQueryArray[ 'relation' ] 	=	'AND';

		if( !empty( $abr_price_range )){
			$priceRangeArray 	= explode( ',', $abr_price_range );
			$priceStartSlot		=	$priceRangeArray[0];
			$priceEndSlot	=	$priceRangeArray[1];
		}
		
		$paginationPerPage =	get_option('abr_searchPaginationNumber', false);
		
		if( empty($paginationPerPage ) ){
			$paginationPerPage = '-1';
		}
		
		$args	=	array(
						'posts_per_page'=> $paginationPerPage,
						'post_type'=> 'apartment',
						'paged' => $paged,
						's' => $search_string
					);
					
		if( !empty( $order_by_value )){
			$orderArray =	explode( '_',$order_by_value );	
			
			if( $orderArray[0] == 'date'){
				$args['orderby'] = 'date';
			}else if( $orderArray[0] == 'price'){
				$args['orderby'] = 'cf_apartment_rent_month';
			}else{				
				$args['orderby'] = 'post_title';
			}			
			
			$args['order']   =  $orderArray[1];
			
			//	echo '<pre>'; print_r( $args ); exit;
		}
		
		if( !empty( $selected_styles )){
			$style_tax =	build_tax_array( 'apartment_style',$selected_styles );
			$taxQueryArray[] = $style_tax;
		}else{
			$taxQueryArray[] =	'';
		}

		if( !empty( $selected_bedrooms )){
			$bedroom_tax =	build_tax_array( 'apartment_bedrooms',$selected_bedrooms);
			$taxQueryArray[] = $bedroom_tax;
		}else{
			$taxQueryArray[] =	'';
		}
		
		if( !empty( $selected_bathrooms )){
			$bathroom_tax =	build_tax_array( 'apartment_bathrooms',$selected_bathrooms);
			$taxQueryArray[] = $bathroom_tax;
		}else{
			$taxQueryArray[] =	'';
		}	
						
		if( !empty( $priceRangeArray )){	
			$price_min_tax =	array( 
									'key'     => 'cf_apartment_rent_month', 
									'value'   => (int)$priceRangeArray[0],
									'compare' => '>=',
									'type'          => 'NUMERIC'
								);
			$metaQueryArray[] = $price_min_tax;
															
			$price_max_tax =	array( 
									'key'     => 'cf_apartment_rent_month', 
									'value'   => (int)$priceRangeArray[1],
									'compare' => '<=',
									'type'          => 'NUMERIC'
								);		
								
			$metaQueryArray[] = $price_max_tax;										
		}else{
			$price_min_tax =	'';
			$price_max_tax =	'';
		}
		
		$args['tax_query']	=	$taxQueryArray;
		$args['meta_query']	=	$metaQueryArray;
								/*
								array(
								'relation' => 'AND',$style_tax,$bedroom_tax,$price_min_tax,$price_max_tax
								);
								*/
	}
				
	$apartment_style = '';
	$apartment_bedrooms = '';
	
	//	Get All Bedrooms
	$all_bedrooms	=	get_terms( array(
							'taxonomy' => 'apartment_bedrooms',
							'hide_empty' => false,
						));
						
	//	Get Apartment Styles
	$all_styles		=	get_terms( array(
							'taxonomy' => 'apartment_style',
							'hide_empty' => false,
						));	
	//	Get Community Location
	$community_locations	=	get_terms( array(
									'taxonomy' => 'community_location',
									'hide_empty' => false,
								));
								
	//	Get All	Bathrooms
	$all_bathrooms	=	get_terms( array(
							'taxonomy' => 'apartment_bathrooms',
							'hide_empty' => false,
						));
?>
	<link rel="stylesheet" href="<?php echo plugin_dir_url( __FILE__ );  ?>css/font-awesome.min.css" data-minify="1">
	<link rel="stylesheet" href="<?php echo plugin_dir_url( __FILE__ );  ?>css/abr-search-style.css" data-minify="1">
	<link rel="stylesheet" href="<?php echo plugin_dir_url( __FILE__ );  ?>css/bootstrap.css" data-minify="1">
	<link rel="stylesheet" href="<?php echo plugin_dir_url( __FILE__ );  ?>css/bootstrap-select.css" data-minify="1">
	<link rel="stylesheet" href="<?php echo plugin_dir_url( __FILE__ );  ?>css/bootstrap-slider.css" data-minify="1">
	<style type="text/css">
		.dropdown-toggle:after{
			border-left-width:0px !important;
			content:none !important;
		}
		.dropdown-toggle{
			display:block !important;
		}
	</style>
	<div id="top" class="abs-wrapper">
		<div class="abs-container">
			<div id="home-search" class="site-section home-section">
				<div class="container" style="background-color:#444 !important;">
					<form id="search_tool_form" name="search_tool_form" style="padding:0px 10px 0px 10px !important;" method="POST" action="<?php echo $form_action; ?>" class="abs-listings-search horizontal">
						<input type="hidden" id="order_by_value" name="order_by_value" value="<?php echo $order_by_value; ?>" />
						<input type="hidden" name="is_form_submitted" id="is_form_submitted" value="<?php echo time(); ?>" />
						<div class="listings-search-default">
							<div class="row gutter-30">
								<div class="listings-search-field listings-search-field-text listings-search-field-keyword col-xs-12 col-sm-9"> 
									<input class="listing-search-keyword text form-control" title="Keyword or Listing ID" name="keyword" id="keyword" type="text" value="<?php echo $search_string; ?>" placeholder="Please enter keyword">
								</div>
								
								<div class="listings-search-field listings-search-field-select listings-search-field-details_1 col-xs-12 col-sm-3">
									<select id="abs-listing-bedrooms" class="select selectpicker form-control" name="bedrooms" tabindex="-98">
										<option value="">Bedrooms</option>
									<?php
										foreach( $all_bedrooms as $bedrooms ){
											if( $bedrooms->slug == $selected_bedrooms ){
												echo '<option value="'.$bedrooms->slug.'" data-default="false" selected="selected">'.$bedrooms->name.'</option>';
											}else{
												echo '<option value="'.$bedrooms->slug.'" data-default="false">'.$bedrooms->name.'</option>';	
											}											
										}
									?>
									</select>						
								</div>
								
								<div class="listings-search-field listings-search-field-select listings-search-field-offer col-xs-12 col-sm-3">
									<select id="abs-listing-style" class="select selectpicker form-control" name="style" tabindex="-98">
										<option value="">Style</option>
									<?php
										foreach( $all_styles as $style ){
											if( $style->slug == $selected_styles ){
												echo '<option selected="selected" value="'.$style->slug.'" data-default="false">'.$style->name.'</option>';
											}else{
												echo '<option value="'.$style->slug.'" data-default="false">'.$style->name.'</option>';
											}
										}
									?>		
									</select>						
								</div>

								<div class="listings-search-field listings-search-field-taxonomy_select listings-search-field-location col-xs-12 col-sm-3">
									<select name="location" id="abs-listing-location" class="listing-search-location select selectpicker form-control" tabindex="-98">
										<option value="" selected="selected">Location</option>
									<?php
										foreach( $community_locations as $location ){
											if( $location->slug == $selected_locations ){
												echo '<option value="'.$location->slug.'" data-default="false" selected="selected">'.$location->name.'</option>';
											}else{
												echo '<option value="'.$location->slug.'" data-default="false">'.$location->name.'</option>';
											}
										}
									?>		
									</select>
								</div>

								<div class="listings-search-field listings-search-field-select listings-search-field-details_2 col-xs-12 col-sm-3">
									<select id="abs-listing-bathrooms" class="select selectpicker form-control" name="bathrooms" tabindex="-98">
										<option value="">Bathrooms</option>
									<?php
										foreach( $all_bathrooms as $bathroom ){
											if( $bathroom->slug == $selected_bathrooms ){
												echo '<option selected="selected" value="'.$bathroom->slug.'" data-default="false">'.$bathroom->name.'</option>';
											}else{
												echo '<option value="'.$bathroom->slug.'" data-default="false">'.$bathroom->name.'</option>';
											}
										}
									?>	
									</select>
								</div>

								<div class="listings-search-field listings-search-field-taxonomy_select listings-search-field-location col-xs-12 col-sm-3">	
									<div style="float:left;width:13%;"><?php echo $priceMin; ?></div><input name="abr_price_range" id="ex2" type="text" class="span2" value="" data-slider-min="<?php echo $priceMin; ?>" data-slider-max="<?php echo $priceMax; ?>" data-slider-step="100" data-slider-value="[<?php echo $priceStartSlot;?>,<?php echo $priceEndSlot;?>]"/><div style="float:right;width:12%;"><?php echo $priceMax; ?></div>
									</div>
								</div>
								
								<div class="listings-search-field listings-search-field-taxonomy_select listings-search-field-location col-xs-12 col-sm-3" id="reset_search_block">	
									<button id="reset_search" type="button" class="btn btn-danger btn-block">Reset Search</button>
								</div>
								
								<div class="listings-search-field listings-search-field-submit listings-search-field-submit col-xs-12 col-sm-3" id="search_block">
									<input type="submit" value="Search" id="search_listing" name="search_listing" class="btn btn-success btn-block">
								</div>
							</div>
						</div>			
					</form>
				</div>
			</div>
<?php
		//echo '<pre>'; print_r( $args ); echo '</pre>';// exit; 
		// The Query
		$searchObject = new WP_Query( $args );
		// Print last SQL query string
		//	echo $wpdb->last_query;exit; 

		if( !empty( $searchObject->found_posts)){
?>			
			<div class="container">
				<div class="abs-listings-sc" style="margin-top:50px;margin-bottom:50px;">
					<div class="listings-panel-wrap">
						<div class="listings-panel" style="display:block !important;">		
							<div class="row gutter-60">		
								<div class="listings-panel-title col-sm-6">			
									<span class="listings-panel-found"><?php echo $searchObject->found_posts; ?></span> Listings				
								</div><!-- .listings-panel-title -->
								
								<div class="listings-panel-actions col-sm-6">			
									<div class="listings-panel-action">					
										<div class="listings-sort">
											<div class="btn-group bootstrap-select form-control input-sm" style="width: 134px;">
												<select name="listings-sort" class="selectpicker form-control input-sm" data-style="btn-default" data-width="auto" id="order_block">
													<option value="">Order by</option>
													<option value="date_ASC" <?php if($order_by_value == 'date_ASC'){?>selected="selected"<?php } ?> >Date (ASC)</option>
													<option value="date_DESC" <?php if($order_by_value == 'date_DESC'){?>selected="selected"<?php } ?>>Date (DESC)</option>
													<option value="price_ASC" <?php if($order_by_value == 'price_ASC'){?>selected="selected"<?php } ?>>Price (ASC)</option>
													<option value="price_DESC" <?php if($order_by_value == 'price_DESC'){?>selected="selected"<?php } ?>>Price (DESC)</option>
													<option value="title_ASC" <?php if($order_by_value == 'title_ASC'){?>selected="selected"<?php } ?>>Title (ASC)</option>
													<option value="title_DESC" <?php if($order_by_value == 'title_DESC'){?>selected="selected"<?php } ?>>Title (DESC)</option>
												</select >
											</div>
										</div><!-- .listings-sort -->
									</div>		
								</div><!-- .listings-panel-actions -->		
							</div><!-- .row -->	
						</div><!-- .listings-panel -->
					</div><!-- .listings-panel-wrap -->
					
					<div>
						<div class="row gutter-60">
							<div class="col-sm-12"><div id="map"></div></div>
						</div>
					</div>
					<style type="text/css">
					  /* Always set the map height explicitly to define the size of the div
					   * element that contains the map. */
					  #map {
						height: 500px;
					  }
					  /* Optional: Makes the sample page fill the window. */
					  html, body {
						height: 100%;
						margin: 0;
						padding: 0;
					  }
					</style>
					<script>
						var map;
						var marker = new Array();
						var myLatLng = new Array();
						initMap();
						function initMap() {
							var myLatLng = {lat: 33.480904, lng: -112.158630};

							map = new google.maps.Map(document.getElementById('map'), {
							  zoom: 4,
							  center: myLatLng
							});
							/*
							var marker = new google.maps.Marker({
							  position: myLatLng,
							  map: map,
							  title: 'Hello World!'
							});
							*/
						}
					</script>
					<div class="abs-listings" id="abs_listing_block">
						<div class="row gutter-60">						
					<?php
						$i = 0 ;
						//echo '<pre>'; print_r( $searchObject->posts ); exit; 

						foreach( $searchObject->posts as $post ){
							$aID =	$post->ID;
					
							$apartmentThumnail	=	get_post_meta( $aID, 'cf_apartment_pic',true  );
							if( empty( $apartmentThumnail )){
								$apartmentThumnail = '';
							}
							$apartmentUrl 	=	get_permalink( $aID );
							$apartmentRent 	=	get_post_meta( $aID, 'cf_apartment_rent_month',true  );	
							$aBedrooms	 	=	wp_get_post_terms($aID,'apartment_bedrooms');
							$aBedroom 		=	(isset($aBedrooms[0]->name))?$aBedrooms[0]->name:0;
							$aBathrooms 	=	wp_get_post_terms($aID,'apartment_bathrooms');
							$aBathroom 		=	(isset($aBathrooms[0]->name))?$aBathrooms[0]->name:0;
							$aPlotAreas 	=	wp_get_post_terms($aID,'apartment_area');
							$aPlotArea 		=	(isset($aPlotAreas[0]->name))?$aPlotAreas[0]->name:0;
							$capacities 	=	wp_get_post_terms($aID,'apartment_rental_occupancy');
							$capacity 		=	(isset($capacities[0]->name))?$capacities[0]->name:0;
							$cmid			=	get_post_meta($aID,'cf_apartment_community',true);
							$cmAdress		=	get_post_meta($cmid,'cf_community_address',true);
							$data =	getLatLong( $cmAdress );	
?>							
							<script>
								myLatLng[<?php echo $i; ?>] = {lat: <?php echo $data[ 'latitude' ]; ?>, lng: <?php echo $data[ 'longitude' ]; ?>};
								
								console.log( myLatLng );
								marker[<?php echo $i; ?>] = new google.maps.Marker({
								  position: myLatLng[<?php echo $i; ?>],
								  map: map,
								  title: '<?php echo get_the_title($cmid); ?>'
								});
								
								google.maps.event.addListener(
									marker[<?php echo $i; ?>],"click", function (e) { window.open('<?php echo get_permalink( $cmid ); ?>', '_blank'); }
							    );							
							</script>
							<div id="listing-<?php echo $aID; ?>" class="listing-wrap col-sm-6">
								<div class="listing abs-listing-archive post-<?php echo $aID; ?> type-listing status-publish has-post-thumbnail hentry location-central-madrid location-north-madrid listing-type-detached-house listing-type-villa feature-air-condition feature-desiger-furniture feature-fantastic-views feature-marble-floor feature-new-kitchen feature-private-garden feature-private-pool feature-recently-refurbished feature-security-system feature-walk-in-wardrobes listing-category-carousel" itemscope="" itemtype="http://schema.org/Product">	
									<meta itemprop="name" content="<?php echo $post->post_title; ?>">		
									<div itemprop="offers" itemscope="" itemtype="http://schema.org/Offer" class="clearfix">							
										<meta itemprop="image" content="<?php echo $apartmentThumnail; ?>">
										<div class="wpsigh--listing-section abs-listing-section-image">	
											<span class="moretag"><a href="<?php echo $apartmentUrl; ?>" class="btn btn-sm btn-primary">More info</a></span>
											<div class="abs-listing-image">
												<a href="<?php echo $apartmentUrl; ?>" rel="bookmark">
													<div class="abs-listing-thumbnail" style="height: 358px;"><img width="740" height="490" src="<?php echo $apartmentThumnail; ?>" class="attachment-abs-large size-abs-large wp-post-image" alt="4 Bedroom Extravagant Villa in Castilla" title="" data-mh="listing-thumbnail" style="height: 358px;"></div><!-- .abs-listing-thumbnail -->
												</a>
											</div>	
										</div><!-- .abs-listing-section -->			
										
										<div class="listing-content equal" data-mh="listing-content" style="height: 266px;">				
											<div class="abs-listing-section abs-listing-section-info">	
												<div class="abs-listing-info">		
													<div class="row gutter-40">
														<div class="col-xs-6">
															<div class="abs-listing-price">
																<span class="listing-price-symbol">$</span><span class="listing-price-value" itemprop="price" content="<?php echo $apartmentRent; ?>"><?php echo $apartmentRent; ?></span>
																<meta itemprop="priceCurrency" content="USD"><span class="listing-rental-period">/ per Month</span>
															</div>
														</div>
														
														<div class="col-xs-6">
															<div class="abs-listing-status">
																<span class="label label-rent" style="background-color:#2980b9">For Rent</span>
															</div>
														</div>
													</div>																
												</div>	
											</div><!-- .abs-listing-section-info -->			
											
											<div class="abs-listing-section abs-listing-section-title">
												<div class="abs-listing-title">
													<a href="<?php echo $apartmentUrl; ?>" rel="bookmark"><?php echo $post->post_title; ?></a>
												</div>	
											</div><!-- .abs-listing-section-title -->				
											
											<div class="abs-listing-section abs-listing-section-description">
												<div class="abs-listing-description" itemprop="description">	
													<p><?php echo $post->post_content; ?><span class="moretag"><a href="<?php echo $apartmentUrl; ?>" class="btn btn-sm btn-primary">More info</a></span></p>
												</div>
											</div><!-- .abs-listing-section -->				
											
											<div class="abs-listing-section abs-listing-section-compare">
												<div class="abs-listing-compare clearfix">
													<span class="listing-details-1 listing-details-detail" title="Beds"><span class="listing-details-label">Beds:</span> <span class="listing-details-value">4</span></span><!-- .listing-details-1 -->
													<span class="listing-details-2 listing-details-detail" title="Baths"><span class="listing-details-label">Baths:</span> <span class="listing-details-value">4</span></span><!-- .listing-details-2 -->
													<span class="listing-details-3 listing-details-detail" title="Plot Size"><span class="listing-details-label">Plot Size:</span> <span class="listing-details-value">1800 m²</span></span><!-- .listing-details-3 -->
													<span class="listing-details-4 listing-details-detail" title="Living Area"><span class="listing-details-label">Living Area:</span> <span class="listing-details-value">485 m²</span></span><!-- .listing-details-4 -->
													<span class="listing-details-5 listing-details-detail" title="Terrace"><span class="listing-details-label">Terrace:</span> <span class="listing-details-value">65 m²</span></span><!-- .listing-details-5 -->
													<span class="listing-details-6 listing-details-detail" title="Parking"><span class="listing-details-label">Parking:</span> <span class="listing-details-value">Garages</span></span><!-- .listing-details-6 -->
													<span class="listing-details-7 listing-details-detail" title="Heating"><span class="listing-details-label">Heating:</span> <span class="listing-details-value">Central</span></span><!-- .listing-details-7 -->
													<span class="listing-details-8 listing-details-detail" title="Built in"><span class="listing-details-label">Built in:</span> <span class="listing-details-value">2005</span></span><!-- .listing-details-8 -->
												</div><!-- .abs-listing-compare -->		
											</div><!-- .abs-listing-section-compare -->			
										</div><!-- .listing-content -->
										
										<div class="abs-listing-section abs-listing-section-summary">	
											<div class="abs-listing-summary listing-summary-count-4 clearfix">
												<span class="listing-details-1 listing-details-detail" title="Beds"><span class="listing-details-label">Beds:</span> <span class="listing-details-value"><?php echo $aBedroom; ?></span></span><!-- .listing-details-1 -->
												<span class="listing-details-2 listing-details-detail" title="Baths"><span class="listing-details-label">Baths:</span> <span class="listing-details-value"><?php echo $aBathroom; ?></span></span><!-- .listing-details-2 -->
												<span class="listing-details-3 listing-details-detail" title="Plot Size"><span class="listing-details-label">Plot Size:</span> <span class="listing-details-value"><?php echo $aPlotArea; ?></span></span><!-- .listing-details-3 -->
												<span class="listing-details-4 listing-details-detail" title="Living Capacity"><span class="listing-details-label">Capacity:</span> <span class="listing-details-value"><?php echo $capacity; ?></span></span><!-- .listing-details-4 -->
											</div><!-- .abs-listing-summary listing-summary-count-4 -->		
										</div><!-- .abs-listing-section-summary -->							
									</div>	
								</div><!-- #listing-210 -->
							</div>	
					<?php
						$i++;
					} 
					?>		
							
						</div><!-- .row -->
							
					</div><!-- .abs-listings -->
		<?php
				$big = 999999999; // need an unlikely integer
				$pagination_argument = array( 'keyword'=>$search_string,'bedrooms'=>$selected_bedrooms );
				$pagination_argument['style'] = $selected_styles;
				$pagination_argument['location'] = $selected_locations;
				$pagination_argument['bathrooms'] = $selected_bathrooms;
				$pagination_argument['abr_price_range'] = $abr_price_range;				
				$pagination_argument['order_by_value'] = $order_by_value;		
			
				$baseUrl =	str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) );
				
				if(!empty( strpos( get_pagenum_link( $big ), 'keyword' ) ) ){
					$baseArray 	=	explode( 'keyword', $baseUrl );
					$baseUrl = $baseArray[0];
				}
				
				$paginationArray	=	array(
											'base' => $baseUrl,
											'format' => '?paged=%#%',
											'current' => max( 1, get_query_var('paged') ),
											'total' => $searchObject->max_num_pages,
											'add_args'=> $pagination_argument,
											'type'  => 'array'
										);
										
				
				
				//	esc_url( get_pagenum_link( $big ) ) 
				$pages = paginate_links( $paginationArray );
			
				//kriesi_pagination( $searchObject->max_num_pages,2 );
				if( is_array( $pages ) ) {
					$paged = ( get_query_var('paged') == 0 ) ? 1 : get_query_var('paged');
					echo '<div class="abs-pagination"><ul class="pagination">';
					foreach ( $pages as $page ) {
						echo "<li>$page</li>";
					}
					echo '</ul></div>';
				}
		?>		
					<!--
					<div class="abs-pagination">
						<nav>
							<ul class="pagination">
								<li class="active"><a href="#"><span class="page-numbers current">1</span></a></li>
								<li><a class="page-numbers" href="http://demo.wpcasa.com/madrid/listings/browse/page/2/?keyword&amp;offer&amp;location&amp;listing-type&amp;bedrooms&amp;bathrooms&amp;min&amp;max&amp;orderby=date&amp;order=desc#038;offer&amp;location&amp;listing-type&amp;bedrooms&amp;bathrooms&amp;min&amp;max&amp;orderby=date&amp;order=desc">2</a></li>
								<li><a class="next page-numbers" href="http://demo.wpcasa.com/madrid/listings/browse/page/2/?keyword&amp;offer&amp;location&amp;listing-type&amp;bedrooms&amp;bathrooms&amp;min&amp;max&amp;orderby=date&amp;order=desc#038;offer&amp;location&amp;listing-type&amp;bedrooms&amp;bathrooms&amp;min&amp;max&amp;orderby=date&amp;order=desc">Next →</a></li>
							</ul>
						</nav>
					</div>
					-->
				</div> 
			</div>
		<?php } ?>		
		</div>
	</div>	
	<script src="http://ajax.aspnetcdn.com/ajax/jQuery/jquery-2.2.4.min.js"></script> 
	<script type="text/javascript" src="<?php echo plugin_dir_url( __FILE__ );  ?>js/jquery.form.min.js?ver=3.51.0-2014.06.20"></script>
	<script type="text/javascript" src="<?php echo plugin_dir_url( __FILE__ );  ?>js/bootstrap.js?ver=3.3.6"></script> 
	<script type="text/javascript" src="<?php echo plugin_dir_url( __FILE__ );  ?>js/bootstrap-dropdownhover.js?ver=1.0.0"></script> 
	<script type="text/javascript" src="<?php echo plugin_dir_url( __FILE__ );  ?>js/bootstrap-select.js?ver=1.10.0"></script> 	
	<script type="text/javascript" src="<?php echo plugin_dir_url( __FILE__ );  ?>js/bootstrap-slider.js?ver=1.10.0"></script> 		

	<script type="text/javascript">
		jQuery( document ).ready(function(){
			jQuery("#order_block").on("changed.bs.select", 
				function(e, clickedIndex, newValue, oldValue) {
				console.log(this.value, clickedIndex, newValue, oldValue);
				jQuery( '#order_by_value' ).val(this.value);
				jQuery( '#search_tool_form' ).submit();				
			});
			
			jQuery( document ).on( 'click','#reset_search',function(){
				jQuery( '#keyword' ).val( '' );
				jQuery('.selectpicker').selectpicker('refresh');
				jQuery( '#abs-listing-bedrooms' ).val( '' );
				jQuery( '#abs-listing-style' ).val( '' );
				jQuery( '#abs-listing-location' ).val( '' );
				jQuery( '#abs-listing-bathrooms' ).val( '' );
			});

			jQuery('#ex2').slider({tooltip: 'always'});

		});
	</script>
<?php
}

function build_tax_array( $type = '',$value = '' ){
	$response  = 	array(
							'taxonomy'	=> $type,
							'field' 	=> 'slug',
							'terms' 	=> array( $value )
						);	
	
	return $response;	
}

function kriesi_pagination($pages = '', $range = 2)
{  
     $showitems = ($range * 2)+1;  

     global $paged;
     if(empty($paged)) $paged = 1;

     if($pages == '')
     {
         global $wp_query;
         $pages = $wp_query->max_num_pages;
         if(!$pages)
         {
             $pages = 1;
         }
     }   

     if(1 != $pages)
     {
         echo "<div class='pagination'>";
         if($paged > 2 && $paged > $range+1 && $showitems < $pages) echo "<a href='".get_pagenum_link(1)."'>&laquo;</a>";
         if($paged > 1 && $showitems < $pages) echo "<a href='".get_pagenum_link($paged - 1)."'>&lsaquo;</a>";

         for ($i=1; $i <= $pages; $i++)
         {
             if (1 != $pages &&( !($i >= $paged+$range+1 || $i <= $paged-$range-1) || $pages <= $showitems ))
             {
                 echo ($paged == $i)? "<span class='current'>".$i."</span>":"<a href='".get_pagenum_link($i)."' class='inactive' >".$i."</a>";
             }
         }

         if ($paged < $pages && $showitems < $pages) echo "<a href='".get_pagenum_link($paged + 1)."'>&rsaquo;</a>";  
         if ($paged < $pages-1 &&  $paged+$range-1 < $pages && $showitems < $pages) echo "<a href='".get_pagenum_link($pages)."'>&raquo;</a>";
         echo "</div>\n";
     }
}