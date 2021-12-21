<?php

$share_by_email = false;

if (!$share_by_email) return;

require_once 'ajax.php';

add_action( 'wp_enqueue_scripts', function(){
    $path = ANOE_UTLS_DIR . 'share-by-email/';
    $uri  = ANOE_UTLS_URI . 'share-by-email/';
        

    wp_enqueue_style( 'anony-sharebyemail' , $uri . 'share-form-style.css',false, filemtime($path . 'share-form-style.css') );

    wp_register_script( 'anony-sharebyemail' , $uri . 'share-script.js',['jquery'], filemtime($path . 'share-script.js') );
});

add_action( 'anony_after_page_footer' , function(){

    $titel = esc_html__('Share by email', ANOE_TEXTDOM);
    $subtitle = esc_html__('Please write down client\'s email address', ANOE_TEXTDOM);
    $submit_txt = esc_html__('Share', ANOE_TEXTDOM);
    
    $page_title  =  '';
    $description =  '' ;
    $permalink   = '' ;

    $q_obj =  get_queried_object();

    if(is_archive()){
        $page_title  = is_object($q_obj) && isset($q_obj->name) ? esc_html($q_obj->name) : '';
        $description = is_object($q_obj) && isset($q_obj->description) ? esc_html($q_obj->description) : '' ;
        $permalink   = esc_url(get_term_link($q_obj)) ;
    }

    $og_featured_image = 'https://cdn.hipwallpaper.com/i/7/70/cmMCyB.jpg';

    if(empty($og_featured_image)){
        $og_featured_image = ANOE_UTLS_URI . 'email-bg.jpg';
    }

    include 'share-form.php';
});

?>
