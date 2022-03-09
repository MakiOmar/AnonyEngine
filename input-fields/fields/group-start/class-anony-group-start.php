<?php
/**
 * Group start render class.
 *
 * @package Anonymous theme
 * @author Makiomar
 * @link http://makiomar.com
 */

class ANONY_Group_start {
	/**
	 * Color field Constructor.
	 *
	 * @param object $parent Field parent object
	 */
	public function __construct( $parent = null ) {

		if ( ! is_object( $parent ) ) {
			return;
		}

		$this->parent = $parent;

		$this->parent->value = '';

		$this->enqueue();

	}

	/**
	 * Render input field
	 */
	public function render() {
		$tag = isset( $this->parent->field['tag'] ) && ! is_null( $this->parent->field['tag'] ) ? $this->parent->field['tag'] : 'h1';

		$collapsible = isset( $this->parent->field['collapsible'] ) ? '<i class="fa fa-chevron-down" aria-hidden="true"></i>
' : '';

		$html = sprintf(
			'<%1$s class="anony-form-group-heading" data-id="%3$s">%2$s%4$s</%1$s>',
			$tag,
			$this->parent->field['title'],
			$this->parent->field['id'],
			$collapsible
		);

		$html .= sprintf(
			'<div class="anony-form-group-container%2$s" id="form-group-%1$s">',
			$this->parent->field['id'],
			isset( $this->parent->field['collapsible'] ) ? ' collapsible' : ''
		);

		return $html;

	}

	/**
	 * Enqueue scripts.
	 */
	public function enqueue() {

		wp_enqueue_script(
			'anony-group-start-js',
			ANONY_FIELDS_URI . 'group-start/group-start.js',
			array( 'jquery' ),
			time(),
			true
		);

	}
}
