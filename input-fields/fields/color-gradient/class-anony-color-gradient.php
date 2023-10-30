<?php
/**
 * Color gradient input render class.
 *
 * This color input depends on wp-color-picker
 */
class ANONY_Color_gradient {

	/**
	 * @var object
	 */
	private $parent;

	// Consructor.
	public function __construct( $parent = null ) {

		if ( ! is_object( $parent ) ) {
			return;
		}

		$this->parent = $parent;

		add_action( 'admin_print_footer_scripts', array( $this, 'footer_scripts' ) );

		$this->enqueue();
	}

	public function render() {

		$default = isset( $this->parent->default ) && ! is_null( $this->parent->default ) ? $this->parent->default : '#fff';

		$from = isset( $this->parent->value['from'] ) ? esc_attr( $this->parent->value['from'] ) : $default;

		$to = isset( $this->parent->value['to'] ) ? esc_attr( $this->parent->value['to'] ) : $default;

		$html = sprintf(
			'<fieldset class="anony-row anony-row-inline" id="fieldset_%1$s">',
			$this->parent->field['id']
		);
		if ( $this->parent->context == 'meta' && isset( $this->parent->field['title'] ) ) {
			$html .= sprintf(
				'<label class="anony-label" for="%1$s">%2$s</label>',
				$this->parent->field['id'],
				$this->parent->field['title']
			);
		}

			$html .= '<div class="anony-metabox-col">';
			// From.

			$html .= sprintf(
				'<label class="anony-label-col" for="%1$s-from">%2$s</label>',
				$this->parent->field['id'],
				esc_html__( 'From', 'anonyengine' )
			);

			$html .= sprintf(
				'<input type="text" class="%1$s-text anony-color-from wp-color-picker-field" id="%2$s-from" name="%2$s[from]" value="%3$s" data-default-color="%4$s" />',
				$this->parent->class_attr,
				$this->parent->input_name,
				$from,
				$default
			);
			// To.

			$html .= sprintf(
				'<label class="anony-label-col" for="%1$s-to">%2$s</label>',
				$this->parent->field['id'],
				esc_html__( 'To', 'anonyengine' )
			);

			$html .= sprintf(
				'<input type="text" class="%1$s-text anony-color-to wp-color-picker-field" id="%2$s-to" name="%2$s[to]" value="%3$s" data-default-color="%4$s" />',
				$this->parent->class_attr,
				$this->parent->input_name,
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
