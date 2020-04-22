<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

/**
 * Callback for WordPress 'post_edit_form_tag' action.
 * 
 * Append enctype - multipart/form-data and encoding - multipart/form-data
 * to allow image uploads for post type 'post'
 * 
 * @global type $post
 * @return type
 */

add_action('post_edit_form_tag', function (){
    
    global $post;
    
    //  if invalid $post object, return
    if(!$post)
        return;
    
    //  get the current post type
    $post_type = get_post_type($post->ID);
    
    //  if post type is not 'post', return
    if('keyword' != $post_type)
        return;
    
    //  append our form attributes
    printf(' enctype="multipart/form-data" encoding="multipart/form-data" ');
    
});

/**
 * Extend custom post types
 * @return array of post types
 */
add_filter('anony_post_types', function($custom_post_types){
	$custom_posts = [
					'keyword'=>
						[
							esc_html__('Keyword',ANOE_TEXTDOM), 
							esc_html__('Keywords',ANOE_TEXTDOM)
						],
					'keyword_template'=>
						[
							esc_html__('Keyword template',ANOE_TEXTDOM), 
							esc_html__('Keyword templates',ANOE_TEXTDOM)
						],
					];

	return array_merge($custom_post_types, $custom_posts);
});


/**
 * Extend custom taxonomies
 * @return array of taxonomies
 */
add_filter('anony_taxonomies', function($anony_custom_taxs){

	$custom_taxs = 
		[
			'keyword_category'=>
				[
					esc_html__('Keyword category',ANOE_TEXTDOM), esc_html__('Keyword categories',ANOE_TEXTDOM)
				],

		];

	return array_merge($anony_custom_taxs, $custom_taxs);
});


/**
 * Extend posts' taxonomies
 * @return array of post's taxonomies
 */
add_filter('anony_post_taxonomies', function($anony_post_taxonomies){

	$post_taxs = [ 
		'keyword' =>['keyword_category'],
		'post'    =>['keyword_category'],
	];

	return array_merge($anony_post_taxonomies, $post_taxs);
});

/**
 * Extend taxonomies' posts
 * @return array of taxonomies' posts
 */
add_filter( 'anony_taxonomy_posts', function($anony_tax_posts){

	$tax_posts = [ 'keyword_category' => ['keyword', 'post'] ];

	return array_merge($anony_tax_posts, $tax_posts);
});


/**
 * change keyword post type support
 * @return array
 */
add_filter( 'anony_keyword_supports', function($support){
	return ['title'];
});
