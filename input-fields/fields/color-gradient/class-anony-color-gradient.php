<?php
/**
 * Color gradient input class.
 *
 * This color input depends on wp-color-picker.
 *
 * @package Anonymous theme
 * @author Makiomar
 * @link http://makiomar.com
 */

/**
 * Color gradient input render class.
 *
 * This color input depends on wp-color-picker
 */
class ANONY_Color_Gradient {

	/**
	 * Parent object
	 *
	 * @var object
	 */
	private $parent_obj;

	/**
	 * Consructor
	 *
	 * @param object $parent_obj Parent object.
	 */
	public function __construct( $parent_obj = null ) {

		if ( ! is_object( $parent_obj ) ) {
			return;
		}

		$this->parent_obj = $parent_obj;

		add_action( 'admin_print_footer_scripts', array( $this, 'footer_scripts' ) );
	}
	/**
	 * Render method.
	 *
	 * @return string Field output.
	 */
	public function render() {

		$default = isset( $this->parent_obj->default ) && ! is_null( $this->parent_obj->default ) ? $this->parent_obj->default : '#fff';

		$from = isset( $this->parent_obj->value['from'] ) ? esc_attr( $this->parent_obj->value['from'] ) : $default;

		$to = isset( $this->parent_obj->value['to'] ) ? esc_attr( $this->parent_obj->value['to'] ) : $default;

		$html = sprintf(
			'<fieldset class="anony-row anony-row-inline" id="fieldset_%1$s">',
			$this->parent_obj->field['id']
		);
		if ( 'meta' === $this->parent_obj->context && isset( $this->parent_obj->field['title'] ) ) {
			$html .= sprintf(
				'<label class="anony-label" for="%1$s">%2$s</label>',
				$this->parent_obj->field['id'],
				$this->parent_obj->field['title']
			);
		}

			$html .= '<div class="anony-metabox-col">';
			// From.

			$html .= sprintf(
				'<label class="anony-label-col" for="%1$s-from">%2$s</label>',
				$this->parent_obj->field['id'],
				esc_html__( 'From', 'anonyengine' )
			);

			$html .= sprintf(
				'<input type="text" class="%1$s-text anony-color-from wp-color-picker-field" id="%2$s-from" name="%2$s[from]" value="%3$s" data-default-color="%4$s" />',
				$this->parent_obj->class_attr,
				$this->parent_obj->input_name,
				$from,
				$default
			);
			// To.

			$html .= sprintf(
				'<label class="anony-label-col" for="%1$s-to">%2$s</label>',
				$this->parent_obj->field['id'],
				esc_html__( 'To', 'anonyengine' )
			);

			$html .= sprintf(
				'<input type="text" class="%1$s-text anony-color-to wp-color-picker-field" id="%2$s-to" name="%2$s[to]" value="%3$s" data-default-color="%4$s" />',
				$this->parent_obj->class_attr,
				$this->parent_obj->input_name,
				$to,
				$default
			);

			$html .= '</div>';

		$html .= '</fieldset>';

		return $html;
	}

	/**
	 * Enqueue scripts.
	 */
	public function enqueue() {
		wp_enqueue_style( 'wp-color-picker' );
		wp_enqueue_script( 'wp-color-picker' );
		wp_enqueue_script( 'jquery' );
	}

	/**
	 * Add needed scripts|styles to admin's footer
	 */
	public function footer_scripts() {
		?>
		
		<script type="text/javascript">
			jQuery(document).ready(function($) {
				//color picker.
				$('.anony-color-from').wpColorPicker();
				$('.anony-color-to').wpColorPicker();
			});
		</script>
		
	<?php }
}
?>
