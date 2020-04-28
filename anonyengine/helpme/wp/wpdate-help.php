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
		 * Check if valid date
		 * @param string $date 
		 * @return boolean true on success otherwise false
		 */
		static function isDate($date){
			// date example mm-dd-year -> 09-25-2012
			$datechunks = explode("-",$date);
			if(sizeof($datechunks)==3){
				if(is_numeric($datechunks[0]) && is_numeric($datechunks[1]) && is_numeric($datechunks[2]))
				{
					// now check if its a valid date
					if(checkdate($datechunks[0], $datechunks[1], $datechunks[2])){
					return true;
					}else{
					return false;
					}

				}else{
				return false;
				}
			}
			
			return false;
		}

		
	}
}