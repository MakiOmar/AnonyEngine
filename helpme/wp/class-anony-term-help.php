<?php
/**
 * Terms helpers
 *
 * PHP version 7.3 Or Later
 *
 * @package  AnonyEngine helpers
 * @author   Makiomar <info@makiomar.com>
 * @license  https://makiomar.com AnonyEngine Licence
 * @link     https://makiomar.com/anonyengine
 */

defined( 'ABSPATH' ) || die(); // Exit if accessed direct.

if ( ! class_exists( 'ANONY_TERM_HELP' ) ) {
	/**
	 * Terms helpers' class
	 *
	 * @package    AnonyEngine helpers
	 * @author     Makiomar <info@makiomar.com>
	 * @license    https://makiomar.com AnonyEngine Licence
	 * @link       https://makiomar.com
	 */
	class ANONY_TERM_HELP extends ANONY_HELP {

		/**
		 * Gets an array of human readable terms slug names by taxonomy.
		 *
		 * Use instead of get_terms for admin purpuses.
		 *
		 * @param  string $taxonomy Taxonomy to get terms from.
		 * @return array             An indexed array of terms slugs.
		 */
		public static function query_terms_in_admin( $taxonomy ) {

			$terms = ANONY_ARRAY_HELP::ObjToAssoc(
				self::query_terms_by_taxonomy( $taxonomy ),
				'',
				'slug'
			);
			return array_map( 'urldecode', $terms );
		}

		/**
		 * Query terms slug names by taxonomy.
		 *
		 * @param  string $taxonomy taxonomy to get terms from.
		 * @return array             An array of terms objects contains only slug name.
		 */
		public static function query_terms_slugs_by_taxonomy( $taxonomy ) {
			global $wpdb;

			$cache_key = 'anony_terms_slug_by_taxonomy_' . $taxonomy;

			$result = wp_cache_get( $cache_key );

			if ( false === $result ) {
				// phpcs:disable WordPress.DB.DirectDatabaseQuery.DirectQuery
				$result = $wpdb->get_results(
					$wpdb->prepare(
						"SELECT DISTINCT 
						t.slug 
						FROM 
							$wpdb->terms t 
						INNER JOIN 
							$wpdb->term_taxonomy tax 
						ON 
							tax.term_id = t.term_id 
						WHERE 
							tax.taxonomy = %s ",
						$taxonomy
					)
				);
				// phpcs:enable.
				wp_cache_set( $cache_key, $result );

				ANONY_Wp_Debug_Help::printDbErrors( $result );
			}

			return $result;
		}
		/**
		 * Query terms by taxonomy.
		 *
		 * @param  string $taxonomy taxonomy to get terms from.
		 * @param  string $operator Query operator.
		 * @return array            An array of terms objects.
		 */
		public static function query_terms_by_taxonomy( $taxonomy, $operator = '=' ) {
			global $wpdb;

			$cache_key = 'anony_terms_by_taxonomy_' . $taxonomy;

			$result = wp_cache_get( $cache_key );

			if ( false === $result ) {
				// phpcs:disable WordPress.DB.DirectDatabaseQuery.DirectQuery
				$result = $wpdb->get_results(
					$wpdb->prepare(
						"SELECT 
							* 
						FROM 
							$wpdb->terms t 
						INNER JOIN 
							$wpdb->term_taxonomy tax 
						ON 
							tax.term_id = t.term_id 
						WHERE 
							tax.taxonomy %s %s",
						$operator,
						$taxonomy
					)
				);
				// phpcs:enable.
				wp_cache_set( $cache_key, $result );

				ANONY_Wp_Debug_Help::printDbErrors( $result );
			}

			return $result;
		}

		/**
		 * Get terms using WP_Term_Query class.
		 *
		 * Use instead of get_terms for admin purpuses.
		 *
		 * @param  string $tax    Taxonomy to get terms from.
		 * @param  string $fields Fields to fetch.
		 * @return array          An array of terms (id, name, slug).
		 */
		public static function wp_term_query( $tax, $fields ) {
			/**
			 * 'fields' to return Accepts:
			 * 'all' (returns an array of complete term objects),
			 * 'all_with_object_id' (returns an array of term objects with the 'object_id' param; works only when
			 * the $object_ids parameter is populated),
			 * 'ids' (returns an array of ids),
			 * 'tt_ids' (returns an array of term taxonomy ids),
			 * 'id=>parent' (returns an associative array with ids as keys, parent term IDs as values),
			 * 'names' (returns an array of term names),
			 * 'count' (returns the number of matching terms),
			 * 'id=>name' (returns an associative array with ids as keys, term names as values), or
			 * 'id=>slug' (returns an associative array with ids as keys, term slugs as values)
			 */
			$terms_object = new WP_Term_Query(
				array(
					'taxonomy' => $tax,
					'fields'   => $fields,
				)
			);

			if ( ! empty( $terms_object->terms ) ) {
				return $terms_object->terms;
			}

			return '';
		}
		/**
		 * Gets post terms from child up to first parent
		 *
		 * @param  int  $id   Term's ID.
		 * @param  type $tax  Term taxonomy.
		 * @return string     Dash separated terms IDs.
		 */
		public static function term_parents( $id, $tax ) {
			$terms  = '';
			$parent = get_term( $id, $tax );

			if ( is_wp_error( $parent ) ) {
				return '';}

			$terms .= $parent->term_id;

			if ( $parent->parent && ( $parent->parent !== $parent->term_id ) ) {

				$terms .= '-' . self::term_parents( $parent->parent, $tax );

			}
			return $terms;
		}

		/**
		 * Delete all terms connected supplied taxonomies. Can also delete taxonomy.
		 *
		 * @param array $taxonomies Array of taxonomies to delete terms connected to.
		 * @param bool  $delete_taxonomy    Boolean to decide weather to delete a taxonomy. default yes.
		 */
		public static function delete_terms( $taxonomies, $delete_taxonomy = 'yes' ) {
			global $wpdb;
			foreach ( $taxonomies as $taxonomy ) {
				// Prepare & excecute SQL, Delete Terms.
				$cache_key = 'anony_delete_terms_by_taxonomy_' . $taxonomy;

				$result = wp_cache_get( $cache_key );

				if ( false === $result ) {
					// phpcs:disable WordPress.DB.DirectDatabaseQuery.DirectQuery
					$result = $wpdb->get_results( $wpdb->prepare( "DELETE t.*, tt.* FROM $wpdb->terms AS t INNER JOIN $wpdb->term_taxonomy AS tt ON t.term_id = tt.term_id WHERE tt.taxonomy IN (%s)", $taxonomy ) );
					// phpcs:enable.

					wp_cache_set( $cache_key, $result );
				}

				// Delete Taxonomy.
				if ( 'yes' === $delete_taxonomy ) {
					// phpcs:disable WordPress.DB.DirectDatabaseQuery.DirectQuery
					$wpdb->delete( $wpdb->term_taxonomy, array( 'taxonomy' => $taxonomy ), array( '%s' ) );
					// phpcs:enable.
				}
			}
		}

		/**
		 * Get term by id
		 *
		 * @param int    $term_id  Term's ID.
		 * @param string $taxonomy Taxonomy slug.
		 * @return mixed (WP_Term|array|false) WP_Term instance (or array) on success, depending on the $output value. False if $taxonomy does not exist or $term was not found.
		 */
		public static function get_term_by( $term_id, $taxonomy ) {

			if ( ANONY_Wpml_Help::is_active() ) {

				return ANONY_Wpml_Help::get_translated_term( $term_id, $taxonomy );
			}

			return get_term_by( 'id', $term_id, $taxonomy );

		}

		/**
		 * Create pagination for terms list.
		 *
		 * @param string $taxonomy Taxonomy slug.
		 * @param int    $posts_per_page Number of posts per page.
		 */
		public static function terms_pagination( $taxonomy, $posts_per_page ) {
			/*
				You should filter get_term args where the code that gets terms exists


				$page = ( get_query_var('paged') ) ? get_query_var( 'paged' ) : 1;

				$posts_per_page = 50;

				$offset = ( $page-1 ) * $posts_per_page;

				$args['number'] = $posts_per_page;
				$args['offset'] = $offset;

			*/
			$pagination  = '';
			$total_terms = wp_count_terms( $taxonomy );
			$pages       = ceil( $total_terms / $posts_per_page );

			// if there's more than one page.
			if ( $pages > 1 ) :
				$pagination .= '<ul class="pagination col-md-8 col-sm-8 col-xs-8" style="margin:auto;float: none;display: block;">';

				for ( $pagecount = 1; $pagecount <= $pages; $pagecount++ ) :
					$pagination .= '<li><a href="' . get_permalink() . 'page/' . $pagecount . '/">' . $pagecount . '</a></li>';
				endfor;

				$pagination .= '</ul>';
			endif;
		}

		/**
		 * Groups terms by parent.
		 *
		 * @param  array $args Arguments required for get_terms.
		 * @return array Groupped terms
		 */
		public static function group_terms_by_parent( $args ) {

			$categories = get_terms( $args );

			// Get all parents values. (May have duplicates).
			$duplicate_parents = array_column( $categories, 'parent' );

			// Remove duplicates.
			$unique_parents = array_unique( $duplicate_parents );

			$grouped = array();

			foreach ( $unique_parents as $parent ) {

				foreach ( $categories as $category ) {

					if ( $category->parent === $parent ) {

						if ( ! isset( $grouped[ $parent ] ) ) {
							$grouped[ $parent ] = array();
						}

						$grouped[ $parent ][] = $category;
					}
				}
			}

			return $grouped;
		}

		/**
		 * Groups top level terms with their children.
		 *
		 * @param  array $args Arguments required for get_terms.
		 * @return array Groupped terms by IDs.
		 */
		public static function top_level_terms_children( $args ) {

			$args['parent'] = 0;
			$categories     = get_terms( $args );

			$grouped = array();

			foreach ( $categories as $category ) {

				$children = get_term_children( $category->term_id, $args['taxonomy'] );

				if ( empty( $children ) ) {
					$grouped[ $category->term_id ] = array( $category->term_id );
				} else {
					$grouped[ $category->term_id ] = $children;
				}
			}

			return $grouped;
		}

		/**
		 * Render top level terms as parent/children groups.
		 *
		 * @param  array $args       Arguments required for get_terms.
		 * @param  array $attributes Select input attributes.
		 *
		 * @return void
		 */
		public static function top_level_terms_option_groups( $args, $attributes ) {

			$groups = self::top_level_terms_children( $args );

			$name  = trim( $attributes['name'] );
			$class = trim( 'terms-options-group ' . $attributes['class'] );

			$select = "<select name='{$name}' class='{$class}'>";

			$select .= "<option value='-1'>" . esc_html__( 'Select category', 'anonyengine' ) . '</option>';
			if ( ! empty( $grouped ) ) {
				foreach ( $grouped as $parent => $children ) {
					$select .= '<optgroup label="' . get_term( $parent )->name . '">';

					foreach ( $children as $child_id ) {
						$select .= "<option value='" . get_term( $child_id )->term_id . "'" . selected( intval( $requested_cat ), get_term( $child_id )->term_id, false ) . '>' . get_term( $child_id )->name . '</option>';
					}

					$select .= '</optgroup>';
				}
			}

		}

	}
}
