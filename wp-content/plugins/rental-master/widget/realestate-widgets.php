<?php
if(!defined('ABSPATH')) exit;
class CF_AR_Realestate_Widgets extends WP_Widget{

	public function __construct(){
		$widget_ops = array(
			'classname' => 'realestate_filters_widget',
			'description' => 'Realestate Search Filters for sidebar',
		);
		parent::__construct('realestate_filters_widget','About Rentals Realestate Search Widget',$widget_ops);
	}

	public function widget($args,$instance){
		$category		=	$instance['realestate_category'];
		$amenity_editor	=	$instance['listing_amenity_editor'];
		$type_editor	=	$instance['construction_type_editor'];
		$listing_bathroom	=	$instance['listing_bathrooms'];
		$listing_bedroom	=	$instance['listing_bedrooms' ];
		$style_editor	 	=	$instance['style_editor'];
		$listing_type		=	$instance['listing_type'];		
		
		$title		=!	empty($instance['title'])?$instance['title']:__('Search RealEstates','ar');
		$categoryTitle	=!	empty($instance['realestate_category'])?$instance['realestate_category']:__('Search by Category','ar');
		$amenityEditorTitle	=!	empty($instance['amenity_editor'])?$instance['amenity_editor']:__('Search by Amenity Editor','ar');
		$typeEditorTitle	=!	empty($instance['type_editor'])?$instance['type_editor']:__('Search by Type Editor','ar');
		$bathroomTitle	=!	empty($instance['listing_bathrooms'])?$instance['listing_bathrooms']:__('Search by Listing Bathrooms','ar');
		$bedroomTitle	=!	empty($instance['listing_bedrooms'])?$instance['listing_bedrooms']:__('Search by Listing Bedrooms','ar');
		$styleEditorTitle	=!	empty($instance['style_editor'])?$instance['style_editor']:__('Search by Style Editor','ar');
		$listingTypeTitle	=!	empty($instance['listing_type'])?$instance['listing_type']:__('Search by Listing Type','ar');
		
		$category_OBJ		=	get_terms(array('taxonomy'=>'realestate_category','hide_empty'=>true));
		$amenityEditor_OBJ	=	get_terms(array('taxonomy'=>'amenity_editor','hide_empty'=>true));
		$typeEditor_OBJ	=	get_terms(array('taxonomy'=>'type_editor','hide_empty'=>true));
		$bathroom_OBJ	=	get_terms(array('taxonomy'=>'listing_bathrooms','hide_empty'=>true));
		$bedroom_OBJ	=	get_terms(array('taxonomy'=>'listing_bedrooms','hide_empty'=>true));
		$styleEditor_OBJ	=	get_terms(array('taxonomy'=>'style_editor','hide_empty'=>true));
		$listingType_OBJ		=	get_terms(array('taxonomy'=>'listingType','hide_empty'=>true));		
		echo $args['before_widget'];

		/*
		
		if(!empty($instance['title'])){
			echo $args['before_title'].apply_filters('widget_title',$title).$args['after_title'];
		}
		else{
			echo '<h3>'.$title.'</h3>';
		}

		echo '<form class="reSearch" method="get" action="'.get_permalink(About_rental_cf_exe::get_id_by_slug('realestate-listing')).'">';
		echo'<ul>';
		if($category=='on'){
			echo '<li>';
				echo $this->abr_select_box($categoryTitle,$category_OBJ,'category');
			echo '</li>';
		}
		if($amenity_editor=='on'){
			echo '<li>';
				echo $this->abr_select_box($amenityEditorTitle,$amenityEditor_OBJ,'amenity_editor');
			echo '</li>';
		}
		if($type_editor=='on'){
			echo '<li>';
				echo $this->abr_select_box($typeEditorTitle,$typeEditor_OBJ,'type_editor');
			echo '</li>';
		}
		if($listing_bathroom=='on'){
			echo '<li>';
				echo $this->abr_select_box($bathroomTitle,$bathroom_OBJ,'listing_bathroom');
			echo '</li>';
		}
		if($listing_bedroom=='on'){
			echo '<li>';
				echo $this->abr_select_box($bedroomTitle,$bedroom_OBJ,'listing_bedroom');
			echo '</li>';
		}
		if($style_editor=='on'){
			echo '<li>';
				echo $this->abr_select_box($styleEditorTitle,$styleEditor_OBJ,'utilities');
			echo '</li>';
		}
		if($listing_type=='on'){
			echo '<li>';
				echo $this->abr_select_box($listingTypeTitle,$listingType_OBJ,'options');
			echo '</li>';
		}

		echo '<li><input type="submit" style="-webkit-appearance:none;font-size:20px" value="Search" id="btnSearch" name="btnSearch"></li>';
		echo '</ul></form>';
		*/
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
		$instance['realestate_category']		=	$new_instance['realestate_category'];
		$instance['listing_amenity_editor']		=	$new_instance['listing_amenity_editor'];
		$instance['construction_type_editor']	=	$new_instance['construction_type_editor'];
		$instance['listing_bathrooms']			=	$new_instance['listing_bathrooms'];
		$instance['listing_bedrooms' ]			=	$new_instance['listing_bedrooms'];
		$instance['style_editor']				=	$new_instance['style_editor'];
		$instance['listing_type']				=	$new_instance['listing_type'];
		return $instance;
	}

