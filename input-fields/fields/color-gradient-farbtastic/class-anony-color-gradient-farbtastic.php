<?php
/**
 * Color field class
 *
 * @package Anonymous theme
 * @author Makiomar
 * @link http://makiomar.com
 */
class ANONY_Color_gradient_farbtastic {
	
	/**
	 * @var object
	 */
	private $parent;
	
	/**
	 * Field Constructor.
	 *
	 * Required - must call the parent constructor, then assign field and value to vars, and obviously call the render field function
	 *
	 * @since Theme_Settings 1.0
	 * @param object $parent Field parent object
	 */
	public function __construct( $parent = null ) {

		if ( ! is_object( $parent ) ) {
			return;
		}

		$this->parent = $parent;

		add_action( 'admin_print_footer_scripts', array( $this, 'footer_scripts' ) );

		$this->enqueue();
	}



	/**
	 * Color gradient field render Function.
	 *
	 * @return void
	 */
	public function render() {

		$from_style = '';
		$from_value = '';

		if ( isset( $this->parent->value['from'] ) ) {
			$from_value = esc_attr( $this->parent->value['from'] );

			$from_style = 'style="background-color:' . $from_value . ';"';

		}

		$to_style = '';
		$to_value = '';

		if ( isset( $this->parent->value['to'] ) ) {

			$to_value = esc_attr( $this->parent->value['to'] );

			$to_style = 'style="background-color:' . $to_value . ';"';

		}

		if ( isset( $field['note'] ) ) {
			echo '<p class=anony-warning>' . $this->parent->field['note'] . '<p>';
		}

		$html = '<div class="farb-popup-wrapper" id="' . $this->parent->field['id'] . '">';

		$html .= sprintf(
			'<fieldset class="anony-row anony-row-inline" id="fieldset_%1$s">',
			$this->parent->field['id']
		);

		// from field
		$html .= sprintf(
			'<label for="%1$s-from" class="anony-input-lable">%2$s</label>',
			$this->parent->field['id'],
			esc_html__( 'From', 'anonyengine' )
		);

		$html .= sprintf(
			'<input type="text" id="%1$s-from" name="%2$s[from]" value="%3$s" class="%4$spopup-colorpicker"/>',
			$this->parent->field['id'],
			$this->parent->input_name,
			$from_value,
			$this->parent->class_attr
		);

		$html .= sprintf(
			'<div class="farb-popup"><div class="farb-popup-inside"><div id="%1$s-frompicker" class="color-picker"></div></div></div>',
			$this->parent->field['id']
		);

		$html .= sprintf(
			'<div class="color-prev prev-%1$s-from" %2$s rel="%1$s-from"></div>',
			$this->parent->field['id'],
			$from_style
		);

		// to field
		$html .= sprintf(
			'<label for="%1$s-to" class="anony-input-lable">%2$s</label>',
			$this->parent->field['id'],
			esc_html__( 'To', 'anonyengine' )
		);

		$html .= sprintf(
			'<input type="text" id="%1$s-to" name="%2$s[to]" value="%3$s" class="%4$spopup-colorpicker"/>',
			$this->parent->field['id'],
			$this->parent->input_name,
			$to_value,
			$this->parent->class_attr
		);

		$html .= sprintf(
			'<div class="farb-popup"><div class="farb-popup-inside"><div id="%1$s-topicker" class="color-picker"></div></div></div>',
			$this->parent->field['id']
		);

		$html .= sprintf(
			'<div class="color-prev prev-%1$s-to" %2$s rel="%1$s-to"></div>',
			$this->parent->field['id'],
			$to_style
		);

		$html .= ( isset( $this->parent->field['desc'] ) && ! empty( $this->parent->field['desc'] ) ) ? ' <div class="description">' . $this->parent->field['desc'] . '</div>' : '';

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

