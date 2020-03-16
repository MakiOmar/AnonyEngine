<?php
/**
 * WP terms helpers class
 *
 * @package Anonymous theme
 * @author Makiomar
 * @link http://makiomar.com
 */

if ( ! class_exists( 'ANONY_TERM_HELP' ) ) {
	class ANONY_TERM_HELP extends ANONY_HELP{

		/**
		 * Gets an array of human readable terms slug names by taxonomy.
		 * 
		 * Use instead of get_terms for admin purpuses.
		 * @param  string  $taxonomy Taxonomy to get terms from
		 * @return arrsy             An indexed array of terms slugs
		 */

		static function queryTermsinAdmin($taxonomy){
			
			$terms = ANONY_ARRAY_HELP::ObjToAssoc(
						ANONY_TERM_HELP::queryTermsByTaxonomy($taxonomy),
						'', 
						'slug'
					);
			return array_map('urldecode', $terms);
		}

		/**
		 * Query terms slug names by taxonomy
		 * @param  string  $taxonomy taxonomy to get terms from
		 * @return array             An array of terms objects contains only slug name
		 */
		static function queryTermsSlugsByTaxonomy($taxonomy){
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

			$result =  $wpdb->get_results($query);

			ANONY_WPDEBUG_HELP::printDbErrors($result);

			return $result;                 
		}

		/**
		 * Query terms by taxonomy
		 * @param  string  $taxonomy taxonomy to get terms from
		 * @return array             An array of terms objects
		 */
		static function queryTermsByTaxonomy($taxonomy, $operator = '='){
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

			$result =  $wpdb->get_results($query);

			ANONY_WPDEBUG_HELP::printDbErrors($result);

			return $result;                 
		}

		/**
		 * Get terms using WP_Term_Query class.
		 * 
		 * Use instead of get_terms for admin purpuses.
		 * @param  string  $tax    Taxonomy to get terms from
		 * @param  string  $fields Fields to fetch.
		 * @return array             array of terms (id, name, slug)
		 */

		static function wpTermQuery($tax, $fields){
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
										'fields'   => $fields
									)
								);
				
			if(!empty ($termsObject->terms)) return $termsObject->terms;

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

			if ( is_wp_error( $parent ) )
				{return '';}

			$terms .= $parent->term_id ;

			if ( $parent->parent && ( $parent->parent != $parent->term_id ) ) {

				$terms .= '-'.ANONY_TERM_HELP::termParents( $parent->parent, $tax );

			}
			return $terms;
		}

		/**
		 * Delete all terms connected supplied taxonomies. Can also delete taxonomy
		 * 
		 * @param array $taxonomies Array of taxonomies to delete terms connected to.
		 * @param bool  $dlt_tax    Boolean to decide weather to delete a taxonomy. default false
		 *
		 */
		static function deleteTerms($taxonomies, $dlt_tax = false){
			global $wpdb;
			foreach ( $taxonomies as $taxonomy ) {
				// Prepare & excecute SQL, Delete Terms
				$result = $wpdb->get_results( $wpdb->prepare( "DELETE t.*, tt.* FROM $wpdb->terms AS t INNER JOIN $wpdb->term_taxonomy AS tt ON t.term_id = tt.term_id WHERE tt.taxonomy IN ('%s')", $taxonomy ) );
				// Delete Taxonomy
				if($dlt_tax) $wpdb->delete( $wpdb->term_taxonomy, array( 'taxonomy' => $taxonomy ), array( '%s' ) );
			}
		}
		
	}
}