<?php
/**
 * WP plugins helpers.
 *
 * PHP version 7.3 Or Later.
 *
 * @package  AnonyEngine.
 * @author   Makiomar <info@makiomar.com>.
 * @license  https:// makiomar.com AnonyEngine Licence.
 * @link     https:// makiomar.com/anonyengine
 */

defined( 'ABSPATH' ) || die(); // Exit if accessed direct.

if ( ! class_exists( 'ANONY_Wp_Plugin_Help' ) ) {

	/**
	 * WP plugins helpers class.
	 *
	 * PHP version 7.3 Or Later.
	 *
	 * @package  AnonyEngine.
	 * @author   Makiomar <info@makiomar.com>.
	 * @license  https:// makiomar.com AnonyEngine Licence.
	 * @link     https:// makiomar.com/anonyengine
	 */
	class ANONY_Wp_Plugin_Help extends ANONY_HELP {
		/**
		 * Check if plugin is active.
		 *
		 * Detect plugin. For use on Front End and Back End.
		 *
		 * @param string $path  Path of plugin file.
		 */
		public static function is_active( $path ) {

			$path = wp_normalize_path( $path );

			if ( in_array( $path, apply_filters( 'active_plugins', get_option( 'active_plugins' ) ), true ) ) {
				return true;
			}

			return false;
		}

		/**
		 * Activate plugin.
		 *
		 * @param string $plugin  Path of plugin file (e.g akismet/akismet.php).
		 */
		public static function activate_plugin( $plugin ) {
			$current = get_option( 'active_plugins' );
			$plugin  = plugin_basename( trim( $plugin ) );

			if ( ! in_array( $plugin, $current, true ) ) {
				$current[] = $plugin;
				sort( $current );
				do_action( 'activate_plugin', trim( $plugin ) );
				update_option( 'active_plugins', $current );
				do_action( 'activate_' . trim( $plugin ) );
				do_action( 'activated_plugin', trim( $plugin ) );
			}
		}
	}
}
