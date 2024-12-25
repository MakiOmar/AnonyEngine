<?php
/**
 * PHP Ssecurity keys.
 *
 * PHP version 7.3 Or Later.
 *
 * @package  AnonyEngine
 * @author   Makiomar <info@makiomar.com>
 * @license  https:// makiomar.com AnonyEngine Licence.
 * @link     https:// makiomar.com/anonyengine
 */

defined( 'ABSPATH' ) || die();

if ( ! class_exists( 'ANONY_SECURITY_KRYS' ) ) {

	/**
	 * PHP String helpers class.
	 *
	 * PHP version 7.3 Or Later.
	 *
	 * @package  AnonyEngine
	 * @author   Makiomar <info@makiomar.com>
	 * @license  https:// makiomar.com AnonyEngine Licence.
	 * @link     https:// makiomar.com/anonyengine
	 */
	class ANONY_SECURITY_KRYS extends ANONY_HELP {
		/**
		 * Generate a unique token.
		 *
		 * @param int $length The desired length of the token. Default is 16.
		 * @return string The generated unique token.
		 */
		public static function generate_token( int $length = 16 ) {
			if ( function_exists( 'random_bytes' ) && version_compare( PHP_VERSION, '7.0.0', '>=' ) ) {
				// Use random_bytes for PHP 7.0 and above.
				return substr( bin2hex( random_bytes( ceil( $length / 2 ) ) ), 0, $length );
			} elseif ( function_exists( 'openssl_random_pseudo_bytes' ) ) {
				// Use openssl_random_pseudo_bytes for older PHP versions.
				return substr( bin2hex( openssl_random_pseudo_bytes( ceil( $length / 2 ) ) ), 0, $length );
			}

			// Fallback for environments without cryptographic functions.
			return substr(
				md5( uniqid( wp_rand(), true ) ),
				0,
				$length
			);
		}
		/**
		 * Generate a REST API key.
		 *
		 * The key combines multiple unique tokens separated by random delimiters.
		 *
		 * @return string The generated REST API key.
		 */
		public static function generate_rest_api_key() {
			$separators = array( '.', ':', '-', '_' );

			// Create the API key with random separators and token lengths.
			$rest_api_key = implode(
				$separators[ wp_rand( 0, count( $separators ) - 1 ) ],
				array(
					self::generate_token( 10 )
					. $separators[ wp_rand( 0, count( $separators ) - 1 ) ]
					. self::generate_token( wp_rand( 6, 16 ) ),
					self::generate_token( wp_rand( 16, 24 ) ),
					self::generate_token( wp_rand( 24, 32 ) ),
				)
			);

			return $rest_api_key;
		}
	}
}
