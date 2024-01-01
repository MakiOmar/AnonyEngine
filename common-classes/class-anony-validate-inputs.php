<?php
/**
 * Inputs validation.
 *
 * PHP version 7.3 Or Later.
 *
 * @package  AnonyEngine
 * @author   Makiomar <info@makiomar.com>
 * @license  https:// makiomar.com AnonyEngine Licence.
 * @link     https:// makiomar.com/anonyengine_elements.
 */

if ( ! class_exists( 'ANONY_Validate_Inputs' ) ) {
	/**
	 * Inputs validation class.
	 *
	 * @package    AnonyEngine fields
	 * @author     Makiomar <info@makiomar.com>
	 * @license    https:// makiomar.com AnonyEngine Licence.
	 * @link       https:// makiomar.com.
	 */
	class ANONY_Validate_Inputs {
		/**
		 * Holds an array of fields and there corresponding error code as key/value pairs.
		 *
		 * @var array
		 */
		public $errors = array();

		/**
		 * Holds an array of fields and there corresponding warning code as key/value pairs.
		 *
		 * @var array
		 */
		public $warnings = array();

		/**
		 * Decide if valid input. Default is <code>false</code>.
		 *
		 * @var boolean
		 */
		public $valid = true;

		/**
		 * Inputs value.
		 *
		 * @var string
		 */
		public $value;

		/**
		 * Validations limits.
		 *
		 * @var string
		 */
		public $limits = '';

		/**
		 * Field data.
		 *
		 * @var array
		 */
		public $field;

		/**
		 * Field's validation type.
		 *
		 * @var string
		 */
		public $validation;

		/**
		 * Field's sanitization function name.
		 *
		 * @var string
		 */
		public $sanitization;

		/**
		 * Field's title.
		 *
		 * @var string
		 */
		public $field_title;

		/**
		 * Constructor.
		 *
		 * @param string|array $args An array of field's date required for validation.<br/>**Note: **Empty $args so the class can be instantiated without $args if needed.
		 */
		public function __construct( $args = '' ) {

			if ( is_array( $args ) && ! empty( $args ) ) {

				// Set field's value to the new value before validation.
				$this->value = $args['new_value'];

				if ( empty( $this->value ) && strpos( $args['field']['validate'], 'required' ) === false ) {
					return;// if level 2-1.
				}

				$this->field = $args['field'];

				// translators: %s is for field's id.
				$this->field_title = ! empty( $this->field['title'] ) ? $this->field['title'] : sprintf( __( 'Field with id %s', 'anonyengine' ), $this->field['id'] );

				if ( isset( $this->field['validate'] ) ) {

					$this->select_sanitization();

					$this->validation = $this->field['validate'];

					$this->validate_inputs();
				}
			}// if level 1.
		}//end __construct()

		/**
		 * Select sanitization function name for a field
		 *
		 * @return void
		 */
		public function select_sanitization() {

			switch ( $this->field['type'] ) {
				case 'textarea':
					$this->sanitization = 'sanitize_textarea_field';
					break;

				case 'email':
					$this->sanitization = 'sanitize_email';
					break;

				case 'url':
					$this->sanitization = 'esc_url_raw';
					break;

				case 'upload':
					$this->sanitization = 'esc_url_raw';
					break;

				default:
					$this->sanitization = 'sanitize_text_field';
					break;
			}
		}//end select_sanitization()

		/**
		 * Inputs validation base function
		 *
		 * **Description: **Invoke the corresponding validtion function according to the <code>$args['validate']</code>.<br>
		 * **Note: **<br/>
		 * * <code>$args['validate']</code> value can be equal to <code>'int|file_type:pdf,doc,docx'</code>.
		 * * validation types are separated with <code>|</code> and if the validation has any limits like supported file types, so sholud be followd by <code>:</code> then the limits.
		 * * Limits should be separated with <code>,</code>.
		 *
		 * @return void  Just set field's value after validation
		 */
		public function validate_inputs() {

			// Start checking if validation is needed.
			if ( ! is_null( $this->validation ) || ! empty( $this->validation ) ) {

				// Check if need multiple validations.
				if ( strpos( $this->validation, '|' ) !== false ) {
					$this->multiple_validation( $this->validation );

				} else {

					$this->single_validation( $this->validation );
				}// if level 2.

			}// if level 1.
		}//end validate_inputs()

		/**
		 * Decide which validation method should be called and sets validation limits.
		 *
		 * @param string $value String that contains validation and its limits.
		 * @return string Returns validation method name
		 */
		public function select_method( $value = '' ) {
			// Check if validation has limits.
			if ( strpos( $value, ':' ) !== false ) {

				$vald = explode( ':', $value );

				// Set Validation limits.
				$this->limits = $vald[1];

				// Validation method name.
				$method = 'valid_' . $vald[0];

			} else {

				// Validation method name.
				$method = 'valid_' . $value;

			}// if level 1.

			return $method;
		}//end select_method()

		/**
		 * Call validation method if the validation is single. e.g. url
		 *
		 * @param string $validation Validation string. can be something like (file_type: pdf, docx)..
		 *
		 * @return void
		 */
		public function single_validation( $validation = '' ) {

			$method = $this->select_method( $validation );
			// Apply validation method.
			if ( method_exists( $this, $method ) ) {
				$this->$method();
			}
		}//end single_validation()

		/**
		 * Call validation method if the validation is multiple. e.g. url|file_type: pdf,docx.
		 *
		 * @param string $validations Validation string.
		 * @return void
		 */
		public function multiple_validation( $validations = '' ) {

			// Array to hold validation types.
			$_validations = explode( '|', $validations );
			// Validate fore each validation type.
			foreach ( $_validations as $validation ) {

				$this->single_validation( $validation );

			}// forach.
		}//end multiple_validation()

		/**
		 * Sanitize field value dynamicaly
		 *
		 * @return string|array  Sanitized value/s
		 */
		public function sanitize() {
			$sanitization = $this->sanitization;

			if ( is_array( $this->value ) ) {
				// Temporary array to hold sanitized values.
				$temp_value = array();

				foreach ( $this->value as $key => $value ) {

					$temp_value[ $key ] = $sanitization( urldecode( $this->value ) );
				}
				$this->value = $temp_value;

			} else {
				$this->value = $sanitization( urldecode( $this->value ) );
			}

			return $this->value;
		}

		/**
		 * Check through multiple options (select, radio, multi-checkbox)
		 */
		public function valid_multiple_options() {
			$this->valid_no_html();
		}//end valid_multiple_options()

		/**
		 * Accept html within input.
		 */
		public function valid_html() {

			$this->value = $this->value;
		}//end valid_html()


		/**
		 * Validate multi-value input
		 */
		public function valid_multi_value() {
			if ( is_array( $this->value ) ) {
				foreach ( $this->value as $index => $value ) {
					// Check if all supplied values are empty.
					if ( implode( '', $value ) === '' ) {
						unset( $this->value[ $index ] );
					}
				}
			}
		}//end valid_multi_value()


		/**
		 * Accept html within input.
		 */
		public function valid_tabs() {
			if ( is_array( $this->value ) ) {
				$count = array_shift( $this->value );
				if ( ! ctype_digit( $count ) ) {
					$count = count( $this->value ) + 1;
				}
				$temp = array();

				$temp['count'] = $count;
				foreach ( $this->value as $name => $v ) {
					foreach ( $v as $key => $value ) {
						$value = wp_strip_all_tags( $value );

						$temp[ $name ][ $key ] = $value;

					}

					$temp_name_values = array_values( $temp[ $name ] );

					// Check if all supplied values are empty.
					if ( implode( '', $temp_name_values ) === '' ) {
						unset( $temp[ $name ] );
					}
				}
				$temp['count'] = empty( $temp ) ? 2 : count( $temp ) + 1;

				$this->value = $temp;
			}
		}//end valid_tabs()

		/**
		 * Date validation.
		 */
		public function valid_date() {

			$timestamp = strtotime( $this->value );

			if ( false === $timestamp ) {
				$this->valid = false;
				return $this->set_error_code( 'not-date' );
			}
		}//end valid_date()

		/**
		 * Remove html within input
		 */
		public function valid_no_html() {

			if ( is_array( $this->value ) ) {

				foreach ( $this->value as $value ) {

					if ( intval( wp_strip_all_tags( $value ) ) !== intval( $value ) ) {
						$this->valid = false;

						return $this->set_error_code( 'remove-html' );
					}
				}
			} elseif ( intval( wp_strip_all_tags( $this->value ) ) !== intval( $this->value ) ) {

					$this->valid = false;

					return $this->set_error_code( 'remove-html' );
			}

			$this->sanitize();
		}//end valid_no_html()

		/**
		 * Check valid email
		 */
		public function valid_email() {

			if ( '#' === $this->value ) {
				return;
			}

			if ( ! is_email( $this->value ) ) {

				$this->valid = false;

				return $this->set_error_code( 'not-email' );
			}

			$this->sanitize();
		}//end valid_email()

		/**
		 * Check valid url
		 */
		public function valid_url() {

			if ( '#' === $this->value || empty( $this->value ) ) {
				return;
			}

			if ( ! filter_var( ( $this->value ), FILTER_VALIDATE_URL ) ) {
				$this->valid = false;

				return $this->set_error_code( 'not-url' );

			}

			$this->sanitize();
		}//end valid_url()

		/**
		 * Check if valid number.
		 */
		public function valid_number() {

			if ( preg_replace( '/[0-9\.\-]/', '', $this->value ) !== '' ) {

				$this->valid = false;

				return $this->set_error_code( 'not-number' );
			}

			$this->sanitize();
		}//end valid_number()

		/**
		 * Check valid integer
		 */
		public function valid_abs() {

			if ( ! ctype_digit( $this->value ) ) {

				$this->valid = false;

				$this->set_error_code( 'not-abs' );
			} else {

				$this->sanitize();

			}
		}//end valid_abs()

		/**
		 * Check againest required.
		 */
		public function valid_required() {

			if ( '' === $this->value || is_null( $this->value ) ) {
				$this->valid = false;

				$this->set_error_code( 'required' );
			} else {
				$this->sanitize();
			}
		}

		/**
		 * Check valid file type
		 */
		public function valid_file_type() {

			$limits = explode( ',', $this->limits );

			$ext = pathinfo( esc_url( $this->value ), PATHINFO_EXTENSION );

			if ( ! empty( $limits ) && ! in_array( $ext, $limits, true ) ) {

				$this->valid = false;

				return $this->set_error_code( 'unsupported' );

			}

			$this->sanitize();
		}//end valid_file_type()

		/**
		 * Check valid hex color
		 */
		public function valid_hex_color() {

			if ( is_array( $this->value ) ) {

				foreach ( $this->value as $key => $hex ) {

					if ( ! $this->is_hex_color( $hex ) ) {
						$this->valid = false;
						break; // Break if any of values is not a hex color.
					}// if level 2.
				}// foreach.

			} elseif ( ! $this->is_hex_color( $this->value ) ) {
				$this->valid = false;
			}

			if ( ! $this->valid ) {
				return $this->set_error_code( 'not-hex' );
			}

			$this->sanitize();
		}

		/**
		 * Check if a string is hex color.
		 *
		 * @param string $_string String to be check.
		 * @return bool  Returns true if is valid hex or false if not.
		 */
		public function is_hex_color( $_string ) {

			if ( empty( $_string ) ) {
				return true;
			}

			$check_hex = preg_match( '/^#[a-f0-9]{3,6}$/i', $_string );

			if ( ! $check_hex || 0 === $check_hex ) {
				return false;
			}

			return true;
		}//end is_hex_color()

		/**
		 * Set error message code.
		 *
		 * @param string $code Error message's code.
		 * @return void
		 */
		public function set_error_code( $code ) {
			if ( ! $this->valid ) {

				$this->errors[ $this->field['id'] ] = array(
					'code'  => $code,
					'title' => $this->field_title,
				);
			}// if level 1.
		}//end set_error_code()

		/**
		 * Gets the error message attached to $code
		 *
		 * @param string $code Message code.
		 * @param string $field_id Field id to be shown with message.
		 * @param string $field_title Field title.
		 * @return string The error message
		 */
		public static function get_error_msg( $code, $field_id = '', $field_title = '' ) {

			if ( empty( $code ) ) {
				return;
			}

			$accepted_tags = array(
				'strong' => array(),
				'a'      => array(
					'href'    => array(),
					'class'   => array(),
					'data-id' => array(),
				),
			);

			switch ( $code ) {
				case 'unsupported':
					return sprintf(
						wp_kses(
							// translators: %1$s Field ID, %2$s Here text.
							__( '<a href="#fieldset_%1$s" data-id="fieldset_%1$s" class="anony-validation-error"><strong>%2$s:</strong></a> Sorry!! Please select another file, the selected file type is not supported. <a>', 'anonyengine' ),
							$accepted_tags
						),
						$field_id,
						! empty( $field_title ) ? $field_title : esc_html__( 'Here', 'anonyengine' )
					);

				case 'not-date':
					return sprintf(
						wp_kses(
							// translators: %1$s Field ID, %2$s Here text.
							__( '<a href="#fieldset_%1$s" data-id="fieldset_%1$s" class="anony-validation-error"><strong>%2$s:</strong></a> Sorry!! The entered date is not valid', 'anonyengine' ),
							$accepted_tags
						),
						$field_id,
						! empty( $field_title ) ? $field_title : esc_html__( 'Here', 'anonyengine' )
					);

				case 'incorrect-date-format':
					return sprintf(
						wp_kses(
							// translators: %1$s Field ID, %2$s Here text.
							__( '<a href="#fieldset_%1$s" data-id="fieldset_%1$s" class="anony-validation-error"><strong>%2$s:</strong></a> Sorry!! Date format is not supported', 'anonyengine' ),
							$accepted_tags
						),
						$field_id,
						! empty( $field_title ) ? $field_title : esc_html__( 'Here', 'anonyengine' )
					);

				case 'not-number':
					return sprintf(
						wp_kses(
							// translators: %1$s Field ID, %2$s Here text.
							__( '<a href="#fieldset_%1$s" data-id="fieldset_%1$s" class="anony-validation-error"><strong>%2$s:</strong></a> Please enter a valid number (e.g. 1,2,-5)', 'anonyengine' ),
							$accepted_tags
						),
						$field_id,
						! empty( $field_title ) ? $field_title : esc_html__( 'Here', 'anonyengine' )
					);

				case 'not-url':
					return sprintf(
						wp_kses(
							// translators: %1$s Field ID, %2$s Here text.
							__( '<a href="#fieldset_%1$s" data-id="fieldset_%1$s" class="anony-validation-error"><strong>%2$s:</strong></a> You must provide a valid URL', 'anonyengine' ),
							$accepted_tags
						),
						$field_id,
						! empty( $field_title ) ? $field_title : esc_html__( 'Here', 'anonyengine' )
					);

				case 'not-email':
					return sprintf(
						wp_kses(
							// translators: %1$s Field ID, %2$s Here text.
							__( '<a href="#fieldset_%1$s" data-id="fieldset_%1$s" class="anony-validation-error"><strong>%2$s:</strong></a> You must enter a valid email address.', 'anonyengine' ),
							$accepted_tags
						),
						$field_id,
						! empty( $field_title ) ? $field_title : esc_html__( 'Here', 'anonyengine' )
					);

				case 'remove-html':
					return sprintf(
						wp_kses(
							// translators: %1$s Field ID, %2$s Here text.
							__( '<a href="#fieldset_%1$s" data-id="fieldset_%1$s" class="anony-validation-error"><strong>%2$s:</strong></a> HTML is not allowed', 'anonyengine' ),
							$accepted_tags
						),
						$field_id,
						! empty( $field_title ) ? $field_title : esc_html__( 'Here', 'anonyengine' )
					);

				case 'not-abs':
					return sprintf(
						wp_kses(
							// translators: %1$s Field ID, %2$s Here text.
							__( '<a href="#fieldset_%1$s" data-id="fieldset_%1$s" class="anony-validation-error"><strong>%2$s:</strong></a> You must enter an absolute integer', 'anonyengine' ),
							$accepted_tags
						),
						$field_id,
						! empty( $field_title ) ? $field_title : esc_html__( 'Here', 'anonyengine' )
					);

				case 'not-hex':
					return sprintf(
						wp_kses(
							// translators: %1$s Field ID, %2$s Here text.
							__( '<a href="#fieldset_%1$s" data-id="fieldset_%1$s" class="anony-validation-error"><strong>%2$s:</strong></a> You must enter a valid hex color', 'anonyengine' ),
							$accepted_tags
						),
						$field_id,
						! empty( $field_title ) ? $field_title : esc_html__( 'Here', 'anonyengine' )
					);

				case 'strange-options':
					return sprintf(
						wp_kses(
							// translators: %1$s Field ID, %2$s Here text.
							__( '<a href="#fieldset_%1$s" data-id="fieldset_%1$s" class="anony-validation-error"><strong>%2$s:</strong></a> Unvalid option/s', 'anonyengine' ),
							$accepted_tags
						),
						$field_id,
						! empty( $field_title ) ? $field_title : esc_html__( 'Here', 'anonyengine' )
					);

				case 'required':
					return sprintf(
						wp_kses(
							// translators: %1$s Field ID, %2$s Here text.
							__( '<a href="#fieldset_%1$s" data-id="fieldset_%1$s" class="anony-validation-error"><strong>%2$s:</strong></a> This is a required field', 'anonyengine' ),
							$accepted_tags
						),
						$field_id,
						! empty( $field_title ) ? $field_title : esc_html__( 'Here', 'anonyengine' )
					);

				case 'invalid-nonce':
					return esc_html__( 'Maybe cheater!!!', 'anonyengine' );

				default:
					return wp_kses(
						__( '<strong>Sorry!! Something wrong:</strong> Please make sure all your inputs are correct', 'anonyengine' ),
						$accepted_tags
					);
			}// switch.
		}//end get_error_msg()
	}
}
