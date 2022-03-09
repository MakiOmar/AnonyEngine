<?php
/**
 * Sliderbar field class
 *
 * @package Anonymous theme
 * @author Makiomar
 * @link http://makiomar.com
 */
class ANONY_Sliderbar {


	/**
	 * Field Constructor.
	 *
	 * Required - must call the parent constructor, then assign field and value to vars, and obviously call the render field function
	 *
	 * @param array  $this->parent->field Array of field's data
	 * @param object $parent Field parent object
	 */
	public function __construct( $parent = null ) {

		if ( ! is_object( $parent ) ) {
			return;
		}

		$this->parent = $parent;
		$this->enqueue();

	}

	/**
	 * Slidebar field render Function.
	 *
	 * @return void
	 */
	public function render() {

		$html = '';
		if ( isset( $field['note'] ) ) {
			$html .= '<p class=anony-warning>' . $field['note'] . '<p>';
		}

		$html .= sprintf(
			'<fieldset class="anony-row anony-row-inline anony-tabs" id="fieldset_%1$s">',
			$this->parent->field['id']
		);
		if ( $this->parent->context == 'meta' && isset( $this->parent->field['title'] ) ) {
			$html .= sprintf(
				'<label class="anony-label" for="%1$s">%2$s</label>',
				$this->parent->field['id'],
				$this->parent->field['title']
			);
		}

		$html .= sprintf(
			'<div class="anony-options-row"><div id="%1$s_sliderbar" class="sliderbar %2$s" rel="%1$s"></div>',
			$this->parent->field['id'],
			$this->parent->class_attr
		);

		$html .= sprintf(
			'<input type="text" id="%1$s" name="%2$s" value="%3$s" class="sliderbar_input %4$s" readonly="readonly"/></div>',
			$this->parent->field['id'],
			$this->parent->input_name,
			$this->parent->value,
			$this->parent->class_attr
		);

		$html .= ( isset( $this->parent->field['desc'] ) && ! empty( $this->parent->field['desc'] ) ) ? ' <div class="description sliderbar_desc' . $this->parent->class_attr . '">' . $this->parent->field['desc'] . '</div>' : '';

		$html .= '</fieldset>';

		return $html;
	}


	/**
	 * Enqueue scripts.
	 */
	public function enqueue() {

		wp_enqueue_style( 'anony-opts-jquery-ui-css' );

		if ( is_rtl() ) {
			wp_enqueue_style( 'jquery.ui.slider-rtl' );
			wp_enqueue_script( 'jquery.ui.slider-rtl.min' );
		}

		wp_enqueue_script(
			'jquery-ui-slider',
			ANONY_FIELDS_URI . 'sliderbar/jquery.ui.slider.js',
			array( 'jquery', 'jquery-ui-core' ),
			time(),
			true
		);

		wp_enqueue_script(
			'anony-opts-field-sliderbar-js',
			ANONY_FIELDS_URI . 'sliderbar/field_sliderbar.js',
			array( 'jquery', 'jquery-ui-core', 'jquery-ui-dialog', 'jquery-ui-slider' ),
			time(),
			true
		);

	}

}

