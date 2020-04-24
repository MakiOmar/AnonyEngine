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
	
	$groups = [];
	
	if(empty($matches)) return $groups;
		
	$placeholders = $matches[0];
	
	$matched      = $matches[1];
	
	foreach ($placeholders as $index => $placeholder) {
		$groups[$placeholder]['index'] = $index;
		$groups[$placeholder]['alts']  = [$matched[$index]];
	}
	
	return $groups;
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
	
	foreach ($word_list as $pattern => $data) {
		
		extract($data);
		
		$pattern = str_replace('(', '\(', $pattern);
		$pattern = str_replace(')', '\)', $pattern);
		
		preg_match('/'.$pattern.'/i', $content, $matches);
		
		if (!empty($matches)) {
			
			//get random index from array $alts.$alt is extracted fro $data
			$randIndex = array_rand($alts);
			
			//get random alternative from alts array
			$alt = $alts[$randIndex];
			
			
			$content = preg_replace('/'.$pattern.'/i', $alt , $content);
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
	
	$thumb_id = false;
	
	if(!empty($keyword_gallery) && is_array($keyword_gallery)){
		
		$gallery = $keyword_gallery['diwanjobs_keyword_gallery']['shift8_portfolio_gallery'];
		
		//get random thumb index from array $gallery
		$thumbIndex = array_rand($gallery);
		
		//get random thumb id
		$thumb_id = $gallery[$thumbIndex];
	}
	
	return $thumb_id;
}

function diwan_post_data($post, $word){
	$thumb_id = diwan_post_thumb($post);
				
	$content = diwan_template_content();
	
	if(empty($content)) return false;
	
	$content = preg_replace('/\(#.*#\)/i', $word , $content);
	
	$title = $word . ' ' . date( 'Y-m-d' );
	
	return ['content' => $content, 'title' => $title, 'thumb_id' => $thumb_id ];
}

/**
 * Auto poster
 */
function diwan_auto_postert(){
	
	$keywords_post = get_posts( ['post_type' => 'keyword'] );
	
	
	if (!empty($keywords_post) && is_array($keywords_post)) {
		
		foreach ($keywords_post as $keyword_post) {
			//Get keywords list
			$keywords_list = get_post_meta( $keyword_post->ID , 'diwan_keywords_list', true );
			
			
			$i = 0;
			foreach ($keywords_list as $word) {
				
				$i++;
				
				$data = diwan_post_data($keyword_post, $word);
				
				if(!$data) continue;
				
				extract($data);
				
				if($i == 0){
					$insert = wp_insert_post( 
								[
									'post_type'    => 'post',
									'post_title'   => wp_strip_all_tags( $title ),
									'post_content' => wp_kses_post( $content ),
									'post_status'  => 'publish',
								] 
							);
					if ($insert && !is_wp_error( $insert )) {
						
						$set = set_post_thumbnail( $insert , intval($thumb_id) );

					}
					
				}
				
				/**echo '<pre dir="ltr">';
					print($content);
				echo '</pre>';*/
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

add_action( 'parse_words_alts', 'diwan_parse_words_alts' );


add_action( 'wp_footer', function(){
	if(!current_user_can( 'administrator' )) return;
	
	diwan_auto_postert();

} );