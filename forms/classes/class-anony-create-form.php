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
		 * Object's ID.
		 *
		 * @var int
		 */
		public $object_id = false;

		/**
		 * An array of inputs that have same HTML markup.
		 *
		 * @var array
		 */
		public $mixed_types = array( 'text', 'number', 'email', 'password', 'url', 'hidden' );

		/**
		 * An array of objects IDs that the form will be use in.
		 *
		 * @var array
		 */
		public $used_in = array();

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
		 * Form context.
		 *
		 * @var string
		 */
		public $context;

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

			$this->form    = $form;
			$this->id      = ! empty( $this->form['id'] ) ? $this->form['id'] : '';
			$this->context = ! empty( $this->form['context'] ) ? $this->form['context'] : 'form';
			$this->fields  = ! empty( $this->form['fields'] ) ? $this->form['fields'] : array();
			$this->used_in = ! empty( $this->form['used_in'] ) ? $this->form['used_in'] : array();
			if ( ! empty( $this->form['defaults'] ) && ! empty( $this->form['defaults']['object_type'] ) && ! empty( $this->form['defaults']['object_id'] ) ) {
				$this->object_id = absint( $this->form['defaults']['object_id'] );
			}
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

				$this->submit_label = ! empty( $this->form['submit_label'] ) ? $this->form['submit_label'] : __( 'Submit', 'anonyengine' );

				add_shortcode( $this->id, array( $this, 'create_shortcode' ) );

				// Submitted form.
				add_action( 'template_redirect', array( $this, 'form_submitted' ) );

				add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
			}
		}

		/**
		 * Set default value
		 *
		 * @param array $fields All fields.
		 * @param array $field Field arguments.
		 * @param mixed $default_value Field's value.
		 * @return void
		 */
		protected function set_default_value( &$fields, $field, $default_value ) {
			if ( $field ) {
				$field['default'] = $default_value;

				foreach ( $this->fields as $index => $field_args ) {
					if ( $field['id'] === $field_args['id'] ) {
						$fields[ $index ] = $field;
					}
				}
			}
		}

		/**
		 * Get query variable value
		 *
		 * @param string $query_variable Query string.
		 * @return mixed Query variable value if has value otherwise false.
		 */
		protected function query_variable_value( $query_variable ) {
			//phpcs:disable
			if ( ! empty( $_GET[ $query_variable ] ) ) {
				return sanitize_text_field( wp_unslash( $_GET[ $query_variable ] ) );
			}
			//phpcs:enable.
			return false;
		}

		/**
		 * Get object id for default values
		 *
		 * @param string $object_type Object type.
		 * @param string $object_id_from Where to get id from.
		 * @return int Object ID.
		 */
		protected function get_object_id( $object_type, $object_id_from ) {
			if ( $this->object_id ) {
				return $this->object_id;
			}
			$object_id = false;
			switch ( $object_type ) {
				case 'post':
					switch ( $object_id_from ) {
						case 'current_post':
							global $post;
							$object_id = $post->ID;
							break;
						case 'query_variable':
							if ( ! empty( $this->form['defaults']['query_variable'] ) ) {
								$object_id = intval( $this->query_variable_value( $this->form['defaults']['query_variable'] ) );
							}
							break;
					}
					break;
				case 'term':
					switch ( $object_id_from ) {
						case 'current_term':
							$object_id = get_queried_object_id();
							break;
						case 'query_variable':
							if ( ! empty( $this->form['defaults']['query_variable'] ) ) {
								$object_id = intval( $this->query_variable_value( $this->form['defaults']['query_variable'] ) );
							}
							break;
					}
					break;
				case 'user':
					switch ( $object_id_from ) {
						case 'current_user':
							$object_id = get_current_user_id();
							break;
						case 'query_variable':
							if ( ! empty( $this->form['defaults']['query_variable'] ) ) {
								$object_id = intval( $this->query_variable_value( $this->form['defaults']['query_variable'] ) );
							}
							break;
					}
					break;
			}

			return $object_id;
		}

		/**
		 * Check if current user can edit the post
		 *
		 * @param integer $object_id Object's ID.
		 * @param integer $object_type Object's type.
		 * @return boolean
		 */
		protected function can_edit( $object_id, $object_type ) {
			$condition = false;
			switch ( $object_type ) {
				case ( 'post' ):
					$post_author = get_post_field( 'post_author', $object_id );

					if ( empty( $post_author ) || ! is_numeric( $post_author ) || absint( $post_author ) !== get_current_user_id() ) {
						$condition = false;
					} else {
						$condition = true;
					}
					break;
				case ( 'user' ):
					$condition = get_current_user_id() === $object_id;
					break;

				case ( 'term' ):
					$condition = current_user_can( 'manage_options' );
					break;
				default:
					$condition = true;
			}
			return $condition;
		}

		/**
		 * Set default value
		 *
		 * @param array $fields All fields.
		 * @param array $configs Action configurations.
		 * @param mixed $post_id Post's ID.
		 * @return void
		 */
		protected function set_post_default_values( &$fields, $configs, $post_id ) {

			if ( ! $this->can_edit( $post_id, 'post' ) ) {
				return;
			}

			foreach ( $configs as $config => $mappings ) {
				if ( 'post_data' === $config ) {
					foreach ( $mappings as $post_field => $value ) {

						$field = $this->get_field( $value );

						$this->set_default_value( $fields, $field, get_post_field( $post_field, absint( $post_id ) ) );
					}
				}

				if ( 'meta' === $config ) {
					foreach ( $mappings as $meta_key => $value ) {

						$field = $this->get_field( $value );

						$this->set_default_value( $fields, $field, get_post_meta( absint( $post_id ), $meta_key, true ) );
					}
				}

				if ( 'tax_query' === $config ) {
					foreach ( $mappings as $taxonomy => $value ) {

						$field = $this->get_field( $value );

						$terms = get_the_terms( absint( $post_id ), $taxonomy );

						if ( $terms && ! is_wp_error( $terms ) ) {
							if ( empty( $field['multiple'] ) ) {
								$term = $terms[0];

								$this->set_default_value( $fields, $field, $term->term_id );
							} else {
								$term_ids = array_map(
									function ( $term ) {
										return $term->term_id;
									},
									$terms
								);

								$this->set_default_value( $fields, $field, $term_ids );
							}
						}
					}
				}
			}
		}

		/**
		 * Set term default value
		 *
		 * @param array $fields All fields.
		 * @param array $configs Action configurations.
		 * @param mixed $post_id Post's ID.
		 * @return bool
		 */
		protected function set_term_default_values( &$fields, $configs, $post_id ) {
			return false;
		}

		/**
		 * Set user default value
		 *
		 * @param array $fields All fields.
		 * @param array $configs Action configurations.
		 * @param mixed $user_id Users's ID.
		 * @return void
		 */
		protected function set_user_default_values( &$fields, $configs, $user_id ) {
			$user = get_user_by( 'id', $user_id );
			if ( ! $user || is_wp_error( $user ) ) {
				return;
			}
			foreach ( $configs as $config => $mappings ) {

				if ( 'user_data' === $config ) {
					foreach ( $mappings as $user_field => $value ) {

						$field = $this->get_field( $value );

						if ( isset( $user->$user_field ) && 'user_pass' !== $user_field && 'ID' !== $user_field ) {
							$this->set_default_value( $fields, $field, $user->$user_field );
						}
					}
				}

				if ( 'meta' === $config ) {
					foreach ( $mappings as $meta_key => $value ) {

						$field = $this->get_field( $value );

						$this->set_default_value( $fields, $field, get_user_meta( absint( $user_id ), $meta_key, true ) );
					}
				}
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
							$this->set_post_default_values( $fields, $configs, $profile_id );
						}
					} elseif ( ! empty( $this->form['defaults'] ) && ! empty( $this->form['defaults']['object_type'] ) && ! empty( $this->form['defaults']['object_id_from'] ) ) {
						$object_type    = $this->form['defaults']['object_type'];
						$object_id_from = $this->form['defaults']['object_id_from'];
						$object_id      = $this->get_object_id( $object_type, $object_id_from );
					} elseif ( ! empty( $this->form['defaults'] ) && ! empty( $this->form['defaults']['object_type'] ) && ! empty( $this->form['defaults']['object_id'] ) ) {
						$object_type = $this->form['defaults']['object_type'];
						$object_id   = absint( $this->form['defaults']['object_id'] );
					}
					if ( isset( $object_id ) && is_numeric( $object_id ) ) {
						switch ( $object_type ) {
							case 'post':
								$this->set_post_default_values( $fields, $configs, $object_id );
								break;
							case 'term':
								$this->set_term_default_values( $fields, $configs, $object_id );
								break;
							case 'user':
								$this->set_user_default_values( $fields, $configs, $object_id );

								break;
						}
					}
				}
			}
			ksort( $fields );

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

		/**
		 * Get form attributes
		 *
		 * @param array $form Form arguments.
		 * @return string Form attributes.
		 */
		protected function form_attributes( $form ) {
			$attributes = '';
			if ( ! empty( $form['form_attributes'] ) ) {
				if (
					! empty( $form['defaults'] ) &&
					! empty( $form['defaults']['object_type'] ) &&
					! empty( $form['defaults']['object_id_from'] )
					) {
					$form['form_attributes']['data-object-type']    = $form['defaults']['object_type'];
					$form['form_attributes']['data-object-id-from'] = $form['defaults']['object_id_from'];
					$form['form_attributes']['data-object-id']      = $this->get_object_id( $form['defaults']['object_type'], $form['defaults']['object_id_from'] );
				}
				foreach ( $form['form_attributes'] as $name => $value ) {
					$attributes .= ' ' . $name . '="' . esc_attr( $value ) . '"';
				}
			}
			return $attributes;
		}

		/**
		 * Check against form conditions
		 *
		 * @param array $form Form arguments.
		 * @return array An array of errors. Empty if no errors.
		 */
		protected function check_conditions( $form ) {
			$errors = array();
			if (
				! empty( $this->form['defaults'] ) &&
				! empty( $this->form['defaults']['object_type'] ) && !
				empty( $this->form['defaults']['object_id_from'] )
				) {
				$object_type    = $this->form['defaults']['object_type'];
				$object_id_from = $this->form['defaults']['object_id_from'];
				$object_id      = $this->get_object_id( $object_type, $object_id_from );

				if ( $object_id && ! $this->can_edit( $object_id, $object_type ) ) {
					$errors[] = 'not_allowed';
				}
			}

			if ( empty( $form['conditions'] ) || ! is_array( $form['conditions'] ) ) {
				return $errors;
			}
			$conditions = $form['conditions'];
			if ( ! empty( $conditions['logged_in'] ) && true === $conditions['logged_in'] && ! is_user_logged_in() ) {
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

		/**
		 * Render conditions errors
		 *
		 * @param array $errors Errors array.
		 * @return void
		 */
		protected function render_conditions_errors( $errors ) {
			$html = '';
			foreach ( $errors as $code ) {

				if ( 'logged_in' === $code ) {
					//phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped
					echo '<p class="form-error">' . $this->get_error_message( $code ) . '</p>';
					//phpcs:enable.
					break;
				} else {
					$html .= '<li class="form-error">' . $this->get_error_message( $code ) . '</li>';
				}
			}

			if ( ! empty( $html ) ) { ?>
				<ul class="form-errors">
					<?php
					//phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped
					echo $html;
					//phpcs:enable.
					?>
				</ul>
				<?php
			}
		}

		/**
		 * Render form fields
		 *
		 * @param bool $_echo Weather to echo or return.
		 * @return mixed
		 */
		public function render( $_echo = true ) {
			if ( $_echo ) {
				$this->create( $this->fields );
			} else {
				return $this->create_shortcode();
			}
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
		 * Render submit errors
		 *
		 * @return void
		 */
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
			//phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped
			?>
			<form id="<?php echo esc_attr( $this->id ); ?>" class="anony-form"  <?php echo $this->form_attributes; ?>>

				<?php
				//phpcs:enable.
				foreach ( $fields as $field ) :
					if ( class_exists( 'ANONY_Input_Base' ) && class_exists( 'ANONY_Form_Input_Field' ) ) {
						$args = array(
							'form'    => $this->form,
							'field'   => $field,
							'form_id' => $this->id,
							'context' => $this->context,
						);

						$render_field = new ANONY_Form_Input_Field( $args );
					} else {
						$render_field = new ANONY_Input_Field( $field, $this->id, 'form' );
					}

					//phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped
					echo $render_field->field_init();
					//phpcs:enable.
					$mappings_data[ $field['id'] ] = $this->get_field_mapping_data( $field );
				endforeach;
				if ( ! empty( $mappings_data ) ) {
					echo '<input id="data-' . esc_attr( $this->id ) . '" type="hidden" data-value="' . esc_attr( rawurlencode( wp_json_encode( $mappings_data ) ) ) . '"/>';
				}
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
		 * Get field mapping data
		 *
		 * @param array $field Field arguments.
		 * @return array
		 */
		protected function get_field_mapping_data( $field ) {
			$mapping_data = array();
			if ( ! empty( $this->form['action_list'] ) ) {
				foreach ( $this->form['action_list'] as $action => $configs ) {
					foreach ( $configs as $config => $mappings ) {
						foreach ( $mappings as $mapped_to_field => $value ) {
							$mapped_form_field = $this->get_field( $value );
							if ( $mapped_form_field && $mapped_form_field['id'] === $field['id'] ) {
								$mapping_data['mapped-to-type']  = $config;
								$mapping_data['mapped-to-field'] = $mapped_to_field;
								break;
							}
						}
					}
				}
			}

			return $mapping_data;
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
				ANONY_Wp_Debug_Help::error_log( 'class-anony-create-form.php', true );
				ANONY_Wp_Debug_Help::error_log( $this->results, true );
			}

			do_action( 'anony_form_submitted', $this->validated, $this->id );
		}

		/**
		 * Get error message
		 *
		 * @param string $code message code.
		 * @return string Message of the code.
		 */
		protected function get_error_message( $code ) {
			$msg = '';
			switch ( $code ) {
				case 'logged_in':
					$msg = esc_html__( 'You must be logged in.', 'anonyengine' ) . ' <a href=" ' . esc_url( wp_login_url( get_permalink() ) ) . '" alt="' . esc_attr__( 'Login Now', 'anonyengine' ) . '">' . esc_html__( 'Login Now', 'anonyengine' ) . '</a>';
					break;

				case 'user_role':
					$msg = esc_html__( 'You are not allowed to do this.', 'anonyengine' );
					break;

				case 'not_allowed':
					$msg = esc_html__( 'You are not allowed to edit this.', 'anonyengine' );
					break;
			}

			return $msg;
		}

		/**
		 * Enqueue form scripts
		 *
		 * @return void
		 */
		public function enqueue_scripts() {
			global $post;
			if ( $post && ( ANONY_Post_Help::has_shortcode( $post, $this->id ) || ( ! empty( $this->used_in ) && in_array( $post->ID, $this->used_in, true ) ) ) ) {
				anony_enqueue_styles();
				// Enqueue fields scripts.
				new ANONY_Fields_Scripts( $this->fields );
			}
		}
	}
}