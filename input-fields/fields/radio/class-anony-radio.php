<?php
/**
 * Radio field class
 *
 * @package Anonymous theme
 * @author Makiomar
 * @link http://makiomar.com
 */

/**
 * Radio render class
 *
 * @package Anonymous theme
 * @author Makiomar
 * @link http://makiomar.com
 */
class ANONY_Radio {

	/**
	 * Parent object
	 *
	 * @var object
	 */
	private $parent_obj;

	/**
	 * Show labels only
	 *
	 * @var string
	 */
	private $show_only_labels;

	/**
	 * Field Constructor.
	 *
	 * Required - must call the parent constructor, then assign field and value to vars, and obviously call the render field function. Example <code>'options'  => array(
	 *                  'input-value1'  => array(
	 *                      'title' => esc_html__( 'input value1 title', 'textdomain' ),
	 *                      'class' => 'slider',
	 *                  ),
	 *
	 *                  'input-value1' => array(
	 *                      'title' => esc_html__( 'input value1 title', 'textdomain' ),
	 *                      'class' => 'slider',
	 *                  ),
	 *              )</code>
	 *
	 * @param object $parent_obj Field parent object.
	 */
	public function __construct( $parent_obj = null ) {

		if ( ! is_object( $parent_obj ) ) {
			return;
		}

		$this->parent_obj = $parent_obj;

		$this->show_only_labels = 'no';

		if ( isset( $this->parent_obj->field['only-labels'] ) ) {
			$this->show_only_labels = $this->parent_obj->field['only-labels'];
		}

		if ( 'yes' === $this->show_only_labels ) {
			$this->parent_obj->class_attr .= ' anony-hidden-radio';
		}
	}

	/**
	 * Radio field render Function.
	 *
	 * @return string Field output.
	 */
	public function render() {

		if ( isset( $this->parent_obj->field['note'] ) ) {
			echo '<p class=anony-warning>' . esc_html( $this->parent_obj->field['note'] ) . '<p>';
		}

		$html = sprintf(
			'<fieldset id="fieldset_%1$s" class="anony-row%2$s">',
			$this->parent_obj->id_attr_value,
			$this->parent_obj->width
		);

		if ( in_array( $this->parent_obj->context, array( 'meta', 'form' ), true ) && isset( $this->parent_obj->field['title'] ) ) {

			$label_prefix = isset( $this->parent_obj->field['label_prefix'] ) ? $this->parent_obj->field['label_prefix'] : '';

			$html .= sprintf(
				'<label class="anony-label" for="anony_%1$s">%2$s%3$s</label>',
				$this->parent_obj->id_attr_value,
				$label_prefix,
				$this->parent_obj->field['title']
			);
		}

		$html .= '<div class="anony-radio-items">';
		foreach ( $this->parent_obj->field['options'] as $k => $v ) {

			$radio_class = isset( $v['class'] ) ? 'class="' . $v['class'] . ' ' . $this->parent_obj->class_attr . '"' : '';

			$html .= '<div class="anony-radio-item">';

				$checked = checked( $this->parent_obj->value, $k, false );

				$search = array_search(
					$k,
					array_keys( $this->parent_obj->field['options'] ),
					true
				);

				$selected = ( '' !== $checked ) ? ' anony-radio-selected' : '';

				$html .= sprintf(
					'<label class="anony-radio%1$s anony-radio-%2$s" for="%2$s_%3$s">',
					$selected,
					$this->parent_obj->id_attr_value,
					$search
				);

					$html .= sprintf(
						'<input %1$s type="radio" id="%2$s_%3$s" name="%4$s" class="%5$s anony-radio-input" value="%6$s" %7$s />',
						$radio_class,
						$this->parent_obj->id_attr_value,
						$search,
						$this->parent_obj->input_name,
						$this->parent_obj->class_attr,
						$k,
						$checked
					);

				$html .= '</label>';

			if ( 'yes' === $this->show_only_labels ) {
				$html .= sprintf( '<span class="radio-title anony-for-hidden-radio" data-id="%1$s_%3$s">%2$s</span>', $this->parent_obj->id_attr_value, $v['title'], $search );
			} else {
				$html .= '<span class="radio-title">' . $v['title'] . '</span>';
			}

			$html .= '</div>';
		}
		$html .= '</div>';

		$html .= ( isset( $this->parent_obj->field['desc'] ) && ! empty( $this->parent_obj->field['desc'] ) ) ? '<br style="clear:both;"/><div class="input-field-description">' . $this->parent_obj->field['desc'] . '</div>' : '';

		$html .= '</fieldset>';

		return $html;
	}

	/**
	 * Enqueue scripts.
	 */
	public function enqueue() {
		wp_enqueue_script( 'anony-field-radio-js', ANONY_FIELDS_URI . 'radio/field_radio.js', array( 'jquery' ), time(), true );
	}
}
