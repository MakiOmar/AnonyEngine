<?php 

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

function diwan_ajax_word_alts($meta_key){
    if(empty($_POST) ) return;
    
    extract($_POST);
    
    if(!isset($post_id) || $post_id === '') return;

    
    $keywords_list = get_post_meta( intval($post_id), $meta_key , true );//'content_keyword_groups'
    
    if(empty($keywords_list)) return;
    
    
    if(!isset($word_element_index)         || $word_element_index === '') return;
    if(!isset($word_element_alt)           ||  $word_element_alt === '') return;
    if(!isset($word_element_alternatives)  ||  $word_element_alternatives === '') return;
    
    $patterns      = $keywords_list[0];
    $alternatives  = $keywords_list[1];
    
    $oldAlts = $alternatives[$word_element_index];
    
    $newAlts = array_map('trim', explode(',', $word_element_alternatives));
    
    $alternatives[$word_element_index] = array_merge($oldAlts, $newAlts);
    
    $keywords_list = [$patterns, $alternatives];
    
    
    $updated =  update_post_meta( intval($post_id), $meta_key, $keywords_list );
    
    $msg = ($updated) ? 'success' : 'failed';
        
    wp_send_json(
            [
                'result' => $msg,
            ]
        );
    
    die();
}
//Update words alternatives
add_action('wp_ajax_content_keyword_groups', function(){
    
    diwan_ajax_word_alts('content_keyword_groups');
});

//Update words alternatives
add_action('wp_ajax_title_keyword_groups', function(){
    
    diwan_ajax_word_alts('title_keyword_groups');
});

