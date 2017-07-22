<?php
function custom_enqueue_script() {
	wp_enqueue_script( 'my-js', get_stylesheet_directory_uri() .'/js/custom.js', false );
}

add_action( 'wp_enqueue_scripts', 'custom_enqueue_script' );

// Disable customizer
add_action('after_setup_theme','child_theme_init');
	function child_theme_init(){
   		remove_action('init', 'et_customizer_init');
}	

function remove_revslider_meta_tag() {
 
    return '';
    
}
 
add_filter( 'revslider_meta_generator', 'remove_revslider_meta_tag' );

add_filter('ae_is_mobile', 'cs_fre_disable_mobile');
function cs_fre_disable_mobile() {
 return false;
}