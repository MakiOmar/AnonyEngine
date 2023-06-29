<?php
/**
 * Color field class
 *
 * @package Anonymous theme
 * @author Makiomar
 * @link http://makiomar.com
 */


/**
 * This field uses the WP color picker.
 */
class ANONY_Color {
	
	/**
	 * @var object
	 */
	private $parent;
	
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

		$this->parent->value = esc_attr( $this->parent->value );

		add_action( 'admin_print_footer_scripts', array( $this, 'footer_scripts' ) );

		$this->enqueue();
	}

	/**
	 * Color field render Function.
	 *
	 * @return void
	 */
	public function render() {

		$html = sprintf(
			'<fieldset class="anony-row anony-row-inline" id="fieldset_%1$s">',
			$this->parent->field['id']
		);

		if ( isset( $this->parent->field['note'] ) ) {
			echo '<p class=anony-warning>' . $this->parent->field['note'] . '<p>';
		}

		if ( $this->parent->context == 'meta' && isset( $this->parent->field['title'] ) ) {
			$html .= sprintf(
				'<label class="anony-label" for="%1$s">%2$s</label>',
				$this->parent->field['id'],
				$this->parent->field['title']
			);
		}

		$html .= sprintf(
			'<input type="text" class="%1$s-text anony-color wp-color-picker-field" id="%2$s" name="%2$s" value="%3$s" data-default-color="%4$s" />',
			$this->parent->class_attr,
			$this->parent->input_name,
			$this->parent->value,
			$this->parent->default
		);

		$html .= '</fieldset>';

		return $html;
	}

	/**
	 * Enqueue scripts.
	 */
	function enqueue() {
		wp_enqueue_style( 'wp-color-picker' );
		wp_enqueue_script( 'wp-color-picker' );
		wp_enqueue_script( 'jquery' );
	}

	/**
	 * Add needed scripts|styles to admin's footer
	 */
	public function footer_scripts(){?>
		
		<script type="text/javascript">
			jQuery(document).ready(function($) {
				//color picker
				$('.anony-color').wpColorPicker();
			});
		</script>
			
		<?php }
}
?>
