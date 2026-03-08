<?php
if(!defined('ABSPATH')) exit;
class CF_AR_Leasing_Special{
	public static function init(){
        $class = __CLASS__;
        new $class;
    }
            	
	public function __construct(){		
		$version	=	get_option('abtrv', false);

		if(($version==1)||($version==2)){
			add_action('init',array($this,'abr_leasing_special_init'));
		}				

		CF_AR_Leasing_Special::update_leasing_special_date_column();
		CF_AR_Leasing_Special::get_expired_leasing_special();

		add_action('cmb2_admin_init',array($this,'cf_leasing_special_register_metabox'));		
		add_action( 'save_post',array( $this,'cf_leasing_special_update_date' ),10,3);
	}

	function abr_leasing_special_init(){
		$labels = array(
			'name'               	=> _x( 'Leasing Specials', 'leasing','ar' ),
			'singular_name'      	=> _x( 'Leasing Specials', 'leasing','ar' ),
			'menu_name'          	=> _x( 'Leasing Specials', 'admin menu','ar' ),
			'name_admin_bar'     	=> _x( 'Leasing Specials', 'add new on admin bar','ar'),
			'add_new'            	=> _x( 'Add New', 'leasing','ar' ),
			'add_new_item'       	=> __( 'Add New Leasing Specials','ar' ),
			'new_item'           	=> __( 'New Leasing Specials','ar' ),
			'edit_item'          	=> __( 'Edit Leasing Specials','ar' ),
			'view_item'          	=> __( 'View Leasing Specials','ar' ),
			'all_items'          	=> __( 'All Leasing Specials','ar' ),
			'search_items'       	=> __( 'Search Leasing Specials','ar' ),
			'parent_item_colon'  	=> __( 'Parent Leasing Specials:','ar' ),
			'not_found'          	=> __( 'No Leasing Specials found.','ar' ),
			'not_found_in_trash' 	=> __( 'No Leasing Specials found in Trash.','ar')
		);
		$args = array(
			'labels'             	=> $labels,
			'description'        	=> __('Leasing Specials Description','ar'),
			'public'             	=> true,
			'publicly_queryable' 	=> true,
			'show_ui'            	=> true,
			'show_in_menu'      	=> true,
			'query_var'         	=> 'leasing',
			'rewrite'           	=> array('slug'=>'leasing'),
			'capability_type'    	=> 'post',
			'has_archive'        	=> true,
			'hierarchical'       	=> true,
			'supports'           	=> array('title'),
			'menu_icon'			=> ABR_PLUGIN_DIR_URL.'img/leasing.png',
		);
		register_post_type('leasing',$args);
	}

	function cf_previous_leasing_special(){
		echo 'Prevoious Leasing Special';
		exit; 
	}
	
	/**
	 *	@description 	This function will fetch the expired leasing special.
	 *	@param			No Parameter
	 *	@return 		None
	 */
	 
	public function get_expired_leasing_special(){
		global $wpdb;
		
		//	Get Table Name
		$table_name =	$wpdb->prefix."postmeta";				
		
		$expired_leasing	=	$wpdb->get_results( "SELECT * from $table_name WHERE `meta_key`='cf_leasing_new_expire_date' and `meta_value`<= '".date( 'Y-m-d' )."'");
		
		if( count( $expired_leasing ) > 0 ){
			foreach( $expired_leasing as $special ){				
				$leasingID 		=	$special->post_id;				
				//	Fetch Leasing Special Detail
				$prSpecial		=	$this->get_leasing_special_detail( $leasingID );	
									
				//	Save Previous Leasing Special
				CF_AR_Previous_Leasing_Special::save_previous_leasing_special( $prSpecial,'expired');
				
				//	Delete Previous Leasing Special
				wp_delete_post( $leasingID );									
			}
		}			
	}
	
