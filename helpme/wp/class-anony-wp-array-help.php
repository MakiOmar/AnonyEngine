<?php
/**
 * WP array helpers.
 *
 * PHP version 7.3 Or Later.
 *
 * @package  AnonyEngine.
 * @author   Makiomar <info@makiomar.com>.
 * @license  https:// makiomar.com AnonyEngine Licence.
 * @link     https:// makiomar.com/anonyengine
 */

defined( 'ABSPATH' ) || die();

if ( ! class_exists( 'ANONY_Wp_Array_Help' ) ) {
	/**
	 * WP array helpers class.
	 *
	 * PHP version 7.3 Or Later.
	 *
	 * @package  AnonyEngine.
	 * @author   Makiomar <info@makiomar.com>.
	 * @license  https:// makiomar.com AnonyEngine Licence.
	 * @link     https:// makiomar.com/anonyengine
	 */
	class ANONY_Wp_Array_Help extends ANONY_HELP {
		/**
		 * Similar to wp_parse_args() just a bit extended to work with multidimensional arrays :).
		 *
		 * @param array $a The default args.
		 * @param array $b To be parsed args.
		 * @return array   Parsed array.
		 */
		public static function parse_args( &$a, $b ) {
			$a      = (array) $a;
			$b      = (array) $b;
			$result = $b;
			foreach ( $a as $k => &$v ) {
				if ( is_array( $v ) && isset( $result[ $k ] ) ) {
					$result[ $k ] = self::parse_args( $v, $result[ $k ] );
				} else {
					$result[ $k ] = $v;
				}
			}
			return $result;
		}

	}
}
