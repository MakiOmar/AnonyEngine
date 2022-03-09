<?php
/**
 * WP users helpers class
 *
 * @package Anonymous theme
 * @author Makiomar
 * @link http://makiomar.com
 */

if ( ! class_exists( 'ANONY_WPUSER_HELP' ) ) {
	class ANONY_WPUSER_HELP extends ANONY_HELP {
		/**
		 * Get curent user role
		 *
		 * @return string|bool Returns current user role on success or false on failure
		 */

		static function getCurrentUserRole() {

			if ( is_user_logged_in() ) {

				$user = wp_get_current_user();

				$role = (array) $user->roles;

				return $role[0];
			}

			return false;
		}

		/**
		 * Generate member_number when a user is registered.
		 * Should be hooked to user_register
		 */
		static function generateMemberNumber( $user_id ) {
			$member_number = get_user_meta( $user_id, 'member_number', true );

			// if no member number, create one
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

				// save to user meta
				update_user_meta( $user_id, 'member_number', $member_number );

				return $member_number;
			}
		}
	}
}
