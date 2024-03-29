<?php
/**
 * Color field class
 *
 * @package Anonymous theme
 * @author Makiomar
 * @link http://makiomar.com
 */

/**
 * Color field render class
 *
 * @package Anonymous theme
 * @author Makiomar
 * @link http://makiomar.com
 */
class ANONY_Color_Gradient_Farbtastic {

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
	 * @since Theme_Settings 1.0
	 * @param object $parent_obj Field parent object.
	 */
	public function __construct( $parent_obj = null ) {

		if ( ! is_object( $parent_obj ) ) {
			return;
		}

		$this->parent_obj = $parent_obj;

		add_action( 'admin_print_footer_scripts', array( $this, 'footer_scripts' ) );
	}



	/**
	 * Color gradient field render Function.
	 *
	 * @return string Field output.
	 */
	public function render() {

		$from_style = '';
		$from_value = '';

		if ( isset( $this->parent_obj->value['from'] ) ) {
			$from_value = esc_attr( $this->parent_obj->value['from'] );

			$from_style = 'style="background-color:' . $from_value . ';"';

		}

		$to_style = '';
		$to_value = '';

		if ( isset( $this->parent_obj->value['to'] ) ) {

			$to_value = esc_attr( $this->parent_obj->value['to'] );

			$to_style = 'style="background-color:' . $to_value . ';"';

		}

		if ( isset( $field['note'] ) ) {
			echo '<p class=anony-warning>' . esc_html( $this->parent_obj->field['note'] ) . '<p>';
		}

		$html = '<div class="farb-popup-wrapper" id="' . $this->parent_obj->id_attr_value . '">';

		$html .= sprintf(
			'<fieldset class="anony-row anony-row-inline" id="fieldset_%1$s">',
			$this->parent_obj->id_attr_value
		);

		// from field.
		$html .= sprintf(
			'<label for="%1$s-from" class="anony-input-lable">%2$s</label>',
			$this->parent_obj->id_attr_value,
			esc_html__( 'From', 'anonyengine' )
		);

		$html .= sprintf(
			'<input type="text" id="%1$s-from" name="%2$s[from]" value="%3$s" class="%4$spopup-colorpicker"/>',
			$this->parent_obj->id_attr_value,
			$this->parent_obj->input_name,
			$from_value,
			$this->parent_obj->class_attr
		);

		$html .= sprintf(
			'<div class="farb-popup"><div class="farb-popup-inside"><div id="%1$s-frompicker" class="color-picker"></div></div></div>',
			$this->parent_obj->id_attr_value
		);

		$html .= sprintf(
			'<div class="color-prev prev-%1$s-from" %2$s rel="%1$s-from"></div>',
			$this->parent_obj->id_attr_value,
			$from_style
		);

		// to field.
		$html .= sprintf(
			'<label for="%1$s-to" class="anony-input-lable">%2$s</label>',
			$this->parent_obj->id_attr_value,
			esc_html__( 'To', 'anonyengine' )
		);

		$html .= sprintf(
			'<input type="text" id="%1$s-to" name="%2$s[to]" value="%3$s" class="%4$spopup-colorpicker"/>',
			$this->parent_obj->id_attr_value,
			$this->parent_obj->input_name,
			$to_value,
			$this->parent_obj->class_attr
		);

		$html .= sprintf(
			'<div class="farb-popup"><div class="farb-popup-inside"><div id="%1$s-topicker" class="color-picker"></div></div></div>',
			$this->parent_obj->id_attr_value
		);

		$html .= sprintf(
			'<div class="color-prev prev-%1$s-to" %2$s rel="%1$s-to"></div>',
			$this->parent_obj->id_attr_value,
			$to_style
		);

		$html .= ( isset( $this->parent_obj->field['desc'] ) && ! empty( $this->parent_obj->field['desc'] ) ) ? ' <div class="description">' . $this->parent_obj->field['desc'] . '</div>' : '';

		$html .= '</fieldset>';

		$html .= '</div>';

		return $html;
	}//end render()


	/**
	 * Enqueue scripts.
	 *
	 * If this field requires any scripts, or css define this function and register/enqueue the scripts/css
	 *
	 * @since Theme_Settings 1.0
	 */
	public function enqueue() {

		wp_enqueue_style( 'farbtastic' );
		wp_enqueue_script( 'anony-farbtastic-color-js', ANONY_FIELDS_URI . 'color-farbtastic/field_color.js', array( 'jquery', 'farbtastic' ), time(), true );
	}//end enqueue()

	/**
	 * Add needed scripts|styles to admin's footer
	 */
	public function footer_scripts() {
	}//end footer_scripts()
}//end class
