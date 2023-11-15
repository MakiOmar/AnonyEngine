<?php
/**
 * AnonyEngine actions base.
 *
 * PHP version 7.3 Or Later.
 *
 * @package AnonyEngine
 * @author  Makiomar <info@makiomar.com>
 * @license https://makiomar.com AnonyEngine Licence
 * @link    https://makiomar.com/anonyengine
 */

defined( 'ABSPATH' ) || die(); // Exit if accessed directly.

if ( ! class_exists( 'ANONY_Actions_Base' ) ) {
    /**
	 * AnonyEngine actions base class.
	 *
	 * @package AnonyEngine
	 * @author  Makiomar <info@makiomar.com>
	 * @license https:// makiomar.com AnonyEngine Licence.
	 * @link    https:// makiomar.com
	 */
	class ANONY_Actions_Base {
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
		public function __construct( $validated_data, $form ) {
            $this->form    = $form;
			$this->request = $validated_data;
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
							} else {
								$return = '';
							}

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
