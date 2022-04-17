<?php
/**
 * WPDB helpers.
 *
 * PHP version 7.3 Or Later.
 *
 * @package  AnonyEngine
 * @author   Makiomar <info@makior.com>
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
	 * @author   Makiomar <info@makior.com>
	 * @license  https://makiomar.com AnonyEngine Licence
	 * @link     https://makiomar.com/anonyengine
	 */
	class ANONY_Wp_Db_Help extends ANONY_HELP {

		/**
		 * Query published posts ids.
		 *
		 * @param string $post_type Post type slug.
		 * @param string $cache_key WP cache key.
		 * @param int    $limi Query limit
		 * @return mixed        Query result.
		 */
		public static function get_posts_ids( $post_type, $cache_key, $limit = 20 ) {

			global $wpdb;

			$results = wp_cache_get( $cache_key );

			if ( false === $results ) {
				$results = $wpdb->get_results(
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
					ARRAY_A
				);

				wp_cache_set( $cache_key, $results, '', 3600 );
			}

			ANONY_Wp_Debug_Help::printDbErrors( $results );

			return array_column( $results, 'ID' );
		}

		/**
		 * Delete term's posts with all connected meta
		 *
		 * @param string $post_type
		 * @param int    $term_id
		 * @param int    $limit
		 * @return Mixed
		 */
		public static function delete_term_posts_completely( $post_type, $term_id, $limit = 20 ) {

			global $wpdb;

			$cache_key = "delete_term_posts_completely_{$post_type}";

			$posts_ids = self::get_posts_ids( $post_type, $cache_key, $limit = 20 );

			if ( empty( $posts_ids ) ) {
				ANONY_Wp_Debug_Help::error_log( "delete_term_posts_completely_{$post_type}: No posts to delete" );
				return;
			}

			$query = implode( ',', $posts_ids );

			$result = $wpdb->query(
				$wpdb->prepare(
					"
			    	DELETE 
			    		*
			        FROM 
			        	$wpdb->posts posts
			        LEFT JOIN 
			        	$wpdb->postmeta pm 
			        ON 
			        	pm.post_id 
			        IN 
			        	('{$query}')
			        LEFT JOIN 
			        	$wpdb->term_relationships pt 
			        ON 
			        	( pt.object_id IN  ('{$query}') AND pt.term_taxonomy_id = %d )
			        WHERE 
			        	posts.ID 
			        IN ('{$query}')
			        ",
					$term_id,
				)
			);

			return $result;
		}
	}
}
