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

if ( ! class_exists( 'ANONY_Update_Post' ) ) {

	/**
	 * AnonyEngine post insertion class.
	 *
	 * @package AnonyEngine
	 * @author  Makiomar <info@makiomar.com>
	 * @license https:// makiomar.com AnonyEngine Licence.
	 * @link    https:// makiomar.com
	 */
	class ANONY_Update_Post extends ANONY_Post_Action_Base {


		/**
		 * Required arguments for post insertion.
		 */
		const REQUIRED_ARGUMENTS = array( 'post_type', 'post_status', 'post_title' );

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
		 * Get post id;
		 *
		 * @return bool Post ID or false.
		 */
		protected function get_post_id() {
			//phpcs:disable WordPress.Security.NonceVerification.Recommended
			if ( ! empty( $_GET['_post_id'] ) && is_numeric( $_GET['_post_id'] ) ) {
				return absint( $_GET['_post_id'] );
			}
			//phpcs:enable.

			return false;
		}

		/**
		 * Get Object ID
		 *
		 * @return int|bool
		 */
		protected function get_object_id() {
			return $this->get_post_id();
		}
	}

}
