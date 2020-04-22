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
	preg_match_all('/{%(.*?)%}/i', $content, $matches);
	
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

add_action( 'wp_footer', function(){
	if(!current_user_can( 'administrator' )) return;

	$word_alternatives = 
[
	'السلام عليكم' => ['مرحباً', ' أهلاً بكم']
];
	$keywords = ['وظائف محاسبين'];
	
	$templates = get_posts( ['post_type' => 'keyword_template'] );

	$first_template = array_shift($templates);

	$first_template_content = $first_template->post_content;
	
	$groups = diwan_read_keyword_groups($first_template_content);
	
	
	foreach ($word_alternatives as $word => $alternatives) {
		preg_match_all('/{%'.$word.'%}/i', $first_template_content, $matches);

		if (!empty($matches)) {

			// get random index from array $arrX
			$randIndex = array_rand($alternatives);

			$alternative = $alternatives[$randIndex];

			$content = preg_replace('/{%'.$word.'%}/i', $alternative , $first_template_content);

			
		}
	}	

} );

add_action( 'parse_words_alts', function($post){
			
	$content = get_post_field('post_content', $post->ID);
	
	if (!empty($content)) {
		$groups_meta = get_post_meta( $post->ID, 'keyword_groups', true );
		
		
		
		if(empty($groups_meta)){
			$groups = diwan_read_keyword_groups($content);
			
			
			
			add_post_meta( $post->ID, 'keyword_groups', $groups );
		}
		
	}

	$groups_meta = get_post_meta( $post->ID, 'keyword_groups', true );
	
	/*echo '<pre dir="ltr">';
	print_r($groups_meta);
	echo '</pre>';*/
		
		

} );