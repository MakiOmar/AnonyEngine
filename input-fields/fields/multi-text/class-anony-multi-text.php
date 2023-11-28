<?php
/**
 * Multi text field class
 * 
 * A text input that allows you to store a multiple value in an array.
 *
 * @package Anonymous theme
 * @author Makiomar
 * @link http://makiomar.com
 */

/**
 * Multi text render class
 *
 * @package Anonymous theme
 * @author Makiomar
 * @link http://makiomar.com
 */
class ANONY_Multi_Text {

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

		$this->parent_obj->value = is_array( $this->parent_obj->value ) ? array_map( 'esc_html', $this->parent_obj->value ) : $this->parent_obj->value;
	}


	/**
	 * Multi text field render Function.
	 *
	 * @return string Field output.
	 */
	public function render() {

		$button_text = ( isset( $this->parent_obj->field['button-text'] ) ) ? ' placeholder="' . $this->parent_obj->field['button-text'] . '"' : esc_html__( 'Add', 'anonyengine' );

		$placeholder = ( isset( $this->parent_obj->field['placeholder'] ) ) ? 'placeholder="' . $this->parent_obj->field['placeholder'] . '"' : '';

		$html = sprintf(
			'<fieldset class="anony-row anony-row-inline" id="fieldset_%1$s">',
			$this->parent_obj->field['id']
		);

		if ( isset( $this->parent_obj->field['note'] ) ) {
			echo '<p class=anony-warning>' . esc_html( $this->parent_obj->field['note'] ) . '<p>';
		}
		if ( 'meta' === $this->parent_obj->context && isset( $this->parent_obj->field['title'] ) ) {
			$html .= sprintf(
				'<label class="anony-label" for="%1$s">%2$s</label>',
				$this->parent_obj->field['id'],
				$this->parent_obj->field['title']
			);
		}

		$html .= sprintf(
			'<div class="anony-inputs-row anony-normal-flex">
						<input type="text" class="multi-text-add small-text"%1$s>',
			$placeholder
		);

		$html .= sprintf(
			'<a href="javascript:void(0);" class="multi-text-btn btn-blue" rel-id="%1$s-ul" rel-name="%2$s[]">%3$s</a></div>',
			$this->parent_obj->field['id'],
			$this->parent_obj->input_name,
			$button_text
		);

		$html .= sprintf(
			'<ul class="multi-text-ul" id="%1$s-ul">',
			$this->parent_obj->field['id']
		);

		if ( isset( $this->parent_obj->value ) && is_array( $this->parent_obj->value ) ) {

			foreach ( $this->parent_obj->value as $k => $value ) {

				if ( '' !== $value ) {

					$value = esc_attr( $value );

					$html .= '<li>';

						$html .= sprintf(
							'<input type="hidden" id="%1$s-%2$s" name="%3$s[]" value="%4$s" class="%5$s"/>',
							$this->parent_obj->field['id'],
							$k,
							$this->parent_obj->input_name,
							$value,
							$this->parent_obj->class_attr
						);

						$html .= sprintf( '<span>%1$s</span>', $value );

						$html .= '<a href="" class="multi-text-remove"><em>delete</em></a>';

					$html .= '</li>';
				}
			}
		}

			$html     .= '<li class="multi-text-default">';
				$html .= '<input type="hidden" name="" value="" class="' . $this->parent_obj->class_attr . '" />';
				$html .= '<span></span>';
				$html .= '<a href="" class="multi-text-remove"><em>delete</em></a>';
			$html     .= '</li>';

		$html .= '</ul>';

		$html .= ( isset( $this->parent_obj->field['desc'] ) && ! empty( $this->parent_obj->field['desc'] ) ) ? ' <div class="description multi-text-desc">' . $this->parent_obj->field['desc'] . '</div>' : '';

		$html .= '</fieldset>';

		return $html;
	}


	/**
	 * Enqueue scripts.
	 */
	public function enqueue() {

		wp_enqueue_script(
			'anony-opts-field-multi-text-js',
			ANONY_FIELDS_URI . 'multi-text/field_multi_text.js',
			array( 'jquery' ),
			time(),
			true
		);
	}
}
