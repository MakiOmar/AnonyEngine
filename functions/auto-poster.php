<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

/**
 * Read all word alternatives in a content
 * @param string $content 
 * @return array
 */
function diwan_read_keyword_groups($content){
	preg_match_all('/\(%(.*?)%\)/i', $content, $matches);

	if(empty($matches)) return [];
		
	$placeholders = $matches[0];
	
	$matchings    = $matches[1];
	
	$temp =[];
	
	foreach ($matchings as $matching) {
		$temp[] = [$matching];
	}
	
	$matchings = $temp;
	
	$matches = [$placeholders, $matchings];
	

	return $matches;
}

/**
 * Replace alternatives
 * @return string Content after replacing alternatives
 */
function diwan_template_content(){
	
	$templates = get_posts( ['post_type' => 'keyword_template'] );
	
	$content = '';
	
	if (empty($templates) || !is_array($templates)) return $content;
		
	//get random template index from array $templates
	$tempIndex = array_rand($templates);
	
	//get random template from $templates array
	$template = $templates[$tempIndex];
	
	//Get template contents
	$content = $template->post_content;
	
	//Get template list of alternatives
	$word_list = get_post_meta( $template->ID, 'keyword_groups', true );
	
	
	if (empty($word_list) || !is_array($word_list)) return;
	
	$patterns      = $word_list[0];
 	$alternatives  = $word_list[1];
 	
	
	foreach ($patterns as $index => $pattern) {
		
		
		$pattern = str_replace('(', '\(', $pattern);
		$pattern = str_replace(')', '\)', $pattern);
		
		$alts = $alternatives[$index];
		
		preg_match('/'.$pattern.'/i', $content, $matches);
		
		if (!empty($matches)) {
			
			//get random index from array $alts.$alt is extracted fro $data
			$randIndex = array_rand($alts);
			
			//get random alternative from alts array
			$alt = $alts[$randIndex];
			
			
			$content = preg_replace('/'.$pattern.'/i', $alt , $content, 1);
		}
		
	}
			

	
	return $content;
}

/**
 * Select post thumbnail id randomly
 * @param  object $post An object of post
 * @param  string $word Keyword string
 * @return mixed        thumbnail ID on success or false on failure
 */
function diwan_post_thumb($post){
	
	$keyword_gallery = get_post_meta( $post->ID , 'diwanjobs_keyword_gallery', true );
	//nvd($post->post_title);
	
	$thumb_id = false;
	
	if(!empty($keyword_gallery) && is_array($keyword_gallery) && !empty($keyword_gallery['diwanjobs_keyword_gallery']['shift8_portfolio_gallery'])){
		
		$gallery = $keyword_gallery['diwanjobs_keyword_gallery']['shift8_portfolio_gallery'];
		
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
				
	$content = diwan_template_content();
	
	if(empty($content)) return false;
	
	$content = preg_replace('/\(#.*#\)/i', $word , $content);
	
	$title = $word . ' ' . date( 'Y-m-d' );
	
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
			$term_id = wp_insert_term($term, $taxonomy, [ 'slug' => $slug ]);
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
function diwan_auto_postert(){
	
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

/**
 * To be hooked before rendering diwn_keywords_template_alts metabox
 * @param object $post 
 */
function diwan_parse_words_alts($post){
	$content = get_post_field('post_content', $post->ID);
	
	if (!empty($content)) {
		$groups_meta = get_post_meta( $post->ID, 'keyword_groups', true );
		
		
		
		if(empty($groups_meta)){
			$groups = diwan_read_keyword_groups($content);			
			
			add_post_meta( $post->ID, 'keyword_groups', $groups );
		}
		
	}
}

//add_action( 'parse_words_alts', 'diwan_parse_words_alts' );


add_filter( 'cron_schedules', function ( $schedules ) { 
    $schedules['one_minute'] = array(
        'interval' => 60,
        'display'  => esc_html__( 'Every one minute' ), );
    return $schedules;
} );



add_action('diwan_autoposter', 'diwan_auto_postert');

add_action( 'init', function(){
	if (! wp_next_scheduled ( 'diwan_autoposter' ))
		wp_schedule_event( time(), 'one_minute', 'diwan_autoposter');
} );