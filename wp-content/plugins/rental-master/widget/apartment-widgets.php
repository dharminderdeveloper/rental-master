<?php
if(!defined('ABSPATH')) exit;
class CF_AR_Apartment_Widgets extends WP_Widget{

	public function __construct(){
		$widget_ops = array(
			'classname' => 'apartment_filters_widget',
			'description' => 'Apartment Search Filters for sidebar',
		);
		parent::__construct('apartment_filters_widget','About Rentals Sidebar Search Widget',$widget_ops);
	}

	public function widget($args,$instance){
		$leasing	=	$instance['apartment_leasing_option'];
		$style		=	$instance['apartment_styles'];
		$amenties	=	$instance['apartment_amenities'];
		$bathroom	=	$instance['apartment_bathrooms'];
		$area		=	$instance['apartment_area' ];
		$utilities=	$instance['apartment_utilities'];
		$options	=	$instance['apartment_options'];
		$bedroom	=	$instance['apartment_bedrooms'];
		$rent		=	$instance['apartment_rent'];
		$location	=	$instance['apartment_location'];
		
		$title		=!	empty($instance['title'])?$instance['title']:__('Search Apartments','ar');
		$bedroomTitle	=!	empty($instance['bedroom'])?$instance['bedroom']:__('Search by Bedrooms','ar');
		$rentTitle	=!	empty($instance['rent'])?$instance['rent']:__('Search by Monthly Rent','ar');
		$locationTitle	=!	empty($instance['location'])?$instance['location']:__('Search by Location','ar');
		$leasingTitle	=!	empty($instance['leasing'])?$instance['leasing']:__('Search by Leasing Option','ar');
		$styleTitle	=!	empty($instance['styles'])?$instance['styles']:__('Search by Styles','ar');
		$amentiesTitle	=!	empty($instance['amenities'])?$instance['amenities']:__('Search by Amenties','ar');
		$bathroomTitle	=!	empty($instance['bathrooms'])?$instance['bathrooms']:__('Search by Bathroom','ar');
		$areaTitle		=!	empty($instance['area'])?$instance['area']:__('Search by Area','ar');
		$utilitiesTitle=!	empty($instance['utility'])?$instance['utility']:__('Search by Utilities','ar');
		$optionTitle	=!	empty($instance['options'])?$instance['options']:__('Search by Availability Options','ar');
		
		$bedrooms_OBJ		=	get_terms(array('taxonomy'=>'apartment_bedrooms','hide_empty'=>true));
		$monthlyRent_OBJ	=	get_terms(array('taxonomy'=>'apartment_monthly_rent','hide_empty'=>true));
		$style_OBJ	=	get_terms(array('taxonomy'=>'apartment_style','hide_empty'=>true));
		$leasing_OBJ	=	get_terms(array('taxonomy'=>'apartment_leasing','hide_empty'=>true));
		$amenties_OBJ	=	get_terms(array('taxonomy'=>'apartment_amenities','hide_empty'=>true));
		$bathroom_OBJ	=	get_terms(array('taxonomy'=>'apartment_bathrooms','hide_empty'=>true));
		$area_OBJ		=	get_terms(array('taxonomy'=>'apartment_area','hide_empty'=>true));
		$utilities_OBJ=	get_terms(array('taxonomy'=>'apartment_utilities','hide_empty'=>true));
		$options_OBJ	=	get_terms(array('taxonomy'=>'apartment_availability_options','hide_empty'=>true));
		$location_OBJ	=	get_terms(array('taxonomy'=>'community_location','hide_empty'=>true));

		echo $args['before_widget'];
		
		if(!empty($instance['title'])){
			echo $args['before_title'].apply_filters('widget_title',$title).$args['after_title'];
		}
		else{
			echo '<h3>'.$title.'</h3>';
		}
		echo '<form class="aptSearch" method="get" action="'.get_permalink(About_rental_cf_exe::get_id_by_slug('apartments-listing')).'">';
		echo'<ul>';
		if($leasing=='on'){
			echo '<li>';
				echo $this->abr_select_box($leasingTitle,$leasing_OBJ,'lease');
			echo '</li>';
		}
		if($style=='on'){
			echo '<li>';
				echo $this->abr_select_box($styleTitle,$style_OBJ,'styles');
			echo '</li>';
		}
		if($amenties=='on'){
			echo '<li>';
				echo $this->abr_select_box($amentiesTitle,$amenties_OBJ,'amenties');
			echo '</li>';
		}
		if($bathroom=='on'){
			echo '<li>';
				echo $this->abr_select_box($bathroomTitle,$bathroom_OBJ,'bathroom');
			echo '</li>';
		}
		if($area=='on'){
			echo '<li>';
				echo $this->abr_select_box($areaTitle,$area_OBJ,'area');
			echo '</li>';
		}
		if($utilities=='on'){
			echo '<li>';
				echo $this->abr_select_box($utilitiesTitle,$utilities_OBJ,'utilities');
			echo '</li>';
		}
		if($options=='on'){
			echo '<li>';
				echo $this->abr_select_box($optionTitle,$options_OBJ,'options');
			echo '</li>';
		}
		if($bedroom=='on'){
			echo '<li>';
				echo $this->abr_select_box($bedroomTitle,$bedrooms_OBJ,'bedroom');
			echo '</li>';
		}
		if($rent=='on'){
			echo '<li>';
				echo $this->abr_select_box($rentTitle,$monthlyRent_OBJ,'prices');
			echo '</li>';
		}
		if($location=='on'){
			echo '<li>';
				echo $this->abr_select_box($locationTitle,$location_OBJ,'location');				
			echo '</li>';
		}
			echo '<li><input type="submit" style="-webkit-appearance:none;font-size:20px" value="Search" id="btnSearch" name="btnSearch"></li>';
		echo '</ul></form>';
		
		echo $args['after_widget'];
	}
	
