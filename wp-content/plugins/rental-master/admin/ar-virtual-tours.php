<?php
class AR_Virtual_Tours{
	
	public static function init(){
        $class = __CLASS__;
        new $class;
    }
	
	public function __construct(){
		add_action('add_meta_boxes',array($this,'virtual_tours_add_meta_box'));
		add_action('save_post',array($this,'abr_virtual_tours_save'),1, 2);
		add_action('wp_ajax_ar_virtual_tour_unzip_ajax',array($this,'ar_virtual_tour_unzip_ajax'));
		add_action('wp_ajax_nopriv_ar_virtual_tour_unzip_ajax',array($this,'ar_virtual_tour_unzip_ajax'));
		 $version	=	get_option('abtrv', false);
		if(($version==1)||($version==2)){
			add_action( 'init',array($this,'abr_virtualtour_post_type'), 0 );
		}
		add_action('admin_print_scripts',array($this,'abr_virtual_tour_admin_scripts'));
		add_action('admin_print_styles',array($this,'abr_virtual_tour_admin_styles'));
	}
	
	// Register Custom Post Type
	public static function abr_virtualtour_post_type() {
		$labels = array(
			'name'                  => _x( 'Virtual Tours', 'Post Type General Name', 'cf' ),
			'singular_name'         => _x( 'Virtual Tour', 'Post Type Singular Name', 'cf' ),
			'menu_name'             => __( 'Virtual Tours', 'cf' ),
			'name_admin_bar'        => __( 'Virtual Tours', 'cf' ),
			'archives'              => __( 'Item Archives', 'cf' ),
			'parent_item_colon'     => __( 'Parent Item:', 'cf' ),
			'all_items'             => __( 'Manage Virtual Tours', 'cf' ),
			'add_new_item'          => __( 'Add New VT', 'cf' ),
			'add_new'               => __( 'Add Virtual Tour', 'cf' ),
			'new_item'              => __( 'New VT', 'cf' ),
			'edit_item'             => __( 'Edit VT', 'cf' ),
			'update_item'           => __( 'Update VT', 'cf' ),
			'view_item'             => __( 'View VT', 'cf' ),
			'search_items'          => __( 'Search Virtual Tours', 'cf' ),
			'not_found'             => __( 'Not found', 'cf' ),
			'not_found_in_trash'    => __( 'Not found in Trash', 'cf' ),
			'featured_image'        => __( 'Featured Image', 'cf' ),
			'set_featured_image'    => __( 'Set featured image', 'cf' ),
			'remove_featured_image' => __( 'Remove featured image', 'cf' ),
			'use_featured_image'    => __( 'Use as featured image', 'cf' ),
			'insert_into_item'      => __( 'Insert into item', 'cf' ),
			'uploaded_to_this_item' => __( 'Uploaded to this item', 'cf' ),
			'items_list'            => __( 'Items list', 'cf' ),
			'items_list_navigation' => __( 'Items list navigation', 'cf' ),
			'filter_items_list'     => __( 'Filter items list', 'cf' ),
		);
		$args = array(
			'label'                 => __( 'Virtual Tour', 'cf' ),
			'description'           => __( 'Virtual Tours', 'cf' ),
			'labels'                => $labels,
			'supports'              => array( 'title', 'editor', 'thumbnail', 'custom-fields', ),
			'hierarchical'          => false,
			'public'                => true,
			'show_ui'               => true,
			'show_in_menu'          => true,
			'menu_icon'             => ABR_PLUGIN_DIR_URL.'img/vr.png',
			'show_in_admin_bar'     => true,
			'show_in_nav_menus'     => true,
			'can_export'            => true,
			'has_archive'           => true,		
			'exclude_from_search'   => true,
			'publicly_queryable'    => true,
			'capability_type'       => 'post',
		);
		register_post_type( 'virtualtour', $args );
	}
	
	public static function virtual_tours_add_meta_box() {
		add_meta_box('virtual_tours-virtual-tours',__('Virtual Tours','virtual_tours'),array($this,'abr_virtual_tours_html'),'virtualtour','normal','default');
	}
	
