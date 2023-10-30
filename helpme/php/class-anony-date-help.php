<?php
/**
 * PHP Date helpers class
 *
 * @package Anonymous theme
 * @author Makiomar
 * @link http://makiomar.com
 */

defined( 'ABSPATH' ) || die();

if ( ! class_exists( 'ANONY_DATE_HELP' ) ) {
	/**
	 * PHP date helpers class
	 *
	 * @package    AnonyEngine
	 * @author     Makiomar <info@makiomar.com>
	 * @license    https://makiomar.com AnonyEngine Licence
	 * @link       https://makiomar.com
	 */
	class ANONY_DATE_HELP extends ANONY_HELP {
		/**
		 * Check if valid date
		 *
		 * @param string $date Date to be checked.
		 * @return boolean true on success otherwise false
		 */
		public static function is_date( $date ) {
			// date example mm-dd-year -> 09-25-2012.
			$datechunks = explode( '-', $date );
			if ( count( $datechunks ) === 3 ) {
				if ( is_numeric( $datechunks[0] ) && is_numeric( $datechunks[1] ) && is_numeric( $datechunks[2] ) ) {
					// now check if its a valid date.
					if ( checkdate( $datechunks[0], $datechunks[1], $datechunks[2] ) ) {
						return true;
					} else {
						return false;
					}
				} else {
					return false;
				}
			}

			return false;
		}

		/**
		 * Convert date/time from timezone to another.
		 *
		 * @param  string $date         required to be converted.
		 * @param  string $timezone     current timezone to be converted from.
		 * @param  string $timezone_to  current timezone to be converted to.
		 * @param  string $format       format to be converted to.
		 * @return string               converted date.
		 */
		public static function convert_date_from_timezone( $date, $timezone, $timezone_to, $format = 'Y-m-d H:i' ) {

			$date = new DateTime( $date, new DateTimeZone( $timezone ) );

			$date->setTimezone( new DateTimeZone( $timezone_to ) );

			return $date->format( $format );
		}

		/**
		 * Get formated date/time difference e.g. 2 days and 3 hours and 40 minutes and 25 seconds.
		 *
		 * @param  string $time_stamp         the timestamp you want to calculate difference from.
		 * @param  string $time_zone          timezone you want to use for conversion.
		 * @var    object   $set_time_zone       an object of DateTimeZone.
		 * @var    string   $converted_date    Store formated timestamp.
		 * @var    object   $date              object of formated date/time according to timezone.
		 * @var    object   $current_date      object of current date/time.
		 * @var    string   $diff              stors formated date/time difference.
		 * @return string
		 */
		public static function get_time_difference( $time_stamp, $time_zone ) {
			$set_time_zone = new DateTimeZone( $time_zone );

			$converted_date = gmdate( 'Y-m-d H:i:s', $time_stamp );

			$date = DateTime::createFromFormat( 'Y-m-d H:i:s', $converted_date, $set_time_zone );

			$current_date = new DateTime();

			$diff = $current_date->diff( $date )->format( '%a days and %H hours and %i minutes and %s seconds' );

			return $diff;
		}

		/**
		 * Calculate the difference between to dates in days.
		 *
		 * @param  string $new_date Date/Time string.
		 * @param  string $old_date Date/Time string.
		 * @param  int    $round    The optional number of decimal digits to round to.(Round percision).
		 * @return float            The date difference.
		 */
		public static function date_diff_in_days( $new_date, $old_date, $round = 3 ) {
			$new_date_timestamp = strtotime( $new_date );
			$old_date_timestamp = strtotime( $old_date );

			$date_diff = $new_date_timestamp - $old_date_timestamp;

			return round( $date_diff / ( 60 * 60 * 24 ), $round );
		}


		/**
		 * Calculate the difference between to dates.
		 * Make sure to have time format with date formate.
		 *
		 * @param  string $date_to Date/Time string for future.
		 * @param  int    $format   Date format.
		 * @return DateInterval     Date interval object.
		 */
		public static function time_remaining( $date_to, $format = 'Y-m-d H:i:s' ) {

			$future_date = DateTime::createFromFormat( $format, $date_to );

			$current_date = new DateTime();

			if ( self::is_past_date( $date_to ) ) {
				return false;
			}
			return $future_date->diff( $current_date );
		}

		/**
		 * Calculate the difference between to dates.
		 * Make sure to have time format with date formate.
		 *
		 * @param  string $date Date/Time string for future.
		 * @param  string $format   Date format.
		 * @return boolean     True if past date, otherwise false.
		 */
		public static function is_past_date( $date, $format = 'Y-m-d H:i:s' ) {
			$item_date = DateTime::createFromFormat( $format, $date );

			$current_date = new DateTime();

			if ( $current_date > $item_date ) {
				return true;
			}

			return false;
		}

		public static function time_elapsed_string( $datetime, $full = false ) {
			$now  = new \DateTime();
			$ago  = new \DateTime( $datetime );
			$diff = $now->diff( $ago );

			$diff->w  = floor( $diff->d / 7 );
			$diff->d -= $diff->w * 7;

			$string = array(
				'y' => esc_html__( 'year', 'anonyengine' ),
				'm' => esc_html__( 'month', 'anonyengine' ),
				'w' => esc_html__( 'week', 'anonyengine' ),
				'd' => esc_html__( 'day', 'anonyengine' ),
				'h' => esc_html__( 'hour', 'anonyengine' ),
				'i' => esc_html__( 'minute', 'anonyengine' ),
				's' => esc_html__( 'second', 'anonyengine' ),
			);
			foreach ( $string as $k => &$v ) {
				if ( $diff->$k ) {
					$v = $diff->$k . ' ' . $v . ( $diff->$k > 1 ? 's' : '' );
				} else {
					unset( $string[ $k ] );
				}
			}

			if ( ! $full ) {
				$string = array_slice( $string, 0, 1 );
			}
			return $string ? implode( ', ', $string ) . esc_html__( ' ago', 'anonyengine' ) : esc_html__( 'just now', 'anonyengine' );
		}
	}
}
