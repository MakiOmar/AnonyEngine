<?php
/**
 * Switch field class
 *
 * @package Anonymous theme
 * @author Makiomar
 * @link http://makiomar.com
 */

/**
 * Switch render class.
 *
 * @package    Fields inputs
 * @author     Makiomar <info@makiomar.com>
 * @license    https://makiomar.com AnonyEngine Licence
 * @link       https://makiomar.com
 */
class ANONY_Switch {


	/**
	 * Parent object
	 *
	 * @var object
	 */
	private $parent_obj;

	/**
	 * Field Constructor.
	 *
	 * Required - must call the parent constructor, then assign field and value to vars, and obviously call the render field function
	 *
	 * @param object $parent_obj Field parent object.
	 */
	public function __construct( $parent_obj = null ) {

		if ( ! is_object( $parent_obj ) ) {
			return;
		}

		$this->parent_obj = $parent_obj;

		$this->parent_obj->value = esc_attr( $this->parent_obj->value );
	}

	/**
	 * Switch field render Function.
	 *
	 * @return string Field output.
	 */
	public function render() {

		$html = '';
		// fix for value "off = 0".
		if ( ! $this->parent_obj->value ) {
			$this->parent_obj->value = 0;
		}

		$html .= sprintf(
			'<fieldset class="anony-row%2$s" id="fieldset_%1$s">',
			$this->parent_obj->field['id'],
			$this->parent_obj->width
		);

		// fix for WordPress 3.6 meta options.
		if ( strpos( $this->parent_obj->field['id'], '[]' ) === false ) {
			$html .= '<input type="hidden" name="' . $this->parent_obj->input_name . '" value="0" />';
		}

		if ( in_array( $this->parent_obj->context, array( 'meta', 'form' ), true ) && isset( $this->parent_obj->field['title'] ) ) {
			$html .= sprintf(
				'<label class="anony-label" for="anony_%1$s">%2$s</label>',
				$this->parent_obj->field['id'],
				$this->parent_obj->field['title']
			);
		}

		if ( ! empty( $this->parent_obj->field['note'] ) ) {
			$html .= '<p class=anony-warning>' . esc_html( $this->parent_obj->field['note'] ) . '<p>';
		}

		$html .= sprintf(
			'<input type="checkbox" data-toggle="switch" id="%1$s" name="%2$s" %3$s value="1" %4$s />',
			$this->parent_obj->field['id'],
			$this->parent_obj->input_name,
			$this->parent_obj->class_attr,
			checked( $this->parent_obj->value, 1, false )
		);

		$html .= ( isset( $this->parent_obj->field['desc'] ) && ! empty( $this->parent_obj->field['desc'] ) ) ? '<div class="input-field-description">' . $this->parent_obj->field['desc'] . '</div>' : '';
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
