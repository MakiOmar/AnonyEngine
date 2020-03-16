<?php
/**
 * WP users helpers class
 *
 * @package Anonymous theme
 * @author Makiomar
 * @link http://makiomar.com
 */

if ( ! class_exists( 'ANONY_WPUSER_HELP' ) ) {
	class ANONY_WPUSER_HELP extends ANONY_HELP{
		/**
		 * Get curent user role
		 * @return string|bool Returns current user role on success or false on failure
		 */

		static function getCurrentUserRole() {

			if( is_user_logged_in() ) {

				$user = wp_get_current_user();

				$role = ( array ) $user->roles;

				return $role[0];
			}

			return false;
		 }
	}
}