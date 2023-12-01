<?php
/**
 * Deprecated class ANONY_Input_Field.
 *
 * @package AnonyEngine
 * @deprecated 1.0.0170 Deprecated in favor of ANONY_Input.
 */

defined( 'ABSPATH' ) || die();
if ( ! class_exists( 'ANONY_Input_Field' ) ) {
	/**
	 * A class that renders input fields according to context
	 */
	class ANONY_Input_Field {

		/**
		 * An array of inputs that have same HTML markup.
		 *
		 * @var array
		 */
		public $mixed_types = array( 'text', 'number', 'email', 'password', 'url', 'hidden' );

		/**
		 * Field php class name.
		 *
		 * @var string
		 */
		public $field_class;

		/**
		 * Input field name attribute value.
		 *
		 * @var string
		 */
		public $input_name;

		/**
		 * An array of field's data.
		 *
		 * @var array
		 */
		public $field;

		/**
		 * Post id for field that should be shown inside a post.
		 *
		 * @var int
		 */
		public $object_id;

		/**
		 * The context of where the field is used.
		 *
		 * @var string
		 */
		public $context;

		/**
		 * An object from the options class.
		 *
		 * @var object
		 */
		public $options;

		/**
		 * Field's value.
		 *
		 * @var mixed
		 */
		public $value;

		/**
		 * Default field value.
		 *
		 * @var mixed
		 */
		public $default;

		/**
		 * HTML class attibute value.
		 *
		 * @var string
		 */
		public $class_attr;

		/**
		 * Wheather field will be used as template or real input.
		 *
		 * @var bool
		 */
		public $as_template;

		/**
		 * Input field value.
		 *
		 * @var mixed
		 */
		public $field_value;

		/**
		 * Index of multi value fields in multi value array.
		 *
		 * @var int
		 */
		public $index;

		/**
		 * Metabox's ID.
		 *
		 * @var string
		 */
		public $metabox_id;

		/**
		 * Parent field ID if nested field.
		 *
		 * @var string
		 */
		public $parent_field_id;

		/**
		 * Field width . Default is 12 columns.
		 *
		 * @var string
		 */
		public $width = ' anony-grid-col-12';

		/**
		 * Inpud field constructor That decides field context
		 *
		 * @param array    $field     An array of field's data.
		 * @param string   $metabox_id Metabox's ID.
		 * @param string   $context   The context of where the field is used (option | meta | form).
		 * @param int|null $object_id Should be an integer if the context is meta box.
		 * @param bool     $as_template If field should be treated as template.
		 * @param mixed    $field_value Field's value.
		 * @param string   $index Field's index if nested.
		 * @param string   $parent_field_id Parent field's ID if nested.
		 */
		public function __construct( $field, $metabox_id = null, $context = 'option', $object_id = null, $as_template = false, $field_value = null, $index = null, $parent_field_id = null ) {
			$this->as_template     = $as_template;
			$this->field_value     = $field_value;
			$this->parent_field_id = $parent_field_id;
			$this->index           = $index;
			$this->options         = ( isset( $field['option_name'] ) && class_exists( 'ANONY_Options_Model' ) ) ? ANONY_Options_Model::get_instance( $field['option_name'] ) : '';
			$this->field           = $field;
			$this->metabox_id      = $metabox_id;
			$this->object_id       = $object_id;
			$this->context         = $context;
			$this->default         = isset( $this->field['default'] ) ? $this->field['default'] : '';
			$this->class_attr      = ( isset( $this->field['class'] ) ) ? $this->field['class'] : 'anony-input-field';
			$this->width           = ( isset( $this->field['width'] ) ) ? ' anony-grid-col-' . $this->field['width'] : $this->width;
			$this->set_field_data();
			$this->select_field();
			$this->enqueue_scripts();
		}
		/**
		 * Set input name for nested field
		 *
		 * @return void
		 */
		protected function nested_field_input_name() {
			$form_id = '';
			if ( 'meta' === $this->context ) {
				$form_id = $this->metabox_id;
			} elseif ( 'option' === $this->context && isset( $this->field['option_name'] ) ) {
				$form_id = $this->field['option_name'];
			}
			if ( $this->index && ! is_null( $this->index ) && $this->parent_field_id && ! is_null( $this->parent_field_id ) ) {
				$this->input_name = $form_id . '[' . $this->parent_field_id . '][' . $this->index . '][' . $this->field['id'] . ']';
			}
		}
		/**
		 * Set field data depending on the context
		 */
		public function set_field_data() {
			switch ( $this->context ) {
				case 'option':
						$this->opt_field_data();
					break;

				case 'meta':
				case 'term':
						$this->meta_field_data();
					break;

				case 'form':
						$this->form_field_data();
					break;

				default:
					$this->input_name = $this->field['id'];
					break;
			}
		}



		/**
		 * Set options field data
		 */
		public function opt_field_data() {
			if ( ! isset( $this->field['id'] ) ) {
				return;
			}

			$form_id = $this->field['option_name'];

			$field_id = $this->field['id'];

			if ( $this->index && ! is_null( $this->index ) && $this->parent_field_id && ! is_null( $this->parent_field_id ) ) {
				$this->input_name = $form_id . '[' . $this->parent_field_id . '][' . $this->index . '][' . $this->field['id'] . ']';
			} else {
				$input_name = isset( $this->field['name'] ) ? $this->field['name'] : $this->field['id'];

				$this->input_name = $form_id . '[' . $input_name . ']';
			}

			$this->value = ( isset( $this->options->$field_id ) && ! empty( $this->options->$field_id ) ) ? $this->options->$field_id : $this->default;
		}

		/**
		 * Set form's field data
		 *
		 * @return void
		 */
		public function form_field_data() {
			$this->input_name = isset( $this->field['name'] ) ? $this->field['name'] : $this->field['id'];
			$this->value      = $this->default;

			$this->value = apply_filters( 'anony_form_value', $this->value, $this->field['id'], $this->metabox_id );
		}

		/**
		 * Set metabox field data
		 */
		public function meta_field_data() {
			if ( $this->index && ! is_null( $this->index ) && $this->parent_field_id && ! is_null( $this->parent_field_id ) ) {
				$this->input_name = $this->metabox_id . '[' . $this->parent_field_id . '][' . $this->index . '][' . $this->field['id'] . ']';
			} elseif ( isset( $this->field['nested-to'] ) && ! empty( $this->field['nested-to'] ) ) {
				$index = ( is_integer( $this->index ) ) ? $this->index : 0;

				$this->input_name = $this->metabox_id . '[' . $this->field['nested-to'] . '][' . $index . '][' . $this->field['id'] . ']';

				$this->field['id'] = $this->field['id'] . '-' . $index;
			} else {
				$this->input_name = ! empty( $this->field['id'] ) ? $this->metabox_id . '[' . $this->field['id'] . ']' : '';
			}

			$single = ( isset( $this->field['multiple'] ) && $this->field['multiple'] ) ? false : true;

			// This should be field value to be passed to input field object.
			// Now within the multi value input field.
			if ( ! is_null( $this->field_value ) ) {

				$meta = $this->field_value;

			} else {

				if ( 'term' === $this->context ) {
					$metabox_options = get_term_meta( $this->object_id, $this->metabox_id, true );
				} else {
					$metabox_options = get_post_meta( $this->object_id, $this->metabox_id, $single );
				}

				$meta = ( is_array( $metabox_options ) && ! empty( $this->field['id'] ) && isset( $metabox_options[ $this->field['id'] ] ) ) ? $metabox_options[ $this->field['id'] ] : '';
			}

			$this->value = ( '' !== $meta ) ? $meta : $this->default;
		}

		/**
		 * Set the desired class name for input field
		 *
		 * @return string Input field class name
		 */
		public function select_field() {
			if ( isset( $this->field['type'] ) ) {
				// Static class name for inputs that have same HTML markup.
				if ( in_array( $this->field['type'], $this->mixed_types, true ) ) {
					$this->field_class = 'ANONY_Mixed';
				} else {
					$this->field_class = str_replace( '-', '_', 'ANONY_' . ucfirst( $this->field['type'] ) );

				}
			}
			return $this->field_class;
		}

		/**
		 * Initialize options field
		 */
		public function field_init() {

			if ( ! is_null( $this->field_class ) && class_exists( $this->field_class ) ) {

				$field_class = $this->field_class;

				$field = new $field_class( $this );

				// Options fields can't be on frontend.
				if ( 'option' === $this->context ) {
					return $field->render();
				}
				//phpcs:disable WordPress.Security.NonceVerification.Recommended
				$req = $_GET;
				//phpcs:disable
				if ( 'meta' === $this->context && ! is_admin() ) {
					// If there is an insert Or edit front end action.
					if ( isset( $req['action'] ) && ! empty( $req['action'] ) && isset( $req['_wpnonce'] ) && ! empty( $req['_wpnonce'] ) ) {

						switch ( $req['action'] ) {
							case 'insert':
								if ( wp_verify_nonce( $req['_wpnonce'], 'anonyinsert' ) ) {
									return $field->render();
								}
								break;

							case 'edit':
								if ( wp_verify_nonce( $req['_wpnonce'], 'anonyinsert_' . $this->object_id ) ) {
									return $field->render();
								}
								break;

							default:
								if ( method_exists( $field, 'renderDisplay' ) ) {
									return $field->renderDisplay();
								}
								break;
						}
					}

					if ( method_exists( $field, 'renderDisplay' ) ) {
						return $field->renderDisplay();
					}
				} else {

					return $field->render();
				}
			} else {
				// Translators: class name.
				return sprintf( esc_html__( '%s class doesn\'t exist' ), $this->field_class );
			}
		}

		/**
		 * Enqueu scripts
		 *
		 * @return void
		 */
		public function enqueue_scripts() {
			wp_register_style( 'anony-inputs', ANONY_INPUT_FIELDS_URI . 'assets/css/inputs-fields.css', array( 'farbtastic' ), time(), 'all' );

			wp_enqueue_style( 'anony-inputs' );

			if ( is_rtl() ) {
				wp_register_style( 'anony-inputs-rtl', ANONY_INPUT_FIELDS_URI . 'assets/css/inputs-fields-rtl.css', array( 'anony-inputs' ), time(), 'all' );
				wp_enqueue_style( 'anony-inputs-rtl' );
			}
		}
	}
}
