<?php
/**
 * Color field class
 *
 * @package Anonymous theme
 * @author Makiomar
 * @link http://makiomar.com
 */

/**
 * Color field class
 *
 * This field uses the WP color picker.
 *
 * @package Anonymous theme
 * @author Makiomar
 * @link http://makiomar.com
 */
class ANONY_Color {

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

		$this->parent_obj->value = esc_attr( $this->parent_obj->value );

		add_action( 'admin_print_footer_scripts', array( $this, 'footer_scripts' ) );
	}

	/**
	 * Color field render Function.
	 *
	 * @return string Field output.
	 */
	public function render() {

		$html = sprintf(
			'<fieldset class="anony-row anony-row-inline" id="fieldset_%1$s">',
			$this->parent_obj->field['id']
		);

		if ( isset( $this->parent_obj->field['note'] ) ) {
			echo '<p class=anony-warning>' . esc_html( $this->parent_obj->field['note'] ) . '<p>';
		}

		if ( 'meta' === $this->parent_obj->context && isset( $this->parent_obj->field['title'] ) ) {
			$html .= sprintf(
				'<label class="anony-label" for="%1$s">%2$s</label>',
				$this->parent_obj->field['id'],
				$this->parent_obj->field['title']
			);
		}

		$html .= sprintf(
			'<input type="text" class="%1$s-text anony-color wp-color-picker-field" id="%2$s" name="%2$s" value="%3$s" data-default-color="%4$s" />',
			$this->parent_obj->class_attr,
			$this->parent_obj->input_name,
			$this->parent_obj->value,
			$this->parent_obj->default
		);

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
				$('.anony-color').wpColorPicker();
			});
		</script>
			
		<?php }
}
?>
