<?php
/**
 * WCFM helpers.
 *
 * PHP version 7.3 Or Later.
 *
 * @package  AnonyEngine.
 * @author   Makiomar <info@makiomar.com>.
 * @license  https:// makiomar.com AnonyEngine Licence.
 * @link     https:// makiomar.com/anonyengine
 */

defined( 'ABSPATH' ) || die(); // Exit if accessed direct.

if ( ! class_exists( 'ANONY_Wcfm_Help' ) ) {
	/**
	 * WCFM helpers class.
	 *
	 * PHP version 7.3 Or Later.
	 *
	 * @package  AnonyEngine.
	 * @author   Makiomar <info@makiomar.com>.
	 * @license  https:// makiomar.com AnonyEngine Licence.
	 * @link     https:// makiomar.com/anonyengine
	 */
	class ANONY_Wcfm_Help extends ANONY_HELP {

		public static function get_users_of_membership( $membership_id ) {
			$user_query = new WP_User_Query(
				array(
					'meta_key'   => 'wcfm_membership',
					'meta_value' => $membership_id,
				)
			);

			$membership_users = array();
			// User Loop
			if ( ! empty( $user_query->get_results() ) ) {
				foreach ( $user_query->get_results() as $user ) {
					$membership_users[] = $user->ID;
				}
			}

			return $membership_users;
		}
	}
}
