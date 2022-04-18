<?php
/**
 * WP miscellaneous helpers.
 *
 * PHP version 7.3 Or Later.
 *
 * @package  AnonyEngine.
 * @author   Makiomar <info@makior.com>.
 * @license  https:// makiomar.com AnonyEngine Licence.
 * @link     https:// makiomar.com/anonyengine
 */
if ( ! class_exists( 'ANONY_Wp_Misc_Help' ) ) {

	/**
	 * WP miscellaneous helpers.
	 *
	 * PHP version 7.3 Or Later.
	 *
	 * @package  AnonyEngine.
	 * @author   Makiomar <info@makior.com>.
	 * @license  https:// makiomar.com AnonyEngine Licence.
	 * @link     https:// makiomar.com/anonyengine
	 */
	class ANONY_Wp_Misc_Help extends ANONY_HELP {
		/**
		 * Gets revolution slider list of silders.
		 *
		 * @return array  Associative array of sliders (id = name).
		 */
		public static function get_rev_sliders() {
			$sliders = array();

			if ( class_exists( 'RevSlider' ) ) {

				$rev_slider = new RevSlider();

				foreach ( $rev_slider->getAllSliderAliases() as $slider ) {

					$sliders[ $slider ] = ucfirst( str_replace( '-', ' ', $slider ) );

				}
			}

			return $sliders;
		}

		/**
		 * Get timestamp of remaining time for WordPress transient to be expired.
		 *
		 * @param  string $transient              the transient name you want to get.
		 * @var    object   $wpdb                   the WordPress database object.
		 * @var    array    $transient_timeout      array contains the transient time for expiry.
		 * @return string                           timestamp of transient expiry.
		 */

		public static function get_transient_timeout( $transient ) {
			// If the transient does not exist, does not have a value, or has expired, then get_transient will return false.
			if ( ! get_transient( $transient ) ) {
				return false;
			}

			global $wpdb;

			$prepared_query = $wpdb->prepare( 
				"
				SELECT 
					option_value
			  	FROM 
			  		$wpdb->options
			  	WHERE 
			  		option_name
			  	LIKE %s
				", "%_transient_timeout_{$transient}%" );

			$cache_key = "get_transient_timeout_{$transient}";

			$transient_timeout = ANONY_Wp_Db_Help::get_col( $prepared_query, $cache_key, 0, '', 3600 );

			return $transient_timeout[0];
		}

		/**
		 * list all functions which are hooked to afilter.
		 *
		 * @param string $hook A substring of the hook name.
		 */
		public static function list_hook_filters( $hook ) {
			global $wp_filter;

			$filters = array();

			$h1  = '<h1>Current Filters</h1>';
			$out = '';
			$toc = '<ul>';

			foreach ( $wp_filter as $key => $val ) {
				if ( false !== strpos( $key, $hook ) ) {
					$filters[ $key ][] = var_export( $val, true );
				}
			}

			foreach ( $filters as $name => $arr_vals ) {
				$out .= "<h2 id=$name>$name</h2><pre>" . implode( "\n\n", $arr_vals ) . '</pre>';
				$toc .= "<li><a href='#$name'>$name</a></li>";
			}

			print "$h1$toc</ul>$out";
		}

		/**
		 * Get current page url.
		 *
		 * **Description: ** Gets current page url and takes in account the ssl.
		 *
		 * @return string
		 */
		public static function get_current_request_url() {
			global $wp;
			return home_url( $wp->request );
		}
	}
}
