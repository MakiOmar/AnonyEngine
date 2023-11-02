<?php
/**
 * Textarea render class
 *
 * @package Input fields.
 */

/**
 * Textarea render class
 */
class ANONY_Textarea {

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

		$this->parent_obj->value = esc_textarea( $this->parent_obj->value );
	}

	/**
	 * Render input field
	 */
	public function render() {
		$placeholder = ( isset( $this->parent_obj->field['placeholder'] ) ) ? 'placeholder="' . $this->parent_obj->field['placeholder'] . '"' : '';

		$class = isset( $this->parent_obj->field['class'] ) && ! is_null( $this->parent_obj->field['class'] ) ? $this->parent_obj->field['class'] : 'anony-meta-field';

		$readonly = isset( $this->parent_obj->field['readonly'] ) && ( true === $this->parent_obj->field['readonly'] ) ? ' readonly' : '';

		$disabled = isset( $this->parent_obj->field['disabled'] ) && ( true === $this->parent_obj->field['disabled'] ) ? ' disabled' : '';

		$cols = isset( $this->parent_obj->field['columns'] ) ? $this->parent_obj->field['columns'] : 24;

		$rows       = isset( $this->parent_obj->field['rows'] ) ? $this->parent_obj->field['rows'] : 5;
		$text_align = isset( $this->parent_obj->field['text-align'] ) ? $this->parent_obj->field['text-align'] : 'initial';
		$direction  = isset( $this->parent_obj->field['direction'] ) ? $this->parent_obj->field['direction'] : 'initial';

		$conditions = '';
		if ( ! empty( $this->parent_obj->field['conditions'] ) ) {
			$conditions = wp_json_encode( $this->parent_obj->field['conditions'] );
		}

		if ( $this->parent_obj->as_template ) {
			$html  = sprintf(
				'<fieldset class="anony-row%2$s" id="fieldset_%1$s">',
				$this->parent_obj->field['id'],
				$this->parent_obj->width
			);
			$html .= sprintf(
				'<textarea style="text-align:%1$s;direction:%2$s" class="%3$s anony-row" rows="' . $rows . '" cols="' . $cols . '" name="%4$s" %5$s %6$s %7$s></textarea>',
				$text_align,
				$direction,
				$class,
				$this->parent_obj->input_name,
				$readonly,
				$disabled,
				$placeholder
			);
			$html .= '</fieldset>';
			return $html;
		}

		$html = sprintf(
			'<fieldset class="anony-row%2$s" id="fieldset_%1$s">',
			$this->parent_obj->field['id'],
			$this->parent_obj->width
		);

		$html .= '<div>';
		if ( in_array( $this->parent_obj->context, array( 'meta', 'form' ), true ) && isset( $this->parent_obj->field['title'] ) ) {
			$html .= sprintf(
				'<label class="anony-label" for="anony_%1$s">%2$s</label>',
				$this->parent_obj->field['id'],
				$this->parent_obj->field['title']
			);
		}

		if ( isset( $this->parent_obj->field['note'] ) ) {
			$html .= '<p class=anony-warning>' . esc_html( $this->parent_obj->field['note'] ) . '<p>';
		}

		$html .= sprintf(
			'<textarea style="text-align:%1$s;direction:%2$s" class="%3$s" rows="' . $rows . '" cols="' . $cols . '" id="%4$s" name="%4$s" %5$s %6$s %7$s data-conditions="%8$s">%9$s</textarea>',
			$text_align,
			$direction,
			$class,
			$this->parent_obj->input_name,
			$readonly,
			$disabled,
			$placeholder,
			$conditions,
			$this->parent_obj->value
		);

		$html .= '</div>';

		if ( ! empty( $this->parent_obj->field['desc'] ) ) {
			$html .= sprintf( '<p class="input-field-description">%s</p>', $this->parent_obj->field['desc'] );
		}

		$html .= '</fieldset>';

		return $html;
	}
}
