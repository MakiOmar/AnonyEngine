<?php
/**
 * Multi-input types render class.
 *
 * Handles rendring these type ['text','number','email', 'password','url'].
 *
 * @package Anonymous theme
 * @author Makiomar
 * @link http://makiomar.com
 */

class ANONY_Div {

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

	}

	/**
	 * Text field render Function.
	 *
	 * @return void
	 */
	public function render() {

		$html = sprintf(
			'<fieldset class="anony-row anony-row-inline" id="fieldset_%1$s">',
			$this->parent->field['id']
		);

		if ( isset( $this->parent->field['note'] ) ) {
			echo '<p class=anony-warning>' . $this->parent->field['note'] . '<p>';
		}

		if ( $this->parent->context == 'meta' && isset( $this->parent->field['title'] ) ) {
			if ( isset( $this->parent->field['title'] ) && ! empty( $this->parent->field['title'] ) ) {
				$html .= sprintf(
					'<label class="anony-label" for="%1$s">%2$s</label>',
					$this->parent->field['id'],
					$this->parent->field['title']
				);
			}
		}

		$html .= '<div id="' . $this->parent->field['id'] . '"></div>';

		$html .= '</fieldset>';

		return $html;

	}

}

