<?php
/**
 * Woocommerce custom registration fields.
 *
 * PHP version 7.3 Or Later.
 *
 * @package  AnonyEngine
 * @author   Makiomar <info@makior.com>
 * @license  https:// makiomar.com AnonyEngine Licence.
 * @link     https:// makiomar.com/anonyengine_elements.
 */

defined( 'ABSPATH' ) || die(); // Exit if accessed direct.

if ( ! class_exists( 'ANONY_Woocommerce_Registration_Fields' ) ) {

	/**
	 * Woocommerce custom registration fields.
	 *
	 * @package    AnonyEngine Woocommerce
	 * @author     Makiomar <info@makior.com>
	 * @license    https:// makiomar.com AnonyEngine Licence.
	 * @link       https:// makiomar.com.
	 */
	class ANONY_Woocommerce_Registration_Fields {


		/**
		 * Registration fields.
		 *
		 * @var array
		 */
		public $registration_fields;

		/**
		 * Field position.
		 *
		 * Use 'end' for woocommerce_register_form and 'start' for woocommerce_register_form_start . Default is 'end'.
		 *
		 * @var array
		 */
		public $field_position = 'end';

		/**
		 * Filter registration fields.
		 *
		 * @param array $fields Custom registration fields array.
		 */
		public function __construct( array $fields = array() ) {

			$this->registration_fields = $this->registration_fields( $fields );

			$this->init();
			add_action( 'woocommerce_register_form', array( $this, 'nonce_field' ) );

			add_action( 'woocommerce_register_post', array( $this, 'validate' ), 10, 3 );

			add_action( 'woocommerce_created_customer', array( $this, 'update_user_meta' ) );

		}

		/**
		 * Insert new user registration meta keys/values.
		 *
		 * @param int $customer_id Customer ID.
		 * @return void
		 */
		public function update_user_meta( $customer_id ) {

			if ( isset( $this->submitted_data ) && ! empty( $this->submitted_data ) ) {

				foreach ( $this->submitted_data as $meta_key => $meta_value ) {
					update_user_meta( $customer_id, $meta_key, $meta_value );
				}
			}
		}
		/**
		 * Filter registration fields.
		 *
		 * @param array $fields Custom registration fields array.
		 * @return array An array of registratio fields
		 */
		protected function registration_fields( $fields ) {
			return apply_filters( 'anony_woocommerce_registraion_fields', $fields );
		}

		/**
		 * Decider the position of registration fields.
		 *
		 * @return string Woocommerce registration form hook
		 */
		protected function decide_position() {

			switch ( $this->field_position ) {
				case 'end':
					return 'woocommerce_register_form';

				case 'start':
					return 'woocommerce_register_form_start';

				default:
					return 'woocommerce_register_form';

			}
		}

		/**
		 * Generate nonce.
		 *
		 * @return void
		 */
		public function nonce_field() {
			wp_nonce_field( 'anony_woocommerce_registration_action', 'anony_woocommerce_registration_nonce' );
		}
		/**
		 * Initialize fields.
		 *
		 * @return void
		 */
		protected function init() {

			if ( array() === $this->registration_fields ) {
				return;
			}

			foreach ( $this->registration_fields as $field_name => $field_data ) {

				if ( isset( $field_data['position'] ) ) {
					$this->field_position = $field_data['position'];
				}

				add_action(
					$this->decide_position(),
					function() use ( $field_name, $field_data ) {

						$render_field = new ANONY_Input_Field( $field_data, null, 'form' );

						// phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped
						echo $render_field->field_init();
						// phpcs:enable

					}
				);

			}
		}

		/**
		 * Validate field value
		 *
		 * @param array $field     Field's data array.
		 * @param mixed $new_value Field's new value.
		 * @return object          Validation object.
		 */
		protected function validate_field( $field, $new_value ) {
			$args     = array(
				'field'     => $field,
				'new_value' => $new_value,
			);
			$validate = new ANONY_Validate_Inputs( $args );

			return $validate;
		}

		/**
		 * Callback for Woocommerce registration validation.
		 *
		 * @param string $username     Customer username.
		 * @param string $email Customer email address.
		 * @param string $validation_errors Error object.
		 * @return \WP_Error          Validation object.
		 */
		public function validate( $username, $email, $validation_errors ) {

			$submitted_data = wp_unslash( $_POST );

			if ( array() === $this->registration_fields
				|| ! isset( $submitted_data['anony_woocommerce_registration_nonce'] )
				|| ! wp_verify_nonce( $submitted_data['anony_woocommerce_registration_nonce'], 'anony_woocommerce_registration_action' ) ) {

				$validation_errors->add( 'anony_nonce_error', esc_html__( 'Maybe cheater!!!', 'anonyengine' ) );

				return $validation_errors;
			}

			foreach ( $this->registration_fields as $field_name => $field ) {

				if ( ! isset( $field['validate'] ) || ! isset( $submitted_data[ $field_name ] ) ) {
					$this->submitted_data[ $field_name ] = $validated->value;
					continue;
				}

				$value = $submitted_data[ $field_name ];

				$validated = $this->validate_field( $field, $value );

				if ( ! empty( $validated->errors ) ) {

					foreach ( $validated->errors as $id => $codes ) {

						foreach ( $codes as $code ) {

							$validation_errors->add( $id . '_error', $validated->get_error_msg( $code, $id ) );

						}
					}
				} else {
					$this->submitted_data[ $field_name ] = $validated->value;
				}
			}

			return $validation_errors;

		}
	}
}
