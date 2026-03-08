<?php
if(!defined('ABSPATH')) exit;
class CF_AR_Apartments_Of_Spec_Comm{
	public static function init(){
        $class = __CLASS__;
        new $class;
    }

 	public function __construct(){
		add_shortcode('ar_apartments_Of_Specific_Community',array($this,'abr_specific_community_apartments'));
	}

	public  function abr_specific_community_apartments(){
		if(isset($_GET['cmid']) && !empty($_GET['cmid'])){
			$cmid = sanitize_text_field($_GET['cmid']);
			$args = array(
				'orderby'		=>	'date',	
				'order'			=>	'DESC',
				'post_type'	=>	'apartment',
				'post_status'	=>'publish',
				'meta_query' 	=> array( 
					array(
						'key' 		=> 'cf_apartment_community',
						'value'		=> $cmid,
						'compare'	=> 'IN' )),
				'suppress_filters' => true);
			$myposts	=	get_posts($args);
			$newArr		=	array();
			foreach($myposts as $post){
				$pid	=	$post->ID;
				array_push($newArr,$pid);
			}
			if($myposts){
				global $wp_query;
				$nowpage	=	get_query_var( 'paged', 1 );
				$data		=	$newArr;
				$page		=	$nowpage;
				$total		=	count($data);
				$limit		=	5;
				if(!$limit)
				$limit		=	5;
				$totalPages 	=	ceil( $total/ $limit );
				$mpage 			=	max($page, 1);
				$page 		=	min($mpage, $totalPages);
				$offset 	=	($page - 1) * $limit;
				if( $offset < 0 ) $offset = 0;
				$myposts = array_slice($data,$offset,$limit);
				$range=  (int) $totalPages+1;
				$out='';
				$out.='<ul class="schaprts">';
				foreach($myposts as $mpost){
					$out.=abr_apartment_post($mpost);
				}
				$out.='</ul>';
				$out.=abr_cf_num_pagination($totalPages);
				return $out;
			}
		}
	}

}
add_action('plugins_loaded',array('CF_AR_Apartments_Of_Spec_Comm','init'));