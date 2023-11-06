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
	class ANONY_Update_Post {


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
				wp_safe_redirect( $url );
				exit();
			}
			if ( ! isset( $action_data['post_data'] ) ) {
				//phpcs:disable WordPress.PHP.DevelopmentFunctions.error_log_error_log
				error_log( 'Class ANONY_Update_Post : Missing post_data parameter' );
				//phpcs:enable
				return;
			}

			$post_data = $action_data['post_data'];
			// Argumnets sent from the form.
			$diff = array_diff( self::REQUIRED_ARGUMENTS, array_keys( $post_data ) );

			if ( ! empty( $diff ) ) {
				//phpcs:disable WordPress.PHP.DevelopmentFunctions.error_log_error_log
				error_log( 'Class ANONY_Update_Post : post_data parameter missing required keys' );
				//phpcs:enable
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

				$post_id = $this->get_post_id();

				if ( ! $post_id ) {
					$id = wp_insert_post( $args );
				} else {
					$args['ID'] = absint( $post_id );

					wp_update_post( $args );

					$id = absint( $post_id );
				}

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

					if ( $action_data['tax_query'] && ! empty( $action_data['tax_query'] ) ) {			
						foreach ( $action_data['tax_query'] as $taxonomy => $value ) {
							if ( is_array( $value ) ) {
								$_value = array();
								foreach ( $value as $v ) {
									$_value[] = absint( $this->get_field_value( $v, $this->get_field( $v ) ) );
								}
								wp_set_object_terms( $id, $_value, $taxonomy );
							} else {
								$this->set_object_terms( $id, $value, $taxonomy );
							}
						}
					}

					$this->result = $id;

				}
			}
		}

		/**
		 * Set object terms
		 *
		 * @param array $id Object ID.
		 * @param array $value Value.
		 * @param array $taxonomy Taxonomy.
		 * @return void
		 */
		protected function set_object_terms( $id, $value, $taxonomy ) {
			$_value = $this->get_field_value( $value, $this->get_field( $value ) );
			if ( ! empty( $_value ) ) {
				if ( is_array( $_value ) ) {
					$_value = array_map(
						function ( $v ) {
							return absint( $v );
						},
						$_value
					);
				} else {
					$_value = absint( $_value );
				}
				wp_set_object_terms( $id, $_value, $taxonomy );
			}
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
