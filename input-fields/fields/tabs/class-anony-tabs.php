<?php
/**
 * Tabs field class
 *
 * @package Anonymous theme
 * @author Makiomar
 * @link http://makiomar.com
 */

class ANONY_Tabs {

	/**
	 * @var object
	 */
	private $parent;

	/**
	 * Field Constructor.
	 *
	 * Required - must call the parent constructor, then assign field and value to vars, and obviously call the render field function
	 *
	 * @param array  $this->parent->field Array of field's data
	 * @param object $parent Field parent object
	 */
	public function __construct( $parent = null ) {

		if ( ! is_object( $parent ) ) {
			return;
		}

		$this->parent = $parent;
		$this->enqueue();
	}

	/**
	 * Tabs field render Function.
	 *
	 * @return void
	 */
	function render() {

		$count = isset( $this->parent->value['count'] ) ? $this->parent->value['count'] + 1 : 1;
		$html  = '';
		if ( isset( $this->parent->field['note'] ) ) {
			$html .= '<p class=anony-warning>' . $this->parent->field['note'] . '<p>';
		}

		$html .= sprintf(
			'<fieldset class="anony-row anony-row-inline anony-tabs" id="fieldset_%1$s">',
			$this->parent->field['id']
		);
		if ( $this->parent->context == 'meta' && isset( $this->parent->field['title'] ) ) {
			$html .= sprintf(
				'<label class="anony-label" for="%1$s">%2$s</label>',
				$this->parent->field['id'],
				$this->parent->field['title']
			);
		}
		$html .= "<div class='anony-multi-value-flex anony-flex-column'>";
		$html .= '<a href="javascript:void(0);" class="btn-blue anony-add-tab" rel-name="' . $this->parent->input_name . '">Add tab</a>';

		$html .= '<input type="hidden" name="' . $this->parent->input_name . '[count]" class="anony-tabs-count" value="' . $count . '" />';

		$html .= '<br style="clear:both;" />';

		$html .= '<ul class="tabs-ul">';

		// default tab to clone.
				$html     .= '<li class="tabs-default">';
					$html .= '<div class="anony-tab-item"><label class="anony-label">' . esc_html__( 'Title', 'anonyengine' ) . '</label>';
					$html .= '<input type="text" name="' . $this->parent->input_name . '[data-' . ( $count ) . '][title]" value="" /></div>';
					$html .= '<div class="anony-tab-item"><label class="anony-label">' . esc_html__( 'Content', 'anonyengine' ) . '</label>';
					$html .= '<textarea name="' . $this->parent->input_name . '[data-' . ( $count ) . '][content]" value=""></textarea></div>';
					$html .= '<br style="clear:both;" />';
					$html .= '<a href="" class="anony-btn-close anony-remove-tab"><em>delete</em></a>';
				$html     .= '</li>';
			$i             = 1;

		if ( isset( $this->parent->value ) && is_array( $this->parent->value ) ) {

			$count = intval( array_shift( $this->parent->value ) );

			foreach ( $this->parent->value as $k => $value ) {

				if ( $i <= $count ) {

					$html .= '<li>';
					$html .= '<div class="anony-tab-item"><label class="anony-label">' . esc_html__( 'Title', 'anonyengine' ) . '</label>';
					$html .= '<input type="text" name="' . $this->parent->input_name . '[data-' . $i . '][title]" value="' . esc_attr( $value['title'] ) . '" /></div>';
					$html .= '<div class="anony-tab-item"><label class="anony-label">' . esc_html__( 'Content', 'anonyengine' ) . '</label>';
					$html .= '<textarea name="' . $this->parent->input_name . '[data-' . $i . '][content]" value="" >' . esc_textarea( $value['content'] ) . '</textarea></div>';
					$html .= '<a href="" class="anony-btn-close anony-remove-tab"><em>delete</em></a>';
					$html .= '</li>';
					++$i;

				}
			}
		}

		$html .= '</ul></div>';

		$html .= ( isset( $this->parent->field['desc'] ) && ! empty( $this->parent->field['desc'] ) ) ? ' <div class="description tabs-desc">' . $this->parent->field['desc'] . '</div>' : '';
		$html .= '</fieldset>';

		return $html;
	}

	/**
	 * Enqueue scripts.
	 */
	function enqueue() {
		wp_enqueue_script( 'anony-opts-field-tabs-js', ANONY_FIELDS_URI . 'tabs/field_tabs.js', array( 'jquery' ), time(), true );
	}
}
