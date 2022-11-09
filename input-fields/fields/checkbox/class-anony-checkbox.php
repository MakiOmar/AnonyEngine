<?php
/**
 * Checkbox field class
 *
 * @package Anonymous theme
 * @author Makiomar
 * @link http://makiomar.com
 */

class ANONY_Checkbox {

	public $parent;
	/**
	 * Checkbox field Constructor.
	 *
	 * @param object $parent Field parent object
	 */
	public function __construct( $parent = null ) {

		if ( ! is_object( $parent ) ) {
			return;
		}

		$this->parent = $parent;
	
		error_log(print_r($options, true));

		$this->data = array(

			'value'    => $this->parent->value ? $this->parent->value : 0, // 0 is fix for value "off = 0"

			'note'     => isset( $this->parent->field['note'] ) ? esc_html( $this->parent->field['note'] ) : false,

			'id'       => esc_attr( $this->parent->field['id'] ),

			'context'  => $this->parent->context,

			'title'    => isset( $this->parent->field['title'] ) ? esc_html( $this->parent->field['title'] ) : false,

			'name'     => esc_attr( $this->parent->input_name ),

			'options'  => isset( $this->parent->field['options'] ) ? $this->parent->field['options'] : false,

			'class'    => esc_attr( $this->parent->class_attr ),

			'desc'     => isset( $this->parent->field['desc'] ) ? esc_html( $this->parent->field['desc'] ) : false,

			'disabled' => isset( $this->parent->field['disabled'] ) && ( $this->parent->field['disabled'] == true ) ? " disabled = 'disabled' " : '',
		);

	}

	public function render() {

		ob_start();

		include 'checkbox.view.php';

		$html = ob_get_contents();

		ob_end_clean();

		return $html;
	}


}

?>
