<?php
/**
 * Tabs field class
 *
 * @package Anonymous theme
 * @author Makiomar
 * @link http://makiomar.com
 */

/**
 * Tabs render class.
 *
 * @package    Fields inputs
 * @author     Makiomar <info@makiomar.com>
 * @license    https://makiomar.com AnonyEngine Licence
 * @link       https://makiomar.com
 */
class ANONY_Tabs {

	/**
	 * Parent object
	 *
	 * @var object
	 */
	private $parent_obj;

	/**
	 * Field Constructor.
	 *
	 * Required - must call the parent constructor, then assign field and value to vars, and obviously call the render field function
	 *
	 * @param object $parent_obj Field parent object.
	 */
	public function __construct( $parent_obj = null ) {

		if ( ! is_object( $parent_obj ) ) {
			return;
		}

		$this->parent_obj = $parent_obj;
	}

	/**
	 * Tabs field render Function.
	 *
	 * @return string Field output.
	 */
	public function render() {

		$count = isset( $this->parent_obj->value['count'] ) ? $this->parent_obj->value['count'] + 1 : 1;
		$html  = '';
		if ( isset( $this->parent_obj->field['note'] ) ) {
			$html .= '<p class=anony-warning>' . esc_html( $this->parent_obj->field['note'] ) . '<p>';
		}

		$html .= sprintf(
			'<fieldset class="anony-row anony-row-inline anony-tabs" id="fieldset_%1$s">',
			$this->parent_obj->id_attr_value
		);
		if ( 'meta' === $this->parent_obj->context && isset( $this->parent_obj->field['title'] ) ) {
			$html .= sprintf(
				'<label class="anony-label" for="%1$s">%2$s</label>',
				$this->parent_obj->id_attr_value,
				$this->parent_obj->field['title']
			);
		}
		$html .= "<div class='anony-multi-value-flex anony-flex-column'>";
		$html .= '<a href="javascript:void(0);" class="btn-blue anony-add-tab" rel-name="' . $this->parent_obj->input_name . '">Add tab</a>';

		$html .= '<input type="hidden" name="' . $this->parent_obj->input_name . '[count]" class="anony-tabs-count" value="' . $count . '" />';

		$html .= '<br style="clear:both;" />';

		$html .= '<ul class="tabs-ul">';

		// default tab to clone.
				$html     .= '<li class="tabs-default">';
					$html .= '<div class="anony-tab-item"><label class="anony-label">' . esc_html__( 'Title', 'anonyengine' ) . '</label>';
					$html .= '<input type="text" name="' . $this->parent_obj->input_name . '[data-' . ( $count ) . '][title]" value="" /></div>';
					$html .= '<div class="anony-tab-item"><label class="anony-label">' . esc_html__( 'Content', 'anonyengine' ) . '</label>';
					$html .= '<textarea name="' . $this->parent_obj->input_name . '[data-' . ( $count ) . '][content]" value=""></textarea></div>';
					$html .= '<br style="clear:both;" />';
					$html .= '<a href="" class="anony-btn-close anony-remove-tab"><em>delete</em></a>';
				$html     .= '</li>';
			$i             = 1;

		if ( isset( $this->parent_obj->value ) && is_array( $this->parent_obj->value ) ) {

			$count = intval( array_shift( $this->parent_obj->value ) );

			foreach ( $this->parent_obj->value as $k => $value ) {

				if ( $i <= $count ) {

					$html .= '<li>';
					$html .= '<div class="anony-tab-item"><label class="anony-label">' . esc_html__( 'Title', 'anonyengine' ) . '</label>';
					$html .= '<input type="text" name="' . $this->parent_obj->input_name . '[data-' . $i . '][title]" value="' . esc_attr( $value['title'] ) . '" /></div>';
					$html .= '<div class="anony-tab-item"><label class="anony-label">' . esc_html__( 'Content', 'anonyengine' ) . '</label>';
					$html .= '<textarea name="' . $this->parent_obj->input_name . '[data-' . $i . '][content]" value="" >' . esc_textarea( $value['content'] ) . '</textarea></div>';
					$html .= '<a href="" class="anony-btn-close anony-remove-tab"><em>delete</em></a>';
					$html .= '</li>';
					++$i;

				}
			}
		}

		$html .= '</ul></div>';

		$html .= ( isset( $this->parent_obj->field['desc'] ) && ! empty( $this->parent_obj->field['desc'] ) ) ? ' <div class="description tabs-desc">' . $this->parent_obj->field['desc'] . '</div>' : '';
		$html .= '</fieldset>';

		return $html;
	}

	/**
	 * Enqueue scripts.
	 */
	public function enqueue() {
		wp_enqueue_script( 'anony-opts-field-tabs-js', ANONY_FIELDS_URI . 'tabs/field_tabs.js', array( 'jquery' ), time(), true );
	}
}
