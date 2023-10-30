<?php
/**
 * Multi text field class
 *
 * @package Anonymous theme
 * @author Makiomar
 * @link http://makiomar.com
 */

class ANONY_Multi_input {

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

		$html .= sprintf(
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
							$render_field = new ANONY_Input_Field( $nested_field, $this->parent->metabox_id, 'meta', $this->parent->post_id, false, $field_value, $index );

							$html .= $render_field->field_init();

						}
					}
				}

				$html .= '</div>';
			}
		} else {

			$html .= "<div class='anony-multi-value-flex'>";
			foreach ( $this->parent->field['fields'] as $nested_field ) {

				$render_field = new ANONY_Input_Field( $nested_field, $this->parent->metabox_id, 'meta', $this->parent->post_id, false );

				$html .= $render_field->field_init();

			}
			$html .= '</div>';
		}

		$html .= '</fieldset>';

		$html .= ( isset( $this->parent->field['desc'] ) && ! empty( $this->parent->field['desc'] ) ) ? ' <div class="description multi-text-desc">' . $this->parent->field['desc'] . '</div>' : '';

		return $html;
	}

	/**
	 * Multi text field render for display only.
	 *
	 * @return void
	 */
	function renderDisplay() {

		$html = sprintf(
			'<fieldset class="anony-row anony-row-inline anony-multi-value-wrapper" id="fieldset_%1$s">',
			$this->parent->field['id']
		);

		if ( $this->parent->context == 'meta' && isset( $this->parent->field['title'] ) ) {
			$html .= sprintf(
				'<label class="anony-label" for="%1$s">%2$s</label>',
				$this->parent->field['id'],
				$this->parent->field['title']
			);
		}

		$counter = 0;
		if ( is_array( $this->parent->value ) && ! empty( $this->parent->value ) ) {

			$counter = count( $this->parent->value );
			foreach ( $this->parent->value as $index => $multi_vals ) {

				$html .= "<div class='anony-multi-value-flex'>";

				foreach ( $multi_vals as $field_id => $field_value ) {

					foreach ( $this->parent->field['fields'] as $nested_field ) {

						if ( $nested_field['id'] == $field_id ) {
							$html .= "<div class='anony-flex-column-center'>";
							$html .= sprintf(
								'<label class="anony-label" for="%1$s">%2$s</label>',
								$nested_field['id'],
								$nested_field['placeholder']
							);

							$html .= sprintf(
								'<span id="%1$s" class="%2$s">%3$s</span>',
								$field_id,
								$this->parent->class_attr,
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

		$html .= ( isset( $this->parent->field['desc'] ) && ! empty( $this->parent->field['desc'] ) ) ? ' <div class="description multi-text-desc">' . $this->parent->field['desc'] . '</div>' : '';

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
