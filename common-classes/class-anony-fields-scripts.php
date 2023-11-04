<?php
/**
 * Inputs fields scripts.
 *
 * PHP version 7.3 Or Later.
 *
 * @package  AnonyEngine
 * @author   Makiomar <info@makiomar.com>
 * @license  https:// makiomar.com AnonyEngine Licence.
 * @link     https:// makiomar.com/anonyengine_elements.
 */

if ( ! class_exists( 'ANONY_Fields_Scripts' ) ) {
	/**
	 * Inputs fields scripts class.
	 *
	 * @package    AnonyEngine fields
	 * @author     Makiomar <info@makiomar.com>
	 * @license    https:// makiomar.com AnonyEngine Licence.
	 * @link       https:// makiomar.com.
	 */
	class ANONY_Fields_Scripts {

		/**
		 * An array of inputs that have same HTML markup.
		 *
		 * @var array
		 */
		public $mixed_types = array( 'text', 'number', 'email', 'password', 'url', 'hidden' );

		/**
		 * Constructor
		 *
		 * @param array $fields An array of fields.
		 */
		public function __construct( array $fields ) {
			$this->enqueue_fields_scripts( $fields );
		}
		/**
		 * Select field class.
		 *
		 * @param array $field Field data array.
		 * @return string Field class name.
		 */
		protected function select_field( $field ) {
			if ( isset( $field['type'] ) ) {
				// Static class name for inputs that have same HTML markup.
				if ( in_array( $field['type'], $this->mixed_types, true ) ) {
					$field_class = 'ANONY_Mixed';
				} else {
					$field_class = str_replace( '-', '_', 'ANONY_' . ucfirst( $field['type'] ) );

				}

				return $field_class;
			}

			return false;
		}

		/**
		 * Enqueue fields scripts.
		 *
		 * @param array $fields An array of fields.
		 * @return void
		 */
		protected function enqueue_fields_scripts( $fields ) {
			$added = array();
			foreach ( $fields as $field ) {
				if ( ! in_array( $field['type'], $added, true ) ) {
					if ( $this->select_field( $field ) ) {
						$class = $this->select_field( $field );

						if ( class_exists( $class ) && method_exists( $class, 'enqueue' ) ) {
							$obj = new $class( null, $field );
							$obj->enqueue();
						}
					}

					$added[] = $field['type'];
				}
			}
		}
	}
}
