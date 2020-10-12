<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

if (!function_exists('anony_taxonomy_posts')) {
	/**
	 * Get post type for a taxonomy  
	 *
	 * @param string $tax The taxonomy to get post types for
	 * @return array An array of post types names
	 */
	function anony_taxonomy_posts($tax){
		$tax_posts = apply_filters( 'anony_taxonomy_posts', [] );

		if(!empty($tax_posts) && array_key_exists($tax, $tax_posts)){
			return $tax_posts[$tax];
		}

		return [];
	}
}


if (!function_exists('anony_post_taxonomies')) {
	/**
	 * Get post type taxonomies
	 *
	 * @param string $post_type The post type to get taxonomies for
	 * @return array An array of taxonomies names
	 */
	function anony_post_taxonomies($post_type){

		$post_taxonomies = apply_filters( 'anony_post_taxonomies', [] );

		if(!empty($post_taxonomies) && array_key_exists($post_type, $post_taxonomies))
		{
			return $post_taxonomies[$post_type];
		}

		return [];
	}
}

if (!function_exists('anony_reg_post_types')) {
	/**
	 * Register post types
	 */
	function anony_reg_post_types(){

		$custom_posts = apply_filters( 'anony_post_types', [] );
		
		if(empty($custom_posts)) return;

		foreach($custom_posts as $custom_post=> $translatable){

			$t_s = $translatable[0];
			$t_p = $translatable[1];
				
			$labels = array(
				'name'                  => sprintf(esc_html_x( '%s', 'General Name'    , ANOE_TEXTDOM ),$t_p ),
				'singular_name'         => sprintf(esc_html_x( '%s', 'Singular Name'   , ANOE_TEXTDOM ),$t_p),
				'menu_name'             => sprintf(esc_html__( '%s'                    , ANOE_TEXTDOM ),$t_p),
				'name_admin_bar'        => sprintf(esc_html__( '%s'                    , ANOE_TEXTDOM ),$t_p),
				'archives'              => sprintf(esc_html__( '%s Archives'           , ANOE_TEXTDOM ),$t_p),
				'attributes'            => sprintf(esc_html__( '%s Attributes'         , ANOE_TEXTDOM ),$t_p),
				'parent_item_colon'     => sprintf(esc_html__( 'Parent %s:'            , ANOE_TEXTDOM ),$t_s),
				'all_items'             => sprintf(esc_html__( 'All %s'                , ANOE_TEXTDOM ),$t_p),
				'add_new_item'          => sprintf(esc_html__( 'Add New %s'            , ANOE_TEXTDOM ),$t_s),
				'add_new'               => sprintf(esc_html__( 'Add New'               , ANOE_TEXTDOM ),$t_s),
				'new_item'              => sprintf(esc_html__( 'New %s'                , ANOE_TEXTDOM ),$t_s),
				'edit_item'             => sprintf(esc_html__( 'Edit %s'               , ANOE_TEXTDOM ),$t_s),
				'update_item'           => sprintf(esc_html__( 'Update %s'             , ANOE_TEXTDOM ),$t_s),
				'view_item'             => sprintf(esc_html__( 'View %s'               , ANOE_TEXTDOM ),$t_s),
				'view_items'            => sprintf(esc_html__( 'View %s'               , ANOE_TEXTDOM ),$t_p),
				'search_items'          => sprintf(esc_html__( 'Search %s'             , ANOE_TEXTDOM ),$t_p),
				'not_found'             => esc_html__( 		   'Not found'             , ANOE_TEXTDOM ),
				'not_found_in_trash'    => esc_html__(         'Not found in Trash'    , ANOE_TEXTDOM ),
				'featured_image'        => esc_html__(         'Featured Image'        , ANOE_TEXTDOM ),
				'set_featured_image'    => esc_html__(         'Set featured image'    , ANOE_TEXTDOM ),
				'remove_featured_image' => esc_html__(         'Remove featured image' , ANOE_TEXTDOM ),
				'use_featured_image'    => esc_html__(          'Use as featured image', ANOE_TEXTDOM ),
				'insert_into_item'      => sprintf(esc_html__( 'Insert into %s'        , ANOE_TEXTDOM ),$t_s),
				'uploaded_to_this_item' => sprintf(esc_html__( 'Uploaded to this %s'   , ANOE_TEXTDOM ),$t_s),
				'items_list'            => sprintf(esc_html__( '%s list'               , ANOE_TEXTDOM ),$t_p),
				'items_list_navigation' => sprintf(esc_html__( '%s list navigation'    , ANOE_TEXTDOM ),$t_p),
				'filter_items_list'     => sprintf(esc_html__( 'Filter %s list'        , ANOE_TEXTDOM ),$t_p),
			);
				
				
			$args = array(
				'label'                 => sprintf(esc_html__( '%s', ANOE_TEXTDOM ),$t_p),
				'description'           => sprintf(esc_html__( 'Here you can add your %s', ANOE_TEXTDOM ),lcfirst($t_p)),
				'labels'                => $labels,
				'supports'              => apply_filters
												( 
														"anony_{$custom_post}_supports",

														['title','editor','excerpt','custom-fields','comments','revisions','thumbnail','author','post-formats'
														] 
												),
				'taxonomies'            => anony_post_taxonomies(lcfirst($custom_post)),
				'public'                => true,
				'hierarchical'          => apply_filters( "anony_{$custom_post}_hierarchical", false),
				'show_ui'               => true,
				'show_in_menu'          => true,
				'menu_position'         => 5,
				'show_in_admin_bar'     => true,
				'show_in_nav_menus'     => true,
				'can_export'            => true,
				'has_archive'           => true,
				'exclude_from_search'   => false,
				'publicly_queryable'    => true,
				'capability_type'       => 'post',
				'rewrite' => array(
								'slug'         => lcfirst($custom_post),
								'with_front'   => false,
							),
			);
			
			register_post_type( lcfirst($custom_post), $args );
		}
	}
}

