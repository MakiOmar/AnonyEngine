<?php
/**
 * WP miscellaneous helpers class
 *
 * @package Anonymous theme
 * @author Makiomar
 * @link http://makiomar.com
 */

if ( ! class_exists( 'ANONY_WPMISC_HELP' ) ) {
	class ANONY_WPMISC_HELP extends ANONY_HELP {
		/**
		 * Gets revolution slider list of silders
		 *
		 * @return array  Associative array of slider id = name
		 */
		static function getRevSliders() {
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
		 * Get timestamp of remaining time for WordPress transient to be expired
		 *
		 * @param  string $transient              the transient name you want to get.
		 * @var    object   $wpdb                   the WordPress database object.
		 * @var    array    $transient_timeout      array contains the transient time for expiry.
		 * @return string                           timestamp of transient expiry;
		 */

		static function getTransientTimeout( $transient ) {
			// If the transient does not exist, does not have a value, or has expired, then get_transient will return false
			if ( ! get_transient( $transient ) ) {
				return false;
			}

			global $wpdb;

			$transient_timeout = $wpdb->get_col(
				"
			  SELECT option_value
			  FROM $wpdb->options
			  WHERE option_name
			  LIKE '%_transient_timeout_$transient%'
			"
			);
			return $transient_timeout[0];
		}

		/**
		 * list all functions which are hooked to afilter
		 *
		 * @param string $hook hook name of a substring of the hook nmae
		 */
		static function listHookFilters( $hook ) {
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
		static function getPageUrl() {
			global $wp;
			return home_url( $wp->request );
		}
	}
}
