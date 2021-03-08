<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

/**
 * Read all word alternatives in a content
 * @param string $content 
 * @return array
 */
function diwan_read_content_keyword_groups($content){
	preg_match_all('/\(%(.*?)%\)/i', $content, $matches);

	if(empty($matches)) return [];
		
	$placeholders = array_unique($matches[0]);
	
	//Convert matched regex groups to arrays, so we can extend alternatives later
	$matchings    = array_map( function($element){
		return [str_replace('_', '', $element)];
	}, array_unique($matches[1]));

	return [$placeholders, $matchings];
}

function diwan_merged_keywords_groups($content){
    $groups = diwan_read_content_keyword_groups($content);
    $placeholders = $groups[0];
    $matchings    = $groups[1];
    
    $temp = [];
    foreach($placeholders as $index => $value){
        $temp[$value] = $matchings[$index];
    }
    
    return $temp;
}

function diwan_replace_alts($template, $content, $meta_key){
	//Get template contents
	$content = $template->$content;
	
	//Get template list of alternatives
	$word_list = get_post_meta( $template->ID, $meta_key , true );
	
	
	if (empty($word_list) || !is_array($word_list)) return false;
	
	$patterns      = $word_list[0];
 	$alternatives  = $word_list[1];
 	
	
	foreach ($patterns as $index => $pattern) {
		
		
		$pattern = str_replace('(', '\(', $pattern);
		$pattern = str_replace(')', '\)', $pattern);
		
		$alts = $alternatives[$index];
		
		//Check if at least has one match
		preg_match('/'.$pattern.'/i', $content, $matches);
		
		if (empty($matches)) continue;
			
		//get random index from array $alts.$alt is extracted fro $data
		$randIndex = array_rand($alts);
		
		//get random alternative from alts array
		$alt = $alts[$randIndex];
		
		
		$content = preg_replace('/'.$pattern.'/i', $alt , $content);
		
		
	}
			

	
	return $content;
}

/**
 * Replace alternatives
 * @return string Content after replacing alternatives
 */
function diwan_template_content($post_id){
	$template = get_post_meta( $post_id, 'diwanjobs_keyword_meta', $single = true );
	
	if(!$template || empty($template)) return [];
	
	$template_id = intval($template['diwanjobs_keyword_meta']['template']);
	
	//get connected template data
	$template = get_post( $template_id );
	
	$data = [];
	
	$content = diwan_replace_alts($template,'post_content', 'content_keyword_groups');
	
	if($content) $data['content'] = $content;
	
	$title = diwan_replace_alts($template,'post_title', 'title_keyword_groups');
	
	if($title) $data['title'] = $title;
	
	return $data;
}

/**
 * Select post thumbnail id randomly
 * @param  object $post An object of post
 * @param  string $word Keyword string
 * @return mixed        thumbnail ID on success or false on failure
 */
function diwan_post_thumb($post){
	
	$keyword_gallery = get_post_meta( $post->ID , 'diwanjobs_keyword_meta', true );
	//nvd($post->post_title);
	
	$thumb_id = false;
	
	if(!empty($keyword_gallery) && is_array($keyword_gallery) && !empty($keyword_gallery['diwanjobs_keyword_meta']['gallery'])){
		
		$gallery = $keyword_gallery['diwanjobs_keyword_meta']['gallery'];
		
		//get random thumb index from array $gallery
		$thumbIndex = array_rand($gallery);
		
		//get random thumb id
		$thumb_id = $gallery[$thumbIndex];
	}
	
	return $thumb_id;
}

/**
 * Array of data required to insert a post
 * @param object $post 
 * @param string $word the word to be replaced
 * @return mixed An array of post data on success or false on failure
 */
function diwan_post_data($post, $word){
	$thumb_id = diwan_post_thumb($post);
				
	$data = diwan_template_content($post->ID);
	
	if(empty($data)) return false;
	
	extract($data);
	
	if(!isset($content) || empty($content)) return false;
	if(!isset($title)   || empty($title)) return false;
	
	$date_format = 'Y-m-d';
	
	$opts = get_option('Diwan_Options');
	
	if(isset($opts['date_format']) && !empty($opts['date_format'])) $date_format = $opts['date_format'];
	
	$content = preg_replace('/\(#.*#\)/i', $word , $content);
	
	$title = preg_replace('/\(#.*#\)/i', $word , $title) . ' ' . date_i18n( $date_format );
	
	return ['content' => $content, 'title' => $title, 'thumb_id' => $thumb_id ];
}