if (!function_exists('anony_reg_taxonomies')) {
	/**
	 * Register taxonomies
	 */
	function anony_reg_taxonomies(){
		$anony_custom_taxs = apply_filters( 'anony_taxonomies', [] );

		if(empty($anony_custom_taxs)) return;

		foreach($anony_custom_taxs as $anony_custom_tax => $translatable ){
			$t_s = $translatable[0];
			$t_p = $translatable[1];

			register_taxonomy(
				$anony_custom_tax,
				anony_taxonomy_posts($anony_custom_tax),
				[
					"hierarchical" => true,
					"label" => $t_p,
					"singular_label" => $t_s,
					"labels"=>
							[
								"all_items"=>sprintf(esc_html__('All %s',ANOE_TEXTDOM),$t_p),
								"edit_item"=>sprintf(esc_html__('Edit %s',ANOE_TEXTDOM),$t_s),
								"view_item"=>sprintf(esc_html__('View %s',ANOE_TEXTDOM),$t_s),
								"update_item"=>sprintf(esc_html__('update %s',ANOE_TEXTDOM),$t_s),
								"add_new_item"=>sprintf(esc_html__('Add new %s',ANOE_TEXTDOM),$t_s),
								"new_item_name"=>sprintf(esc_html__('new %s',ANOE_TEXTDOM),$t_s),
								"parent_item"=>sprintf(esc_html__('Parent %s',ANOE_TEXTDOM),$t_s),
								"parent_item_colon"=>sprintf(esc_html__('Parent %s:',ANOE_TEXTDOM),$t_s),
								"search_items"=>sprintf(esc_html__('search %s',ANOE_TEXTDOM),$t_p),
								"not_found"=>sprintf(esc_html__('No %s found',ANOE_TEXTDOM),$t_p),
							],
					"show_admin_column" => true,
				]
			);
		}
	}
}



add_action( 'init', function(){
	//Register Post Types
	anony_reg_post_types();
	
	//Register Taxonomies
	anony_reg_taxonomies();
}, 10 );