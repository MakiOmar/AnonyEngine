<?php
/**
 * WP users helpers.
 *
 * PHP version 7.3 Or Later.
 *
 * @package  AnonyEngine.
 * @author   Makiomar <info@makior.com>.
 * @license  https:// makiomar.com AnonyEngine Licence.
 * @link     https:// makiomar.com/anonyengine
 */

defined( 'ABSPATH' ) || die(); // Exit if accessed direct.

if ( ! class_exists( 'ANONY_Wp_User_Help' ) ) {
	/**
	 * WP users helpers class.
	 *
	 * PHP version 7.3 Or Later.
	 *
	 * @package  AnonyEngine.
	 * @author   Makiomar <info@makior.com>.
	 * @license  https:// makiomar.com AnonyEngine Licence.
	 * @link     https:// makiomar.com/anonyengine
	 */
	class ANONY_Wp_User_Help extends ANONY_HELP {
		/**
		 * Get curent user role.
		 *
		 * @return string|bool Returns current user role on success or false on failure.
		 */

		public static function get_current_user_role() {

			if ( is_user_logged_in() ) {

				$user = wp_get_current_user();

				$role = (array) $user->roles;

				return $role[0];
			}

			return false;
		}

		/**
		 * Generate member_number when a user is registered.
		 * Should be hooked to user_register.
		 *
		 * @param int $user_id User's ID.
		 * @return string Member's number
		 */
		public static function generate_member_number( $user_id ) {
			$member_number = get_user_meta( $user_id, 'member_number', true );

			// if no member number, create one.
			if ( empty( $member_number ) ) {
				global $wpdb;

				// this code generates a string 10 characters long of numbers and letters
				while ( empty( $member_number ) ) {
					$scramble      = md5( AUTH_KEY . current_time( 'timestamp' ) . $user_id . SECURE_AUTH_KEY );
					$member_number = substr( $scramble, 0, 10 );
					$check         = $wpdb->get_var( "SELECT meta_value FROM $wpdb->usermeta WHERE meta_value = '" . esc_sql( $member_number ) . "' LIMIT 1" );
					if ( $check || is_numeric( $member_number ) ) {
						$member_number = null;
					}
				}

				// save to user meta.
				update_user_meta( $user_id, 'member_number', $member_number );

				return $member_number;
			}
		}
	}
}
