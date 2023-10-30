<?php
/**
 * Radio img field class
 *
 * @package Anonymous theme
 * @author Makiomar
 * @link http://makiomar.com
 */

class ANONY_Radio_img {

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
	 * Radioo img field render Function.
	 *
	 * @return void
	 */
	public function render( $meta = false ) {

		$html = '';
		if ( isset( $this->parent_obj->field['note'] ) ) {
			$html .= '<p class=anony-warning>' . $this->parent_obj->field['note'] . '<p>';
		}

		$html .= sprintf(
			'<fieldset class="anony-row anony-row-inline" id="fieldset_%1$s">',
			$this->parent_obj->field['id']
		);
		if ( $this->parent_obj->context == 'meta' && isset( $this->parent_obj->field['title'] ) ) {
			$html .= sprintf(
				'<label class="anony-label" for="%1$s">%2$s</label>',
				$this->parent_obj->field['id'],
				$this->parent_obj->field['title']
			);
		}
		foreach ( $this->parent_obj->field['options'] as $k => $v ) {

			$html .= '<div class="anony-radio-item">';

				$checked = checked( $this->parent_obj->value, $k, false );

				$selected = ( $checked != '' ) ? ' anony-radio-img-selected' : '';

				$search = array_search(
					$k,
					array_keys( $this->parent_obj->field['options'] )
				);

				$html .= sprintf(
					'<label class="anony-radio-img%1$s anony-radio-img-%2$s" for="%2$s_%3$s">',
					$selected,
					$this->parent_obj->field['id'],
					$search
				);

					$html .= sprintf(
						'<input type="radio" id="%1$s_%2$s" name="%3$s" class="%4$s" value="%5$s" %6$s/>',
						$this->parent_obj->field['id'],
						$search,
						$this->parent_obj->input_name,
						$this->parent_obj->class_attr,
						$k,
						$checked
					);

					$html .= sprintf(
						'<img src="%1$s" alt="%2$s" onclick="jQuery:anony_radio_img_select(\'%3$s_%4$s\', \'%3$s\');" />',
						$v['img'],
						$v['title'],
						$this->parent_obj->field['id'],
						$search
					);

				$html .= '</label>';

				$html .= '<span class="description">' . $v['title'] . '</span>';

			$html .= '</div>';
		}

			$html .= ( isset( $this->parent_obj->field['desc'] ) && ! empty( $this->parent_obj->field['desc'] ) ) ? '<br style="clear:both;"/><div class="description">' . $this->parent_obj->field['desc'] . '</div>' : '';

		$html .= '</fieldset>';

		return $html;
	}

	/**
	 * Enqueue scripts.
	 */
	public function enqueue() {
		wp_enqueue_script( 'anony-opts-field-radio_img-js', ANONY_FIELDS_URI . 'radio-img/field_radio_img.js', array( 'jquery' ), time(), true );
	}
}
