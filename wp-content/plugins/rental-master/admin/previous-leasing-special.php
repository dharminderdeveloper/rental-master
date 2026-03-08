<?php
if(!defined('ABSPATH')) exit;
class CF_AR_Previous_Leasing_Special{
	public static function init(){
        $class = __CLASS__;
        new $class;
    }
	
	public function __construct(){
		$version	=	get_option('abtrv', false);
		if(($version==1)||($version==2)){
			add_action( 'admin_menu',array($this,'abr_previous_leasing_special_menu'));		
			add_action('init',array($this,'abr_previous_leasing_special_init'));
		}		
		
		add_action('cmb2_admin_init',array($this,'cf_previous_leasing_special_register_metabox'));
		
	}
	
	/**
	 *	@description	This function will register "Previous Leasing Special"
	 *					custom post type. 
	 *
	 */
	
	function abr_previous_leasing_special_init(){
		$labels = array(
			'name'               	=> _x( 'Previous Leasing Specials', 'previous-leasing','ar' ),
			'singular_name'      	=> _x( 'Previous Leasing Specials', 'previous-leasing','ar' ),
			'menu_name'          	=> _x( 'Previous Leasing Specials', 'admin menu','ar' ),
			'name_admin_bar'     	=> _x( 'Previous Leasing Specials', 'add new on admin bar','ar'),
			'add_new'            	=> _x( 'Add New', 'leasing','ar' ),
			'add_new_item'       	=> __( 'Add New Leasing Specials','ar' ),
			'new_item'           	=> __( 'New Leasing Specials','ar' ),
			'edit_item'          	=>
			__( 'Edit Leasing Specials','ar' ),
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
			'public'             	=> false,
			'publicly_queryable' 	=> true,
			'show_ui'            	=> false,
			'show_in_menu'      	=> false,
			'query_var'         	=> 'previous-leasing',
			'rewrite'           	=> array('slug'=>'previous-leasing'),
			'capability_type'    	=> 'post',
			'has_archive'        	=> true,
			'hierarchical'       	=> true,
			'supports'           	=> array('title'),
			'menu_icon'			=> ABR_PLUGIN_DIR_URL.'img/leasing.png',
		);
		
		register_post_type('previous-leasing',$args);		
		
	}	
	
	/**
	 *	@description 	This function will save the previous leasing special with differ status.
	 *
	 *	@param 			Array 		This array will contain all the basic fields of previous leasing special
	 *					Status		This will be status for saving the previous leasing special	
	 *
	 *	@return 		POST ID 	This will be Id of newly inserted record.
	 *					
	 */				
	public function save_previous_leasing_special($plSpecial, $status ){
		$post_data = array(
				'post_title' 	=> $plSpecial[ 'title' ],
				'post_content' 	=> $plSpecial[ 'description' ],
				'post_type' 	=> 'previous-leasing',
		);
		
		$post_id = wp_insert_post( $post_data );
		
		if( $post_id ){
			// Save Meta Data
			update_post_meta($post_id,'cf_previous_leasing_publish_date', $plSpecial[ 'public_date' ] );
			update_post_meta($post_id,'cf_previous_leasing_expire_date', $plSpecial[ 'expire_date' ] );
			update_post_meta($post_id,'cf_previous_leasing_description', $plSpecial[ 'description' ] );
			update_post_meta($post_id,'cf_previous_leasing_apartment', $plSpecial[ 'apartment_ids' ] );
			update_post_meta($post_id,'cf_previous_leasing_id', $plSpecial[ 'leasing_id' ] );			
			update_post_meta($post_id,'cf_previous_leasing_status', $status );			
		}	
		
		return $post_id;		
	}
		
	/**
	 *	@description 	Add Sub Menu to Leasing Special named as 
	 *					"Previous Leasing Special"	
	 *	
	 */

	function abr_previous_leasing_special_menu(){		
		add_submenu_page( 'edit.php?post_type=leasing', __('Previous Leasing Special','ar'), __('Previous Leasing Special','ar'),'manage_options', 'previous-leasing-special',array($this,'cf_previous_leasing_special'));
	}

	function cf_previous_leasing_special(){
		?>
		<h1><?php echo __('Previous Leasing Special','ar'); ?></h1>
		<div class="pr-leasing-special"">
		<?php echo CF_AR_Previous_Leasing_Special::abr_previous_leasing_special_content();?>
		</div><!--/*welcome-panel Ends*/-->
		<?php
	}

	/**
	 * Function: abr_previous_leasing_special_content
	 *
	 * Description: Display Page Content
	 */

	function abr_previous_leasing_special_content(){
		//	Get All Expired Leasing Special
		$today = date('m/d/Y');
		$cc_args = array(
			'posts_per_page'   =>	-1,
			'post_type'        =>	'previous-leasing'			
		);		
		
		$previous_leasing_list	=	new WP_Query( $cc_args );			
	
		
?>
		<ul class="availHead">
			<li class="welcome-panel">
			<span><?php echo __('Name','ar'); ?></span>
			<span><?php echo __('Publish Date','ar'); ?></span>
			<span><?php echo __('Expired Date','ar'); ?></span>
			<span><?php echo __('Log Status','ar'); ?></span>
			<span><?php echo __('Description','ar'); ?></span>
			<span><?php echo __('Action','ar'); ?></span>			
			</li>
<?php
			if( $previous_leasing_list->posts  <= 0 ){
?>
				
<?php				
			}else{										
				foreach( $previous_leasing_list->posts as $listing ){						
					$publish_date 	=	get_post_meta($listing->ID,'cf_previous_leasing_publish_date', true);
					$expire_date 	=	get_post_meta($listing->ID,'cf_previous_leasing_expire_date', true);	
					$desc 			=	get_post_meta($listing->ID,'cf_previous_leasing_description', true);
					$status			=	get_post_meta($listing->ID,'cf_previous_leasing_status', true);
	
?>			
					<li pid="<?php echo $listing->ID; ?>">
					<span><a href="<?php echo get_permalink( $listing->ID ); ?>" > <?php echo __($listing->post_title,'ar'); ?> </a></span>
					<span><?php echo $publish_date;  ?></span>
					<span><?php echo $expire_date;  ?></span>
					<span><?php echo ucfirst($status);	?></span>					
					<span><?php echo $desc;  ?></span>					
					<span><a href="javascript:void(0)" class="button button-primary button-large updateApartment" ><?php echo __('Update','ar');?></a></span>
					</li>
<?php
				}
			}
?>				
		</ul>
<?php	
	}
	
	/**
	 * Function: abr_previous_leasing_special_content
	 *
	 * Description: Display Page Content
	 */
	
	function cf_previous_leasing_special_register_metabox(){
		$cf_leasing = new_cmb2_box(array(
			'id'            => 'cf_leasing_metabox',
			'title'         => __('New Leasing Special','ar'),
			'object_types'  => array('leasing')
		));
		$cf_leasing->add_field(array(
			'name' => __('Publishing Date','ar'),
			'id'   => 'cf_leasing_publish_date',
			'type' => 'text_date',
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

add_action('plugins_loaded',array('CF_AR_Previous_Leasing_Special','init'));