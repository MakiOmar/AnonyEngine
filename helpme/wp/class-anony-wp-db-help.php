<?php
/**
 * WPDB helpers.
 *
 * PHP version 7.3 Or Later.
 *
 * @package  AnonyEngine
 * @author   Makiomar <info@makiomar.com>
 * @license  https://makiomar.com AnonyEngine Licence
 * @link     https://makiomar.com/anonyengine
 */

defined( 'ABSPATH' ) || die(); // Exit if accessed direct.

if ( ! class_exists( 'ANONY_Wp_Db_Help' ) ) {
	/**
	 * WPDB helpers class.
	 *
	 * PHP version 7.3 Or Later.
	 *
	 * @package  AnonyEngine
	 * @author   Makiomar <info@makiomar.com>
	 * @license  https://makiomar.com AnonyEngine Licence
	 * @link     https://makiomar.com/anonyengine
	 */
	class ANONY_Wp_Db_Help extends ANONY_HELP {

		/**
		 * Get results and make sure check cache first.
		 *
		 * @param string $prepared_query Mysql query.Must be prepared first.
		 * @param string $cache_key WP cache key.
		 * @param string $output The output of get_results.
		 * @param string $group Where to group the cache contents. Enables the same key to be used across groups.
		 * @param int    $expiry When to expire the cache contents, in seconds. Default 0 (no expiration).
		 * @return array|object|null Query result.
		 */
		public static function get_result( $prepared_query, $cache_key, $output = OBJECT, $group = '', $expiry = 0 ) {

			global $wpdb;

			$results = wp_cache_get( $cache_key );

			if ( false === $results ) {

				// phpcs:disable
				$results = $wpdb->get_results( $prepared_query, $output );
				// phpcs:enable

				wp_cache_set( $cache_key, $results, $group, $expiry );

			}

			return $results;
		}

		/**
		 * Get results and make sure check cache first.
		 *
		 * @param string $prepared_query Mysql query.Must be prepared first.
		 * @param string $cache_key WP cache key.
		 * @param string $x Column to return. Indexed from 0.
		 * @param string $group Where to group the cache contents. Enables the same key to be used across groups.
		 * @param int    $expiry When to expire the cache contents, in seconds. Default 0 (no expiration).
		 * @return array Database query result. Array indexed from 0 by SQL result row number.
		 */
		public static function get_col( $prepared_query, $cache_key, $x = 0, $group = '', $expiry = 0 ) {

			global $wpdb;

			$results = wp_cache_get( $cache_key );

			if ( false === $results ) {

				// phpcs:disable
				$results = $wpdb->get_col( $prepared_query, $x );
				// phpcs:enable

				wp_cache_set( $cache_key, $results, $group, $expiry );

			}

			return $results;
		}

		/**
		 * Retrieves one variable from the database and make sure check cache first.
		 *
		 * @param string $prepared_query Mysql query.Must be prepared first.
		 * @param string $cache_key WP cache key.
		 * @param string $x Column to return. Indexed from 0.
		 * @param string $y Row of value to return. Indexed from 0.
		 * @param string $group Where to group the cache contents. Enables the same key to be used across groups.
		 * @param int    $expiry When to expire the cache contents, in seconds. Default 0 (no expiration).
		 * @return array Database query result. Array indexed from 0 by SQL result row number.
		 */
		public static function get_var( $prepared_query, $cache_key, $x = 0, $y = 0, $group = '', $expiry = 0 ) {

			global $wpdb;

			$results = wp_cache_get( $cache_key );

			if ( false === $results ) {

				// phpcs:disable
				$results = $wpdb->get_var( $prepared_query, $x, $y );
				// phpcs:enable

				wp_cache_set( $cache_key, $results, $group, $expiry );

			}

			return $results;
		}

		/**
		 * Query published posts ids.
		 *
		 * @param string $post_type Post type slug.
		 * @param string $cache_key WP cache key.
		 * @param int    $limit Query limit.
		 * @return mixed        Query result.
		 */
		public static function get_posts_ids( $post_type, $cache_key, $limit = 20 ) {

			global $wpdb;

			$results = self::get_result(
				$wpdb->prepare(
					"SELECT 
							ID 
						FROM 
							$wpdb->posts 
						WHERE 
							post_type = %s 
						AND
						    post_status = %s 
						LIMIT 
							%d",
					$post_type,
					'publish',
					$limit
				),
				$cache_key,
				ARRAY_A,
				'',
				3600
			);

			ANONY_Wp_Debug_Help::printDbErrors( $results );

			return array_column( $results, 'ID' );
		}

		/**
		 * Delete term's posts with all connected meta
		 *
		 * @param string $post_type Post type.
		 * @param int    $term_id Term's ID.
		 * @param int    $limit Query limit.
		 * @return Mixed
		 */
		public static function delete_term_posts_completely( $post_type, $term_id, $limit = 20 ) {

			global $wpdb;

			$cache_key = "delete_term_posts_completely_{$post_type}";

			$posts_ids = self::get_posts_ids( $post_type, $cache_key, $limit );

			if ( empty( $posts_ids ) ) {
				ANONY_Wp_Debug_Help::error_log( "delete_term_posts_completely_{$post_type}: No posts to delete" );
				return;
			}

			$query_ids = implode( ',', $posts_ids );

			// phpcs:disable
			$result = $wpdb->query( $wpdb->prepare( "
			    	DELETE 
			    		posts,pm,pt
			        FROM 
			        	$wpdb->posts posts
			        LEFT JOIN 
			        	$wpdb->postmeta pm 
			        ON 
			        	pm.post_id 
			        IN 
			        	('{$query_ids}') 
			        LEFT JOIN 
			        	$wpdb->term_relationships pt 
			        ON 
			        	( pt.object_id IN  ('{$query_ids}') AND pt.term_taxonomy_id = %d )
			        WHERE 
			        	posts.ID 
			        IN ('{$query_ids}')
			        ", $term_id ) );
			// phpcs:enable.
			return $result;
		}

		public function delete_posts_of_type( $post_type ) {
			global $wpdb;

			$result = $wpdb->query(
				$wpdb->prepare(
					"DELETE a,b,c\n"

					. "    FROM {$wpdb->prefix}_posts a\n"

					. "    LEFT JOIN {$wpdb->prefix}_term_relationships b\n"

					. "        ON (a.ID = b.object_id)\n"

					. "    LEFT JOIN {$wpdb->prefix}_postmeta c\n"

					. "        ON (a.ID = c.post_id)\n"

					. "    WHERE a.post_type = \'%s\'"
				),
				$post_type
			);
		}
		/**
		 * This function works for Jet Engine plugin's relation ships.
		 */
		public static function anony_get_related_objects( $cache_key, $select, $where, $object_id, $rel_id, $separate_table = false ) {
			global $wpdb;

			$table_suffix = ( $separate_table ) ? $rel_id : 'default';

			$results = wp_cache_get( $cache_key );

			if ( false === $results ) {

				$results = $wpdb->get_results(
					$wpdb->prepare(
						"
						SELECT {$select} 
						FROM 
							{$wpdb->prefix}jet_rel_{$table_suffix} t
						WHERE 
							t.rel_id = %d
						AND t.{$where} = %d",
						$rel_id,
						$object_id
					),
					ARRAY_A
				);

				if ( $results && ! empty( $results ) && ! is_null( $results ) ) {
					wp_cache_set( $cache_key, $results );
				}
			}

			$data = array();

			if ( $results && ! empty( $results ) && ! is_null( $results ) ) {
				foreach ( $results as $result ) {
						$obj                  = new stdClass();
						$obj->child_object_id = $result[ $select ];
						$data[]               = $obj;
				}
			}

			return $data;
		}

		function anony_query_related_children( $object_jd, $rel_id, $separate_table = false ) {
			global $wpdb;

			$cache_key = 'anony_get_related_children_' . $object_jd;

			return self::anony_get_related_objects( $cache_key, 'child_object_id', 'parent_object_id', $object_jd, $rel_id, $separate_table );
		}

		function anony_query_related_parent( $object_jd, $rel_id, $separate_table = false ) {
				global $wpdb;

				$cache_key = 'anony_get_related_parent_' . $object_jd;

				return self::anony_get_related_objects( $cache_key, 'parent_object_id', 'child_object_id', $object_jd, $rel_id, $separate_table );
		}
	}
}
