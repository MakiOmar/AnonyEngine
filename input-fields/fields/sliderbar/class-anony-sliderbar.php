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
	 * @var object
	 */
	private $parent_obj;


	/**
	 * Field Constructor.
	 *
	 * Required - must call the parent constructor, then assign field and value to vars, and obviously call the render field function
	 *
	 * @param array  $this->parent_obj->field Array of field's data
	 * @param object $parent_obj Field parent object
	 */
	public function __construct( $parent_obj = null ) {

		if ( ! is_object( $parent_obj ) ) {
			return;
		}

		$this->parent_obj = $parent_obj;
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
			$this->parent_obj->field['id']
		);
		if ( $this->parent_obj->context == 'meta' && isset( $this->parent_obj->field['title'] ) ) {
			$html .= sprintf(
				'<label class="anony-label" for="%1$s">%2$s</label>',
				$this->parent_obj->field['id'],
				$this->parent_obj->field['title']
			);
		}

		$html .= sprintf(
			'<div class="anony-options-row"><div id="%1$s_sliderbar" class="sliderbar %2$s" rel="%1$s"></div>',
			$this->parent_obj->field['id'],
			$this->parent_obj->class_attr
		);

		$html .= sprintf(
			'<input type="text" id="%1$s" name="%2$s" value="%3$s" class="sliderbar_input %4$s" readonly="readonly"/></div>',
			$this->parent_obj->field['id'],
			$this->parent_obj->input_name,
			$this->parent_obj->value,
			$this->parent_obj->class_attr
		);

		$html .= ( isset( $this->parent_obj->field['desc'] ) && ! empty( $this->parent_obj->field['desc'] ) ) ? ' <div class="description sliderbar_desc' . $this->parent_obj->class_attr . '">' . $this->parent_obj->field['desc'] . '</div>' : '';

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
