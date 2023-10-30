<?php
/**
 * Post views counter.
 *
 * PHP version 7.3 Or Later.
 *
 * @package  AnonyEngine
 * @author   Makiomar <info@makiomar.com>
 * @license  https:// makiomar.com AnonyEngine Licence.
 * @link     https:// makiomar.com/anonyengine.
 */

defined( 'ABSPATH' ) || die(); // Exit if accessed direct.

if ( ! class_exists( 'ANONY_POST_VIEWS_COUNT' ) ) {

	/**
	 * Post views counter.
	 *
	 * PHP version 7.3 Or Later.
	 *
	 * @package  AnonyEngine
	 * @author   Makiomar <info@makiomar.com>
	 * @license  https:// makiomar.com AnonyEngine Licence.
	 * @link     https:// makiomar.com/anonyengine.
	 */
	class ANONY_POST_VIEWS_COUNT {

		public function __construct( $post_type ) {
			$this->post_type = $post_type;
			add_action( 'wp', array( $this, 'track_post_views' ) );
		}
		public function increment_post_view_count( $post_id ) {

			$view_count = get_post_meta( $post_id, 'post_view_count', true );

			$view_count = $view_count ? $view_count + 1 : 1;

			update_post_meta( $post_id, 'post_view_count', $view_count );
		}

		public function should_count_view( $post_id ) {
			$expiration_period = 24 * 60 * 60; // 24 hours
			$cookie_name       = 'post_viewed_' . $post_id;

			if ( ! isset( $_COOKIE[ $cookie_name ] ) ) {
				setcookie( $cookie_name, time(), time() + $expiration_period, '/' );
				return true;
			}

			return false;
		}


		public function track_post_views() {
			if ( is_singular( $this->post_type ) ) {
				$post_id = get_the_ID();
				if ( $this->should_count_view( $post_id ) ) {
					$this->increment_post_view_count( $post_id );
				}
			}
		}
	}

}
