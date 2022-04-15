<?php
/**
 * WP JSON helpers.
 *
 * PHP version 7.3 Or Later.
 *
 * @package  AnonyEngine.
 * @author   Makiomar <info@makior.com>.
 * @license  https:// makiomar.com AnonyEngine Licence.
 * @link     https:// makiomar.com/anonyengine
 */

if ( ! class_exists( 'ANONY_JSON_HELP' ) ) {
	/**
	 * WP JSON helpers class.
	 *
	 * PHP version 7.3 Or Later.
	 *
	 * @package  AnonyEngine.
	 * @author   Makiomar <info@makior.com>.
	 * @license  https:// makiomar.com AnonyEngine Licence.
	 * @link     https:// makiomar.com/anonyengine
	 */
	class ANONY_Wp_Json_Help extends ANONY_HELP {

		/**
		 * WP JSON helpers class.
		 *
		 * @param array $array To be converted array.
		 * @return string JSON formated string.
		 */
		public static function to_js_json( array $array ) {
			return wp_json_encode( $arr, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES );
		}

	}
}
