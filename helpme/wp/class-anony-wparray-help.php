<?php
/**
 * WP array helpers class
 *
 * @package Anonymous theme
 * @author Makiomar
 * @link http://makiomar.com
 */

if ( ! class_exists( 'ANONY_WPARRAY_HELP' ) ) {
	class ANONY_WPARRAY_HELP extends ANONY_HELP{
		/**
		 * Similar to wp_parse_args() just a bit extended to work with multidimensional arrays :)
		 * @param array &$a The default args
		 * @param array $b  To be parsed args
		 * @return array    Parsed array
		 */
		static function wpParseArgs( &$a, $b ) {
			$a = (array) $a;
			$b = (array) $b;
			$result = $b;
			foreach ( $a as $k => &$v ) {
				if ( is_array( $v ) && isset( $result[ $k ] ) ) {
					$result[ $k ] = self::wpParseArgs( $v, $result[ $k ] );
				} else {
					$result[ $k ] = $v;
				}
			}
			return $result;
		}
		
	}
}