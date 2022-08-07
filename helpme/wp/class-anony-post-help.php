<?php
/**
 * WP posts helpers.
 *
 * PHP version 7.3 Or Later.
 *
 * @package  AnonyEngine.
 * @author   Makiomar <info@makiomar.com>.
 * @license  https:// makiomar.com AnonyEngine Licence..
 * @link     https:// makiomar.com/anonyengine.
 */

defined( 'ABSPATH' ) || die();

if ( ! class_exists( 'ANONY_Post_Help' ) ) {
	/**
	 * WP posts helpers class.
	 *
	 * PHP version 7.3 Or Later.
	 *
	 * @package  AnonyEngine.
	 * @author   Makiomar <info@makiomar.com>.
	 * @license  https:// makiomar.com AnonyEngine Licence..
	 * @link     https:// makiomar.com/anonyengine.
	 */
	class ANONY_Post_Help extends ANONY_HELP {

		/**
		 * Get all meta keys for post by id.
		 *
		 * @param int $post_id Post's ID..
		 * @return array An array of meta keys..
		 */
		public static function getPostMetaKeys( $post_id ) {
			$clause = array(
				'select'   => 'meta_key',
				'from'     => 'postmeta',
				'where'    => 'post_id',
				'operator' => '=',
				'value'    => $post_id,
			);

			return ANONY_Wp_Db_Help::direct_select( $clause );
		}

		/**
		 * Checks if a shortcode exists in page/post
		 *
		 * @param  obj    $post Post object.
		 * @param  string $shortcode_tag Shortcode tag to search for.
		 * @return bool True if shortcode exist, otherwise false.
		 */
		public static function isPageHasShortcode( $post, $shortcode_tag ) {
			if ( $post instanceof WP_Post ) {
				setup_postdata( $post );
				$content = get_the_content();
				if ( has_shortcode( $content, $shortcode_tag ) ) {
					return true;
				}
			}

			return false;
		}

		/**
		 * Get posts' IDs and titles
		 *
		 * @param string $post_type Post tyye.
		 * @return array Returns an array of published post posts IDs and titles. empty array if no results.
		 */
		public static function queryPostTypeSimple( $post_type = 'post' ) {
			$wpml_plugin = 'sitepress-multilingual-cms/sitepress.php';

			if ( ANONY_Wp_Plugin_Help::is_active( $wpml_plugin ) && function_exists( 'icl_get_languages' ) ) {

				return ANONY_Wpml_Help::query_post_typeSimple( $post_type );
			}

			global $wpdb;

			$posts_ids = array();

			$cache_key = 'anony_simple_post_type_query_' . $post_type;

			$results = wp_cache_get( $cache_key );

			if ( false === $results ) {
				// phpcs:disable WordPress.DB.DirectDatabaseQuery.DirectQuery
				$results = $wpdb->get_results(
					$wpdb->prepare(
						"SELECT ID , post_title 
						FROM 
							$wpdb->posts 
						WHERE 
							post_type = %s 
						AND 
							post_status = 'publish'",
						$post_type
					)
				);
				// phpcs:enable.
				wp_cache_set( $cache_key, $results );

				ANONY_Wp_Debug_Help::printDbErrors( $results );
			}

			if ( ! empty( $results ) && ! is_null( $results ) ) {

				foreach ( $results as $result ) {

					$posts_ids[ $result->ID ] = $result->post_title;

				}
			}

			return $posts_ids;

		}
		/**
		 * Gets post id by it;s title
		 *
		 * @param string $title Post's title.
		 * @param string $post_type Post's type.
		 * @return mixed Post's id on success, otherwise false.
		 */
		public static function queryIdByTitle( $title, $post_type = 'post' ) {
			global $wpdb;

			$cache_key = 'query_id_by_title_' . sanitize_title( $title );

			$post_id = wp_cache_get( $cache_key );

			if ( false === $post_id ) {
				// phpcs:disable WordPress.DB.DirectDatabaseQuery.DirectQuery
				$post_id = $wpdb->get_col(
					$wpdb->prepare(
						"SELECT ID 
						FROM 
							$wpdb->posts 
						WHERE 
							post_title = %s
						AND post_type = %s",
						$title,
						$post_type
					)
				);
				// phpcs:enable.
				wp_cache_set( $cache_key, $post_id );

				ANONY_Wp_Debug_Help::printDbErrors( $post_id );

			}

			if ( count( $post_id ) > 0 ) {
				return intval( $post_id[0] );
			} else {
				return false;
			}

		}

		/**
		 * Renders an array of options to html select input
		 *
		 * @param  array       $options    Array of options to be rendered.
		 * @param  string|null $selected   The selected option stored in DB.
		 * @return string      $html       Rendered ooptions.
		 */
		public static function renderHtmlOptions( $options, $selected = null ) {

			$html = '';

			foreach ( $options as $option ) {
				// Will be used to compare with the sanitized value.
				$sanitized_opt = sanitize_title( $option );

				$html .= sprintf(
					'<option value="%1$s"%3$s>%2$s</option>',
					$sanitized_opt,
					$option,
					selected( $selected, $sanitized_opt, false )
				);

			}

			return $html;
		}

		/**
		 * Render select option groups.
		 *
		 * @param  array  $options      Array of all options groups..
		 * @param  array  $opts_groups  array of option groups names and there option group lable ['system' => 'option group label'].
		 * @param  string $selected     Value to check selected option against.
		 * @return string $html         HTML of options groups.
		 */
		public static function renderHtmlOptsGroups( $options, $opts_groups, $selected ) {

			$html = '';

			foreach ( $opts_groups as $key => $group_name ) {

				if ( isset( $options[ $key ] ) ) {

					$html .= '<optgroup label="' . $group_name . '">';

					$html .= self::renderHtmlOptions( $options[ $key ], $selected );

					$html .= '</optgroup>';

				}
			}

			return $html;
		}

		/**
		 * Gets post excerpt.
		 *
		 * **Dscription: ** Excerpt length varies from languages to another, so this function helps to get equal length excerpt.
		 *
		 * @param int $id The post ID to get excerpt for.
		 * @param int $words_count number of words.
		 * @return string The excerpt.
		 */
		public static function crossLangExcerpt( $id, $words_count = 25 ) {

			if ( ! defined( 'ORIGINAL_LANG' ) ) {
				return '<p>' . get_the_excerpt( $id ) . '</p>';
			}

			$text = get_the_content( $id );
			$text = strip_shortcodes( $text );
			$text = str_replace( ']]>', ']]&gt;', $text );
			$text = wp_strip_all_tags( $text );
			$text = explode( ' ', $text );
			$text = array_slice( $text, 0, $words_count );
			$text = '<p>' . implode( ' ', $text ) . '...</p>';
			if ( get_bloginfo( 'language' ) === ORIGINAL_LANG ) {
				return $text;
			} else {
				return '<p>' . get_the_excerpt( $id ) . '</p>';
			}
		}

		/**
		 * Query posts IDs by meta key and meta value
		 *
		 * @param  string $key    The meta key you want to query with.
		 * @param  string $value  The meta value you want to query with.
		 * @return array          An array of posts IDs or empty array if nothing found.
		 */
		public static function queryIdsByMeta( $key, $value ) {
			global $wpdb;

			$posts_ids = array();

			$cache_key = "query_id_by_meta_key_{$key}";

			$results = wp_cache_get( $cache_key );

			if ( false === $results ) {
				// phpcs:disable WordPress.DB.DirectDatabaseQuery.DirectQuery
				$results = $wpdb->get_results(
					$wpdb->prepare(
						"SELECT post_id 
					FROM 
						$wpdb->postmeta 
					WHERE 
						meta_key = %s 
					AND 
						meta_value = %s",
						$key,
						$value
					)
				);
				// phpcs:enable.
				if ( ! empty( $results ) && ! is_null( $results ) ) {
					foreach ( $results as $result ) {
						foreach ( $result as $id ) {
							$posts_ids[] = $id;
						}
					}
				}

				wp_cache_set( $cache_key, $results );

				ANONY_Wp_Debug_Help::printDbErrors( $results );

			}

			return $posts_ids;
		}

		/**
		 * Query meta values by meta key.
		 *
		 * @param string $key    the meta key you want to query with.
		 * @return array Returns an array of meta values.
		 */
		public static function queryMetaValuesByKey( $key ) {
			global $wpdb;

			$meta_values = array();

			$cache_key = "query_meta_value_by_{$key}";

			$results = wp_cache_get( $cache_key );

			if ( false === $results ) {
				// phpcs:disable WordPress.DB.DirectDatabaseQuery.DirectQuery
				$results = $wpdb->get_results(
					$wpdb->prepare(
						"SELECT meta_value 
					FROM 
						$wpdb->postmeta 
					WHERE 
						meta_key = %s",
						$key
					)
				);
				// phpcs:enable.
				if ( ! empty( $results ) && ! is_null( $results ) ) {
					foreach ( $results as $result ) {
						foreach ( $result as $value ) {
							$meta_values[] = $value;
						}
					}
				}

				wp_cache_set( $cache_key, $results );

				ANONY_Wp_Debug_Help::printDbErrors( $results );
			}

			return array_values( $meta_values );
		}

		/**
		 * Assign a post to its corresponding terms
		 *
		 * @param array $post_terms An array of taxonomies as its keys and terms' IDs as values.
		 * @param int   $post_id The ID of the post.
		 */
		public static function set_post_terms( array $post_terms, $post_id ) {

			if ( empty( $post_terms ) ) {
				return;
			}

			foreach ( $post_terms as $taxonomy  => $terms ) {

				$set = wp_set_object_terms( $post_id, $terms, $taxonomy, false );
			}

		}

		/**
		 * Duplicates a post & its meta and it returns the new duplicated Post ID
		 *
		 * @param  [int]   $post_id The Post you want to clone.
		 * @param  [array] $args New post args.
		 * @return [int] The duplicated Post ID.
		 */
		public static function duplicate( $post_id, $args = array() ) {

			if ( ! current_user_can( 'edit_posts' ) ) {
				return;
			}
			$duplicated = array();

			$oldpost = get_post( $post_id, ARRAY_A );

			if ( ! $oldpost || is_null( $oldpost ) ) {
				return;
			}

			unset( $oldpost['ID'], $oldpost['guid'] );

			$oldpost = ANONY_WPARRAY_HELP::wpParseArgs( $oldpost, $args );

			$new_post_id = wp_insert_post( $oldpost );

			if ( ! $new_post_id || is_wp_error( $new_post_id ) ) {
				return $new_post_id;
			}

			// Copy post metadata.
			$data = get_post_custom( $post_id );
			foreach ( $data as $key => $values ) {

				if ( '_wp_old_slug' === $key ) { // do nothing for this meta key.
					continue;
				}

				foreach ( $values as $value ) {
					add_post_meta( $new_post_id, $key, maybe_unserialize( $value ) );// it is important to unserialize data to avoid conflicts.
				}
			}

			if ( ! ANONY_Wpml_Help::is_active() ) {
				/*
				 * get all current post terms ad set them to the new post draft.
				 */
				$taxonomies = get_object_taxonomies( $oldpost['post_type'] ); // returns array of taxonomy names for post type, ex array("category", "post_tag").
				if ( $taxonomies ) {
					foreach ( $taxonomies as $taxonomy ) {
						$post_terms = wp_get_object_terms( $post_id, $taxonomy, array( 'fields' => 'slugs' ) );
						wp_set_object_terms( $new_post_id, $post_terms, $taxonomy, false );
					}
				}
			}

			return $new_post_id;
		}

		/**
		 * Get a list of public post types.
		 *
		 * @return [array] An array of post types as ( 'post_type_name' => post_type_lable ).
		 */
		public static function get_post_types_list() {
			$args       = array(
				'public' => true,
			);
			$post_types = get_post_types( $args, 'objects' );

			foreach ( $post_types as $post_type_obj ) :

				$labels = get_post_type_labels( $post_type_obj );

				$list[ $post_type_obj->name ] = $labels->name;

			endforeach;

			return $list;

		}

		/**
		 * Get post type taxonomies.
		 *
		 * @param string $post_type The post type to get taxonomies for.
		 * @return array An array of taxonomies names.
		 */
		public static function anony_post_taxonomies( $post_type ) {

			$post_taxonomies = apply_filters( 'anony_post_taxonomies', array() );

			if ( ! empty( $post_taxonomies ) && array_key_exists( $post_type, $post_taxonomies ) ) {
				return $post_taxonomies[ $post_type ];
			}

			return array();
		}

		/**
		 * Register post types
		 */
		public static function register_post_types() {

			$custom_posts = apply_filters( 'anony_post_types', array() );

			if ( empty( $custom_posts ) ) {
				return;
			}

			foreach ( $custom_posts as $custom_post => $translatable ) {

				$singular_label = $translatable[0];
				$plural_label   = $translatable[1];

				$labels = array(
					'name'                  => $plural_label,
					'singular_name'         => $plural_label,
					'menu_name'             => $plural_label,
					'name_admin_bar'        => $plural_label,
					// Translators: Post type plural label.
					'archives'              => sprintf( esc_html__( '%s Archives', 'anonyengine' ), $plural_label ),
					// Translators: Post type plural label.
					'attributes'            => sprintf( esc_html__( '%s Attributes', 'anonyengine' ), $plural_label ),
					// Translators: Post type singular label.
					'parent_item_colon'     => sprintf( esc_html__( 'Parent %s:', 'anonyengine' ), $singular_label ),
					// Translators: Post type plural label.
					'all_items'             => sprintf( esc_html__( 'All %s', 'anonyengine' ), $plural_label ),
					// Translators: Post type singular label.
					'add_new_item'          => sprintf( esc_html__( 'Add New %s', 'anonyengine' ), $singular_label ),
					// Translators: Post type singular label.
					'add_new'               => sprintf( esc_html__( 'Add New', 'anonyengine' ), $singular_label ),
					// Translators: Post type singular label.
					'new_item'              => sprintf( esc_html__( 'New %s', 'anonyengine' ), $singular_label ),
					// Translators: Post type singular label.
					'edit_item'             => sprintf( esc_html__( 'Edit %s', 'anonyengine' ), $singular_label ),
					// Translators: Post type singular label.
					'update_item'           => sprintf( esc_html__( 'Update %s', 'anonyengine' ), $singular_label ),
					// Translators: Post type singular label.
					'view_item'             => sprintf( esc_html__( 'View %s', 'anonyengine' ), $singular_label ),
					// Translators: Post type plural label.
					'view_items'            => sprintf( esc_html__( 'View %s', 'anonyengine' ), $plural_label ),
					// Translators: Post type plural label.
					'search_items'          => sprintf( esc_html__( 'Search %s', 'anonyengine' ), $plural_label ),
					'not_found'             => esc_html__( 'Not found', 'anonyengine' ),
					'not_found_in_trash'    => esc_html__( 'Not found in Trash', 'anonyengine' ),
					'featured_image'        => esc_html__( 'Featured Image', 'anonyengine' ),
					'set_featured_image'    => esc_html__( 'Set featured image', 'anonyengine' ),
					'remove_featured_image' => esc_html__( 'Remove featured image', 'anonyengine' ),
					'use_featured_image'    => esc_html__( 'Use as featured image', 'anonyengine' ),
					// Translators: Post type singular label.
					'insert_into_item'      => sprintf( esc_html__( 'Insert into %s', 'anonyengine' ), $singular_label ),
					// Translators: Post type singular label.
					'uploaded_to_this_item' => sprintf( esc_html__( 'Uploaded to this %s', 'anonyengine' ), $singular_label ),
					// Translators: Post type singular label.
					'items_list'            => sprintf( esc_html__( '%s list', 'anonyengine' ), $plural_label ),
					// Translators: Post type plural label.
					'items_list_navigation' => sprintf( esc_html__( '%s list navigation', 'anonyengine' ), $plural_label ),
					// Translators: Post type plural label.
					'filter_items_list'     => sprintf( esc_html__( 'Filter %s list', 'anonyengine' ), $plural_label ),
				);

				$args = array(
					'label'               => $plural_label,
					// Translators: Post type plural label.
					'description'         => sprintf( esc_html__( 'Here you can add your %s', 'anonyengine' ), lcfirst( $plural_label ) ),
					'labels'              => $labels,
					'supports'            => apply_filters(
						"anony_{$custom_post}_supports",
						array(
							'title',
							'editor',
							'excerpt',
							'custom-fields',
							'comments',
							'revisions',
							'thumbnail',
							'author',
							'post-formats',
						)
					),
					'taxonomies'          => self::anony_post_taxonomies( lcfirst( $custom_post ) ),
					'public'              => true,
					'hierarchical'        => apply_filters( "anony_{$custom_post}_hierarchical", false ),
					'show_ui'             => true,
					'show_in_menu'        => true,
					'menu_position'       => 5,
					'show_in_admin_bar'   => true,
					'show_in_nav_menus'   => true,
					'can_export'          => true,
					'has_archive'         => true,
					'exclude_from_search' => false,
					'publicly_queryable'  => true,
					'capability_type'     => 'post',
					'rewrite'             => array(
						'slug'       => lcfirst( $custom_post ),
						'with_front' => false,
					),
				);

				register_post_type( lcfirst( $custom_post ), $args );
			}
		}

	}
}
