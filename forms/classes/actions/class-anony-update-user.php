<?php
/**
 * AnonyEngine user insertion.
 *
 * PHP version 7.3 Or Later.
 *
 * @package AnonyEngine
 * @author  Makiomar <info@makiomar.com>
 * @license https://makiomar.com AnonyEngine Licence
 * @link    https://makiomar.com/anonyengine
 */

defined( 'ABSPATH' ) || die(); // Exit if accessed directly.

if ( ! class_exists( 'ANONY_Update_User' ) ) {

	/**
	 * AnonyEngine user insertion class.
	 *
	 * @package AnonyEngine
	 * @author  Makiomar <info@makiomar.com>
	 * @license https:// makiomar.com AnonyEngine Licence.
	 * @link    https:// makiomar.com
	 */
	class ANONY_Update_User extends ANONY_Actions_Base {


		/**
		 * Required arguments for user insertion.
		 */
		const REQUIRED_ARGUMENTS = array( 'ID' );

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
			parent::__construct( $validated_data, $form );

			if ( ! is_user_logged_in() ) {
				$url = add_query_arg( array( 'status' => 'not-allowed' ), home_url( wp_get_referer() ) );
				wp_safe_redirect( $url );
				exit();
			}
			if ( ! isset( $action_data['user_data'] ) ) {
				ANONY_Wp_Debug_Help::error_log( 'Class ANONY_Update_User : Missing user_data parameter' );
				return;
			}

			$user_data = $action_data['user_data'];

			// Argumnets sent from the form.
			$diff = array_diff( array_keys( $user_data ), self::REQUIRED_ARGUMENTS );

			if ( ! empty( $diff ) ) {
				ANONY_Wp_Debug_Help::error_log( 'Class ANONY_Update_User : user_data parameter missing required keys' );
				return;
			}

			if ( ! ANONY_HELP::empty( $user_data['ID'] ) && get_current_user_id() === absint( $user_data['ID'] ) ) {

				$user_id = absint( $user_data['ID'] );

				$user = get_user_by( 'id', $user_id );

				if ( $user && ! is_wp_error( $user ) ) {

					$args = array(
						'ID' => $this->get_field_value( $user_data['ID'], $this->get_field( $user_data['ID'] ) ),
					);

					if ( $action_data['meta'] && ! empty( $action_data['meta'] ) ) {
						foreach ( $action_data['meta'] as $key => $value ) {
								$_value = $this->get_field_value( $value, $this->get_field( $value ) );
							if ( ! empty( $_value ) ) {
								$args['meta_input'][ $key ] = $_value;
							}
						}
					}
					$user_id = wp_update_user( $args );

					$this->result = $user_id;

				}
			}
		}
	}

}
