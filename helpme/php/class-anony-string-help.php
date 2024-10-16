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
		 * @param string $text    The String.
		 * @return string
		 */
		public static function upper_case_after_delimiter( $delimiter, $text ) {

			return implode( $delimiter, array_map( 'ucfirst', explode( $delimiter, $text ) ) );
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
		/**
		 * Replace English time units with corresponding Arabic values.
		 *
		 * @param string $text The text containing English time units.
		 * @return string The text with time units replaced by Arabic values.
		 */
		public static function replace_time_units_to_arabic( $text ) {
			// Define an array with English time units as keys and Arabic values as their corresponding values.
			$english_to_arabic = array(
				'seconds' => 'ثواني',
				'second'  => 'ثانية',
				'minutes' => 'دقائق',
				'minute'  => 'دقيقة',
				'hours'   => 'ساعات',
				'hour'    => 'ساعة',
				'days'    => 'أيام',
				'day'     => 'يوم',
				'weeks'   => 'أسابيع',
				'week'    => 'أسبوع',
				'months'  => 'أشهر',
				'month'   => 'شهر',
				'years'   => 'سنوات',
				'year'    => 'سنة',
			);

			// Replace each occurrence of the English time units with the corresponding Arabic values.
			return str_replace( array_keys( $english_to_arabic ), array_values( $english_to_arabic ), $text );
		}
	}
}
