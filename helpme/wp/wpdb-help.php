<?php
/**
 * WPDB helpers class
 *
 * @package Anonymous theme
 * @author Makiomar
 * @link http://makiomar.com
 */
if ( ! class_exists( 'ANONY_WPDB_HELP' ) ) {
	class ANONY_WPDB_HELP extends ANONY_HELP{

		/**
		 * Query data from one table depending on value
		 * @param array $clause Clause to query (select, from, where, operator, value)
		 * @return mixed        Query result
		 */
		public static function DirectSelect($clause){

			global $wpdb;

			extract( $clause);
			
			$data = [];

			$query = $wpdb->prepare("SELECT $select FROM {$wpdb->$from} WHERE $where $operator '%d'", $value);

			$results = $wpdb->get_results($query);
			
			if(!empty($results) && !is_null($results)){
				foreach($results as $result){
					
						$data[] = $result->$select;
				}
			}

			ANONY_WPDEBUG_HELP::printDbErrors($results);
			
			return $data;
		}
		
		/**
		 * Delete term's posts with all connected meta
		 * @param string $post_type 
		 * @param int    $term_id 
		 * @param int    $limit 
		 * @return Mixed
		 */
		public static function deleteTermPostsCompletely($post_type, $term_id, $limit){
			
			global $wpdb;
			
			$select = $wpdb->get_results( 
		       "SELECT ID FROM wp_posts WHERE post_type='$post_type' LIMIT $term_id", ARRAY_A
		    );
		    
		   foreach( $select as $selected){
		       $temp[] = $selected['ID'];
		   }
		   
		   $temp = array_map('intval',  $temp);
   			
   		   $query = implode(',', $temp);
   
			$result = $wpdb->query( 
			    $wpdb->prepare("
			    	DELETE posts,pt,pm
			        FROM wp_posts posts
			        LEFT JOIN wp_postmeta pm ON pm.post_id IN ('".$query."')
			        LEFT JOIN wp_term_relationships pt ON ( pt.object_id IN ('".$query."') AND pt.term_taxonomy_id = %d)
			        WHERE posts.ID IN ('".$query."')
			        ",
			        $term_id,
			        $post_type,
			        
			    ) 
			);
			
			return $result;
		}
	}
}