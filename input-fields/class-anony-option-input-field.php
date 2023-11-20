<?php
/**
 * AnonyEngine option's input field render class.
 *
 * PHP version 7.3 Or Later.
 *
 * @package  AnonyEngine
 * @author   Makiomar <info@makiomar.com>
 * @license  https://makiomar.com AnonyEngine Licence
 * @link     https://makiomar.com/anonyengine
 */

if ( ! class_exists( 'ANONY_Option_Input_Field' ) ) {
	/**
	 * A class that renders input fields for an option
	 */
	class ANONY_Option_Input_Field extends ANONY_Input_Base {
		/**
		 * Options object
		 *
		 * @var object
		 */
		public $options;

		/**
		 * Set field data depending on the context
		 */
		public function set_field_data() {
			$this->opt_field_data();
		}

		/**
		 * Set field data
		 */
		public function opt_field_data() {
			if ( ! isset( $this->field['id'] ) ) {
				return;
			}

			$this->options = ANONY_Options_Model::get_instance( $this->form_id );

			$input_name = isset( $this->field['name'] ) ? $this->field['name'] : $this->field['id'];

			$this->input_name = $this->form_id . '[' . $input_name . ']';

			$field_id = $this->field['id'];

			$this->value = ( isset( $this->options->$field_id ) && ! empty( $this->options->$field_id ) ) ? $this->options->$field_id : $this->default;
		}
	}
}
