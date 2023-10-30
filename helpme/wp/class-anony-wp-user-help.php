<?php
/**
 * WP users helpers.
 *
 * PHP version 7.3 Or Later.
 *
 * @package  AnonyEngine.
 * @author   Makiomar <info@makiomar.com>.
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
	 * @author   Makiomar <info@makiomar.com>.
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

			$roles = self::get_current_user_roles();
			if ( $roles && is_array( $roles ) ) {
				return $roles[0];
			}
			return false;
		}
		public static function get_current_user_roles() {
			if ( is_user_logged_in() ) {
				$user  = wp_get_current_user();
				$roles = (array) $user->roles;
				return $roles;
			} else {
				return false;
			}
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

				// this code generates a string 10 characters long of numbers and letters.
				while ( empty( $member_number ) ) {
					// phpcs:disable WordPress.DateTime.CurrentTimeTimestamp.Requested
					$scramble = md5( AUTH_KEY . current_time( 'timestamp' ) . $user_id . SECURE_AUTH_KEY );
					// phpcs:enable.

					$member_number = substr( $scramble, 0, 10 );

					$check = ANONY_Wp_Db_Help::get_var(
						$wpbd->prepare(
							"
							SELECT 
								meta_value 
							FROM 
								$wpdb->usermeta 
							WHERE 
								meta_value = %s 
							LIMIT 1
							",
							esc_sql( $member_number )
						)
					);

					if ( $check || is_numeric( $member_number ) ) {
						$member_number = null;
					}
				}

				// save to user meta.
				update_user_meta( $user_id, 'member_number', $member_number );

				return $member_number;
			}
		}
		public static function admin_search_users_by_meta( $meta_key ) {
			add_action(
				'pre_user_query',
				function ( $query ) {
					global $wpdb;
					global $pagenow;

					if ( is_admin() && 'users.php' == $pagenow ) {
						if ( empty( $_REQUEST['s'] ) ) {
							return;}
						$query->query_fields = 'DISTINCT ' . $query->query_fields;
						$query->query_from  .= ' LEFT JOIN ' . $wpdb->usermeta . ' ON ' . $wpdb->usermeta . '.user_id = ' . $wpdb->users . '.ID';
						$query->query_where  = "WHERE 1=1 AND ( user_login LIKE '%" . $_REQUEST['s'] . "%' OR ID = '" . $_REQUEST['s'] . "' OR (meta_value LIKE '%" . $_REQUEST['s'] . "%' AND meta_key = '" . $meta_key . "'))";
					}
					return $query;
				}
			);
		}
		public function login_with_email_only() {
			/*
			------------------------------------------------------------------------- *
			* Add custom authentication function
			/* ------------------------------------------------------------------------- */
			add_filter(
				'authenticate',
				function ( $user, $email, $password ) {

					// Check for empty fields
					if ( empty( $email ) ) {
						// create new error object and add errors to it.
						$error = new WP_Error();

						if ( empty( $email ) ) { // No email
							$error->add( 'empty_username', __( '<strong>ERROR</strong>: Email field is empty.' ) );
						} elseif ( ! filter_var( $email, FILTER_VALIDATE_EMAIL ) ) { // Invalid Email
							$error->add( 'invalid_username', __( '<strong>ERROR</strong>: Email is invalid.' ) );
						}

						return $error;
					}

					// Check if user exists in WordPress database
					$user = get_user_by( 'email', $email );

					// bad email
					if ( ! $user ) {
						$error = new WP_Error();
						$error->add( 'invalid', __( '<strong>ERROR</strong>: Either the email or password you entered is invalid.' ) );
						return $error;
					}
					return $user;
				},
				20,
				3
			);

			/*
			------------------------------------------------------------------------- *
			* Change text "Username" in wp-login.php to "Email"
			/* ------------------------------------------------------------------------- */
			add_filter(
				'gettext',
				function ( $translation, $text, $domain ) {
					if ( 'woocommerce' === $domain ) {
						if ( 'Username or email address' == $text ) {
							return esc_html__( 'Email address', 'woocommerce' );
						}
					}

					if ( in_array( $GLOBALS['pagenow'], array( 'wp-login.php' ) ) ) {
						if ( 'Username' == $text ) {
							return esc_html__( 'Email address', 'WordPress' );
						}
					}
					return $translation;
				},
				20,
				3
			);
		}
	}
}
