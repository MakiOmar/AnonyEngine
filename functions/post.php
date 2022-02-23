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
				'name'                  => sprintf(esc_html_x( '%s', 'General Name'    , 'anonyengine' ),$t_p ),
				'singular_name'         => sprintf(esc_html_x( '%s', 'Singular Name'   , 'anonyengine' ),$t_p),
				'menu_name'             => sprintf(esc_html__( '%s'                    , 'anonyengine' ),$t_p),
				'name_admin_bar'        => sprintf(esc_html__( '%s'                    , 'anonyengine' ),$t_p),
				'archives'              => sprintf(esc_html__( '%s Archives'           , 'anonyengine' ),$t_p),
				'attributes'            => sprintf(esc_html__( '%s Attributes'         , 'anonyengine' ),$t_p),
				'parent_item_colon'     => sprintf(esc_html__( 'Parent %s:'            , 'anonyengine' ),$t_s),
				'all_items'             => sprintf(esc_html__( 'All %s'                , 'anonyengine' ),$t_p),
				'add_new_item'          => sprintf(esc_html__( 'Add New %s'            , 'anonyengine' ),$t_s),
				'add_new'               => sprintf(esc_html__( 'Add New'               , 'anonyengine' ),$t_s),
				'new_item'              => sprintf(esc_html__( 'New %s'                , 'anonyengine' ),$t_s),
				'edit_item'             => sprintf(esc_html__( 'Edit %s'               , 'anonyengine' ),$t_s),
				'update_item'           => sprintf(esc_html__( 'Update %s'             , 'anonyengine' ),$t_s),
				'view_item'             => sprintf(esc_html__( 'View %s'               , 'anonyengine' ),$t_s),
				'view_items'            => sprintf(esc_html__( 'View %s'               , 'anonyengine' ),$t_p),
				'search_items'          => sprintf(esc_html__( 'Search %s'             , 'anonyengine' ),$t_p),
				'not_found'             => esc_html__( 		   'Not found'             , 'anonyengine' ),
				'not_found_in_trash'    => esc_html__(         'Not found in Trash'    , 'anonyengine' ),
				'featured_image'        => esc_html__(         'Featured Image'        , 'anonyengine' ),
				'set_featured_image'    => esc_html__(         'Set featured image'    , 'anonyengine' ),
				'remove_featured_image' => esc_html__(         'Remove featured image' , 'anonyengine' ),
				'use_featured_image'    => esc_html__(          'Use as featured image', 'anonyengine' ),
				'insert_into_item'      => sprintf(esc_html__( 'Insert into %s'        , 'anonyengine' ),$t_s),
				'uploaded_to_this_item' => sprintf(esc_html__( 'Uploaded to this %s'   , 'anonyengine' ),$t_s),
				'items_list'            => sprintf(esc_html__( '%s list'               , 'anonyengine' ),$t_p),
				'items_list_navigation' => sprintf(esc_html__( '%s list navigation'    , 'anonyengine' ),$t_p),
				'filter_items_list'     => sprintf(esc_html__( 'Filter %s list'        , 'anonyengine' ),$t_p),
			);
				
				
			$args = array(
				'label'                 => sprintf(esc_html__( '%s', 'anonyengine' ),$t_p),
				'description'           => sprintf(esc_html__( 'Here you can add your %s', 'anonyengine' ),lcfirst($t_p)),
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
								"all_items"=>sprintf(esc_html__('All %s','anonyengine'),$t_p),
								"edit_item"=>sprintf(esc_html__('Edit %s','anonyengine'),$t_s),
								"view_item"=>sprintf(esc_html__('View %s','anonyengine'),$t_s),
								"update_item"=>sprintf(esc_html__('update %s','anonyengine'),$t_s),
								"add_new_item"=>sprintf(esc_html__('Add new %s','anonyengine'),$t_s),
								"new_item_name"=>sprintf(esc_html__('new %s','anonyengine'),$t_s),
								"parent_item"=>sprintf(esc_html__('Parent %s','anonyengine'),$t_s),
								"parent_item_colon"=>sprintf(esc_html__('Parent %s:','anonyengine'),$t_s),
								"search_items"=>sprintf(esc_html__('search %s','anonyengine'),$t_p),
								"not_found"=>sprintf(esc_html__('No %s found','anonyengine'),$t_p),
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