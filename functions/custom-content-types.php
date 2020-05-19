<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

/**
 * Extend custom post types
 * @return array of post types
 */
add_filter('anony_post_types', function($custom_post_types){
	$custom_posts = [
					'diwan_test'=>
						[
							esc_html__('Diwan test',ANOE_TEXTDOM), 
							esc_html__('Diwan test',ANOE_TEXTDOM)
						],
					];
	return array_merge($custom_post_types, $custom_posts);
});

/**
 * Extend posts' taxonomies
 * @return array of post's taxonomies
 */
add_filter('anony_post_taxonomies', function($anony_post_taxonomies){

	$post_taxs = [ 'diwan_test' =>['category', 'post_tag'] ];

	return array_merge($anony_post_taxonomies, $post_taxs);
});

/**
 * Extend taxonomies' posts
 * @return array of taxonomies' posts
 */
add_filter( 'anony_taxonomy_posts', function($anony_tax_posts){

	$tax_posts = [ 
		
		'category' => ['diwan_test'] ,
		'post_tag' => ['diwan_test'] ,
	
	];

	return array_merge($anony_tax_posts, $tax_posts);
});


/**
 * No index for test post
 */
add_action('wp_head', function()
{
    if ( is_singular( 'diwan_test' ) ) {
        echo '<meta name="robots" content="noindex, follow">';
    }
});