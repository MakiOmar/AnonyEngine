<?php
/**
 * Heading render class.
 *
 * @package Anonymous theme
 * @author Makiomar
 * @link http://makiomar.com
 */

class ANONY_Heading {
	/**
	 * Color field Constructor.
	 *
	 * @param object $parent Field parent object
	 */
	public function __construct( $parent = null ) {

		if ( ! is_object( $parent ) ) {
			return;
		}

		$this->parent = $parent;

		$this->parent->value = '';

	}

	/**
	 * Render input field
	 */
	public function render() {

		$class = isset( $this->parent->field['class'] ) && ! is_null( $this->parent->field['class'] ) ? $this->parent->field['class'] : 'anony-meta-field';

		$tag = isset( $this->parent->field['tag'] ) && ! is_null( $this->parent->field['tag'] ) ? $this->parent->field['tag'] : 'h1';

		$html = sprintf(
			'<fieldset class="anony-row%2$s" id="fieldset_%1$s">',
			$this->parent->field['id'],
			$this->parent->width
		);

		$html .= sprintf(
			'<%1$s class="anony-form-heading">%2$s</%1$s>',
			$tag,
			$this->parent->field['title']
		);

		$html .= '</fieldset>';

		return $html;

	}
}