	/**
	*	Function:		update
	*
	*	Description:	Default function of WP_Widget (class)
	*	@param:			$new_instance
	*	@param:			$old_instance
	*
	*	@return:		$instance
	*/
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
	   
		$instance[ 'apartment_leasing_option' ]	= $new_instance[ 'apartment_leasing_option' ];
		$instance[ 'apartment_styles' ]			= $new_instance[ 'apartment_styles' ];
		$instance[ 'apartment_amenities' ] 		= $new_instance[ 'apartment_amenities' ];
		$instance[ 'apartment_bedrooms' ] 		= $new_instance[ 'apartment_bedrooms' ];
		$instance[ 'apartment_bathrooms' ] 		= $new_instance[ 'apartment_bathrooms' ];
		$instance[ 'apartment_area' ] 			= $new_instance[ 'apartment_area' ];
		$instance[ 'apartment_utilities' ] 		= $new_instance[ 'apartment_utilities' ];
		$instance[ 'apartment_options' ] 		= $new_instance[ 'apartment_options' ];
		$instance[ 'apartment_rent' ] 			= $new_instance[ 'apartment_rent' ];
		$instance[ 'apartment_location' ] 		= $new_instance[ 'apartment_location' ];		
		return $instance;
	}

	function form( $instance ) {
		//	echo '<pre>';	print_r( $instance );

		?>
		<h4><?php echo _e( 'Select Apartment Options for Search:', 'abr' );?></h4>
		<p>
			<input class="checkbox" type="checkbox" <?php checked( $instance[ 'apartment_leasing_option' ], 'on' ); ?> id="<?php echo $this->get_field_id( 'apartment_leasing_option' ); ?>" name="<?php echo $this->get_field_name( 'apartment_leasing_option' ); ?>" />
			<label for="<?php echo $this->get_field_id( 'apartment_leasing_option' ); ?>"><?php echo  _e( 'Leasing Options', 'abr' );?></label>
		</p>
		<p>
			<input class="checkbox" type="checkbox" <?php checked( $instance[ 'apartment_styles' ], 'on' ); ?> id="<?php echo $this->get_field_id( 'apartment_styles' ); ?>" name="<?php echo $this->get_field_name( 'apartment_styles' ); ?>" /> 
			<label for="<?php echo $this->get_field_id( 'apartment_styles' ); ?>"><?php echo _e( 'Apartment Styles', 'abr' );?></label>
		</p>
		<p>
			<input class="checkbox" type="checkbox" <?php checked( $instance[ 'apartment_amenities' ], 'on' ); ?> id="<?php echo $this->get_field_id( 'apartment_amenities' ); ?>" name="<?php echo $this->get_field_name( 'apartment_amenities' ); ?>" /> 
			<label for="<?php echo $this->get_field_id( 'apartment_amenities' ); ?>"><?php echo _e( 'Amenities', 'abr' );?></label>
		</p>
		<p>
			<input class="checkbox" type="checkbox" <?php checked( $instance[ 'apartment_bedrooms' ], 'on' ); ?> id="<?php echo $this->get_field_id( 'apartment_bedrooms' ); ?>" name="<?php echo $this->get_field_name( 'apartment_bedrooms' ); ?>" />
			<label for="<?php echo $this->get_field_id( 'apartment_bedrooms' ); ?>"><?php echo _e( 'Bedrooms', 'abr' );?></label>
		</p>
		<p>
			<input class="checkbox" type="checkbox" <?php checked( $instance[ 'apartment_bathrooms' ], 'on' ); ?> id="<?php echo $this->get_field_id( 'apartment_bathrooms' ); ?>" name="<?php echo $this->get_field_name( 'apartment_bathrooms' ); ?>" /> 
			<label for="<?php echo $this->get_field_id( 'apartment_bathrooms' ); ?>"><?php echo _e( 'Bathrooms', 'abr' );?></label>
		</p>
		<p>
			<input class="checkbox" type="checkbox" <?php checked( $instance[ 'apartment_area' ], 'on' ); ?> id="<?php echo $this->get_field_id( 'apartment_area' ); ?>" name="<?php echo $this->get_field_name( 'apartment_area' ); ?>" /> 
			<label for="<?php echo $this->get_field_id( 'apartment_area' ); ?>"><?php echo _e( 'Area', 'abr' );?></label>
		</p>
		<p>
			<input class="checkbox" type="checkbox" <?php checked( $instance[ 'apartment_utilities' ], 'on' ); ?> id="<?php echo $this->get_field_id( 'apartment_utilities' ); ?>" name="<?php echo $this->get_field_name( 'apartment_utilities' ); ?>" /> 
			<label for="<?php echo $this->get_field_id( 'apartment_utilities' ); ?>"><?php echo _e( 'Utilities', 'abr' );?></label>
		</p>
		<p>
			<input class="checkbox" type="checkbox" <?php checked( $instance[ 'apartment_options' ], 'on' ); ?> id="<?php echo $this->get_field_id( 'apartment_options' ); ?>" name="<?php echo $this->get_field_name( 'apartment_options' ); ?>" /> 
			<label for="<?php echo $this->get_field_id( 'apartment_options' ); ?>"><?php echo _e( 'Availability Options', 'abr' );?></label>
		</p>
		<p>
			<input class="checkbox" type="checkbox" <?php checked( $instance[ 'apartment_rent' ], 'on' ); ?> id="<?php echo $this->get_field_id( 'apartment_rent' ); ?>" name="<?php echo $this->get_field_name( 'apartment_rent' ); ?>" />
			<label for="<?php echo $this->get_field_id( 'apartment_rent' ); ?>"><?php echo _e( 'Monthly Rent', 'abr' );?></label>
		</p>
		<p>
			<input class="checkbox" type="checkbox" <?php checked( $instance[ 'apartment_location' ], 'on' ); ?> id="<?php echo $this->get_field_id( 'apartment_location' ); ?>" name="<?php echo $this->get_field_name( 'apartment_location' ); ?>" />
			<label for="<?php echo $this->get_field_id( 'apartment_location' ); ?>"><?php echo _e( 'Location', 'abr' );?></label>
		</p>
	<?php
	}
	
	/**
	*	Function:	abr_select_box
	*
	*	$param	:	$bedroomTitle
	*	$param	:	$bedrooms_OBJ
	*	$param	:	$name
	*/
	function abr_select_box($title,$itemsobj,$name){
		if( isset( $_GET[$name] ) ){
			$searched_for	=	$_GET[$name];
		}else{
			$searched_for	=	'';
		}		
		echo '<label>'.__($title,'ar').'</label><br>';
		
		echo '<select class="searchboxes " id="'.$name.'[]" atr="'.$name.'"  multiple="multiple" size="6" name="'.$name.'[]">';
		
		foreach($itemsobj as $item){
			if(!empty($searched_for)){
				if( in_array( $item->term_id,$searched_for)){
					
					echo '<option value="'.$item->term_id.'" selected="selected">'.__($item->name,'ar').
					'</option>';
				}else{
				echo '<option value="'.$item->term_id.'">'.__($item->name,'ar').'</option>';
				}
			}
			else{
				echo '<option value="'.$item->term_id.'">'.__($item->name,'ar').'</option>';
			}
		}
		echo '</select>';
		echo '  <input class="checkAll" type="button" value="'.__('Select All','ar').'">';
	}

}
add_action('widgets_init',create_function('','return register_widget("CF_AR_Apartment_Widgets");'));