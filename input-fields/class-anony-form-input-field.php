<?php
/**
 * AnonyEngine form's input field render class.
 *
 * PHP version 7.3 Or Later.
 *
 * @package  AnonyEngine
 * @author   Makiomar <info@makiomar.com>
 * @license  https://makiomar.com AnonyEngine Licence
 * @link     https://makiomar.com/anonyengine
 */

if ( ! class_exists( 'ANONY_Form_Input_Field' ) ) {
	/**
	 * A class that renders input fields for an form
	 */
	class ANONY_Form_Input_Field extends ANONY_Input_Base {
		/**
		 * Set field data depending on the context
		 */
		public function set_field_data() {
			$this->form_field_data();
		}

		/**
		 * Set form field data
		 *
		 * @return void
		 */
		public function form_field_data() {
			$this->input_name = isset( $this->field['name'] ) ? $this->field['name'] : $this->field['id'];
			$this->value      = $this->default;

			$this->value = apply_filters( "anony_form_value_{$this->field['id']}", $this->value, $this->field['id'], $this->metabox_id );
		}
	}
}
