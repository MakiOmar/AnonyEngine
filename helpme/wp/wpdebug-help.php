<?php
/**
 * WP debug helpers class
 *
 * @package Anonymous theme
 * @author Makiomar
 * @link http://makiomar.com
 */

if ( ! class_exists( 'ANONY_WPDEBUG_HELP' ) ) {
	class ANONY_WPDEBUG_HELP extends ANONY_HELP{
		/**
		 * Debug query result.
		 * 
		 * @param mixed $results Query result
		 * @return void
		 */
		static function printDbErrors($results){
			global $wpdb; 
			if(is_null($results) && WP_DEBUG == true){
				$wpdb->show_errors();
				$wpdb->print_error();
			}
		}
		
	}
}