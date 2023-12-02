<?php
/**
 * Heading field class.
 *
 * @package Anonymous theme
 * @author Makiomar
 * @link http://makiomar.com
 */

/**
 * Heading render class.
 *
 * @package Anonymous theme
 * @author Makiomar
 * @link http://makiomar.com
 */
class ANONY_Heading {

	/**
	 * Parent object
	 *
	 * @var object
	 */
	private $parent_obj;

	/**
	 * Color field Constructor.
	 *
	 * @param object $parent_obj Field parent object.
	 */
	public function __construct( $parent_obj = null ) {

		if ( ! is_object( $parent_obj ) ) {
			return;
		}

		$this->parent_obj = $parent_obj;

		$this->parent_obj->value = '';
	}

	/**
	 * Render input field
	 */
	public function render() {

		$class = isset( $this->parent_obj->field['class'] ) && ! is_null( $this->parent_obj->field['class'] ) ? $this->parent_obj->field['class'] : 'anony-meta-field';

		$tag = isset( $this->parent_obj->field['tag'] ) && ! is_null( $this->parent_obj->field['tag'] ) ? $this->parent_obj->field['tag'] : 'h1';

		$html = sprintf(
			'<fieldset class="anony-row%2$s" id="fieldset_%1$s">',
			$this->parent_obj->id_attr_value,
			$this->parent_obj->width
		);

		$html .= sprintf(
			'<%1$s class="anony-form-heading">%2$s</%1$s>',
			$tag,
			$this->parent_obj->field['title']
		);

		$html .= '</fieldset>';

		return $html;
	}
}
