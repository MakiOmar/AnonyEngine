<?php
/**
 * Checkbox field class
 *
 * @package Anonymous theme
 * @author Makiomar
 * @link http://makiomar.com
 */

/**
 * Checkbox field render class
 *
 * @package Anonymous theme
 * @author Makiomar
 * @link http://makiomar.com
 */
class ANONY_Checkbox {


	/**
	 * Parent object
	 *
	 * @var object
	 */
	private $parent_obj;

	/**
	 * Checkbox data
	 *
	 * @var array
	 */
	private $data;

	/**
	 * Checkbox field Constructor.
	 *
	 * @param object $parent_obj Field parent object.
	 */
	public function __construct( $parent_obj = null ) {

		if ( ! is_object( $parent_obj ) ) {
			return;
		}

		$this->parent_obj = $parent_obj;

		$this->data = array(

			'value'    => $this->parent_obj->value ? $this->parent_obj->value : 0, // 0 is fix for value "off = 0"

			'note'     => isset( $this->parent_obj->field['note'] ) ? esc_html( $this->parent_obj->field['note'] ) : false,

			'id'       => esc_attr( $this->parent_obj->field['id'] ),

			'context'  => $this->parent_obj->context,

			'title'    => isset( $this->parent_obj->field['title'] ) ? esc_html( $this->parent_obj->field['title'] ) : false,

			'name'     => esc_attr( $this->parent_obj->input_name ),

			'options'  => isset( $this->parent_obj->field['options'] ) ? $this->parent_obj->field['options'] : false,

			'class'    => esc_attr( $this->parent_obj->class_attr ),

			'desc'     => isset( $this->parent_obj->field['desc'] ) ? esc_html( $this->parent_obj->field['desc'] ) : false,

			'disabled' => isset( $this->parent_obj->field['disabled'] ) && ( true === $this->parent_obj->field['disabled'] ) ? " disabled = 'disabled' " : '',
		);
	}
	/**
	 * Checkbox render.
	 *
	 * @return string Field output.
	 */
	public function render() {

		ob_start();

		include 'checkbox-view.php';

		$html = ob_get_contents();

		ob_end_clean();

		return $html;
	}
}
