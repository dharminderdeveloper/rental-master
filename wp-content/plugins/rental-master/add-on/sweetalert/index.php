<?php
/*---------------------*/
/* Add sweet alert js */
/*-------------------*/

add_action('wp_enqueue_scripts','abr_add_sweetalert_js');
add_action('admin_enqueue_scripts','abr_add_sweetalert_js');
function abr_add_sweetalert_js(){
	wp_register_script('sweet-js',ABR_PLUGIN_DIR_URL.'/add-on/sweetalert/sweetalert.min.js');
	wp_enqueue_script('sweet-js');
}
/*----------------------*/
/* Add sweet alert css */
/*--------------------*/
add_action('wp_enqueue_scripts','abr_add_sweetalert_css');
add_action('admin_enqueue_scripts','abr_add_sweetalert_css');
function abr_add_sweetalert_css(){
	wp_enqueue_style('sweet-css',ABR_PLUGIN_DIR_URL.'/add-on/sweetalert/sweetalert.css');
}
?>