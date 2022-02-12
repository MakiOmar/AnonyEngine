<?php
/**
 * WP JSON helpers class
 *
 * @package Anonymous theme
 * @author Makiomar
 * @link http://makiomar.com
 */

if ( ! class_exists( 'ANONY_JSON_HELP' ) ) {
	class ANONY_JSON_HELP extends ANONY_HELP{
		
		static function jsonizeArrayForJs(array $arr){
			return wp_json_encode( $arr, JSON_UNESCAPED_UNICODE| JSON_UNESCAPED_SLASHES);
		}

	}
}