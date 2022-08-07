<?php
/**
 * File helpers.
 *
 * PHP version 7.3 Or Later.
 *
 * @package  AnonyEngine
 * @author   Makiomar <info@makiomar.com>
 * @license  https://makiomar.com AnonyEngine Licence
 * @link     https://makiomar.com/anonyengine
 */

defined( 'ABSPATH' ) || die(); // Exit if accessed direct.

if ( ! class_exists( 'ANONY_File_Help' ) ) {
	/**
	 * AnonyEngine file helper class.
	 *
	 * @package    AnonyEngine
	 * @author     Makiomar <info@makiomar.com>
	 * @license    https://makiomar.com AnonyEngine Licence
	 * @link       https://makiomar.com
	 */
	class ANONY_File_Help extends ANONY_HELP {

		/**
		 * Escapes file's name
		 *
		 * @param string $file File's name.
		 */
		public static function escape_filename( $file ) {

			// everything to lower and no spaces begin or end.
			$file = strtolower( trim( $file ) );

			// adding - for spaces and union characters.
			$find = array( ' ', '&', '\r\n', '\n', '+', ',' );
			$file = str_replace( $find, '-', $file );

			// delete and replace rest of special chars.
			$find = array( '/[^a-z0-9\-<>]/', '/[\-]+/', '/<[^>]*>/' );
			$repl = array( '', '-', '' );
			$file = preg_replace( $find, $repl, $file );

			return $file;
		}

	}
}
