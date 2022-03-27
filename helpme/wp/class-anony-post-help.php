<?php
/**
 * WP posts helpers class
 *
 * @package Anonymous theme
 * @author Makiomar
 * @link http://makiomar.com
 */

if ( ! class_exists( 'ANONY_POST_HELP' ) ) {
	class ANONY_POST_HELP extends ANONY_HELP {

		/**
		 * Get all meta keys for post by id
		 *
		 * @param int $post_id
		 * @return array An array of meta keys
		 */
		public static function getPostMetaKeys( $post_id ) {
			$clause = array(
				'select'   => 'meta_key',
				'from'     => 'postmeta',
				'where'    => 'post_id',
				'operator' => '=',
				'value'    => $post_id,
			);

			return ANONY_WPDB_HELP::DirectSelect( $clause );
		}
		/**
		 * Checks if a shortcode exists in page/post
		 *
		 * @param  obj $post
		 * @return bool True if shortcode exist, otherwise false
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
		 * Get posts IDs and titles
		 *
		 * @param string $post_type
		 * @return array Returns an array of published post posts IDs and titles. empty array if no results
		 */
		public static function queryPostTypeSimple( $post_type = 'post' ) {
			$wpml_plugin = 'sitepress-multilingual-cms/sitepress.php';

			if ( ANONY_WPPLUGIN_HELP::isActive( $wpml_plugin ) && function_exists( 'icl_get_languages' ) ) {

				return ANONY_WPML_HELP::queryPostTypeSimple( $post_type );
			}

			global $wpdb;

			$postIDs = array();

			$query = $wpdb->prepare( "SELECT ID , post_title FROM $wpdb->posts WHERE post_type = '%s' AND post_status = 'publish'", $post_type );

			$results = $wpdb->get_results( $query );

			if ( ! empty( $results ) && ! is_null( $results ) ) {
				foreach ( $results as $result ) {

						$postIDs[ $result->ID ] = $result->post_title;
				}
			}

			ANONY_WPDEBUG_HELP::printDbErrors( $results );

			return $postIDs;

		}
		/**
		 * Gets post id by it;s title
		 *
		 * @param string $title Post's title
		 * @return int Post's id
		 */
		public static function queryIdByTitle( $title ) {
			global $wpdb;
			$post_id = $wpdb->get_col( "select ID from $wpdb->posts where post_title LIKE '" . $title . "%' " );
			return $post_id;
		}

		/**
		 * Gets an array of pages IDs and titles
		 *
		 * @return array Return an associative array of pages IDs and titles. key (id) equal value (title)
		 */
		public static function getPagesIdsTitles() {
			$pages_data = array();

			$pages = get_pages( 'sort_column=post_title&hierarchical=0' );

			foreach ( $pages as $page ) {
				$pages_data[ $page->ID ] = $page->post_title;
			}

			return $pages_data;
		}

		/**
		 * Gets an array of posts IDs and titles
		 *
		 * @return array Return an associative array of posts IDs and titles. key (id) equal value (title)
		 */
		public static function getPostsIdsTitles( $args ) {
			$posts_data = array();

			$posts = get_posts( $args );

			foreach ( $posts as $post ) {
				$posts_data[ $post->ID ] = $post->post_title;
			}

			return $posts_data;
		}

		/**
		 * Renders an array of options to html select input
		 *
		 * @param  array       $options    Array of options to be rendered
		 * @param  string|null $selected   The selected option stored in DB
		 * @return string      $html       Rendered ooptions
		 */
		public static function renderHtmlOptions( $options, $selected = null ) {

			$html = '';

			foreach ( $options as $option ) {
				// Will be used to compare with the sanitized value
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
		 * @param  array  $options      Array of all options groups.
		 * @param  array  $opts_groups  array of option groups names and there option group lable ['system' => 'option group label']
		 * @param  string $selected     Value to check selected option against
		 * @return string $html         HTML of options groups
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
		 * **Dscription: ** Echoes out an excerpt depending on the language
		 *
		 * @param int $id The post ID to get excerpt for
		 * @param int $words_count number of words
		 * @return void
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
			if ( get_bloginfo( 'language' ) == ORIGINAL_LANG ) {
				return $text;
			} else {
				return '<p>' . get_the_excerpt( $id ) . '</p>';
			}
		}

		/**
		 * Query posts IDs by meta key and meta value
		 *
		 * @param  string $key    The meta key you want to query with
		 * @param  string $value  The meta value you want to query with
		 * @return array          An array of posts IDs
		 */
		public static function queryIdsByMeta( $key, $value ) {
			global $wpdb;

			$postIDs = array();

			$query = "SELECT post_id FROM $wpdb->postmeta WHERE meta_key = 'anony__set_as_featured' AND meta_value = 'on'";

			$results = $wpdb->get_results( $query );

			if ( ! empty( $results ) && ! is_null( $results ) ) {
				foreach ( $results as $result ) {
					foreach ( $result as $id ) {
						$postIDs[] = $id;
					}
				}
			}

			ANONY_WPDEBUG_HELP::printDbErrors( $results );

			return $postIDs;
		}

		/**
		 * Query meta values by meta key.
		 *
		 * @param string $key    the meta key you want to query with
		 * @return array Returns an array of meta values
		 */

		public static function queryMetaValuesByKey( $key ) {
			global $wpdb;

			$metaValues = array();

			$query = "SELECT meta_value FROM $wpdb->postmeta WHERE meta_key = '$key'";

			$results = $wpdb->get_results( $query );

			if ( ! empty( $results ) && ! is_null( $results ) ) {
				foreach ( $results as $result ) {
					foreach ( $result as $value ) {
						$metaValues[] = $value;
					}
				}
			}

			ANONY_WPDEBUG_HELP::printDbErrors( $results );

			return array_values( $metaValues );
		}

		/**
		 * Assign a post to its corresponding terms
		 *
		 * @param array  $post_terms An array of taxonomies as its keys and terms' IDs as values
		 * @param string $post_id The ID of the post
		 */
		public static function setPostTerms( array $post_terms, int $post_id ) {

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
		 * @param  [int]                      $post_id The Post you want to clone
		 * @param  [array] args New post args
		 * @return [int] The duplicated Post ID
		 */
		public static function duplicate( $post_id, $args = array() ) {
			// delete_transient('duplicated_posts'); return;
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

			// Copy post metadata
			$data = get_post_custom( $post_id );
			foreach ( $data as $key => $values ) {

				if ( '_wp_old_slug' == $key ) { // do nothing for this meta key
					continue;
				}

				foreach ( $values as $value ) {
					add_post_meta( $new_post_id, $key, maybe_unserialize( $value ) );// it is important to unserialize data to avoid conflicts.
				}
			}

			if ( ! ANONY_WPML_HELP::isActive() ) {
				/*
				 * get all current post terms ad set them to the new post draft
				 */
				$taxonomies = get_object_taxonomies( $oldpost['post_type'] ); // returns array of taxonomy names for post type, ex array("category", "post_tag");
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
		 * @return [array] An array of post types as ( 'post_type_name' => post_type_lable )
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

	}
}