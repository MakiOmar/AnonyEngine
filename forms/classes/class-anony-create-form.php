<?php
/**
 * AnonyEngine forms' creation  file.
 *
 * PHP version 7.3 Or Later.
 *
 * @package  AnonyEngine
 * @author   Makiomar <info@makiomar.com>
 * @license  https://makiomar.com AnonyEngine Licence
 * @link     https://makiomar.com/anonyengine
 */

defined( 'ABSPATH' ) || die(); // Exit if accessed directly.

if ( ! class_exists( 'ANONY_Create_Form' ) ) {

	/**
	 * AnonyEngine forms' creation class.
	 *
	 * @package    AnonyEngine
	 * @author     Makiomar <info@makiomar.com>
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
		 * An array of inputs that have same HTML markup.
		 *
		 * @var array
		 */
		public $mixed_types = array( 'text', 'number', 'email', 'password', 'url', 'hidden' );

		/**
		 * A list of actions the form should perfom.
		 *
		 * @var array
		 */
		public $action_list = array();

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
		public $form;

		/**
		 * Form fields.
		 *
		 * @var array
		 */
		public $fields;

		/**
		 * Form fields layout.
		 *
		 * @var array
		 */
		public $fields_layout = 'rows';

		/**
		 * Form submit label.
		 *
		 * @var array
		 */
		public $submit_label;


		/**
		 * Form error messages.
		 *
		 * @var array
		 */
		public $error_msgs;


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
		 * Holds form actions results.
		 *
		 * @var array
		 */
		public $results = array();

		/**
		 * Constructor.
		 *
		 * @param array $form A multi-dimensional array of form's fields.
		 */
		public function __construct( array $form ) {

			$this->form = $form;
			$this->id     = ! empty( $this->form['id'] ) ? $this->form['id'] : '';
			$this->fields = ! empty( $this->form['fields'] ) ? $this->form['fields'] : array();
			$this->default_values();

			$this->form_attributes = $this->form_attributes( $this->form );

			// Set form Settings.
			if ( isset( $settings ) && is_array( $settings ) ) {
				$this->form_settings( $settings );
			}

			if ( isset( $this->form['action_list'] ) && is_array( $this->form['action_list'] ) ) {
				$this->action_list = $this->form['action_list'];
			}

			

			if (
				count( array_intersect( $this->form_init, array_keys( $this->form ) ) ) !== count( $this->form_init )
				||
				'' === $this->id
			) {
				//phpcs:disable
				error_log( printf( 'Form must be of structure %s', var_export( $this->form_init, true ) ) );
				//phpcs:enable.
				return;
			} else {

				if ( ! empty( $this->form['fields_layout'] ) ) {
					$this->fields_layout = $this->form['fields_layout'];
				}

				$this->submit_label = isset( $this->form['submit_label'] ) && ! empty( $this->form['submit_label'] ) ? $this->form['submit_label'] : __( 'Submit', 'anonyengine' );

				add_shortcode( $this->id, array( $this, 'create_shortcode' ) );

				// Submitted form.
				add_action( 'template_redirect', array( $this, 'form_submitted' ) );

				add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
			}
		}

		/**
		 * Set default values
		 *
		 * @return void
		 */
		protected function default_values() {
			$fields = array();
			if ( ! empty( $this->form['action_list'] ) ) {
				foreach ( $this->form['action_list'] as $action => $configs ) {
					if ( 'Profile' === $action ) {
						$profile_id = get_user_meta( get_current_user_id(), 'anony_user_profile', true );

						if ( $profile_id && ! empty( $profile_id ) ) {
							foreach ( $configs as $config => $mappings ) {
								if ( 'post_data' === $config ) {
									foreach ( $mappings as $post_field => $value ) {
										$field = $this->get_field( $value );
										if ( $field ) {
											$field['default'] = get_post_field( $post_field, absint( $profile_id ) );
											$fields[]         = $field;
										}
									}
								}

								if ( 'meta' === $config ) {
									foreach ( $mappings as $meta_key => $value ) {
										$field = $this->get_field( $value );
										if ( $field ) {
											$field['default'] = get_post_meta( absint( $profile_id ), $meta_key, true );
											$fields[]         = $field;
										}
									}
								}
							}
						}
					}
				}
			}

			if ( ! empty( $fields ) ) {
				$this->form['fields'] = $fields;
				$this->fields         = $fields;
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

				foreach ( $this->fields as $field ) {
					if ( $field['id'] === $input_field ) {
						return $field;
					}
				}
			}

			return false;
		}

		protected function form_attributes( $form ) {
			$attributes = '';
			if ( ! empty( $form['form_attributes'] ) ) {
				foreach ( $form['form_attributes'] as $name => $value ) {
					$attributes .= ' ' . $name . '="' . esc_attr( $value ) . '"';
				}
			}

			return $attributes;
		}

		protected function check_conditions( $form ) {
			$errors = array();
			if ( empty( $form['conditions'] ) || ! is_array( $form['conditions'] ) ) {
				return $errors;
			}
			$conditions = $form['conditions'];
			if ( ! empty( $conditions['logged_in'] ) && $conditions['logged_in'] == true && ! is_user_logged_in() ) {
				$errors[] = 'logged_in';
			}

			if ( ! empty( $conditions['user_role'] ) && is_user_logged_in() ) {
				$current_roles = ANONY_Wp_User_Help::get_current_user_roles();

				$intersect = array_intersect( $current_roles, $conditions['user_role'] );

				if ( empty( $intersect ) ) {
					$errors[] = 'user_role';
				}
			}

			return $errors;
		}

		protected function render_conditions_errors( $errors ) {
			$html = '';
			foreach ( $errors as $code ) {

				if ( 'logged_in' === $code ) {
					echo '<p class="form-error">' . $this->get_error_message( $code ) . '</p>';
					break;
				} else {
					$html .= '<li class="form-error">' . $this->get_error_message( $code ) . '</li>';
				}
			}

			if ( ! empty( $html ) ) { ?>
				<ul class="form-errors">
					<?php echo $html; ?>
				</ul>
				<?php
			}
		}

		public function render() {
			$this->create( $this->fields );
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
		protected function render_submit_errors() {
			$this->error_msgs = get_transient( 'anony_form_submit_errors_' . $this->id );

			if ( false !== $this->error_msgs ) {
				echo '<ul>';
				foreach ( $this->error_msgs as $msg ) {
					//phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped
					echo '<li>' . $msg . '</li>';
					//phpcs:enable.
				}
				echo '</ul>';

				delete_transient( 'anony_form_submit_errors_' . $this->id );
			}
		}
		/**
		 * Form render function.
		 *
		 * @param array $fields An array of fields.
		 */
		public function create( array $fields ) {
			$condition_errors = $this->check_conditions( $this->form );
			if ( ! empty( $condition_errors ) ) {
				$this->render_conditions_errors( $condition_errors );

				return;
			}

			$this->render_submit_errors();
			if ( 'columns' === $this->fields_layout ) {
				?>
					<style>

						fieldset.anony-row{
							flex-direction:column;
							align-items: flex-start;
						}
					</style>
				<?php
			}
			?>
			<form id="<?php echo esc_attr( $this->id ); ?>" class="anony-form"  <?php echo $this->form_attributes; ?>>

				<?php
				foreach ( $fields as $field ) :

					$args = array(
						'field'      => $field,
						'context'    => 'form',
						'metabox_id' => $this->id,
					);

					if ( class_exists( 'ANONY_Input' ) ) {

						$render_field = new ANONY_Input( $args );

					} else {
						// Deprecated ANONY_Input_Field.
						$render_field = new ANONY_Input_Field( $field, $this->id, 'form' );
					}

					//phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped
					echo $render_field->field_init();
					//phpcs:enable.
				endforeach;
				wp_nonce_field( 'anony_form_submit_' . $this->id, 'anony_form_submit_nonce_' . $this->id );
				do_action( 'anony_form_fields', $fields );
				?>
				<p>
					<button type="submit" id="submit-<?php echo esc_attr( $this->id ); ?>" class="button-primary" name="submit-<?php echo esc_attr( $this->id ); ?>" value="submit-<?php echo esc_attr( $this->id ); ?>"><?php echo esc_html( $this->submit_label ); ?></button>
				</p>

			</form>
			<?php

			do_action( 'anony_form_after', $fields );
		}

		/**
		 * Form settings.
		 *
		 * @param array $form_settings Form's settings.
		 * @return void
		 */
		public function form_settings( array $form_settings ) {
			$this->settings['inline_lable'] = true;

			$this->settings = ANONY_ARRAY_HELP::defaults_mapping( $this->settings, $form_settings );
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

			$not_validated = wp_unslash( $_POST );

			// Verify nonce.
			if ( ! isset( $not_validated[ 'anony_form_submit_nonce_' . $this->id ] ) || ! wp_verify_nonce( $not_validated[ 'anony_form_submit_nonce_' . $this->id ], 'anony_form_submit_' . $this->id ) ) {
				return;
			}
			foreach ( $fields as $field ) :
				if ( ! isset( $not_validated[ $field['id'] ] ) ) {
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
			$not_validated = wp_unslash( $_POST );
			//phpcs:enable.

			// Types that can't be validated.
			if ( in_array( $field['type'], $this->no_validation, true ) ) {
				return;
			}
			$field_id = $field['id'];
			// Check if validation required.
			if ( isset( $field['validate'] ) ) {

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

			$not_validated = wp_unslash( $_REQUEST );

			if ( ! isset( $not_validated[ 'submit-' . $this->id ] ) ) {
				return;
			}

			// Verify nonce.
			if ( ! isset( $not_validated[ 'anony_form_submit_nonce_' . $this->id ] ) || ! wp_verify_nonce( $not_validated[ 'anony_form_submit_nonce_' . $this->id ], 'anony_form_submit_' . $this->id ) ) {
				return;
			}

			// Validation.
			$this->validate_form_fields( $this->fields ); // Validation problem because fields' ids looks like field[key].

			if ( isset( $this->error_msgs ) ) {
				set_transient( 'anony_form_submit_errors_' . $this->id, $this->error_msgs );

				return;
			}

			if ( ! empty( $this->action_list ) ) {

				foreach ( $this->action_list as $action => $action_data ) {
					$class_name = "ANONY_{$action}";
					if ( class_exists( $class_name ) ) :

						$obj = new $class_name( $this->validated, $action_data, $this );

						if ( isset( $obj->result ) && $obj->result ) {
							$this->results[ $action ] = $obj->result;
						}

					endif;
				}
			}

			if ( ! empty( $this->results ) ) {
				// error_log(print_r($this->results, true));
			}

			do_action( 'anony_form_submitted', $this->validated, $this->id );
		}


		protected function get_error_message( $code ) {

			switch ( $code ) {
				case 'logged_in':
					return esc_html__( 'You must be logged in.', 'anonyengine' ) . ' ' . '<a href=" ' . esc_url( wp_login_url( get_permalink() ) ) . '" alt="' . esc_attr__( 'Login Now', 'anonyengine' ) . '">' . esc_html__( 'Login Now', 'anonyengine' ) . '</a>';
					break;

				case 'user_role':
					return esc_html__( 'You are not allowed to do this.', 'anonyengine' );
					break;
			}
		}

		public function enqueue_scripts() {
			anony_enqueue_styles();
			// Enqueue fields scripts.
			new ANONY_Fields_Scripts( $this->fields );
		}
	}
}
