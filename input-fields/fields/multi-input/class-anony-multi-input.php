<?php
/**
 * Multi text field class
 *
 * @package Anonymous theme
 * @author Makiomar
 * @link http://makiomar.com
 */

/**
 * Multi input render class
 *
 * @package Anonymous theme
 * @author Makiomar
 * @link http://makiomar.com
 */
class ANONY_Multi_Input {

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
			$this->parent_obj->id_attr_value
		);

		if ( isset( $this->parent_obj->field['note'] ) ) {
			$html .= '<p class=anony-warning>' . esc_html( $this->parent_obj->field['note'] ) . '<p>';
		}
		if ( 'meta' === $this->parent_obj->context && isset( $this->parent_obj->field['title'] ) ) {
			$html .= sprintf(
				'<label class="anony-label" for="%1$s">%2$s</label>',
				$this->parent_obj->id_attr_value,
				$this->parent_obj->field['title']
			);
		}

		$html .= sprintf(
			'<input type="hidden" name="%1$s" id="%2$s" value=""/>',
			$this->parent_obj->input_name,
			$this->parent_obj->id_attr_value
		);

		$counter = 0;
		if ( is_array( $this->parent_obj->value ) && ! empty( $this->parent_obj->value ) ) {

			$counter = count( $this->parent_obj->value );
			foreach ( $this->parent_obj->value as $index => $multi_vals ) {

				$html .= "<div class='anony-multi-value-flex'>";

				foreach ( $multi_vals as $field_id => $field_value ) {

					foreach ( $this->parent_obj->field['fields'] as $nested_field ) {

						if ( $nested_field['id'] === $field_id ) {
							$args = array(
								'field'       => $nested_field,
								'field_value' => $field_value,
								'form_id'     => $this->parent_obj->form_id,
								'object_id'   => $this->parent_obj->post_id,
								'index'       => $index,
							);

							if ( class_exists( 'ANONY_Input_Base' ) ) {

								$render_field = new ANONY_Input_Base( $args );

							} else {
								// Deprecated ANONY_Input_Field.
								$render_field = new ANONY_Input_Field( $nested_field, $this->parent_obj->form_id, 'meta', $this->parent_obj->post_id, false, $field_value, $index );
							}

							$html .= $render_field->field_init();

						}
					}
				}

				$html .= '</div>';
			}
		} else {

			$html .= "<div class='anony-multi-value-flex'>";
			foreach ( $this->parent_obj->field['fields'] as $nested_field ) {

				$args = array(
					'field'     => $nested_field,
					'form_id'   => $this->parent_obj->form_id,
					'object_id' => $this->parent_obj->object_id,
				);

				if ( class_exists( 'ANONY_Input_Base' ) ) {

					$render_field = new ANONY_Input_Base( $args );

				} else {
					// Deprecated ANONY_Input_Field.
					$render_field = new ANONY_Input_Field( $nested_field, $this->parent_obj->metabox_id, 'meta', $this->parent_obj->post_id, false );
				}
				$html .= $render_field->field_init();

			}
			$html .= '</div>';
		}

		$html .= '</fieldset>';

		$html .= ( isset( $this->parent_obj->field['desc'] ) && ! empty( $this->parent_obj->field['desc'] ) ) ? ' <div class="description multi-text-desc">' . $this->parent_obj->field['desc'] . '</div>' : '';

		return $html;
	}
	//phpcs:disable WordPress.NamingConventions.ValidFunctionName.MethodNameInvalid
	/**
	 * Multi text field render for display only.
	 *
	 * @return string Field output.
	 */
	public function renderDisplay() {
		//phpcs:enable.
		$html = sprintf(
			'<fieldset class="anony-row anony-row-inline anony-multi-value-wrapper" id="fieldset_%1$s">',
			$this->parent_obj->id_attr_value
		);

		if ( 'meta' === $this->parent_obj->context && isset( $this->parent_obj->field['title'] ) ) {
			$html .= sprintf(
				'<label class="anony-label" for="%1$s">%2$s</label>',
				$this->parent_obj->id_attr_value,
				$this->parent_obj->field['title']
			);
		}

		$counter = 0;
		if ( is_array( $this->parent_obj->value ) && ! empty( $this->parent_obj->value ) ) {

			$counter = count( $this->parent_obj->value );
			foreach ( $this->parent_obj->value as $index => $multi_vals ) {

				$html .= "<div class='anony-multi-value-flex'>";

				foreach ( $multi_vals as $field_id => $field_value ) {

					foreach ( $this->parent_obj->field['fields'] as $nested_field ) {

						if ( $nested_field['id'] === $field_id ) {
							$html .= "<div class='anony-flex-column-center'>";
							$html .= sprintf(
								'<label class="anony-label" for="%1$s">%2$s</label>',
								$nested_field['id'],
								$nested_field['placeholder']
							);

							$html .= sprintf(
								'<span id="%1$s" class="%2$s">%3$s</span>',
								$field_id,
								$this->parent_obj->class_attr,
								$field_value
							);
							$html .= '</div>';

						}
					}
				}

				$html .= '</div>';
			}
		}

		$html .= '</fieldset>';

		$html .= ( isset( $this->parent_obj->field['desc'] ) && ! empty( $this->parent_obj->field['desc'] ) ) ? ' <div class="description multi-text-desc">' . $this->parent_obj->field['desc'] . '</div>' : '';

		return $html;
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
