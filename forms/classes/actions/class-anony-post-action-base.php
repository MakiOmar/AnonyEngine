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

if ( ! class_exists( 'ANONY_Post_Action_Base' ) ) {

	/**
	 * AnonyEngine post insertion class.
	 *
	 * @package AnonyEngine
	 * @author  Makiomar <info@makiomar.com>
	 * @license https:// makiomar.com AnonyEngine Licence.
	 * @link    https:// makiomar.com
	 */
	class ANONY_Post_Action_Base extends ANONY_Actions_Base {


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
		 * Post's data
		 *
		 * @var array
		 */
		public $post_data;

		/**
		 * Actions's data
		 *
		 * @var array
		 */
		public $action_data;

		/**
		 * Post's ID
		 *
		 * @var int
		 */
		public $post_id = false;

		/**
		 * Object's ID
		 *
		 * @var int
		 */
		public $object_id = false;

		/**
		 * Request
		 *
		 * @var array
		 */
		public $requested;

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
			if ( ! isset( $action_data['post_data'] ) ) {
				//phpcs:disable WordPress.PHP.DevelopmentFunctions.error_log_error_log
				error_log( 'Class ' . get_class( $this ) . ' : Missing post_data parameter' );
				//phpcs:enable
				return;
			}
			$this->requested   = $validated_data;
			$this->post_data   = $action_data['post_data'];
			$this->action_data = $action_data;
			// Argumnets sent from the form.
			$diff = array_diff( self::REQUIRED_ARGUMENTS, array_keys( $this->post_data ) );

			if ( ! empty( $diff ) ) {
				//phpcs:disable WordPress.PHP.DevelopmentFunctions.error_log_error_log
				error_log( 'Class' . get_class( $this ) . ' : post_data parameter missing required keys' );
				//phpcs:enable
				return;
			}

			if ( ! ANONY_HELP::empty( $this->post_data['post_type'], $this->post_data['post_status'], $this->post_data['post_title'] ) ) {

				$args = $this->get_post_data_args();

				$this->object_id = $this->get_object_id();

				if ( ! $this->object_id ) {
					$this->post_id = wp_insert_post( $args );

				} else {
					if ( ! $this->can_edit() ) {
						return;
					}
					$args['ID'] = absint( $this->object_id );

					wp_update_post( $args );

					$this->post_id = absint( $this->object_id );
				}
			}

			if ( $this->post_id && ! is_wp_error( $this->post_id ) ) {
				$this->update_meta( $this->post_id );
				$this->wp_set_object_terms( $this->post_id );
				$this->result = $this->post_id;

			}

			$this->after_post_action( $this );
		}

		/**
		 * Check if current user can edit the post
		 *
		 * @return boolean
		 */
		protected function can_edit() {
			$post_author = get_post_field( 'post_author', $this->object_id );

			if ( empty( $post_author ) || ! is_numeric( $post_author ) || absint( $post_author ) !== get_current_user_id() ) {
				return false;
			}

			return true;
		}

		/**
		 * After success post action
		 *
		 * @param ANONY_Post_Action_Base $post_action Post action object.
		 * @return mixed
		 */
		protected function after_post_action( ANONY_Post_Action_Base $post_action ) {
			return $post_action;
		}

		/**
		 * Get Object ID
		 *
		 * @return int|bool
		 */
		protected function get_object_id() {
			if ( isset( $this->requested['object_id'] ) ) {
				return absint( $this->requested['object_id'] );
			}
			return false;
		}

		/**
		 * Set object terms
		 *
		 * @param int $object_id object's ID.
		 * @return void
		 */
		protected function wp_set_object_terms( $object_id ) {
			$action_data = $this->action_data;
			if ( ! empty( $action_data['tax_query'] ) && is_array( $action_data['tax_query'] ) ) {
				foreach ( $action_data['tax_query'] as $taxonomy => $value ) {
					if ( is_array( $value ) ) {
						$_value = array();
						foreach ( $value as $v ) {
							$_value[] = absint( $this->get_field_value( $v, $this->get_field( $v ) ) );
						}
						wp_set_object_terms( $object_id, $_value, $taxonomy );
					} else {
						$this->set_object_terms( $object_id, $value, $taxonomy );
					}
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
		 * Update meta
		 *
		 * @param int $object_id object's ID.
		 * @return void
		 */
		protected function update_meta( $object_id ) {
			$action_data = $this->action_data;
			$args        = array( 'ID' => $object_id );
			if ( ! empty( $action_data['meta'] && is_array( $action_data['meta'] ) ) ) {
				foreach ( $action_data['meta'] as $key => $value ) {
						$_value = $this->get_field_value( $value, $this->get_field( $value ) );
					if ( ! empty( $_value ) ) {
						$args['meta_input'][ $key ] = $_value;
					}
				}
			}

			wp_update_post( $args );
		}
		/**
		 * Get post data arguments
		 *
		 * @return array
		 */
		protected function get_post_data_args() {
			$post_data = $this->post_data;
			$args      = array(
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

			return $args;
		}
	}

}
