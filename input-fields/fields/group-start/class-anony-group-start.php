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
	 * @var object
	 */
	private $parent_obj;

	/**
	 * Color field Constructor.
	 *
	 * @param object $parent_obj Field parent object
	 */
	public function __construct( $parent_obj = null ) {

		if ( ! is_object( $parent_obj ) ) {
			return;
		}

		$this->parent_obj = $parent_obj;

		$this->parent_obj->value = '';
	}

	/**
	 * Render input field.
	 *
	 * Please note that if you want to show groups as tabs, you need to add the `layout = tabs` in the metabox definition.
	 */
	public function render() {
		$tag = isset( $this->parent_obj->field['tag'] ) && ! is_null( $this->parent_obj->field['tag'] ) ? $this->parent_obj->field['tag'] : 'h1';

		$collapsible        = isset( $this->parent_obj->field['collapsible'] ) ? '<i class="fa fa-chevron-down" aria-hidden="true"></i>
' : '';
		$heading_link_style = 'style="display:flex;height:100%"';

		$html = sprintf(
			'<%1$s class="anony-form-group-heading-tag"><a class="anony-form-group-heading" href="#" data-id="%2$s" %3$s>%4$s%5$s</a></%1$s>',
			$tag,
			$this->parent_obj->field['id'],
			$heading_link_style,
			$this->parent_obj->field['title'],
			$collapsible
		);

		$html .= sprintf(
			'<div class="anony-form-group-container%2$s" id="form-group-%1$s">',
			$this->parent_obj->field['id'],
			isset( $this->parent_obj->field['collapsible'] ) ? ' collapsible' : ''
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
