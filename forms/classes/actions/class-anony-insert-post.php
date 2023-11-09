<?php
/**
 * AnonyEngine post insertion.
 *
 * PHP version 7.3 Or Later.
 *
 * @package AnonyEngine
 * @author  Makiomar <info@makiomar.com>
 * @license https://makiomar.com AnonyEngine Licence
 * @link    https://makiomar.com/anonyengine
 */

defined( 'ABSPATH' ) || die(); // Exit if accessed directly.

if ( ! class_exists( 'ANONY_Insert_Post' ) ) {

	/**
	 * AnonyEngine post insertion class.
	 *
	 * @package AnonyEngine
	 * @author  Makiomar <info@makiomar.com>
	 * @license https:// makiomar.com AnonyEngine Licence.
	 * @link    https:// makiomar.com
	 */
	class ANONY_Insert_Post {


		/**
		 * Required arguments for post insertion.
		 */
		const REQUIRED_ARGUMENTS = array( 'post_type', 'post_status', 'post_title' );

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
				wp_redirect( $url );
				exit();
			}
			if ( ! isset( $action_data['post_data'] ) ) {
				error_log( 'Class ANONY_Insert_Post : Missing post_data parameter' );
				return;
			}

			$post_data = $action_data['post_data'];
			// Argumnets sent from the form.
			$diff = array_diff( array_keys( $post_data ), self::REQUIRED_ARGUMENTS );

			if ( ! empty( $diff ) ) {
				error_log( 'Class ANONY_Insert_Post : post_data parameter missing required keys' );
				return;
			}

			if ( ! ANONY_HELP::empty( $post_data['post_type'], $post_data['post_status'], $post_data['post_title'] ) ) {

				$args = array(
					'post_title'  => $this->get_field_value( $post_data['post_title'], $this->get_field( $post_data['post_title'] ) ),
					'post_type'   => $this->get_field_value( $post_data['post_type'], $this->get_field( $post_data['post_type'] ) ),
					'post_status' => $this->get_field_value( $post_data['post_status'], $this->get_field( $post_data['post_status'] ) ),
					'post_author' => get_current_user_id(),
				);

				// Argumnets sent from the form.
				$optional_post_data = array_diff( array_keys( $post_data ), self::REQUIRED_ARGUMENTS );

				if ( ! empty( $optional_post_data ) ) {
					foreach ( $optional_post_data as $optional_post_field ) {
						$args[ $optional_post_field ] = $this->get_field_value( $post_data[ $optional_post_field ], $this->get_field( $post_data[ $optional_post_field ] ) );
					}
				}

				$id = wp_insert_post( $args );

				if ( $id && ! is_wp_error( $id ) ) {
					$args = array( 'ID' => $id );
					if ( $action_data['meta'] && ! empty( $action_data['meta'] ) ) {
						foreach ( $action_data['meta'] as $key => $value ) {
								$_value = $this->get_field_value( $value, $this->get_field( $value ) );
							if ( ! empty( $_value ) ) {
								$args['meta_input'][ $key ] = $_value;
							}
						}
					}

					wp_update_post( $args );

					$this->result = $id;

				}
			}
		}
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

		protected function get_attachment( $input_field ) {
			$attachment = ANONY_Wp_File_Help::handle_attachments( $input_field, 0 );
			if ( $attachment && ! is_wp_error( $attachment ) ) {
				return $attachment;
			}
			return '';
		}
		protected function maybe_array( $value ) {
			if ( is_array( $value ) ) {
				$map = array_map( 'wp_strip_all_tags', $value );

				return implode( ',', $map );
			} else {
				return wp_strip_all_tags( $value );
			}
		}
		protected function get_field_value( $value, $field = false ) {
			if ( strpos( $value, '#' ) !== false ) {

				$input_field = str_replace( '#', '', $value );

				if ( $field && isset( $_FILES[ $input_field ] ) ) {

					switch ( $field['type'] ) {
						case ( 'upload' ):
							return $this->get_attachment( $input_field );
						break;

						case ( 'file-upload' ):
							return $this->get_attachment( $input_field );
							break;

						case ( 'gallery' ):
								$ids = ANONY_Wp_File_Help::gallery_upload( $input_field );

							if ( $ids && is_array( $ids ) ) {
								return implode( ',', $ids );
							}

							return '';

							break;

						case ( 'uploader' ):
							return $this->get_attachment( $input_field );
							break;

						default:
							return $this->maybe_array( $this->request[ $input_field ] );

					}
				} elseif ( isset( $this->request[ $input_field ] ) ) {
					return $this->maybe_array( $this->request[ $input_field ] );
				} else {
					return '';
				}
			}

			return wp_strip_all_tags( $value );
		}
	}

}
