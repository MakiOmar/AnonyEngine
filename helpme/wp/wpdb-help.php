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
	}
}