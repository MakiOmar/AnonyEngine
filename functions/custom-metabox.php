<?php 

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

/**
 * Disable autosave
 */
add_action('admin_enqueue_scripts', function () {
  switch(get_post_type()) {
    case 'keyword':
    case 'keyword_template':
      wp_dequeue_script('autosave');
      break;
  }
});


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

function diwan_content_words_alts($post, $meta_key){
  $groups_meta = get_post_meta( $post->ID, $meta_key, true );
 
 if (!$groups_meta || empty($groups_meta)) return;
 
 $success_msg = esc_html__('Alternatives have been successfully updated',ANOE_TEXTDOM);
 $failed_msg  = esc_html__('Nothing changed',ANOE_TEXTDOM);
 
 $group_title  = ucfirst(str_replace('_', ' ', $meta_key));
 $save_action = $meta_key;
 
 $patterns      = $groups_meta[0];
 $alternatives  = $groups_meta[1];
 
 echo '<h1>'.$group_title . '</h1>';
 foreach ($patterns as $parent_index => $pattern) {
  $rel_id = $meta_key.'-'.$parent_index;
  
  $alts = $alternatives[$parent_index];
  
  $value = implode(',', $alts);
  
  $label = esc_html__('Words alternatives',ANOE_TEXTDOM);
   
    $button_text = esc_html__('Save',ANOE_TEXTDOM);
    
    include ANOE_DIR .'templates/word-alts.php';
 }
}

/**
 * Callback to add metabox for alternative words
 * @param object $post 
 */
function diwn_words_alts($post){
	 
  diwan_content_words_alts($post, 'title_keyword_groups');
  diwan_content_words_alts($post, 'content_keyword_groups');
 
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

function diwan_update_content_alts($id, $content, $meta_key){
  if($content === '' ) return;

  $new_word_list = diwan_read_content_keyword_groups($content);


  if(empty($new_word_list)) return delete_post_meta( $id, $meta_key);

  //Get template list of alternatives
  $old_word_list = get_post_meta( $id, $meta_key, true );

  if(empty($old_word_list) || !is_array($old_word_list)) return update_post_meta( $id, $meta_key, $new_word_list );


  if(array_values($old_word_list[0]) === array_values($new_word_list[0])) return;

  foreach ($new_word_list[0] as $index => $value) {
    
    $search_old_index = array_search($value, $old_word_list[0]);
    
    $temp_new_patterns[$index] = $value;

    $temp_new_alternatives[$index] = ($search_old_index !== false) ? $old_word_list[1][$search_old_index] : $new_word_list[1][$index];
    
  }
 
  $temp_new_word_list = [$temp_new_patterns, $temp_new_alternatives];

  update_post_meta( $id, $meta_key, $temp_new_word_list);
 
}


function diwan_update_content_alts_2($id, $content, $meta_key){
  if($content === '' ) return;

  $new_word_list = diwan_read_content_keyword_groups($content);

  if(empty($new_word_list)) return;

  //Get template list of alternatives
  $old_word_list = get_post_meta( $id, $meta_key, true );

  if(empty($old_word_list) || !is_array($old_word_list)) return update_post_meta( $id, $meta_key, $new_word_list );
  
  //Check patterns match
  if(array_values($old_word_list[0]) === array_values($new_word_list[0])) return;
  
  /*-------------------------------------------------------------------------------*/
  
  if (count($old_word_list[0]) == count($new_word_list[0])) {
    foreach ($new_word_list[0] as $index => $value) {

      $temp_new_patterns[$index] = $value;
       
      if ($old_word_list[0][$index] == $value) {
        $temp_new_alternatives[$index] = $old_word_list[1][$index];
      }else{
        $temp_new_alternatives[$index] = $new_word_list[1][$index];
      }

    }

    $temp_new_word_list = [$temp_new_patterns, $temp_new_alternatives];
    
    return update_post_meta( $id, $meta_key, $temp_new_word_list);
    
  }else{
    //We check if something new has been added to the content 
    
    $array_diff_new = ANONY_ARRAY_HELP::diffWithDupplicate( $old_word_list[0], $new_word_list[0]);
    
    nvd($array_diff_new); 

    nvd_compare($old_word_list[0], $new_word_list[0]);

    die();
    
  }

  
  
  
 
  if (!empty($array_diff_new)) {
    
    foreach ($array_diff_new as $index => $pattern) {      
      //injecting newly added words, to be in the same order
      array_splice($old_word_list[0], $index, 0, [$new_word_list[0][$index]]);
      array_splice($old_word_list[1], $index, 0, [$new_word_list[1][$index]]);
    }
    
    $old_word_list = [array_values($old_word_list[0]), array_values($old_word_list[1])];
      
  }
  
  //Note: array_diff checks if something in array1 that is not existed in array2
  //So we check if something missing from the content
  $array_diff_missing = array_diff($old_word_list[0], $new_word_list[0]);

  if(!empty($array_diff_missing)){
    
    foreach ($array_diff_missing as $index => $pattern) {
      
      $index = intval($index);
      
      unset($old_word_list[0][$index]);
      unset($old_word_list[1][$index]);
    }
  }

  update_post_meta( $id, $meta_key, $old_word_list);
}

add_action('save_post_keyword', function ($id) {
    if(!isset($_FILES['diwn_keywords_excel']) || empty($_FILES['diwn_keywords_excel'])) return;
    
    $keywords = read_excel_data('diwn_keywords_excel');
   
    if(empty($keywords) || !is_array($keywords)) return;
    
    $keywords_list = get_post_meta( $id, 'diwan_keywords_list', true );
    
    if($keywords === $keywords_list) return;
  
 	update_post_meta($id, 'diwan_keywords_list', $keywords);
    
});

add_action('save_post_keyword_template', function ($id, $post) {

 $content = $post->post_content;
 
 diwan_update_content_alts($id, $content, 'content_keyword_groups');
 
 $content = $post->post_title;
 
 diwan_update_content_alts($id, $content, 'title_keyword_groups');
 

 
}, 10, 2);