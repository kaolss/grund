<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

include_once( get_template_directory() . '/lib/init.php' );
define( 'CHILD_THEME_NAME', 'KoBoToLo' );
define( 'CHILD_THEME_VERSION', '0.0.1' );

add_action( 'wp_enqueue_scripts', 'theme_enqueue_styles' );
function theme_enqueue_styles() {
    wp_enqueue_style( 'parent-style', get_template_directory_uri() . '/style.css' );
//    wp_enqueue_style( 'print-style', get_stylesheet_directory_uri() . '/print.css' ,array(), '0.01', 'print');
    wp_enqueue_style( 'child-style', get_stylesheet_directory_uri() . '/style.css', array('parent-style'));
    wp_enqueue_style( 'theme-style', CHILD_URL .  '/kobotolo.less' ,array('child-style'));
    wp_enqueue_script( 'responsive-menu', CHILD_URL .  '/js/responsive-menu.js' ,array('jquery'), '0.5');
    wp_enqueue_style( 'dashicons' );
    
}

add_theme_support( 'html5' );
add_theme_support( 'genesis-responsive-viewport' );
add_theme_support( 'genesis-footer-widgets', 3 );
add_theme_support( 'post-thumbnails');
add_theme_support( 'genesis-post-format-images' );
add_theme_support( 'genesis-structural-wraps', array(
	'header',
	'nav',
	'subnav',
	'site-inner',
	'content',
	'footer-widgets',
	'footer'
) );

/****************************************************************/
/******************** Images  ***********************************/
/****************************************************************/
add_action( 'init', 'add_image_sizes' );
function add_image_sizes() {
	add_image_size('top1', 220, 220, TRUE);
	add_image_size('top1-dubbel', 460, 460, TRUE);
	add_image_size('erbj', 460, 300, TRUE);
}

function filter_image_sizes( $sizes) {   
    unset( $sizes['top1']);
    unset( $sizes['top1-dubbel']);
    unset( $sizes['erbj']);
    return $sizes;
}
add_filter('intermediate_image_sizes_advanced', 'filter_image_sizes');
add_filter( 'genesis_footer_creds_text', 'kobotolo_footer_creds_text' );
function kobotolo_footer_creds_text() {
	echo '<div class="creds">';
//change this to first post
	echo '<p><span class="cprighthide">Copyright © 2012-'.date('Y').'</span>';
	echo ' &middot; <a href="'.get_site_url().'">'.get_bloginfo().'</a> &middot; Webbproduktion <a href="http://www.kobotolo.se" title="KoBoToLo">KoBoToLo</a>';
	echo '</p></div>';	
}

/****************************************************************/
/******************** Favicon   *********************************/
/****************************************************************/
add_filter( 'genesis_pre_load_favicon', 'kobotolo_custom_favicon' );
function kobotolo_custom_favicon( ) {
	return ''. trailingslashit( get_bloginfo('url') ) .'favicon.ico';
}

/****************************************************************/
/******************** Check if image size exists   **************/
/****************************************************************/
add_filter('image_downsize', 'kobotolo_media_downsize', 10, 3);
function kobotolo_media_downsize($out, $id, $size) {
    if (is_admin()) return;    
    $imagedata = wp_get_attachment_metadata($id); 
    if (is_array($imagedata) && isset($imagedata['sizes'][$size]))
    return false;
    
    global $_wp_additional_image_sizes;
    if (!isset($_wp_additional_image_sizes[$size])) return false;
    $filepath=get_attached_file($id);
        
    $resized = image_make_intermediate_size(
	    $filepath,
	    $_wp_additional_image_sizes[$size]['width'],
	    $_wp_additional_image_sizes[$size]['height'],
	    $_wp_additional_image_sizes[$size]['crop']
            );
    if (!$resized )	return false;

    $imagedata['sizes'][$size] = $resized;
    wp_update_attachment_metadata($id, $imagedata);

    $att_url = wp_get_attachment_url($id);
    return array(dirname($att_url) . '/' . $resized['file'], $resized['width'], $resized['height'], true);
}

/****************************************************************/
/********************** Handle Cache ****************************/
/****************************************************************/
add_action( 'save_post', 'delete_cache' );
add_action(  'transition_post_status',  'post_published', 10, 3 );
function post_published( $new_status, $old_status, $post ) {
    if (   $old_status !== 'publish' && $new_status == 'publish' ) {
	delete_cache($post);
        // A function to perform actions when a post status changes from publish to any non-public status.
    }
}

function removeDirectory($path) {
 	$files = glob($path . '/*');
	foreach ($files as $file) {
		is_dir($file) ? removeDirectory($file) : unlink($file);
	}
	rmdir($path);
 	return;
}


function delete_cache( $post_id ) {
    $domain = $_SERVER['HTTP_HOST'];
    error_log($domain);
    $domain2 =get_home_url();
    
    $domain2=str_replace("http://","",$domain2);
    error_log($domain2);
    $domain3="";
    if ($domain!==$domain2) :
        $domain3=str_replace($domain,"",$domain2);
        error_log('olika'.$domain3);
    endif;
    
    
    $s = WP_CONTENT_DIR.'/cache/all/'.$domain3;
    error_log($s);
    $post = get_post($post_id);
    $t=$s.$post->post_name.'/index.html';
    
    
    if (file_exists ( $t )) :
	unlink($t);
    endif;

    $post_categories =  wp_get_post_categories( $post_id);
    foreach($post_categories as $c){
	$cat = get_category( $c );
	$t=$s.'/wp-content/cache/all/'.$cat->slug;
	if (is_dir($t)) {
	    removeDirectory($t);
	    error_log('direktoryt finns category = '.$cat->slug, 0);
	}
	else {
	    $t=$s.'/category/'.$cat->slug;
	    if (is_dir($t)) {
		removeDirectory($t);
		error_log('direktoryt finns under category = '.$cat->slug, 0);
	    
	    }

	}
	
	    }
    $post_tags = wp_get_post_tags( $post_id);
    if ($post_tags) :
	foreach($post_tags as $c){
	$t=$s.'wpkol/wp-content/cache/all/tag/'.$c->slug;
	if (is_dir($t)) {
	    removeDirectory($t);
	    error_log('direktoryt finns tag = '.$c->slug, 0);
	}
	else {
	    $t=$s.'/tag/'.$c->slug;
	    if (is_dir($t)) {
		removeDirectory($t);
		error_log('direktoryt finns under category = '.$cat->slug, 0);	    
	    }
	}
    }
    endif;  
}
