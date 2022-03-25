<?php
/**
 * AnonyEngine forms' creation  file.
 *
 * PHP version 7.3 Or Later.
 *
 * @package  AnonyEngine
 * @author   Makiomar <info@makior.com>
 * @license  https://makiomar.com AnonyEngine Licence
 * @link     https://makiomar.com/anonyengine_elements
 */

defined( 'ABSPATH' ) || die(); // Exit if accessed directly.

if ( ! class_exists( 'ANONY_Create_Form' ) ) {

	/**
	 * AnonyEngine forms' creation class.
	 *
	 * @package    AnonyEngine
	 * @author     Makiomar <info@makior.com>
	 * @license    https:// makiomar.com AnonyEngine Licence.
	 * @link       https:// makiomar.com
	 */
	class ANONY_Create_Form {

		/**
		 * Form's ID. should be unique foreach form.
		 *
		 * @var string
		 */
		public $id = null;

		/**
		 * Form's method.
		 *
		 * @var string
		 */
		public $method = 'post';

		/**
		 * Form's action.
		 *
		 * @var string
		 */
		public $action = '';

		/**
		 * Form's attributes.
		 *
		 * @var string
		 */
		public $form_attributes = '';

		/**
		 * Form errors.
		 *
		 * @var array
		 */
		public $errors = array();

		/**
		 * Form settings.
		 *
		 * @var array
		 */
		public $settings = array();

		/**
		 * Form mandatory fields.
		 *
		 * @var array
		 */
		public $form_init = array( 'id', 'fields' );

		/**
		 * Fields that can't be validated.
		 *
		 * @var array
		 */
		public $no_validation = array( 'heading', 'group-start', 'group-close' );


		/**
		 * Form fields.
		 *
		 * @var array
		 */
		public $fields;


		/**
		 * Holds an object from ANONY_Validate_Inputs.
		 *
		 * @var object
		 */
		public $validate;

		/**
		 * Holds validated form data.
		 *
		 * @var object
		 */
		public $validated = array();

		/**
		 * Constructor.
		 *
		 * @param array $form A multi-dimensional array of form's fields.
		 */
		public function __construct( array $form ) {

			if (
				count( array_intersect( $this->form_init, array_keys( $form ) ) ) !== count( $this->form_init )
				||
				'' === $form['id']
			) {
				$this->errors['missing-for-id'] = esc_html__( 'Form id is missing' );
			}

			// Set form Settings.
			if ( isset( $settings ) && is_array( $settings ) ) {
				$this->form_settings( $settings );
			}

			$this->id           = $form['id'];
			$this->fields       = $form['fields'];
			$this->submit_label = isset( $form['submit_label'] ) && ! empty( $form['submit_label'] ) ? $form['submit_label'] : __( 'Submit', 'anonyengine' );

			add_shortcode( $this->id, array( $this, 'create_shortcode' ) );

			// Submitted form.
			add_action( 'init', array( $this, 'form_submitted' ) );

		}

		/**
		 * Form render shortcode callback.
		 *
		 * @return string Rendered form.
		 */
		public function create_shortcode() {
			ob_start();
			$this->create( $this->fields );
			return ob_get_clean();
		}

		/**
		 * Form render shortcode callback.
		 *
		 * @param array $form_settings Form's settings.
		 * @return void
		 */
		public function form_settings( array $form_settings ) {
			$this->settings['inline_lable'] = true;

			$this->settings = ANONY_ARRAY_HELP::defaults_mapping( $this->settings, $form_settings );

		}

		/**
		 * Form render function.
		 *
		 * @param array $fields An array of fields.
		 */
		public function create( array $fields ) {

			$this->error_msgs = get_transient( 'anony_form_errors_' . $this->id );

			if ( false !== $this->error_msgs ) {
				echo '<ul>';
				foreach ( $this->error_msgs as $msg ) {
					//phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped
					echo '<li>' . $msg . '</li>';
					//phpcs:enable.
				}
				echo '</ul>';

				delete_transient( 'anony_form_errors_' . $this->id );
			}
			?>
			<form id="<?php echo esc_attr( $this->id ); ?>" class="anony-form" action="<?php echo esc_attr( $this->action ); ?>" method="<?php echo esc_attr( $this->method ); ?>" <?php echo esc_html( $this->form_attributes ); ?>>

				<?php
				foreach ( $fields as $field ) :
					$render_field = new ANONY_Input_Field( $field, $this->id, 'form' );
					//phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped
					echo $render_field->field_init();
					//phpcs:enable.
				endforeach;
				wp_nonce_field( 'anony_form_submit_' . $this->id, 'anony_form_submit_nonce_' . $this->id );
				do_action( 'anony_form_fields', $fields );
				?>
				<p>
					<button type="submit" id="submit-<?php echo esc_attr( $this->id ); ?>" name="submit-<?php echo esc_attr( $this->id ); ?>" value="submit-<?php echo esc_attr( $this->id ); ?>"><?php echo esc_html( $this->submit_label ); ?></button>
				</p>

			</form>
			<?php

			do_action( 'anony_form_after', $fields );

		}

		/**
		 * Form validation.
		 *
		 * @param array $fields An array of fields.
		 */
		public function validate_form_fields( $fields ) {

			// Check request method.
			if ( isset( $_SERVER['REQUEST_METHOD'] ) && 'POST' !== $_SERVER['REQUEST_METHOD'] ) {
				return;
			}

			// Verify nonce.
			if ( ! isset( $_POST[ 'anony_form_submit_nonce_' . $this->id ] ) || ! wp_verify_nonce( 'anony_form_submit_nonce_' . $this->id, 'anony_form_submit_' . $this->id ) ) {
				return;
			}
			foreach ( $fields as $field ) :
				if ( ! isset( $_POST[ $field['id'] ] ) ) {
					continue;
				}
				$this->validate( $field );
			endforeach;

		}

		/**
		 * Field validation.
		 *
		 * @param array $field An array of field's arguments.
		 */
		public function validate( $field ) {

			//phpcs:disable WordPress.Security.NonceVerification.Missing
			$not_validated = $_POST;
			//phpcs:enable.

			// Types that can't be validated.
			if ( in_array( $field['type'], $this->no_validation, true ) ) {
				return;
			}

			// Check if validation required.
			if ( isset( $field['validate'] ) ) {

				$field_id = $field['id'];

				$args = array(
					'field'     => $field,
					'new_value' => $not_validated[ $field_id ],
				);

				$this->validate = new ANONY_Validate_Inputs( $args );

				// Add to errors if not valid.
				if ( ! empty( $this->validate->errors ) ) {

					$this->errors = array_merge( (array) $this->errors, (array) $this->validate->errors );

					foreach ( $this->errors as $id => $arr ) {
						$this->error_msgs[] = $this->validate->get_error_msg( $arr['code'], $id );
					}

					return;// We will not add to $validated.
				}

				$this->validated[ $field_id ] = is_null( $this->validate->value ) ? '' : $this->validate->value;

			} else {

				$this->validated[ $field_id ] = $not_validated[ $field_id ];
			}
		}

		/**
		 * Form submition proccessing.
		 */
		public function form_submitted() {

			if ( isset( $_SERVER['REQUEST_METHOD'] ) !== 'POST' && ! isset( $_POST[ 'submit-' . $this->id ] ) ) {
				return;
			}

			// Verify nonce.
			if ( ! isset( $_POST[ 'anony_form_submit_nonce_' . $this->id ] ) || ! wp_verify_nonce( 'anony_form_submit_nonce_' . $this->id, 'anony_form_submit_' . $this->id ) ) {
				return;
			}

			// Validation.
			$this->validate_form_fields( $this->fields ); // Validation problem because fields' ids looks like field[key].

			if ( isset( $this->error_msgs ) ) {
				set_transient( 'anony_form_errors_' . $this->id, $this->error_msgs );
			}

			do_action( 'anony_form_submitted', $this->validated, $this->id );

		}
	}
}
