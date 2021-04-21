<?php
/**
 * WP date helpers class
 *
 * @package Anonymous theme
 * @author Makiomar
 * @link http://makiomar.com
 */

if ( ! class_exists( 'ANONY_WPDATE_HELP' ) ) {
	class ANONY_WPDATE_HELP extends ANONY_HELP{
		
		/**
		 * Check if a date has passed
		 * @param string $datetime Date time string
		 * @return bool
		 */
		static function isPastDate($datetime){
    
		    $date = new DateTime( $datetime );
		    
		    $current = new DateTime(current_time('mysql')); // current date

		    if($date > $current) return false; //Course hasn't been started
		    
		    return true; //Course has been started
		}

		
	}
}