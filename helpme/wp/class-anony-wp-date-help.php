<?php
/**
 * WP date helpers.
 *
 * PHP version 7.3 Or Later.
 *
 * @package  AnonyEngine
 * @author   Makiomar <info@makiomar.com>
 * @license  https://makiomar.com AnonyEngine Licence
 * @link     https://makiomar.com/anonyengine
 */

defined( 'ABSPATH' ) || die(); // Exit if accessed direct.

if ( ! class_exists( 'ANONY_Wp_Date_Help' ) ) {

	/**
	 * WP date helpers class.
	 *
	 * PHP version 7.3 Or Later.
	 *
	 * @package  AnonyEngine
	 * @author   Makiomar <info@makiomar.com>
	 * @license  https://makiomar.com AnonyEngine Licence
	 * @link     https://makiomar.com/anonyengine
	 */
	class ANONY_Wp_Date_Help extends ANONY_HELP {

		/**
		 * Check if a date is in the past.
		 *
		 * @param string $datetime Date time string.
		 * @return bool
		 */
		public static function is_past_date( $datetime ) {

			$date = new DateTime( $datetime );

			$current = new DateTime( current_time( 'mysql' ) ); // current date.

			if ( $date > $current ) {
				return false; // date hasn't been passed.
			}

			return true; // date has been passed.
		}


	}
}
