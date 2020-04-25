<?php 

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}


//Update words alternatives
add_action('wp_ajax_diwan_update_alts', 'diwan_update_alts');

function diwan_update_alts(){
    if(empty($_POST) ) return;
    
    extract($_POST);
    
    if(!isset($post_id) || $post_id === '') return;

    
    $keywords_list = get_post_meta( intval($post_id), 'keyword_groups', true );
    
    if(empty($keywords_list)) return;
    
    
    if(!isset($word_element_index)         || $word_element_index === '') return;
    if(!isset($word_element_alt)           ||  $word_element_index === '') return;
    if(!isset($word_element_alternatives)  ||  $word_element_alternatives === '') return;
    
    $patterns      = $keywords_list[0];
    $alternatives  = $keywords_list[1];
    
    $oldAlts = $alternatives[$word_element_index];
    
    $newAlts = array_map('trim', explode(',', $word_element_alternatives));
    
    $alternatives[$word_element_index] = array_merge($oldAlts, $newAlts);
    
    $keywords_list = [$patterns, $alternatives];
    
    $updated =  update_post_meta( intval($post_id), 'keyword_groups', $keywords_list );
    
    $msg = ($updated) ? 'success' : 'failed';
        
    wp_send_json(
            [
                'result' => $msg,
            ]
        );
    
    die();
}