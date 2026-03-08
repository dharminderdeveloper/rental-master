<?php

/******************************************************
*	Page: availability-manager.php
*
******************************************************/

	/**
	* Function: abr_availability_manager_menu
	*
	* Description: Creates a menu to display user help page
	*/
	add_action( 'admin_menu', 'abr_availability_manager_menu' );
	function abr_availability_manager_menu(){
		add_menu_page(__('Availability Manager','ar'),__('Availability Manager','ar'),'administrator','availability-manager','abr_availability_manager_page',ABR_PLUGIN_DIR_URL.'/img/am.png',0);
	}
	
	/**
	* Function: abr_availability_manager_page
	*
	* Description: Call Back function to display page
	*/
	function abr_availability_manager_page(){
		?>
		<h1><?php echo __('Availability Manager','ar'); ?></h1>
		<div class="availability-manager"">
		<?php echo abr_availability_manager_page_content();?>
		</div><!--/*welcome-panel Ends*/-->
		<?php
	}
	
	/**
	* Function: abr_availability_manager_page_content
	*
	* Description: Display Page Content
	*/
	function abr_availability_manager_page_content(){
		?>
		<ul class="availHead">
			<li class="welcome-panel">
			<span><?php echo __('Title','ar'); ?></span>
			<span><?php echo __('Rent','ar'); ?></span>
			<span><?php echo __('Monthly Rent Range','ar'); ?></span>
			<span><?php echo __('No of Units Available','ar'); ?></span>
			<span><?php echo __('No of Units','ar'); ?></span>
			<span><?php echo __('Date Available','ar'); ?></span>
			<span><?php echo __('Featured ','ar'); ?></span>
			<span><?php echo __('Action ','ar'); ?></span>
			</li>
			<?php
			$ids	=	abr_apartment_ids();
			foreach($ids as $id){
					echo abr_list_apartment_by_id($id);
			}
			?>
		</ul>
		<?php
	}
	
	/**
	* Function: abr_apartment_ids
	*
	* Description: Display Page Content
	* @return:	array
	*/
	function abr_apartment_ids(){
		$args = array(
			'orderby'			=>	'date',
			'order'			=>	'DESC',
			'post_type'		=>	'apartment',
			'post_status'		=>	'publish',
			'fields'			=>	'ids',
			'posts_per_page'	=>	-1
		);
		$ids	=	get_posts($args);
		return $ids;
	}
	
	/**
	* Function: abr_list_apartment_by_id
	*
	* @param:	$id
	*/
	function abr_list_apartment_by_id($id){
		$title			=	get_the_title($id);
		$selected_range=	wp_get_post_terms($id,'apartment_monthly_rent');
		$featured		=	get_post_meta($id,'cf_apartment_featured',true);
		$rent			=	get_post_meta($id,'cf_apartment_rent_month',true);
		$units			=	get_post_meta($id,'cf_apartment_no_of_units',true);
		$date			=	get_post_meta($id,'cf_apartment_date_available',true);
		$units_avail	=	get_post_meta($id,'cf_apartment_no_of_units_available',true);
		?>
		<li pid="<?php echo $id?>">
			<span><a href="<?php echo get_permalink($id); ?>" > <?php echo __($title,'ar'); ?> </a></span>
			<span><input type="text" value="<?php echo $rent; ?>" id="Rent"></span>
			<span><?php echo abr_rent_range_select_box($selected_range);	?></span>
			<span><input type="text" value="<?php echo $units_avail; ?>" id="unitAvail"></span>
			<span><input type="text" value="<?php echo $units; ?>" id="Units"></span>
			<span><input type="date" value="<?php echo $date; ?>" class="datepicker" id="availDate">
			</span>
			<span><input class="checkbox" type="checkbox" <?php checked( $featured, 'on' ); ?> id="isfeatured" value="<?php echo $featured?>"/></span>
			<span><a href="javascript:void(0)" class="button button-primary button-large updateApartment" ><?php echo __('Update','ar');?></a></span>
			</li>
		<?php
	}
	
	/**
	* Function: abr_rent_range_select_box
	*
	* @param:	$selected_range
	*/
	function abr_rent_range_select_box($selected_range){
		$available_ranges	=	get_terms(array('taxonomy'=>'apartment_monthly_rent','hide_empty'=>false));
		@$selected_range=	array($selected_range[0]->term_id);
		?>
		<select id="rentRange">
		<?php
			foreach($available_ranges as $range){
			if( in_array( $range->term_id,$selected_range)){
				echo '<option value="'.$range->term_id.'" selected="selected">'.__($range->name,'ar').'</option>';
			}else{
				echo '<option value="'.$range->term_id.'">'.__($range->name,'ar').'</option>';
			}
		}
		?>
		</select>
		<?php
	}