<?php
/**
 * AnonyEngine metabox's input field render class.
 *
 * PHP version 7.3 Or Later.
 *
 * @package  AnonyEngine
 * @author   Makiomar <info@makiomar.com>
 * @license  https://makiomar.com AnonyEngine Licence
 * @link     https://makiomar.com/anonyengine
 */

if ( ! class_exists( 'ANONY_Meta_Input_Field' ) ) {
	/**
	 * A class that renders input fields for an metabox
	 */
	class ANONY_Meta_Input_Field extends ANONY_Input_Base {


		/**
		 * Set field data depending on the context
		 */
		public function set_field_data() {
			$this->meta_field_data();
		}

		/**
		 * Set metabox field data
		 */
		public function meta_field_data() {

			if ( isset( $this->field['nested-to'] ) && ! empty( $this->field['nested-to'] ) ) {
				$index = ( is_integer( $this->index ) ) ? $this->index : 0;

				$this->input_name = $this->form_id . '[' . $this->field['nested-to'] . '][' . $index . '][' . $this->field['id'] . ']';

				$this->field['id'] = $this->field['id'] . '-' . $index;
			} else {
				$this->input_name = ! empty( $this->field['id'] ) ? $this->form_id . '[' . $this->field['id'] . ']' : '';
			}

			$single = ( isset( $this->field['multiple'] ) && $this->field['multiple'] ) ? false : true;

			// This should be field value to be passed to input field object.
			// Now within the multi value input field.
			if ( $this->field_value && ! is_null( $this->field_value ) ) {

				$meta = $this->field_value;

			} else {
				$metabox_options = get_post_meta( $this->object_id, $this->form_id, $single );

				$meta = ( is_array( $metabox_options ) && ! empty( $this->field['id'] ) && isset( $metabox_options[ $this->field['id'] ] ) ) ? $metabox_options[ $this->field['id'] ] : '';
			}

			$this->value = ( '' !== $meta ) ? $meta : $this->default;
		}

		/**
		 * Initialize options field
		 */
		public function field_init() {

			if ( ! is_null( $this->field_class ) && class_exists( $this->field_class ) ) {

				$field_class = $this->field_class;

				$field = new $field_class( $this );

				$request = wp_unslash( $_GET );

				if ( ! is_admin() ) {
					// If there is an insert Or edit front end action.
					if ( isset( $request['action'] ) && ! empty( $request['action'] ) && isset( $request['_wpnonce'] ) && ! empty( $request['_wpnonce'] ) ) {

						switch ( $request['action'] ) {
							case 'insert':
								if ( wp_verify_nonce( $request['_wpnonce'], 'anonyinsert' ) ) {
									return $field->render();
								}
								break;

							case 'edit':
								if ( wp_verify_nonce( $request['_wpnonce'], 'anonyinsert_' . $this->object_id ) ) {
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
				return $this->field_class . esc_html__( ' class doesn\'t exist' );
			}
		}
	}


}