/**
 * Set post terms after insertion
 * @param  int    $post_id 
 * @param  array  $terms 
 * @param  string $taxonomy 
 * @return mixed
 */
function diwan_set_post_terms($post_id, $terms, $taxonomy){
	
	foreach ($terms as $term) {
		$slug = sanitize_title_with_dashes($term, '', 'save');
		
		if(!term_exists( $slug, $taxonomy)){
			
			$term_inserted = wp_insert_term($term, $taxonomy, [ 'slug' => $slug ]);
			
			$term_id = $term_inserted['term_id'];
			
		}else{
			$termData   = get_term_by( 'slug', $slug, $taxonomy);
			
			$term_id = $termData->term_id;
		}
		
		$term_ids[] = $term_id; 
	}
	
	
	wp_set_post_terms($post_id, $term_ids, $taxonomy);
	
}
/**
 * Auto poster
 */
function diwan_auto_poster(){
	
	global $diwanOptions;
	
	if (!isset($diwanOptions->activate_publish) || $diwanOptions->activate_publish == '' || $diwanOptions->activate_publish == '0' )  return;
	
	$post_type = 'post';
	
	if (isset($diwanOptions->test_mode) && $diwanOptions->test_mode == '1' )  $post_type = 'diwan_test';
	
	$keywords_post = get_posts( ['post_type' => 'keyword'] );
	
	//nvd($keywords_post);
	if (empty($keywords_post) && is_array($keywords_post)) return;
		
	foreach ($keywords_post as $keyword_post) {
		//Get keywords list
		$keywords_list = get_post_meta( $keyword_post->ID , 'diwan_keywords_list', true );
		
		
		$current_date = current_time('Y-m-d H:i:s');

		$i = 0;
		foreach ($keywords_list as $word) {
			extract($word);
			
			$trimmed_title = str_replace(' ', '', $title);
			
			$transient = get_transient( md5($trimmed_title).'_interval' );
			
			if ($transient){
				
				$publish_date = str_replace('published_', '', $transient);
				
				$date_diff = ANONY_DATE_HELP::dateDiffInDays($current_date, $publish_date);
			}else{
				
				$main_keyword_date = get_the_date('Y-m-d H:i:s', $keyword_post->ID );
				
				$date_diff = ANONY_DATE_HELP::dateDiffInDays($current_date, $main_keyword_date);
				
			}
			
			
			
			
			if($date_diff < $interval) continue;
						 
			$i++;
			
			$data = diwan_post_data($keyword_post, $title);
			
			if(!$data) continue;
			
			extract($data);
			if($i <= 1 ){
				$insert = wp_insert_post( 
							[
								'post_type'    => $post_type,
								'post_title'   => wp_strip_all_tags( $title ),
								'post_content' => wp_kses_post( $content ),
								'post_status'  => 'publish',
								'post_author'  => 1,
							] 
						);
				
				if ($insert && !is_wp_error( $insert )) {
					
					$set = set_post_thumbnail( $insert , intval($thumb_id) );
					
					set_transient( md5($trimmed_title).'_interval'  , 'published_'.current_time('Y-m-d H:i:s') );
					
					if (isset($categories) && is_array($categories) && !empty($categories)) {
						
						diwan_set_post_terms($insert, $categories, 'category');
					}
					
					if (isset($tags) && is_array($tags) && !empty($tags)) {
						
						diwan_set_post_terms($insert, $tags, 'post_tag');
					}

				}
				
			}
			
		}
		
	}
	
}

add_action('wp_footer', function(){
	//diwan_auto_poster();
});

add_filter( 'cron_schedules', function ( $schedules ) { 
    $schedules['one_minute'] = array(
        'interval' => 60,
        'display'  => esc_html__( 'Every one minute' ), );
    return $schedules;
} );



add_action('diwan_autoposter', 'diwan_auto_poster');

add_action( 'init', function(){
	if (! wp_next_scheduled ( 'diwan_autoposter' ))
		wp_schedule_event( time(), 'one_minute', 'diwan_autoposter');
} );
