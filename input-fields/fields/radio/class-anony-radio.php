<?php
/**
 * Radio field class
 *
 * @package Anonymous theme
 * @author Makiomar
 * @link http://makiomar.com
 */
class ANONY_Radio {

	/**
	 * Field Constructor.
	 *
	 * Required - must call the parent constructor, then assign field and value to vars, and obviously call the render field function. Example <code>'options'  => array(
						'input-value1'  => array(
							'title' => esc_html__( 'input value1 title', 'textdomain' ),
							'class' => 'slider',
						),

						'input-value1' => array(
							'title' => esc_html__( 'input value1 title', 'textdomain' ),
							'class' => 'slider',
						),
					)</code>
	 *
	 * @param array  $this->parent->field Array of field's data
	 * @param object $parent Field parent object
	 */
	public function __construct( $parent = null ) {

		if ( ! is_object( $parent ) ) {
			return;
		}

		$this->parent = $parent;

		$this->show_only_labels = 'no';

		if ( isset( $this->parent->field['only-labels'] ) ) {
			$this->show_only_labels = $this->parent->field['only-labels'];
		}

		if ( 'yes' === $this->show_only_labels ) {
			$this->parent->class_attr .= ' anony-hidden-radio';
		}
		$this->enqueue();
	}

	/**
	 * Radio field render Function.
	 *
	 * @return void
	 */
	public function render() {

		if ( isset( $this->parent->field['note'] ) ) {
			echo '<p class=anony-warning>' . $this->parent->field['note'] . '<p>';
		}

		$html = sprintf(
			'<fieldset class="anony-row%2$s" id="fieldset_%1$s">',
			$this->parent->field['id'],
			$this->parent->width
		);

		if ( in_array( $this->parent->context, array( 'meta', 'form' ) ) && isset( $this->parent->field['title'] ) ) {

			$label_prefix = isset( $this->parent->field['label_prefix'] ) ? $this->parent->field['label_prefix'] : '';

			$html .= sprintf(
				'<label class="anony-label" for="anony_%1$s">%2$s%3$s</label>',
				$this->parent->field['id'],
				$label_prefix,
				$this->parent->field['title']
			);
		}

		// options sample
		/*
			$options = array(
				'featured-cat'	=> array(
					'title' => esc_html__('Featured category', ANONY_TEXTDOM),
					'class' => 'slider'
				),

				'featured-post'	=> array(
					'title' => esc_html__('Featured posts', ANONY_TEXTDOM),
					'class' => 'slider'
				),
			),
		*/
		$html .= '<div class="anony-radio-items">';
		foreach ( $this->parent->field['options'] as $k => $v ) {

			$radioClass = isset( $v['class'] ) ? 'class="' . $v['class'] . ' ' . $this->parent->class_attr . '"' : '';

			$html .= '<div class="anony-radio-item">';

				$checked = checked( $this->parent->value, $k, false );

				$search = array_search(
					$k,
					array_keys( $this->parent->field['options'] )
				);

				$selected = ( $checked != '' ) ? ' anony-radio-selected' : '';

				$html .= sprintf(
					'<label class="anony-radio%1$s anony-radio-%2$s" for="%2$s_%3$s">',
					$selected,
					$this->parent->field['id'],
					$search
				);

					$html .= sprintf(
						'<input %1$s type="radio" id="%2$s_%3$s" name="%4$s" class="%5$s anony-radio-input" value="%6$s" %7$s />',
						$radioClass,
						$this->parent->field['id'],
						$search,
						$this->parent->input_name,
						$this->parent->class_attr,
						$k,
						$checked
					);

				$html .= '</label>';

				if ( 'yes' === $this->show_only_labels ) {
					$html .= sprintf('<span class="radio-title anony-for-hidden-radio" data-id="%1$s_%3$s">%2$s</span>', $this->parent->field['id'], $v['title'], $search);
				}else{
					$html .= '<span class="radio-title">' . $v['title'] . '</span>';
				}

				

			$html .= '</div>';
		}
		$html .= '</div>';

		$html .= ( isset( $this->parent->field['desc'] ) && ! empty( $this->parent->field['desc'] ) ) ? '<br style="clear:both;"/><div class="description">' . $this->parent->field['desc'] . '</div>' : '';

		$html .= '</fieldset>';

		echo $html;

	}

	/**
	 * Enqueue scripts.
	 */
	public function enqueue() {
		wp_enqueue_script('anony-field-radio-js', ANONY_FIELDS_URI.'radio/field_radio.js', array('jquery'),time(),true);
	}

}
