<?php
/**
 * WP JSON helpers.
 *
 * PHP version 7.3 Or Later.
 *
 * @package  AnonyEngine.
 * @author   Makiomar <info@makiomar.com>.
 * @license  https:// makiomar.com AnonyEngine Licence.
 * @link     https:// makiomar.com/anonyengine
 */

defined( 'ABSPATH' ) || die();

if ( ! class_exists( 'ANONY_Wp_Json_Help' ) ) {
	/**
	 * WP JSON helpers class.
	 *
	 * PHP version 7.3 Or Later.
	 *
	 * @package  AnonyEngine.
	 * @author   Makiomar <info@makiomar.com>.
	 * @license  https:// makiomar.com AnonyEngine Licence.
	 * @link     https:// makiomar.com/anonyengine
	 */
	class ANONY_Wp_Json_Help extends ANONY_HELP {

		/**
		 * WP JSON helpers class.
		 * To call in JS you need to <code>decodeURIComponent</code> then <code>JSON.parse</code>.
		 *
		 * @param array $array To be converted array.
		 * @return string JSON formated string.
		 */
		public static function to_js_json( array $array ) {
			return rawurlencode( wp_json_encode( $arr, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) );
		}
	}
}