	function form( $instance ) {
		?>
		<h4><?php echo _e( 'Select Realestate Options for Search:', 'abr' );?></h4>
		<p>
			<input class="checkbox" type="checkbox" <?php checked( $instance[ 'realestate_category' ], 'on' ); ?> id="<?php echo $this->get_field_id( 'realestate_category' ); ?>" name="<?php echo $this->get_field_name( 'realestate_category' ); ?>" />
			<label for="<?php echo $this->get_field_id( 'realestate_category' ); ?>"><?php echo  _e( 'Realestate Category', 'abr' );?></label>
		</p>
		<p>
			<input class="checkbox" type="checkbox" <?php checked( $instance[ 'listing_amenity_editor' ], 'on' ); ?> id="<?php echo $this->get_field_id( 'listing_amenity_editor' ); ?>" name="<?php echo $this->get_field_name( 'listing_amenity_editor' ); ?>" /> 
			<label for="<?php echo $this->get_field_id( 'listing_amenity_editor' ); ?>"><?php echo _e( 'Listing Amenity Editor', 'abr' );?></label>
		</p>
		<p>
			<input class="checkbox" type="checkbox" <?php checked( $instance[ 'construction_type_editor' ], 'on' ); ?> id="<?php echo $this->get_field_id( 'construction_type_editor' ); ?>" name="<?php echo $this->get_field_name( 'construction_type_editor' ); ?>" /> 
			<label for="<?php echo $this->get_field_id( 'construction_type_editor' ); ?>"><?php echo _e( 'Construction Type Editor', 'abr' );?></label>
		</p>
		<p>
			<input class="checkbox" type="checkbox" <?php checked( $instance[ 'listing_bathrooms' ], 'on' ); ?> id="<?php echo $this->get_field_id( 'listing_bathrooms' ); ?>" name="<?php echo $this->get_field_name( 'listing_bathrooms' ); ?>" />
			<label for="<?php echo $this->get_field_id( 'listing_bathrooms' ); ?>"><?php echo _e( 'Listing Bathrooms', 'abr' );?></label>
		</p>
		<p>
			<input class="checkbox" type="checkbox" <?php checked( $instance[ 'listing_bedrooms' ], 'on' ); ?> id="<?php echo $this->get_field_id( 'listing_bedrooms' ); ?>" name="<?php echo $this->get_field_name( 'listing_bedrooms' ); ?>" /> 
			<label for="<?php echo $this->get_field_id( 'listing_bedrooms' ); ?>"><?php echo _e( 'Listing Bedrooms', 'abr' );?></label>
		</p>
		<p>
			<input class="checkbox" type="checkbox" <?php checked( $instance[ 'style_editor' ], 'on' ); ?> id="<?php echo $this->get_field_id( 'style_editor' ); ?>" name="<?php echo $this->get_field_name( 'style_editor' ); ?>" /> 
			<label for="<?php echo $this->get_field_id( 'style_editor' ); ?>"><?php echo _e( 'Style Editor', 'abr' );?></label>
		</p>
		<p>
			<input class="checkbox" type="checkbox" <?php checked( $instance[ 'listing_type' ], 'on' ); ?> id="<?php echo $this->get_field_id( 'listing_type' ); ?>" name="<?php echo $this->get_field_name( 'listing_type' ); ?>" /> 
			<label for="<?php echo $this->get_field_id( 'listing_type' ); ?>"><?php echo _e( 'Listing Type', 'abr' );?></label>
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
add_action('widgets_init',create_function('','return register_widget("CF_AR_Realestate_Widgets");'));