<?php
/**
 * Switch field class
 *
 * @package Anonymous theme
 * @author Makiomar
 * @link http://makiomar.com
 */
class ANONY_Switch {

	
	/**
	 * @var object
	 */
	private $parent;
	
	/**
	 * Field Constructor.
	 *
	 * Required - must call the parent constructor, then assign field and value to vars, and obviously call the render field function
	 *
	 * @param array  $field Array of field's data
	 * @param object $parent Field parent object
	 */
	public function __construct( $parent = null ) {

		if ( ! is_object( $parent ) ) {
			return;
		}

		$this->parent = $parent;

		$this->parent->value = esc_attr( $this->parent->value );

		$this->enqueue();
	}

	/**
	 * Switch field render Function.
	 *
	 * @return void
	 */
	public function render() {

		$html = '';
		// fix for value "off = 0"
		if ( ! $this->parent->value ) {
			$this->parent->value = 0;
		}

		$html .= sprintf(
			'<fieldset class="anony-row%2$s" id="fieldset_%1$s">',
			$this->parent->field['id'],
			$this->parent->width
		);

		// fix for WordPress 3.6 meta options
		if ( strpos( $this->parent->field['id'], '[]' ) === false ) {
			$html .= '<input type="hidden" name="' . $this->parent->input_name . '" value="0" />';
		}


		if ( in_array( $this->parent->context, array( 'meta', 'form' ) ) && isset( $this->parent->field['title'] ) ) {
			$html .= sprintf(
				'<label class="anony-label" for="anony_%1$s">%2$s</label>',
				$this->parent->field['id'],
				$this->parent->field['title']
			);
		}

		if ( !empty( $this->parent->field['note'] ) ) {
			$html .= '<p class=anony-warning>' . $this->parent->field['note'] . '<p>';
		}


		$html .= sprintf(
			'<input type="checkbox" data-toggle="switch" id="%1$s" name="%2$s" %3$s value="1" %4$s />',
			$this->parent->field['id'],
			$this->parent->input_name,
			$this->parent->class_attr,
			checked( $this->parent->value, 1, false )
		);

		$html .= ( isset( $this->parent->field['desc'] ) && ! empty( $this->parent->field['desc'] ) ) ? '<div class="input-field-description">' . $this->parent->field['desc'] . '</div>' : '';
		$html .= '</fieldset>';
		return $html;
	}

	/**
	 * Enqueue scripts.
	 */
	public function enqueue() {
		wp_enqueue_script( 'anony-opts-field-switch-js', ANONY_FIELDS_URI . 'switch/field_switch.js', array( 'jquery' ), time(), true );
	}

}

