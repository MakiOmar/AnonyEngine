<?php
/**
 * Font select field class
 *
 * @package Anonymous theme
 * @author Makiomar
 * @link http://makiomar.com
 */

class ANONY_Font_select {

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

		$this->parent_obj->value = esc_attr( $this->parent_obj->value );
	}

	/**
	 * Font select field render Function.
	 *
	 * @return void
	 */
	public function render() {

		$fonts = anony_fonts();

		$opts_groups =
		array(
			'default' => esc_html__( 'Default Webfont', 'anonyengine' ),
			'system'  => esc_html__( 'System', 'anonyengine' ),
			'popular' => esc_html__( 'Popular Google Fonts', 'anonyengine' ),
			'all'     => esc_html__( 'Google Fonts', 'anonyengine' ),
		);

		$html = sprintf(
			'<fieldset class="anony-row anony-row-inline" id="fieldset_%1$s">',
			$this->parent_obj->field['id']
		);

		if ( isset( $field['note'] ) ) {
			echo '<p class=anony-warning>' . $this->parent_obj->field['note'] . '<p>';
		}

		if ( $this->parent_obj->context == 'meta' && isset( $this->parent_obj->field['title'] ) ) {
			$html .= sprintf(
				'<label class="anony-label" for="%1$s">%2$s</label>',
				$this->parent_obj->field['id'],
				$this->parent_obj->field['title']
			);
		}

		$html .= sprintf( '<select name="%1$s" class="%2$s-select %2$s" rows="6" >', $this->parent_obj->input_name, $this->parent_obj->class_attr );

		$html .= ANONY_Post_Help::renderHtmlOptsGroups( $fonts, $opts_groups, $this->parent_obj->value );

		$html .= '</select>';

		$html .= ( isset( $this->parent_obj->field['desc'] ) && ! empty( $this->parent_obj->field['desc'] ) ) ? ' <div class="description">' . $this->parent_obj->field['desc'] . '</div>' : '';

		$html .= '</fieldset>';

		return $html;
	}
}
