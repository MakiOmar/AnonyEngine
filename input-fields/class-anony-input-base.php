<?php
/**
 * AnonyEngine input field render class.
 *
 * PHP version 7.3 Or Later.
 *
 * @package  AnonyEngine
 * @author   Makiomar <info@makiomar.com>
 * @license  https://makiomar.com AnonyEngine Licence
 * @link     https://makiomar.com/anonyengine
 */

if ( ! class_exists( 'ANONY_Input_Base' ) ) {
	/**
	 * A class that renders input fields according to context
	 */
	class ANONY_Input_Base {

		/**
		 * An array of inputs that have same HTML markup
		 *
		 * @var array
		 */
		public $mixed_types = array( 'text', 'number', 'email', 'password', 'url', 'hidden' );

		/**
		 * Field php class name
		 *
		 * @var string
		 */
		public $field_class;

		/**
		 * Parent form ID
		 *
		 * @var string
		 */
		public $form_id = '';

		/**
		 * Form arguments
		 *
		 * @var array
		 */
		public $form;

		/**
		 * Field object id.
		 * The ID of which field value comes from.
		 *
		 * @var int
		 */
		public $object_id;


		/**
		 * Field object type.
		 * The type of which field value comes from. Accepts post, term or user.
		 *
		 * @var int
		 */
		public $object_type;

		/**
		 * Input field name attribute value
		 *
		 * @var string
		 */
		public $input_name;

		/**
		 * An array of field's data
		 *
		 * @var array
		 */
		public $field;

		/**
		 * The context of where the field is used
		 *
		 * @var string
		 */
		public $context;

		/**
		 * An object from the options class
		 *
		 * @var object
		 */
		public $options;

		/**
		 * Field value
		 *
		 * @var mixed
		 */
		public $value;

		/**
		 * Default field value
		 *
		 * @var mixed
		 */
		public $default;

		/**
		 * HTML class attibute value
		 *
		 * @var string
		 */
		public $class_attr;

		/**
		 * Wheather field will be used as template or real input
		 *
		 * @var bool
		 */
		public $as_template;

		/**
		 * Input field value
		 *
		 * @var mixed
		 */
		public $field_value;

		/**
		 * Index of multi value fields in multi value array
		 *
		 * @var int
		 */
		public $index;

		/**
		 * Metabox's ID
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
		 * Field width . Default is 12 columns
		 *
		 * @var string
		 */
		public $width = ' anony-grid-col-12';

		/**
		 * Inpud field constructor That decides field context
		 *
		 * @param array $args    Field arguments.
		 */
		public function __construct( $args ) {

			if ( empty( $args['field'] ) ) {
				return;
			}

			$this->field           = $args['field'];
			$this->form_id         = $args['form_id'];
			$this->as_template     = ! empty( $args['as_template'] ) ? $args['as_template'] : false;
			$this->field_value     = ! empty( $args['field_value'] ) ? $args['field_value'] : false;
			$this->index           = ! empty( $args['index'] ) ? $args['index'] : null;
			$this->parent_field_id = ! empty( $args['parent_field_id'] ) ? $args['parent_field_id'] : null;
			$this->object_id       = ! empty( $args['object_id'] ) ? $args['object_id'] : null;
			$this->default         = ! empty( $this->field['default'] ) ? $this->field['default'] : '';
			$this->class_attr      = isset( $this->field['class'] ) ? $this->field['class'] : 'anony-input-field';
			$this->width           = isset( $this->field['width'] ) ? ' anony-grid-col-' . $this->field['width'] : $this->width;
			if ( $this->field_value ) {
				$this->value = $this->field_value;
			}
			$this->set_field_data();
			$this->select_field();
			$this->enqueue_scripts();
		}


		/**
		 * Set field data depending on the context
		 */
		public function set_field_data() {
			if ( $this->index && ! is_null( $this->index ) && $this->parent_field_id && ! is_null( $this->parent_field_id ) ) {
				$this->input_name = $this->form_id . '[' . $this->parent_field_id . '][' . $this->index . '][' . $this->field['id'] . ']';
			} else {
				$this->input_name = $this->field['id'];
			}
		}

		/**
		 * Set the desired class name for input field
		 *
		 * @return string Input field class name
		 */
		protected function select_field() {
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

				return $field->render();

			} else {
				return $this->field_class . esc_html__( ' class doesn\'t exist' );
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
