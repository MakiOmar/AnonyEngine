<?php
/**
 * AnonyEngine profile create/update.
 *
 * PHP version 7.3 Or Later.
 *
 * @package AnonyEngine
 * @author  Makiomar <info@makiomar.com>
 * @license https://makiomar.com AnonyEngine Licence
 * @link    https://makiomar.com/anonyengine
 */

defined( 'ABSPATH' ) || die(); // Exit if accessed directly.

if ( ! class_exists( 'ANONY_Profile' ) ) {

	/**
	 * AnonyEngine post insertion class.
	 *
	 * @package AnonyEngine
	 * @author  Makiomar <info@makiomar.com>
	 * @license https:// makiomar.com AnonyEngine Licence.
	 * @link    https:// makiomar.com
	 */
	class ANONY_Profile extends ANONY_Post_Action_Base {

		/**
		 * Result
		 *
		 * @var mixed
		 */
		public $result = false;

		/**
		 * Constructor.
		 *
		 * @param array  $validated_data $_POST after validation.
		 * @param array  $action_data    Fields mapping.
		 * @param object $form           Form object.
		 */
		public function __construct( $validated_data, $action_data, $form ) {
			parent::__construct( $validated_data, $action_data, $form );

			$this->result = $this->post_id;
		}

		/**
		 * Check if a user has a profile
		 *
		 * @return bool Profile ID if a user has a profile otherwise false.
		 */
		protected function get_user_profile() {
			$profile_id = get_user_meta( get_current_user_id(), 'anony_user_profile', true );

			if ( $profile_id && ! empty( $profile_id ) ) {
				return absint( $profile_id );
			}

			return false;
		}

		/**
		 * Get Object ID
		 *
		 * @return int|bool
		 */
		protected function get_object_id() {
			return $this->get_user_profile();
		}

		/**
		 * After success post action
		 *
		 * @param ANONY_Post_Action_Base $post_action Post action object.
		 * @return mixed
		 */
		protected function after_post_action( ANONY_Post_Action_Base $post_action ) {
			if ( ! $post_action->object_id && $post_action->post_id ) {
				add_user_meta( get_current_user_id(), 'anony_user_profile', $post_action->post_id );
			}
		}
	}

}
