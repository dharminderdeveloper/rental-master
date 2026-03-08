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
			add_action( 'wp_ajax_download_leasing',array($this,'downloadLesingFile' ) );
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
	public static function save_previous_leasing_special($plSpecial, $status ){
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
		
		<div class="pr-leasing-header">
			<div class="pr-leasing-left"><h1><?php echo __('Previous Leasing Special','ar'); ?></h1></div>	
			<div class="pr-leasing-right"><input type="button" class="button" id="download_pr_csv" value="Export Previous Leasing" /></div>	
		</div>
		<div class="clear"></div>
																				
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
		$paged = ( isset( $_GET['paged'] ) ) ? $_GET['paged']: '1';	

		$cc_args = array(
			'paged'            =>   $paged,
    		'posts_per_page'   =>   '-1',
			'post_type'        =>	'previous-leasing'			
		);		
		
		$previous_leasing_list	=	new WP_Query( $cc_args );			

		$fileName 	=	ABR_PLUGIN_DIR_PATH.'admin/premium/downloadablefiles/';		
		$file = fopen($fileName.'previousleasing.csv', 'w');
		 
		// save the column headers
		fputcsv($file, array('Name', 'Publish Date', 'Expired Date', 'Log Status', 'Description'));		
?>
		<script type="text/javascript">
			jQuery( document ).ready(function(){		
				jQuery( '#download_pr_csv' ).click(function(){
					window.location.href = '<?php echo admin_url('admin-ajax.php'); ?>?action=download_leasing';					
				});
				
				jQuery( ".listing_read_more" ).click(function(){						
					//	Get Listing Title
					var lTitle 	=	jQuery( this ).attr( 'listing_title' );

					//	Get description Id Variable
					var descID 	=	jQuery( this ).attr( 'desc_id' );

					//	Get Original Description
					var oDescription =	jQuery( '#'+descID ).val();	
										
					swal({
						  title: "<i>"+lTitle+"</i>", 
						  html: ""+oDescription,  
						  confirmButtonText: "OK", 
					});		
				});
			});
		</script>		
		<ul class="availHead">
			<li class="welcome-panel">
				<span class="minWidth"><?php echo __('Name','ar'); ?></span>
				<span class="minWidth"><?php echo __('Publish Date','ar'); ?></span>
				<span class="minWidth"><?php echo __('Expired Date','ar'); ?></span>
				<span class="minWidth"><?php echo __('Log Status','ar'); ?></span>
				<span class="minWidth"><?php echo __('Description','ar'); ?></span>
				<!--<span><?php echo __('Action','ar'); ?></span>-->			
			</li>
<?php
			if( $previous_leasing_list->posts  <= 0 ){
?>
				
<?php				
			}else{														
				foreach( $previous_leasing_list->posts as $listing ){	
					//	Get Previous Leasing Title
					$prTitle 		=	__($listing->post_title,'ar');		

					$publish_date 	=	get_post_meta($listing->ID,'cf_previous_leasing_publish_date', true);										
					$expire_date 	=	get_post_meta($listing->ID,'cf_previous_leasing_expire_date', true);					

					$oDesc 			=	get_post_meta($listing->ID,'cf_previous_leasing_description', true);

					//	Strip All Images	
    				$desc 			= preg_replace("/<img[^>]+\>/i", " ", $oDesc); 

					//	Count length of the string
					$descLength 	=	strlen( $oDesc );

					//	Encode Special Chars	
					$oDesc 			=	htmlspecialchars( $oDesc );

					if( $descLength > 100){
						$readMore 	=	'<a class="listing_read_more" listing_title="'.__($listing->post_title,'ar').'" desc_id="listing'.$listing->ID.'" href="#">Read More</a>';
					}else{
						$readMore 	=	'';
					}

					$status			=	get_post_meta($listing->ID,'cf_previous_leasing_status', true);

					$csvRow			=	array( $prTitle,$publish_date,$expire_date,$status,$desc );

					fputcsv($file, $csvRow);
?>			
					<input type="hidden" id="listing<?php echo $listing->ID; ?>" value="<?php echo $oDesc; ?>" /> 
					<li pid="<?php echo $listing->ID; ?>" style="min-width:20% !important;">
						<!--<span><a href="<?php echo get_permalink( $listing->ID ); ?>" > <?php echo __($listing->post_title,'ar'); ?> </a></span>-->
						<span class="minWidth"><?php echo $prTitle; ?></span>
						<span class="minWidth"><?php echo $publish_date;  ?></span>
						<span class="minWidth"><?php echo $expire_date;  ?></span>
						<span class="minWidth"><?php echo ucfirst($status);	?></span>					
						<span class="minWidth"><?php echo substr($desc,0,100);  ?><?php echo ' '.$readMore; ?></span>					
						<!--<span><a href="javascript:void(0)" class="button button-primary button-large updateApartment" ><?php echo __('Update','ar');?></a></span>-->
					</li>
<?php
				}
			
				echo '<li>';
				$big = 999999999; // need an unlikely integer
				$translated = __( 'Page', 'ar' ); // Supply translatable string				
				
				echo paginate_links( array(
					'base' => admin_url( 'edit.php?post_type=leasing&page=previous-leasing-special&paged=%#%' ),				
					'current' => max( 1, $paged ),
					'total'   => $previous_leasing_list->max_num_pages,
				        'before_page_number' => '<span class="screen-reader-text">'.$translated.' </span>'
				) );
				echo '</li>';
			}

			// Close the file
			fclose($file);
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

	/**
	 *	@description 	This function will write the previous leasing 
	 *					records and make a csv file for same.
	 *	
	 *	@param 			Record 	This will be a array of record to insert
	 * 	
	 * 	@return 		True 	When csv will be created
	 *					False 	When csv will not be created	
	 */

	public function writeLeasingRecords( $record = '' ){

	}

	/**
	 *	@description 	This function will download the csv file for previous
	 *					leasing special records.
	 *
	 *	@param 			File 	This will be the file name that needs to be 
	 * 				 	downloaded.
	 *
	 * 	@return 		True 	When csv will be created
	 *					False 	When csv will not be created	
	 */

	public function downloadLesingFile(){		
		$file  = ABR_PLUGIN_DIR_PATH.'admin/premium/downloadablefiles/previousleasing.csv';

		if(file_exists($file)){
            header('Content-Description: File Transfer');
            header('Content-Type: text/csv');
            header('Content-Disposition: attachment; filename='.basename($file));
            header('Content-Transfer-Encoding: binary');
            header('Expires: 0');
            header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
            header('Pragma: public');
            header('Content-Length: ' . filesize($file));
            ob_clean();
            flush();
            readfile($file);
            exit;
		}
	}
}

add_action('plugins_loaded',array('CF_AR_Previous_Leasing_Special','init'));