<?php
/**
 * Select field class
 *
 * @package Anonymous theme
 * @author Makiomar
 * @link http://makiomar.com
 */

/**
 * Select render class.
 *
 * @package    Fields inputs
 * @author     Makiomar <info@makiomar.com>
 * @license    https://makiomar.com AnonyEngine Licence
 * @link       https://makiomar.com
 */
class ANONY_Select {

	/**
	 * Parent object
	 *
	 * @var object
	 */
	private $parent_obj;

	/**
	 * Option number
	 *
	 * @var object
	 */
	private $numbered;

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

		$this->numbered = isset( $this->parent_obj->field['numbered'] ) && 'yes' === $this->parent_obj->field['numbered'] ? true : false;
	}

	/**
	 * Select field render Function.
	 *
	 * @return string Field output.
	 */
	public function render() {

		$disabled = isset( $this->parent_obj->field['disabled'] ) && ( true === $this->parent_obj->field['disabled'] ) ? ' disabled' : '';

		$first_option = ! empty( $this->parent_obj->field['first_option'] ) ? $this->parent_obj->field['first_option'] : esc_html__( 'Select', 'anonyengine' );

		$autocomplete = ( isset( $this->parent_obj->field['auto-complete'] ) && 'on' === $this->parent_obj->field['auto-complete'] ) ? 'autocomplete="on"' : 'autocomplete="off"';

		if ( isset( $this->parent_obj->field['multiple'] ) && $this->parent_obj->field['multiple'] ) {
			$multiple                     = ' multiple ';
			$this->parent_obj->input_name = $this->parent_obj->input_name . '[]';

		} else {
			$multiple = '';
		}

		$html = sprintf(
			'<fieldset class="anony-row anony-row-inline" id="fieldset_%1$s">',
			$this->parent_obj->field['id']
		);

		if ( isset( $this->parent_obj->field['note'] ) ) {
			echo '<p class=anony-warning>' . esc_html( $this->parent_obj->field['note'] ) . '<p>';
		}

		if ( in_array( $this->parent_obj->context, array( 'meta', 'form' ), true ) && isset( $this->parent_obj->field['title'] ) ) {
			$html .= sprintf(
				'<label class="anony-label" for="%1$s">%2$s</label>',
				$this->parent_obj->field['id'],
				$this->parent_obj->field['title']
			);
		}

		$html .= sprintf(
			'<select class="%1$s" name="%2$s" id="' . $this->parent_obj->field['id'] . '" %3$s %4$s %5$s>',
			$this->parent_obj->class_attr,
			$this->parent_obj->input_name,
			$disabled,
			$multiple,
			$autocomplete
		);

		if ( is_array( $this->parent_obj->field['options'] ) && ! empty( $this->parent_obj->field['options'] ) ) {

			$html .= sprintf(
				'<option value="">%1$s</option>',
				apply_filters(
					'anony_select_first_option_label',
					$first_option,
					$this->parent_obj->field['id']
				)
			);

			$option_number = 1;

			if ( empty( $multiple ) ) :

				if ( ANONY_ARRAY_HELP::is_assoc( $this->parent_obj->field['options'] ) ) {

					foreach ( $this->parent_obj->field['options'] as $key => $label ) {

						$label = $this->numbered ? $option_number . '- ' . $label : $label;

						++$option_number;

						$html .= sprintf(
							'<option value="%1$s"%2$s>%3$s</option>',
							$key,
							selected( $this->parent_obj->value, $key, false ),
							$label
						);
					}
				} else {
					foreach ( $this->parent_obj->field['options'] as $value ) {

						$html .= sprintf(
							'<option value="%1$s"%2$s>%1$s</option>',
							$value,
							selected( $this->parent_obj->value, $value, false )
						);
					}
				}

				elseif ( ANONY_ARRAY_HELP::is_assoc( $this->parent_obj->field['options'] ) ) :
					foreach ( $this->parent_obj->field['options'] as $key => $label ) {

						$label = $this->numbered ? $option_number . '- ' . $label : $label;
						++$option_number;

						$selected = is_array( $this->parent_obj->value ) && in_array( $key, $this->parent_obj->value, true ) && '' !== $key ? ' selected' : '';

						$html .= sprintf(
							'<option value="%1$s"%2$s>%3$s</option>',
							$key,
							$selected,
							$label
						);
					}
					else :
						foreach ( $this->parent_obj->field['options'] as $value ) {

							$selected = is_array( $this->parent_obj->value ) && in_array( $value, $this->parent_obj->value, true ) && '' !== $value ? ' selected' : '';

							$html .= sprintf(
								'<option value="%1$s"%2$s>%1$s</option>',
								$value,
								$selected
							);
						}

				endif;
		} else {
			$html .= sprintf(
				'<option value="">%1$s</option>',
				esc_html__( 'No options', 'anonyengine' )
			);
		}

		$html .= '</select>';

		$html .= ( isset( $this->parent_obj->field['desc'] ) && ! empty( $this->parent_obj->field['desc'] ) ) ? ' <div class="description">' . $this->parent_obj->field['desc'] . '</div>' : '';

		$html .= '</fieldset>';

		return $html;
	}
}
