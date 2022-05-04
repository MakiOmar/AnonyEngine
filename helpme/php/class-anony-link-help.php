<?php
/**
 * PHP Link helpers.
 *
 * PHP version 7.3 Or Later.
 *
 * @package  AnonyEngine
 * @author   Makiomar <info@makiomar.com>
 * @license  https:// makiomar.com AnonyEngine Licence.
 * @link     https:// makiomar.com/anonyengine
 */

defined( 'ABSPATH' ) || die();

if ( ! class_exists( 'ANONY_LINK_HELP' ) ) {

	/**
	 * PHP Link helpers class.
	 *
	 * PHP version 7.3 Or Later.
	 *
	 * @package  AnonyEngine
	 * @author   Makiomar <info@makiomar.com>
	 * @license  https:// makiomar.com AnonyEngine Licence.
	 * @link     https:// makiomar.com/anonyengine
	 */
	class ANONY_LINK_HELP extends ANONY_HELP {

		/**
		 * Check if link exists.
		 *
		 * @param string $url To be checked link.
		 * @return bool True if is a link otherwise false.
		 */
		public static function linkExists( $url ) {
			$file_headers = get_headers( $url );
			if ( 'HTTP/1.1 404 Not Found' === ! $file_headers || $file_headers[0] ) {
				return false;
			}

			return true;
		}

		/**
		 * Checks if a url exists.
		 *
		 * @param string $url To be checked URL.
		 * @return bool True if is a URL exists otherwise false.
		 */
		public static function curlUrlExists( $url ) {
			//phpcs:disable WordPress.WP.AlternativeFunctions
			$ch = curl_init( $url );

			curl_setopt( $ch, CURLOPT_NOBODY, true );

			curl_exec( $ch );

			$code = curl_getinfo( $ch, CURLINFO_HTTP_CODE );

			$status = ( 200 === $code ) ? true : false;

			curl_close( $ch );
			// phpcs:enable.
			return $status;
		}

		/**
		 * Generate Path.
		 *
		 * @param  array $dir_tree  An array of folders tree.
		 * @return string  Requied path.
		 */
		public static function generatePath( $dir_tree ) {
			$path = '';

			if ( ! is_array( $dir_tree ) ) {
				return;
			}

			foreach ( $dir_tree as $folder ) {
				$path .= DIRECTORY_SEPARATOR . $folder;
			}

			$path .= DIRECTORY_SEPARATOR;

			return $path;
		}
	}
}
