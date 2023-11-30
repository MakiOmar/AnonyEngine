<?php
/**
 * Multi value field class
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
class ANONY_Multi_Value {

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

		if ( ! isset( $this->parent_obj->field['fields'] ) ) {
			return;
		}
	}


	/**
	 * Multi text field render Function.
	 *
	 * @return string Field output.
	 */
	public function render() {

		$button_text = ( isset( $this->parent_obj->field['button-text'] ) ) ? ' ' . $this->parent_obj->field['button-text'] : esc_html__( 'Add', 'anonyengine' );

		$html = sprintf(
			'<fieldset class="anony-row anony-row-inline anony-multi-value-wrapper" id="fieldset_%1$s">',
			$this->parent_obj->field['id']
		);

		if ( isset( $this->parent_obj->field['note'] ) ) {
			$html .= '<p class=anony-warning>' . esc_html( $this->parent_obj->field['note'] ) . '<p>';
		}
		if ( 'meta' === $this->parent_obj->context && isset( $this->parent_obj->field['title'] ) ) {
			$html .= sprintf(
				'<label class="anony-label" for="%1$s">%2$s</label>',
				$this->parent_obj->field['id'],
				$this->parent_obj->field['title']
			);
		}

		$html   .= sprintf(
			'<input type="hidden" name="%1$s" id="%2$s" value=""/>',
			$this->parent_obj->input_name,
			$this->parent_obj->field['id']
		);
		$counter = 0;
		ANONY_Wp_Debug_Help::neat_var_dump($this->parent_obj->value);
		if ( is_array( $this->parent_obj->value ) && ! empty( $this->parent_obj->value ) ) {
			
			foreach ( $this->parent_obj->value as $field_id => $value ) {

				$html .= "<div class='anony-multi-value-flex'>";

				foreach ( $multi_vals as $field_id => $field_value ) {

					foreach ( $this->parent_obj->field['fields'] as $nested_field ) {

						if ( $nested_field['id'] === $field_id ) {
							$render_field = new ANONY_Input_Field( $nested_field, null, 'meta', $this->parent_obj->object_id, false, $field_value, $index );

							$html .= $render_field->field_init();

						}
					}
				}

				$html .= '</div>';
			}
		}
		$html .= sprintf( '<div id="%1$s-add" class="%1$s-add anony-multi-values-wrapper"></div>', $this->parent_obj->field['id'] );

		$html .= sprintf(
			'<a href="javascript:void(0);" class="multi-value-btn btn-blue" rel-id="%1$s" rel-name="%2$s[#index#]" rel-class="%2$s-wrapper">%3$s</a>',
			$this->parent_obj->field['id'],
			$this->parent_obj->input_name,
			$button_text
		);

		$html   .= '</fieldset>';
		$default = sprintf( '<script id="%s-default" type="text/html">', $this->parent_obj->field['id'] );

		$default .= sprintf(
			'<div class="%1$s-template anony-multi-value-flex">',
			$this->parent_obj->input_name
		);

		foreach ( $this->parent_obj->field['fields'] as $nested_field ) {

			// render default template. Passed true as fourth parameter to ANONY_Input_Field.
			$render_default = new ANONY_Input_Field( $nested_field, null, 'meta', $this->parent_obj->object_id, true, true, ( $counter + 1 ) );
			$default       .= $render_default->field_init();
		}

		$default .= '</div></script>';

		$html .= ( isset( $this->parent_obj->field['desc'] ) && ! empty( $this->parent_obj->field['desc'] ) ) ? ' <div class="description multi-text-desc">' . $this->parent_obj->field['desc'] . '</div>' : '';

		$html .= sprintf( '<input type="hidden" id="%1$s-counter" value="%2$s"/>', $this->parent_obj->field['id'], $counter );

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
