<?php
/**
 * Textarea render class.
 *
 * @package Anonymous theme
 * @author Makiomar
 * @link http://makiomar.com
 */

class ANONY_Textarea {

	/**
	 * @var object
	 */
	private $parent;
	
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

		$this->parent->value = esc_textarea( $this->parent->value );

	}

	/**
	 * Render input field
	 */
	public function render() {
		$placeholder = ( isset( $this->parent->field['placeholder'] ) ) ? 'placeholder="' . $this->parent->field['placeholder'] . '"' : '';

		$class = isset( $this->parent->field['class'] ) && ! is_null( $this->parent->field['class'] ) ? $this->parent->field['class'] : 'anony-meta-field';

		$readonly = isset( $this->parent->field['readonly'] ) && ( $this->parent->field['readonly'] == true ) ? ' readonly' : '';

		$disabled = isset( $this->parent->field['disabled'] ) && ( $this->parent->field['disabled'] == true ) ? ' disabled' : '';

		$cols = isset( $this->parent->field['columns'] ) ? $this->parent->field['columns'] : 24;

		$rows       = isset( $this->parent->field['rows'] ) ? $this->parent->field['rows'] : 5;
		$text_align = isset( $this->parent->field['text-align'] ) ? $this->parent->field['text-align'] : 'initial';
		$direction = isset( $this->parent->field['direction'] ) ? $this->parent->field['direction'] : 'initial';

		$conditions = '';
		if( !empty( $this->parent->field['conditions'] ) )
		{
			$conditions = json_encode($this->parent->field['conditions']);
		}

		if ( $this->parent->as_template ) {
			$html  = sprintf(
				'<fieldset class="anony-row%2$s" id="fieldset_%1$s">',
				$this->parent->field['id'],
				$this->parent->width
			);
			$html .= sprintf(
				'<textarea style="text-align:%1$s;direction:%2$s" class="%3$s anony-row" rows="' . $rows . '" cols="' . $cols . '" name="%4$s" %5$s %6$s %7$s></textarea>',
				$text_align,
				$direction,
				$class,
				$this->parent->input_name,
				$readonly,
				$disabled,
				$placeholder
			);
			$html .= '</fieldset>';
			return $html;
		}

		$html = sprintf(
			'<fieldset class="anony-row%2$s" id="fieldset_%1$s">',
			$this->parent->field['id'],
			$this->parent->width
		);

		$html .= '<div>';
		if ( in_array( $this->parent->context, array( 'meta', 'form' ) ) && isset( $this->parent->field['title'] ) ) {
			$html .= sprintf(
				'<label class="anony-label" for="anony_%1$s">%2$s</label>',
				$this->parent->field['id'],
				$this->parent->field['title']
			);
		}

		if ( isset( $this->parent->field['note'] ) ) {
			$html .= '<p class=anony-warning>' . $this->parent->field['note'] . '<p>';
		}

		$html .= sprintf(
			'<textarea style="text-align:%1$s;direction:%2$s" class="%3$s" rows="' . $rows . '" cols="' . $cols . '" id="%4$s" name="%4$s" %5$s %6$s %7$s data-conditions="%8$s">%9$s</textarea>',
			$text_align,
			$direction,
			$class,
			$this->parent->input_name,
			$readonly,
			$disabled,
			$placeholder,
			$conditions,
			$this->parent->value
			
		);

		$html .= '</div>';
		
		if( !empty( $this->parent->field['desc'] ) )
		{
			$html .= sprintf('<p class="input-field-description">%s</p>', $this->parent->field['desc']);
		}
		
		$html .= '</fieldset>';



		return $html;

	}
}
