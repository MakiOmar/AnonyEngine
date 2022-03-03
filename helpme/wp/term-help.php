<?php
/**
 * WP terms helpers class
 *
 * @package Anonymous theme
 * @author Makiomar
 * @link http://makiomar.com
 */

if ( ! class_exists( 'ANONY_TERM_HELP' ) ) {
	class ANONY_TERM_HELP extends ANONY_HELP {

		/**
		 * Gets an array of human readable terms slug names by taxonomy.
		 *
		 * Use instead of get_terms for admin purpuses.
		 *
		 * @param  string $taxonomy Taxonomy to get terms from
		 * @return arrsy             An indexed array of terms slugs
		 */

		static function queryTermsinAdmin( $taxonomy ) {

			$terms = ANONY_ARRAY_HELP::ObjToAssoc(
				self::queryTermsByTaxonomy( $taxonomy ),
				'',
				'slug'
			);
			return array_map( 'urldecode', $terms );
		}

		/**
		 * Query terms slug names by taxonomy
		 *
		 * @param  string $taxonomy taxonomy to get terms from
		 * @return array             An array of terms objects contains only slug name
		 */
		static function queryTermsSlugsByTaxonomy( $taxonomy ) {
			global $wpdb;
			$query = "SELECT DISTINCT 
						t.slug 
						FROM 
							$wpdb->terms t 
						INNER JOIN 
							$wpdb->term_taxonomy tax 
						ON 
							tax.term_id = t.term_id 
						WHERE 
							tax.taxonomy = '$taxonomy'";

			$result = $wpdb->get_results( $query );

			ANONY_WPDEBUG_HELP::printDbErrors( $result );

			return $result;
		}

		/**
		 * Query terms by taxonomy
		 *
		 * @param  string $taxonomy taxonomy to get terms from
		 * @return array             An array of terms objects
		 */
		static function queryTermsByTaxonomy( $taxonomy, $operator = '=' ) {
			global $wpdb;
			$query = "SELECT 
							* 
						FROM 
							$wpdb->terms t 
						INNER JOIN 
							$wpdb->term_taxonomy tax 
						ON 
							tax.term_id = t.term_id 
						WHERE 
							tax.taxonomy $operator '$taxonomy'";

			$result = $wpdb->get_results( $query );

			ANONY_WPDEBUG_HELP::printDbErrors( $result );

			return $result;
		}

		/**
		 * Get terms using WP_Term_Query class.
		 *
		 * Use instead of get_terms for admin purpuses.
		 *
		 * @param  string $tax    Taxonomy to get terms from
		 * @param  string $fields Fields to fetch.
		 * @return array             array of terms (id, name, slug)
		 */

		static function wpTermQuery( $tax, $fields ) {
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
			$termsObject = new WP_Term_Query(
				array(
					'taxonomy' => $tax,
					'fields'   => $fields,
				)
			);

			if ( ! empty( $termsObject->terms ) ) {
				return $termsObject->terms;
			}

			return '';
		}
		/**
		 * Gets post terms from child up to first parent
		 *
		 * @param  int  $id   Term id
		 * @param  type $tax  Term taxonomy
		 * @return string     Dash separated terms IDs
		 */
		static function termParents( $id, $tax ) {
			$terms  = '';
			$parent = get_term( $id, $tax );

			if ( is_wp_error( $parent ) ) {
				return '';}

			$terms .= $parent->term_id;

			if ( $parent->parent && ( $parent->parent != $parent->term_id ) ) {

				$terms .= '-' . self::termParents( $parent->parent, $tax );

			}
			return $terms;
		}

		/**
		 * Delete all terms connected supplied taxonomies. Can also delete taxonomy
		 *
		 * @param array $taxonomies Array of taxonomies to delete terms connected to.
		 * @param bool  $dlt_tax    Boolean to decide weather to delete a taxonomy. default false
		 */
		static function deleteTerms( $taxonomies, $dlt_tax = false ) {
			global $wpdb;
			foreach ( $taxonomies as $taxonomy ) {
				// Prepare & excecute SQL, Delete Terms
				$result = $wpdb->get_results( $wpdb->prepare( "DELETE t.*, tt.* FROM $wpdb->terms AS t INNER JOIN $wpdb->term_taxonomy AS tt ON t.term_id = tt.term_id WHERE tt.taxonomy IN ('%s')", $taxonomy ) );
				// Delete Taxonomy
				if ( $dlt_tax ) {
					$wpdb->delete( $wpdb->term_taxonomy, array( 'taxonomy' => $taxonomy ), array( '%s' ) );
				}
			}
		}

		/**
		 * Get term by id
		 *
		 * @param int    $term_id
		 * @param string $taxonomy
		 * @return mixed
		 */
		static function getTermBy( $term_id, $taxonomy ) {

			if ( ANONY_WPML_HELP::isActive() ) {

				return ANONY_WPML_HELP::getTranslatedTerm( $term_id, $taxonomy );
			}

			return get_term_by( 'id', $term_id, $taxonomy );

		}

		/**
		 * Create pagination for terms list
		 *
		 * @param string $taxonomy
		 * @param int    $per_page
		 */
		static function termsPagination( $taxonomy, $per_page ) {
			/*
				You should filter get_term args where the code that gets terms exists


				$page = ( get_query_var('paged') ) ? get_query_var( 'paged' ) : 1;

				$per_page = 50;

				$offset = ( $page-1 ) * $per_page;

				$args['number'] = $per_page;
				$args['offset'] = $offset;

			*/
			$pagination  = '';
			$total_terms = wp_count_terms( $taxonomy );
			$pages       = ceil( $total_terms / $per_page );

			// if there's more than one page
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
		static function group_terms_by_parent( $args ) {

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
		static function top_level_terms_children( $args ) {

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
		 * @return void
		 */
		static function top_level_terms_option_groups( $args, $attributes ) {

			$groups = self::top_level_terms_children( $args );

			$name = trim($attributes['name']);
			$class = trim( 'terms-options-group '. $attributes['class'] );

			$select = "<select name='{$name}' class='{$class}'>";

		    $select.= "<option value='-1'>".esc_html__('Select category', ABBL_DOMAIN)."</option>";
		    if(!empty($grouped)){
		        foreach($grouped as $parent => $children){
		            $select .= '<optgroup label="'. get_term($parent)->name .'">';
		            
		            foreach ($children as $child_id) {
		                $select.= "<option value='".get_term($child_id)->term_id."'".selected(intval($requested_cat), get_term($child_id)->term_id, false).">".get_term($child_id)->name."</option>";
		            }
		            
		            
		            $select .= '</optgroup>';
		        }
		    }

		}

	}
}