	public static function abr_get_image_id($image_url) {
		global $wpdb;
		$attachment = $wpdb->get_col($wpdb->prepare("SELECT ID FROM $wpdb->posts WHERE guid='%s';", $image_url)); 
		if(isset($attachment[0])){
			return $attachment[0];
		}
	}
	
	public static function ar_virtual_tour_unzip_ajax(){
		if(isset($_REQUEST)){
			WP_Filesystem();
			$zipurl				=	$_REQUEST['zipurl'];
			$zipbasename		=	basename($zipurl);
			$zipID				=	AR_Virtual_Tours::abr_get_image_id($zipurl);
			$zipLoc				=	get_attached_file($zipID);
			$destination 		=	wp_upload_dir();
			$destination_path	=	$destination['path'];
			$unzipfile 			=	unzip_file($zipLoc, $destination_path);
			if($unzipfile==1){echo 'Successfully unzipped the file!';}
			else {echo 'There was an error unzipping the file.';}
		}
		die();
	}
	
	public static function abr_virtual_tours_html($post){
		global $post;
		global $wpdb;
		// Noncename needed to verify where the data originated
    	echo '<input type="hidden" name="podcastmeta_noncename" id="podcastmeta_noncename" value="'.wp_create_nonce(plugin_basename(__FILE__)).'" />';
		$strFile 		=	get_post_meta($post -> ID, $key = 'podcast_file', true);
		$media_file	=	get_post_meta($post -> ID, $key = '_wp_attached_file', true);
		if(!empty($media_file)){ $strFile = $media_file; } ?>
        <script type = "text/javascript">
		var file_frame;
		jQuery('#upload_image_button').live('click',function(podcast){
			podcast.preventDefault();
			if(file_frame){file_frame.open();return}
			file_frame=wp.media.frames.file_frame=wp.media({
				title:jQuery(this).data('uploader_title'),button:{text:jQuery(this).data('uploader_button_text'),},multiple:false
			});
			file_frame.on('select', function(){
				attachment = file_frame.state().get('selection').first().toJSON();
				var url = attachment.url;
				var field = document.getElementById("podcast_file");
				field.value = url;
				zipurl=field.value;				
				jQuery.ajax({
					type:'POST',url:'<?php echo admin_url('admin-ajax.php'); ?>',
					data:{'action':'ar_virtual_tour_unzip_ajax','zipurl':zipurl},
					success:function(data){
						alert(data);
					}
				});
			});file_frame.open();
		});
    </script>
    <div>
	<table id="upvtr">
		<tr valign = "top">
			<td>
				<input type = "text" name = "podcast_file" class="upvtrtxt" id="podcast_file" size = "70" value = "<?php echo $strFile; ?>" />
				<input id = "upload_image_button" type = "button" value = "Upload">
				<input type="button" value="Process" class="upvtrpro" style="display:none">
			</td>
		</tr>
	</table>
    <input type = "hidden" name = "img_txt_id" id = "img_txt_id" value = ""/>
    </div>
	<?php
	}
    	
	public static function abr_virtual_tours_save($post_id,$post){
		if (!wp_verify_nonce(@$_POST['podcastmeta_noncename'], plugin_basename(__FILE__))) {
			return $post -> ID;
		}
    	if (!current_user_can('edit_post', $post -> ID))
			return $post -> ID;
		$podcasts_meta['podcast_file'] = $_POST['podcast_file'];
		foreach($podcasts_meta as $key => $value) {
			if ($post -> post_type == 'revision') return;
			$value = implode(',', (array) $value);
			if(get_post_meta($post -> ID, $key, FALSE)) { // If the custom field already has a value it will update
				update_post_meta($post -> ID, $key, $value);
			}
			else {
				// If the custom field doesn't have a value it will add
				add_post_meta($post -> ID, $key, $value);
			}
			if (!$value) delete_post_meta($post -> ID, $key); // Delete if blank value
		}
	}
	
	function abr_virtual_tour_admin_scripts() {
        wp_enqueue_script('media-upload');
        wp_enqueue_script('thickbox');
    }
	
    function abr_virtual_tour_admin_styles(){
		wp_enqueue_style('thickbox'); 
	}
}
add_action('plugins_loaded',array('AR_Virtual_Tours','init'));