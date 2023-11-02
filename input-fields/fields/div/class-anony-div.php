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
	 * Parent object
	 *
	 * @var object
	 */
	private $parent_obj;

	/**
	 * Color field Constructor.
	 *
	 * @param object $parent_obj Field parent object
	 */
	public function __construct( $parent_obj = null ) {
		if ( ! is_object( $parent_obj ) ) {
			return;
		}

		$this->parent_obj = $parent_obj;
	}

	/**
	 * Text field render Function.
	 *
	 * @return string Field output.
	 */
	public function render() {

		$html = sprintf(
			'<fieldset class="anony-row anony-row-inline" id="fieldset_%1$s">',
			$this->parent_obj->field['id']
		);

		if ( isset( $this->parent_obj->field['note'] ) ) {
			echo '<p class=anony-warning>' . esc_html( $this->parent_obj->field['note'] ) . '<p>';
		}

		if ( 'meta' === $this->parent_obj->context && isset( $this->parent_obj->field['title'] ) ) {
			if ( isset( $this->parent_obj->field['title'] ) && ! empty( $this->parent_obj->field['title'] ) ) {
				$html .= sprintf(
					'<label class="anony-label" for="%1$s">%2$s</label>',
					$this->parent_obj->field['id'],
					$this->parent_obj->field['title']
				);
			}
		}

		$div_content = '';

		if ( ! empty( $this->parent_obj->field['content'] ) ) {
			$div_content = $this->parent_obj->field['content'];
		}

		$html .= '<div id="' . $this->parent_obj->field['id'] . '">' . $div_content . '</div>';

		$html .= '</fieldset>';

		return $html;
	}
}
