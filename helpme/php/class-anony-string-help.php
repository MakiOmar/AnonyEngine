<?php
/**
 * PHP String helpers.
 *
 * PHP version 7.3 Or Later.
 *
 * @package  AnonyEngine
 * @author   Makiomar <info@makiomar.com>
 * @license  https:// makiomar.com AnonyEngine Licence.
 * @link     https:// makiomar.com/anonyengine
 */

defined( 'ABSPATH' ) || die();

if ( ! class_exists( 'ANONY_STRING_HELP' ) ) {

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
	class ANONY_STRING_HELP extends ANONY_HELP {

		/**
		 * Slice text to a specific length.
		 *
		 * @param string $text Text to be sliced.
		 * @param string $length Slice length.
		 * @return string
		 */
		public static function slice_text( $text, $length ) {

			$words = str_word_count( $text, 1 );

			$len = min( $length, count( $words ) );

			return join( ' ', array_slice( $words, 0, $len ) );
		}

		/**
		 * Uppercase first litter after delimiter
		 *
		 * @param string $delimiter The delimiter.
		 * @param string $string    The String.
		 * @return string
		 */
		public static function upper_case_after_delimiter( $delimiter, $string ) {

			return implode( $delimiter, array_map( 'ucfirst', explode( $delimiter, $q ) ) );
		}

		/**
		 * Read textarea content line by line.
		 *
		 * @param string $content To be read multi-line text.
		 * @return array An array of text's lines.
		 */
		public static function line_by_line_textarea( $content ) {

			return explode( "\r\n", trim( $content ) );
		}
		/**
		 * English number
		 *
		 * @param string $input The string.
		 * @return string
		 */
		public static function to_english_numbers( $input ) {

			$arabic_numbers = array( '٠', '١', '٢', '٣', '٤', '٥', '٦', '٧', '٨', '٩' );

			$english_numbers = array( '0', '1', '2', '3', '4', '5', '6', '7', '8', '9' );

			return str_replace( $arabic_numbers, $english_numbers, $input );
		}
	}
}