	public function update_leasing_special_date_column(){			
		//	Check if leasing special updated or not
		$is_leasing_update 	=	 get_option( 'leasing_special_updated' );
		
		if( $is_leasing_update != 1){			
			$leasing_special = array(
				'posts_per_page'   =>	-1,
				'post_type'        =>	'leasing'			
			);	
			
			
			$cc_query	=	new WP_Query( $leasing_special );			
			
			foreach( $cc_query->posts as $lSepcial){				
				$expire_date 	=	get_post_meta( $lSepcial->ID,'cf_leasing_expire_date',true );
				$date_array 	=	explode(  '/', $expire_date );				
				$new_expire_date =  $date_array[2].'-'.$date_array[0].'-'.$date_array[1];	
				update_post_meta( $lSepcial->ID, 'cf_leasing_new_expire_date',$new_expire_date  );		
			}
			
			update_option( 'leasing_special_updated',1 );			
		}
	}
	
	function cf_leasing_special_update_date(  $post_id, $post, $update ) {		
		global $pr_leasing_counter;
		// If this is just a revision, don't send the email.		
		if ( wp_is_post_revision( $post_id ) )
			return;
		//	echo '<pre>'; print_r( $post );
		
		if ($post->post_type == 'leasing' &&  $pr_leasing_counter!=1 && $update == 1 && $post->post_status == 'publish' && isset( $_POST[ 'save' ])  ) {
			//	echo '<pre>'; print_r( $_POST ); exit;
						
			//	Get Expriy Date
			$expire_date 	=	sanitize_text_field($_POST['cf_leasing_expire_date']);
			$date_array 	=	explode(  '/', $expire_date );				
			$new_expire_date =  $date_array[2].'-'.$date_array[0].'-'.$date_array[1];	
			update_post_meta( $post_id, 'cf_leasing_new_expire_date',$new_expire_date  );
			
			//	Fetch Leasing Special Detail
			$prSpecial		=	$this->get_leasing_special_detail( $post_id );	
			
			//	Save Previous Leasing Special
			CF_AR_Previous_Leasing_Special::save_previous_leasing_special( $prSpecial,'Updated');			 				
			
			$pr_leasing_counter =1;
		}
	}
	
	public function get_leasing_special_detail( $leasingID = '' ){
		$publish_date 	=	get_post_meta($leasingID,'cf_leasing_publish_date', true);
		$expire_date 	=	get_post_meta($leasingID,'cf_leasing_expire_date', true);
		$desc 			=	get_post_meta($leasingID,'cf_leasing_description', true);
		$aids 			=	get_post_meta($leasingID,'cf_leasing_apartment', true);
		$aids 			=	is_array($aids) ? $aids : array($aids);												
		$prSpecial		=	array(
								'title'	 		=> get_the_title($leasingID ),
								'public_date'   => $publish_date,
								'expire_date'   => $expire_date, 
								'description'   => $desc,
								'apartment_ids' => implode( ",", $aids ),
								'leasing_id'    => $leasingID											
							);											
							
		return	$prSpecial;					
	}
	
	function cf_leasing_special_register_metabox(){
		$cf_leasing = new_cmb2_box(array(
			'id'            => 'cf_leasing_metabox',
			'title'         => __('New Leasing Special','ar'),
			'object_types'  => array('leasing')
		));
		$cf_leasing->add_field(array(
			'name' => __('Publishing Date','ar'),
			'id'   => 'cf_leasing_publish_date',
			'type' => 'text_date'			
		));
		
		$cf_leasing->add_field(array(
			'name' => __('Expire Date','ar'),
			'id'   => 'cf_leasing_expire_date',
			'type' => 'text_date',
		));
		$cf_leasing->add_field( array(
			'name'    => __('Description','ar'),
			'id'      => 'cf_leasing_description',
			'type'    => 'wysiwyg',
			'options' => array('textarea_rows'=>5),
		));
		$cf_leasing->add_field(array(
			'name'     => __('Select Apartments','ar'),
			'id'       => 'cf_leasing_apartment',
			'type'     => 'multicheck',
			'show_option_none' =>false,
			'options'          => Cf_Abr_Apartment::get_apartment_id_title_ARR()
		));
	}
}
add_action('plugins_loaded',array('CF_AR_Leasing_Special','init'));