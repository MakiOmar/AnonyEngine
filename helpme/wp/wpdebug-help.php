<?php
/**
 * AnonyEngine WP DEBUG.
 *
 * PHP version 7.3 Or Later.
 *
 * @package  AnonyEngine
 * @author   Makiomar <info@makior.com>
 * @license  https://makiomar.com AnonyEngine Licence
 * @link     https://makiomar.com/anonyengine_elements
 */

defined( 'ABSPATH' ) || die(); // Exit if accessed direct.

if ( ! class_exists( 'ANONY_WPDEBUG_HELP' ) ) {
	/**
	 * AnonyEngine wp debug class.
	 *
	 * @package    AnonyEngine
	 * @author     Makiomar <info@makior.com>
	 * @license    https://makiomar.com AnonyEngine Licence
	 * @link       https://makiomar.com
	 */
	class ANONY_WPDEBUG_HELP extends ANONY_HELP{
		/**
		 * Debug query result.
		 * 
		 * @param mixed $results Query result
		 * @return void
		 */
		static function printDbErrors($results){
			global $wpdb; 
			if(is_null($results) && WP_DEBUG == true){
				$wpdb->show_errors();
				$wpdb->print_error();
			}
		}

		/**
		 * For debugging. used when page direction is rtl.
		 * 
		 * @param mixed $data Debug data
		 * @return void
		 */
		static function neat_var_dump($data){
			if ( defined( 'WP_DEBUG' ) && WP_DEBUG === true ) {
				echo '<pre styel="direction:ltr;text-align:left">';
					var_dump($data);
				echo '</pre>';
			}
		}

		/**
		 * Write to error_log.
		 * 
		 * @param array $data Debug data
		 * @return void
		 */
		static function error_log($data){
			if ( defined( 'WP_DEBUG' ) && WP_DEBUG === true ) {
				echo '<pre styel="direction:ltr;text-align:left">';
					error_log(print_r($data, true));
				echo '</pre>';
			}
		}
		
	}
}