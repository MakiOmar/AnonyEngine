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
	class ANONY_Update_User {


		/**
		 * Required arguments for user insertion.
		 */
		const REQUIRED_ARGUMENTS = array( 'ID' );

		/**
		 * Form object
		 *
		 * @var object
		 */
		protected $form;

		/**
		 * Submited data request
		 *
		 * @var array
		 */
		protected $request;

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
			$this->form    = $form;
			$this->request = $validated_data;

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

		/**
		 * Get field name
		 *
		 * @param string $value Value mapped to the field.
		 * @return mixed Field's name if it is mapped field otherwise false.
		 */
		protected function get_field( $value ) {
			if ( is_string( $value ) && strpos( $value, '#' ) !== false ) {

				$input_field = str_replace( '#', '', $value );

				foreach ( $this->form->fields as $field ) {
					if ( $field['id'] === $input_field ) {
						return $field;
					}
				}
			}

			return false;
		}

		/**
		 * Get attachment id for file input
		 *
		 * @param string $input_field Upload field name.
		 * @return mixed Attachment ID or empty.
		 */
		protected function get_attachment( $input_field ) {
			$attachment = ANONY_Wp_File_Help::handle_attachments( $input_field, 0 );
			if ( $attachment && ! is_wp_error( $attachment ) ) {
				return $attachment;
			}
			return '';
		}

		/**
		 * Ensure value is string.
		 *
		 * @param mixed $value Field value.
		 * @return string Field value.
		 */
		protected function maybe_array( $value ) {
			if ( is_array( $value ) ) {
				$map = array_map( 'wp_strip_all_tags', $value );

				return implode( ',', $map );
			} else {
				return wp_strip_all_tags( $value );
			}
		}
		/**
		 * Get field value
		 *
		 * @param string $value Field mapped value.
		 * @param mixed  $field Field arguments or false.
		 * @return mixed Field value.
		 */
		protected function get_field_value( $value, $field = false ) {
			if ( strpos( $value, '#' ) !== false ) {

				$input_field = str_replace( '#', '', $value );
				//phpcs:disable WordPress.Security.NonceVerification.Missing
				if ( $field && isset( $_FILES[ $input_field ] ) ) {
					//phpcs:enable.
					switch ( $field['type'] ) {
						case ( 'upload' ):
							$return = $this->get_attachment( $input_field );
							break;

						case ( 'file-upload' ):
							$return = $this->get_attachment( $input_field );
							break;

						case ( 'gallery' ):
								$ids = ANONY_Wp_File_Help::gallery_upload( $input_field );

							if ( $ids && is_array( $ids ) ) {
								$return = implode( ',', $ids );
							}

							$return = '';

							break;

						case ( 'uploader' ):
							$return = $this->get_attachment( $input_field );
							break;

						default:
							$return = $this->maybe_array( $this->request[ $input_field ] );

					}
				} elseif ( isset( $this->request[ $input_field ] ) ) {
					$return = $this->maybe_array( $this->request[ $input_field ] );
				} else {
					$return = '';
				}

				return $return;
			}

			return wp_strip_all_tags( $value );
		}
	}

}
