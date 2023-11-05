<?php
/**
 * Multi-input types class.
 *
 * Handles rendring these type ['text','number','email', 'password','url', 'hidden'].
 *
 * @package Anonymous plugin
 * @author Makiomar
 * @link http://makiomar.com
 */

/**
 * Multi-input types render class.
 *
 * Handles rendring these type ['text','number','email', 'password','url', 'hidden'].
 *
 * @package Anonymous plugin
 * @author Makiomar
 * @link http://makiomar.com
 */
class ANONY_Mixed {

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

		switch ( $this->parent_obj->field['type'] ) {
			case 'url':
				$this->parent_obj->value = esc_url( $this->parent_obj->value );
				break;

			case 'email':
				$this->parent_obj->value = sanitize_email( $this->parent_obj->value );
				break;

			case 'password':
				$this->parent_obj->value = ''; // Passwords can't be visible.
				break;

			default:
				esc_attr( $this->parent_obj->value );
				break;
		}
	}

	/**
	 * Text field render Function.
	 *
	 * Suitable if editing/submitting is enabled
	 *
	 * @return string Field output.
	 */
	public function render() {

		$readonly = '';
		if ( ! empty( $this->parent_obj->field['readonly'] ) && $this->parent_obj->field['readonly'] ) {
			$readonly = ' readonly';
		}
		$placeholder = ( isset( $this->parent_obj->field['placeholder'] ) ) ? 'placeholder="' . $this->parent_obj->field['placeholder'] . '"' : '';

		if ( 'number' === $this->parent_obj->field['type'] ) {

			$step = ( isset( $this->parent_obj->field['step'] ) && ! empty( $this->parent_obj->field['step'] ) ) ? 'step="' . $this->parent_obj->field['step'] . '"' : '';

			$lang = 'lang="en-EN"';

			$lang = ( isset( $this->parent_obj->field['lang'] ) && ! empty( $this->parent_obj->field['lang'] ) ) ? $this->parent_obj->field['lang'] : $lang;
		}

		$icon = apply_filters( "anony_{$this->parent_obj->field['type']}_icon", '' );

		if ( $this->parent_obj->as_template ) {
			$html  = sprintf(
				'<fieldset class="anony-row anony-row-inline"%2$s%3$s>',
				$this->parent_obj->field['id'],
				'hidden' === $this->parent_obj->field['type'] ? ' style="display:none"' : '',
				$this->parent_obj->width
			);
			$html .= sprintf(
				'<div style="position:relative"><input  type="%1$s" name="%2$s" class="%3$s anony-row" %4$s %5$s %6$s %7$s/>',
				$this->parent_obj->field['type'],
				$this->parent_obj->input_name,
				$this->parent_obj->class_attr,
				isset( $step ) ? ' ' . $step : '',
				isset( $lang ) ? ' ' . $lang : '',
				$placeholder,
				$readonly
			);
			if ( ! empty( $icon ) ) {
				$html .= '<span class="anony-field-icon">' . $icon . '</span></div>';
			} else {
				$html .= '</div>';
			}

			$html .= '</fieldset>';

			return $html;
		}

		$html = sprintf(
			'<fieldset class="anony-row anony-row-inline%3$s" id="fieldset_%1$s"%2$s>',
			$this->parent_obj->field['id'],
			'hidden' === $this->parent_obj->field['type'] ? ' style="display:none"' : '',
			$this->parent_obj->width
		);

		if ( isset( $this->parent_obj->field['note'] ) ) {
			$html .= '<p class=anony-warning>' . esc_html( $this->parent_obj->field['note'] ) . '<p>';
		}

		if ( in_array( $this->parent_obj->context, array( 'meta', 'form' ), true ) && isset( $this->parent_obj->field['title'] ) ) {
			$html .= sprintf(
				'<label class="anony-label" for="%1$s">%2$s</label>',
				$this->parent_obj->field['id'],
				$this->parent_obj->field['title']
			);
		}

		$min = isset( $this->parent_obj->field['min'] ) ? ' min="' . $this->parent_obj->field['min'] . '"' : '';
		$max = isset( $this->parent_obj->field['max'] ) ? ' max="' . $this->parent_obj->field['max'] . '"' : '';

		$html .= sprintf(
			'<div style="position:relative"><input id="%1$s" type="%2$s" name="%3$s" value="%4$s" class="%5$s" %6$s %7$s %8$s%9$s%10$s%11$s/>',
			$this->parent_obj->field['id'],
			$this->parent_obj->field['type'],
			$this->parent_obj->input_name,
			$this->parent_obj->value,
			$this->parent_obj->class_attr,
			isset( $step ) ? ' ' . $step : '',
			isset( $lang ) ? ' ' . $lang : '',
			$placeholder,
			$min,
			$max,
			$readonly
		);
		if ( ! empty( $icon ) ) {
			$html .= '<span class="anony-field-icon">' . $icon . '</span></div>';
		} else {
			$html .= '</div>';
		}

		$html .= ( isset( $this->parent_obj->field['desc'] ) && ! empty( $this->parent_obj->field['desc'] ) ) ? ' <div class="description ' . $this->parent_obj->class_attr . '">' . $this->parent_obj->field['desc'] . '</div>' : '';

		$html .= '</fieldset>';

		return $html;
	}

	//phpcs:disable WordPress.NamingConventions.ValidFunctionName.MethodNameInvalid
	/**
	 * Text field render Function.
	 *
	 * Suitable if editing/submitting is enabled
	 *
	 * @return string Field output.
	 */
	public function renderDisplay() {
		//phpcs:enable.
		$html = sprintf(
			'<div class="anony-row anony-row-inline" id="fieldset_%1$s">',
			$this->parent_obj->field['id']
		);

		if ( 'meta' === $this->parent_obj->context && isset( $this->parent_obj->field['title'] ) ) {
			$html .= sprintf(
				'<label class="anony-label" for="%1$s">%2$s</label>',
				$this->parent_obj->field['id'],
				$this->parent_obj->field['title']
			);
		}

		$html .= sprintf(
			'<span id="%1$s" class="%2$s">%3$s</span>',
			$this->parent_obj->field['id'],
			$this->parent_obj->class_attr,
			$this->parent_obj->value
		);

		$html .= '</div>';

		return $html;
	}
}
