<?php
/**
 * Color field class.
 *
 * @package Anonymous theme
 * @author Makiomar
 * @link http://makiomar.com
 */

/**
 * Color field class.
 *
 * This field uses the Farbtastic color picker.
 *
 * @package Anonymous theme
 * @author Makiomar
 * @link http://makiomar.com
 */
class ANONY_Color_Farbtastic {

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

		$this->parent_obj->value = esc_attr( $this->parent_obj->value );

		add_action( 'admin_print_footer_scripts', array( $this, 'footer_scripts' ) );
	}

	/**
	 * Color field render Function.
	 *
	 * @return string Field output.
	 */
	public function render() {

		$html = sprintf(
			'<fieldset class="anony-row anony-row-inline" id="fieldset_%1$s">',
			$this->parent_obj->id_attr_value
		);
		if ( 'meta' === $this->parent_obj->context && isset( $this->parent_obj->field['title'] ) ) {
			$html .= sprintf(
				'<label class="anony-label" for="%1$s">%2$s</label>',
				$this->parent_obj->id_attr_value,
				$this->parent_obj->field['title']
			);
		}

		if ( isset( $this->parent_obj->field['note'] ) ) {
			echo '<p class=anony-warning>' . esc_html( $this->parent_obj->field['note'] ) . '<p>';
		}

		$html .= '<div class="farb-popup-wrapper">';

		$html .= sprintf(
			'<input type="text" id="%1$s" name="%2$s" value="%3$s" class="%3$s popup-colorpicker"/>',
			$this->parent_obj->id_attr_value,
			$this->parent_obj->input_name,
			$this->parent_obj->value,
			$this->parent_obj->class_attr
		);

		$html .= sprintf(
			'<div class="farb-popup"><div class="farb-popup-inside"><div id="%1$spicker" class="color-picker"></div></div></div>',
			$this->parent_obj->id_attr_value
		);

		$html .= sprintf(
			'<div class="color-prev prev-%1$s" style="background-color:%2$s;" rel="%1$s"></div>',
			$this->parent_obj->id_attr_value,
			$this->parent_obj->value
		);

		$html .= ( isset( $this->parent_obj->field['desc'] ) && ! empty( $this->parent_obj->field['desc'] ) ) ? ' <div class="description">' . $this->parent_obj->field['desc'] . '</div>' : '';

		$html .= '</div>';

		$html .= '</fieldset>';

		return $html;
	}

	/**
	 * Enqueue scripts.
	 */
	public function enqueue() {
		wp_enqueue_style( 'farbtastic' );
		wp_enqueue_script( 'anony-farbtastic-color-js', ANONY_FIELDS_URI . 'color-farbtastic/field_color.js', array( 'jquery', 'farbtastic' ), time(), true );
	}

	/**
	 * Add needed scripts|styles to admin's footer
	 */
	public function footer_scripts() {
	}
}
