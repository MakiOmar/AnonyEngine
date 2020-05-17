<?php 

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}


/**
 * Enqueue required scripts
 */
add_action("admin_enqueue_scripts", function (){
    wp_enqueue_media();
 $scripts = array('wp-media-uploader.min','wp-media-uploader-custom');
 
 foreach($scripts as $script){
 	
	wp_register_script( 
    $script ,
    ANOE_URI.'assets/js/wordpress-media-uploader/dist/jquery.'.$script.'.js' ,
    array('jquery'),
    filemtime(ANOE_DIR.'assets/js/wordpress-media-uploader/dist/jquery.'.$script.'.js'),
    true
  );
   
	wp_enqueue_script($script);
  }
});

//Add download's upload meta box
add_action('add_meta_boxes', function () {
    add_meta_box('diwn_keywords_excel', esc_html__('Add your keywords Excel sheet',ANOE_TEXTDOM), 'diwn_read_keywords_excel', 'keyword', 'normal', 'high');
    
    add_meta_box('diwn_keywords_template_alts', esc_html__('Template words alternatives',ANOE_TEXTDOM), 'diwn_words_alts', 'keyword_template', 'normal', 'high');  
});  

/**
 * Callback to add metabox for alternative words
 * @param object $post 
 */
function diwn_words_alts($post){
	
 do_action('parse_words_alts', $post);
 
 $groups_meta = get_post_meta( $post->ID, 'keyword_groups', true );
 
 if (!$groups_meta || empty($groups_meta)) return;
 
 $success_msg = esc_html__('Alternatives have been successfully updated',ANOE_TEXTDOM);
 $failed_msg  = esc_html__('Nothing changed',ANOE_TEXTDOM);
 
 $patterns      = $groups_meta[0];
 $alternatives  = $groups_meta[1];
 
 
 foreach ($patterns as $index => $pattern) {
 	$rel_id = 'word-element-'.$index;
 	
 	$alts = $alternatives[$index];
 	
 	$value = implode(',', $alts);
 	
 	$label = esc_html__('Words alternatives',ANOE_TEXTDOM);
   
    $button_text = esc_html__('Save',ANOE_TEXTDOM);
    
    include ANOE_DIR .'templates/word-alts.php';
 }
 
}

/**
 * Upload file
 * @param object $post 
 */
function diwn_upload_keywords_excel($post) {
 
 $metabox_id = 'diwn_keywords_excel';
 
 $image_types = array('xls','csv');
  
 $file_url = get_post_meta( get_the_ID(), 'diwn_keywords_excel', true );
 
 if(is_array($file_url)){
  
  delete_post_meta( get_the_ID(), 'diwn_keywords_excel' );
 }
 
 $ext = pathinfo($file_url, PATHINFO_EXTENSION);
 
 $label         = esc_html__('Keywords excel sheet',ANOE_TEXTDOM);
 $select_text   = esc_html__('Select your file',ANOE_TEXTDOM);
 $no_file_text  = esc_html__('No selected file',ANOE_TEXTDOM);
 $current_text  = esc_html__('Current file:',ANOE_TEXTDOM);
 $download_text = esc_html__('Download',ANOE_TEXTDOM);
 $basename      = basename($file_url);
 
 include ANOE_DIR .'templates/file-upload.php';
}

/**
 * Read data from an excel sheet input
 * @param object $post 
 */
function diwn_read_keywords_excel($post) {
 
 $metabox_id = 'diwn_keywords_excel';
 
 $file_types = array('xls','csv');
  
 $file_label      = esc_html__('Keywords excel sheet',ANOE_TEXTDOM);
 
 $keywords_label  = esc_html__('Keywords list',ANOE_TEXTDOM);
 
 $keywords_list = get_post_meta( $post->ID, 'diwan_keywords_list', true );
 
 if (is_array($keywords_list) && !empty($keywords_list))
  $keywords_list = esc_textarea(serialize($keywords_list));
 else
  $keywords_list = esc_textarea($keywords_list);
 
 include ANOE_DIR .'templates/file-read.php';
}

add_action('save_post_keyword', function ($id) {
    if(!isset($_FILES['diwn_keywords_excel']) || empty($_FILES['diwn_keywords_excel'])) return;
    
    $keywords = read_excel_data('diwn_keywords_excel');
   
    if(empty($keywords) || !is_array($keywords)) return;
    
    $keywords_list = get_post_meta( $post->ID, 'diwan_keywords_list', true );
    
    if($keywords === $keywords_list) return;
  
 	update_post_meta($id, 'diwan_keywords_list', $keywords);
    
});

add_action('save_post_keyword_template', function ($id, $post) {

 $content = $post->post_content;
 
 if($content === '') return;
 
 $new_word_list = diwan_read_keyword_groups($content);

 
 if(empty($new_word_list)) return;
 
 $new_patterns     = $new_word_list[0];
 $new_alternatives = $new_word_list[1];
 
 //Get template list of alternatives
 $old_word_list = get_post_meta( $id, 'keyword_groups', true );
  
 if(empty($old_word_list) || !is_array($old_word_list)){
 	
   return update_post_meta( $id, 'keyword_groups', $new_word_list );
 }
 
 if($new_word_list === $old_word_list) return;
 
 $old_patterns     = $old_word_list[0];
 $old_alternatives = $old_word_list[1];
 
 //Note: array_diff checks if something in array1 that is not existed in array2
 
 //So we check if something new has been added to the content
 $array_diff_new = array_diff($new_patterns, $old_patterns);
 
 $word_list_update = $new_word_list;
 
 if (!empty($array_diff_new)) {
 	
 	foreach ($array_diff_new as $index => $pattern) {
 		$index = intval($index);
 		
 		array_splice($old_patterns, $index, 0, [$new_patterns[$index]]);
 		array_splice($old_alternatives, $index, 0, [$new_alternatives[$index]]);
 	}
  
  $new_word_list = [];
  $new_word_list[] = $old_patterns;
  $new_word_list[] = $old_alternatives;
  
  $word_list_update = $new_word_list;
  
  
 }
 
 //So we check if something missing from the content
 $array_diff_missing = array_diff($old_patterns, $new_patterns);
 
 if(!empty($array_diff_missing)){
 	
 	$old_patterns     = $old_word_list[0];
 	$old_alternatives = $old_word_list[1];
 	
 	foreach ($array_diff_missing as $index => $pattern) {
 		
 		$index = intval($index);
 		
 		unset($old_patterns[$index]);
 		unset($old_alternatives[$index]);
 	}
 	
	$new_word_list = [];
	$new_word_list[] = $old_patterns;
	$new_word_list[] = $old_alternatives;

	$word_list_update = $new_word_list;
 }
 return update_post_meta( $id, 'keyword_groups', $word_list_update);

 
}, 10, 2);