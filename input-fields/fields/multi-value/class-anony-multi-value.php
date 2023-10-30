<?php
/**
 * Multi text field class
 *
 * @package Anonymous theme
 * @author Makiomar
 * @link http://makiomar.com
 */

class ANONY_Multi_value {

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

		if ( ! isset( $this->parent->field['fields'] ) ) {
			return;
		}

		$this->enqueue();
	}


	/**
	 * Multi text field render Function.
	 *
	 * @return void
	 */
	function render() {

		$buttonText = ( isset( $this->parent->field['button-text'] ) ) ? ' ' . $this->parent->field['button-text'] : esc_html__( 'Add', 'anonyengine' );

		$html = sprintf(
			'<fieldset class="anony-row anony-row-inline anony-multi-value-wrapper" id="fieldset_%1$s">',
			$this->parent->field['id']
		);

		if ( isset( $this->parent->field['note'] ) ) {
			$html .= '<p class=anony-warning>' . $this->parent->field['note'] . '<p>';
		}
		if ( $this->parent->context == 'meta' && isset( $this->parent->field['title'] ) ) {
			$html .= sprintf(
				'<label class="anony-label" for="%1$s">%2$s</label>',
				$this->parent->field['id'],
				$this->parent->field['title']
			);
		}

		$html   .= sprintf(
			'<input type="hidden" name="%1$s" id="%2$s" value=""/>',
			$this->parent->input_name,
			$this->parent->field['id']
		);
		$counter = 0;

		if ( is_array( $this->parent->value ) && ! empty( $this->parent->value ) ) {
			$counter = count( $this->parent->value );
			foreach ( $this->parent->value as $index => $multi_vals ) {

				$html .= "<div class='anony-multi-value-flex'>";

				foreach ( $multi_vals as $field_id => $field_value ) {

					foreach ( $this->parent->field['fields'] as $nested_field ) {

						if ( $nested_field['id'] == $field_id ) {
							$render_field = new ANONY_Input_Field( $nested_field, 'meta', $this->parent->post_id, false, $field_value, $index );

							$html .= $render_field->field_init();

						}
					}
				}

				$html .= '</div>';
			}

			$html .= sprintf( '<div id="%1$s-add" class="%1$s-add"></div>', $this->parent->field['id'] );

			$html .= sprintf(
				'<a href="javascript:void(0);" class="multi-value-btn btn-blue" rel-id="%1$s" rel-name="%2$s[]" rel-class="%2$s-wrapper">%3$s</a>',
				$this->parent->field['id'],
				$this->parent->input_name,
				$buttonText
			);

			$html .= '</fieldset>';
		}

		$default = sprintf( '<script id = "%s-default" type="text/template">', $this->parent->field['id'] );

		$default .= sprintf(
			'<div class="%1$s-template anony-multi-value-flex">',
			$this->parent->input_name
		);

		foreach ( $this->parent->field['fields'] as $nested_field ) {

			// render default template. Passed true as fourth parameter to ANONY_Input_Field
			$render_default = new ANONY_Input_Field( $nested_field, 'meta', $this->parent->post_id, true, '', ( $counter + 1 ) );
			$default       .= $render_default->field_init();
		}

		$default .= '</div></script>';

		$html .= ( isset( $this->parent->field['desc'] ) && ! empty( $this->parent->field['desc'] ) ) ? ' <div class="description multi-text-desc">' . $this->parent->field['desc'] . '</div>' : '';

		$html .= sprintf( '<input type="hidden" id="%1$s-counter" value="%2$s"/>', $this->parent->field['id'], $counter );

		$html .= '</div>';

		return $html . $default;
	}


	/**
	 * Enqueue scripts.
	 */
	public function enqueue() {

		wp_enqueue_script(
			'anony-opts-field-multi-value-js',
			ANONY_FIELDS_URI . 'multi-value/field_multi_value.js',
			array( 'jquery' ),
			time(),
			true
		);
	}
}
