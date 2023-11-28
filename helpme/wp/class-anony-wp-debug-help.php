<?php
/**
 * WP DEBUG.
 *
 * PHP version 7.3 Or Later.
 *
 * @package  AnonyEngine
 * @author   Makiomar <info@makiomar.com>
 * @license  https://makiomar.com AnonyEngine Licence
 * @link     https://makiomar.com/anonyengine
 */

defined( 'ABSPATH' ) || die(); // Exit if accessed direct.

if ( ! class_exists( 'ANONY_Wp_Debug_Help' ) ) {
	/**
	 * AnonyEngine wp debug class.
	 *
	 * @package    AnonyEngine
	 * @author     Makiomar <info@makiomar.com>
	 * @license    https://makiomar.com AnonyEngine Licence
	 * @link       https://makiomar.com
	 */
	class ANONY_Wp_Debug_Help extends ANONY_HELP {
		/**
		 * Debug query result.
		 *
		 * @param mixed $results Query result.
		 * @return void
		 */
		public static function printDbErrors( $results ) {
			global $wpdb;
			if ( is_null( $results ) && defined( 'WP_DEBUG' ) && true === WP_DEBUG ) {
				$wpdb->show_errors();
				$wpdb->print_error();
			}
		}

		/**
		 * For debugging. used when page direction is rtl.
		 *
		 * @param mixed $data Debug data.
		 * @return void
		 */
		public static function neat_var_dump( $data ) {
			if ( defined( 'WP_DEBUG' ) && true === WP_DEBUG ) {
				echo '<pre dir="ltr" styel="direction:ltr;text-align:left">';
					// phpcs:disable WordPress.PHP.DevelopmentFunctions
					var_dump( $data );
					// phpcs:enable
				echo '</pre>';
			}
		}

		/**
		 * For debugging. used when page direction is rtl.
		 *
		 * @param mixed $data Debug data.
		 * @return void
		 */
		public static function neat_print_r( $data ) {
			if ( defined( 'WP_DEBUG' ) && true === WP_DEBUG ) {
				echo '<pre dir="ltr" styel="direction:ltr;text-align:left">';
					// phpcs:disable WordPress.PHP.DevelopmentFunctions
					print_r( $data );
					// phpcs:enable
				echo '</pre>';
			}
		}

		/**
		 * Write to error_log.
		 *
		 * @param array $data Debug data.
		 * @return void
		 */
		public static function error_log( $data ) {
			if ( defined( 'WP_DEBUG' ) && true === WP_DEBUG ) {

				// phpcs:disable WordPress.PHP.DevelopmentFunctions
				error_log( print_r( $data, true ) );
				// phpcs:enable
			}
		}

		/**
		 * Track deprecated functions.
		 * **Description: ** Should be hooked to deprecated_argument_run | doing_it_wrong_run |deprecated_function_run
		 *
		 * @param string $function_name The function that was called.
		 * @param string $message  A message regarding the change.
		 * @param string $version  The version of WordPress that deprecated the argument used.
		 * @return void
		 */
		public static function deprecated_argument_run( $function_name, $message, $version ) {
			// phpcs:disable WordPress.PHP.DevelopmentFunctions
			self::error_log( 'Deprecated Argument Detected' );

			$trace = debug_backtrace();
			foreach ( $trace as $frame ) {
				self::error_log( var_export( $frame, true ) );
			}
			// phpcs:enable
		}

		/**
		 * Suppress deprecated
		 *
		 * @return void
		 */
		public static function suppress_deprecated_notices() {
			add_filter( 'deprecated_function_trigger_error', '__return_false' );
			add_filter( 'deprecated_argument_trigger_error', '__return_false' );
			add_filter( 'deprecated_file_trigger_error', '__return_false' );
			add_filter( 'deprecated_hook_trigger_error', '__return_false' );
			add_filter('doing_it_wrong_trigger_error', '__return_false');
		}
	}
}
