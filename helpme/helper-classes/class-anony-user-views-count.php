<?php
/**
 * User views counter.
 *
 * PHP version 7.3 Or Later.
 *
 * @package  AnonyEngine
 * @author   Makiomar <info@makiomar.com>
 * @license  https:// makiomar.com AnonyEngine Licence.
 * @link     https:// makiomar.com/anonyengine.
 */

defined( 'ABSPATH' ) || die(); // Exit if accessed direct.

if ( ! class_exists( 'ANONY_USER_VIEWS_COUNT' ) ) {

	/**
	 * User views counter.
	 *
	 * PHP version 7.3 Or Later.
	 *
	 * @package  AnonyEngine
	 * @author   Makiomar <info@makiomar.com>
	 * @license  https:// makiomar.com AnonyEngine Licence.
	 * @link     https:// makiomar.com/anonyengine.
	 */
	class ANONY_USER_VIEWS_COUNT {

		public function __construct( $user_id ) {
			$this->user_id = $user_id;
			add_action( 'wp', array( $this, 'track_user_views' ) );
		}
		public function increment_user_view_count( $user_id ) {

			$view_count = get_user_meta( $this->user_id, 'user_view_count', true );

			$view_count = $view_count ? $view_count + 1 : 1;

			update_user_meta( $this->user_id, 'user_view_count', $view_count );
		}

		public function should_count_view( $user_id ) {
			$expiration_period = 24 * 60 * 60; // 24 hours
			$cookie_name       = 'user_viewed_' . $this->user_id;

			if ( ! isset( $_COOKIE[ $cookie_name ] ) ) {
				setcookie( $cookie_name, time(), time() + $expiration_period, '/' );
				return true;
			}

			return false;
		}


		public function track_user_views() {

			if ( $this->should_count_view( $this->user_id ) ) {
				$this->increment_user_view_count( $this->user_id );
			}
		}
	}

}
