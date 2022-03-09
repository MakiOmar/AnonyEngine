<?php
/**
 * PHP Link helpers class
 *
 * @package Anonymous theme
 * @author Makiomar
 * @link http://makiomar.com
 */
if ( ! class_exists( 'ANONY_LINK_HELP' ) ) {
	class ANONY_LINK_HELP extends ANONY_HELP {

		/**
		 * Check if link exists
		 */
		static function linkExists( $url ) {
			$file_headers = @get_headers( $url );
			if ( ! $file_headers || $file_headers[0] == 'HTTP/1.1 404 Not Found' ) {
				return false;
			}

			return true;
		}

		/**
		 * Checks if a url exists
		 */
		static function curlUrlExists( $url ) {
			$ch = curl_init( $url );

			curl_setopt( $ch, CURLOPT_NOBODY, true );

			curl_exec( $ch );

			$code = curl_getinfo( $ch, CURLINFO_HTTP_CODE );

			$status = ( $code == 200 ) ? true : false;

			curl_close( $ch );

			return $status;
		}

		/**
		 * Generate Path
		 *
		 * @param  array   An array of folders tree
		 * @return string  Requied path
		 */
		static function generatePath( $dir_tree ) {
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
