<?php
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
    
    if (isset($keywords_list[$word_element_alt] ) && $keywords_list[$word_element_alt]['index'] == $word_element_index ) {
        
        $keywords_list[$word_element_alt]['alts'] = array_map('trim', explode(',', $word_element_alternatives)) ;
        
        $updated =  update_post_meta( intval($post_id), 'keyword_groups', $keywords_list );
        
        $msg = ($updated) ? 'success' : 'failed';
        
        wp_send_json(
                [
                    'result' => $msg,
                ]
            ); die();
    }
    

}