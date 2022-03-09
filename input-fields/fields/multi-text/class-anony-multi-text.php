<?php
/**
 * Multi text field class
 *
 * @package Anonymous theme
 * @author Makiomar
 * @link http://makiomar.com
 */

class ANONY_Multi_text {

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

		$this->parent->value = is_array( $this->parent->value ) ? array_map( 'esc_html', $this->parent->value ) : $this->parent->value;

		$this->enqueue();
	}


	/**
	 * Multi text field render Function.
	 *
	 * @return void
	 */
	function render() {

		$buttonText = ( isset( $this->parent->field['button-text'] ) ) ? ' placeholder="' . $this->parent->field['button-text'] . '"' : esc_html__( 'Add', 'anonyengine' );

		$placeholder = ( isset( $this->parent->field['placeholder'] ) ) ? 'placeholder="' . $this->parent->field['placeholder'] . '"' : '';

		$html = sprintf(
			'<fieldset class="anony-row anony-row-inline" id="fieldset_%1$s">',
			$this->parent->field['id']
		);

		if ( isset( $this->parent->field['note'] ) ) {
			echo '<p class=anony-warning>' . $this->parent->field['note'] . '<p>';
		}
		if ( $this->parent->context == 'meta' && isset( $this->parent->field['title'] ) ) {
			$html .= sprintf(
				'<label class="anony-label" for="%1$s">%2$s</label>',
				$this->parent->field['id'],
				$this->parent->field['title']
			);
		}

		$html .= sprintf(
			'<div class="anony-inputs-row anony-normal-flex">
						<input type="text" class="multi-text-add small-text"%1$s>',
			$placeholder
		);

		$html .= sprintf(
			'<a href="javascript:void(0);" class="multi-text-btn btn-blue" rel-id="%1$s-ul" rel-name="%2$s[]">%3$s</a></div>',
			$this->parent->field['id'],
			$this->parent->input_name,
			$buttonText
		);

		$html .= sprintf(
			'<ul class="multi-text-ul" id="%1$s-ul">',
			$this->parent->field['id']
		);

		if ( isset( $this->parent->value ) && is_array( $this->parent->value ) ) {

			foreach ( $this->parent->value as $k => $value ) {

				if ( $value != '' ) {

					$value = esc_attr( $value );

					$html .= '<li>';

						$html .= sprintf(
							'<input type="hidden" id="%1$s-%2$s" name="%3$s[]" value="%4$s" class="%5$s"/>',
							$this->parent->field['id'],
							$k,
							$this->parent->input_name,
							$value,
							$this->parent->class_attr
						);

						$html .= sprintf( '<span>%1$s</span>', $value );

						$html .= '<a href="" class="multi-text-remove"><em>delete</em></a>';

					$html .= '</li>';
				}
			}
		}

			$html     .= '<li class="multi-text-default">';
				$html .= '<input type="hidden" name="" value="" class="' . $this->parent->class_attr . '" />';
				$html .= '<span></span>';
				$html .= '<a href="" class="multi-text-remove"><em>delete</em></a>';
			$html     .= '</li>';

		$html .= '</ul>';

		$html .= ( isset( $this->parent->field['desc'] ) && ! empty( $this->parent->field['desc'] ) ) ? ' <div class="description multi-text-desc">' . $this->parent->field['desc'] . '</div>' : '';

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

