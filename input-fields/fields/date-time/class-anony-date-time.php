<?php
/**
 * Date and Time field class
 *
 * @package Anonymous theme
 * @author Makiomar
 * @link http://makiomar.com
 */

/**
 * Date and Time field render class
 *
 * @package Anonymous theme
 * @author Makiomar
 * @link http://makiomar.com
 */
class ANONY_Date_Time {

	/**
	 * Parent object
	 *
	 * @var object
	 */
	private $parent_obj;


	/**
	 * Dtae format.
	 *
	 * @var string
	 */
	private $date_format;

	/**
	 * Time format.
	 *
	 * @var string
	 */
	private $time_format;

	/**
	 * Get date only. time only or both.
	 *
	 * @var string
	 */
	private $get;

	/**
	 * DateTime picker options.
	 *
	 * @var array
	 */
	private $picker_options;

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

		$this->parent_obj->value = esc_attr( $this->parent_obj->value );

		$this->date_format = isset( $this->parent_obj->field['date-format'] ) ? $this->parent_obj->field['date-format'] : 'dd-mm-yy';

		$this->time_format = isset( $this->parent_obj->field['time-format'] ) ? $this->parent_obj->field['time-format'] : 'hh:mm:s';

		$this->get = isset( $this->parent_obj->field['get'] ) ? $this->parent_obj->field['get'] : 'datetime';

		$this->picker_options = isset( $this->parent_obj->field['picker-options'] ) ? $this->parent_obj->field['picker-options'] :

			array(
				'dateFormat' => $this->date_format,
				'timeFormat' => $this->time_format,
			);

		add_action( 'admin_print_footer_scripts', array( &$this, 'footer_scripts' ) );

		if ( isset( $this->parent_obj->field['show_on_front'] ) && true === $this->parent_obj->field['show_on_front'] ) {
			add_action( 'wp_print_footer_scripts', array( &$this, 'footer_scripts' ) );
		}
	}

	/**
	 * Date field render Function.
	 *
	 * @return string Field output.
	 */
	public function render() {

		$placeholder = isset( $this->parent_obj->field['placeholder'] ) ? ' placeholder="' . $this->parent_obj->field['placeholder'] . '"' : ' placeholder="' . $this->parent_obj->field['title'] . '"';

		if ( $this->parent_obj->as_template ) {
			$html  = sprintf(
				'<fieldset class="anony-row anony-row-inline" id="fieldset_%1$s">',
				$this->parent_obj->field['id']
			);
			$html .= sprintf(
				'<input type="text" name="%1$s" class="anony-%2$s %3$s"%4$s/>',
				$this->parent_obj->input_name,
				$this->parent_obj->field['id'],
				$this->parent_obj->class_attr,
				$placeholder
			);

			$html .= '</fieldset>';

			return $html;
		}

		$html = sprintf(
			'<fieldset class="anony-row anony-row-inline" id="anony_fieldset_%1$s">',
			$this->parent_obj->field['id']
		);

		if ( 'meta' === $this->parent_obj->context && isset( $this->parent_obj->field['title'] ) ) {
			$html .= sprintf(
				'<label class="anony-label" for="%1$s">%2$s</label>',
				$this->parent_obj->field['id'],
				$this->parent_obj->field['title']
			);
		}

		$html .= '<div class="anony-flex-column">';

		if ( isset( $field['note'] ) ) {
			echo '<p class=anony-warning>' . esc_html( $this->parent_obj->field['note'] ) . '<p>';
		}

		$html .= sprintf(
			'<input type="text" name="%1$s" id="anony-%2$s" value="%3$s" class="anony-%2$s %4$s"%5$s/>',
			$this->parent_obj->input_name,
			$this->parent_obj->field['id'],
			$this->parent_obj->value,
			$this->parent_obj->class_attr,
			$placeholder
		);

		$html .= ( isset( $this->parent_obj->field['desc'] ) && ! empty( $this->parent_obj->field['desc'] ) ) ? ' <div class="description ' . $this->parent_obj->class_attr . '">' . $this->parent_obj->field['desc'] . '</div>' : '';

		$html .= '<div></fieldset>';

		return $html;
	}

	/**
	 * Enqueue scripts.
	 */
	public function enqueue() {

		$wp_scripts = wp_scripts();

		// Scripts.
		wp_enqueue_script( 'jquery' );
		wp_enqueue_script( 'jquery-ui-core' );
		wp_enqueue_script( 'jquery-ui-datepicker' );
		wp_enqueue_script( 'jquery-ui-timepicker-addon', ANONY_FIELDS_URI . 'date-time/jquery-ui-timepicker-addon.js', array( 'jquery', 'jquery-ui-datepicker', 'jquery-ui-core' ), time(), array( 'in_footer' => true ) );

		// Styles.
		wp_enqueue_style( 'jquery-ui-css', ANONY_FIELDS_URI . 'date-time/jquery-ui.css', array(), time() );
		wp_enqueue_style( 'jquery-ui-timepicker-addon', ANONY_FIELDS_URI . 'date-time/jquery-ui-timepicker-addon.css', array( 'jquery-ui-css' ), time() );
	}

	/**
	 * Add date/time picker footer scripts
	 */
	public function footer_scripts() {
		$options = '';
		// Options for datetime picker.
		foreach ( $this->picker_options as $key => $value ) {
			$options .= $key . ':"' . $value . '",';
		}
		?>

		<script type="text/javascript">
			jQuery(document).ready(function($){

				var fieldClass = <?php echo '".anony-' . esc_js( $this->parent_obj->field['id'] ) . '"'; ?>;
				//console.log(fieldClass);

				var DateTimeOptions = {<?php echo esc_js( $options ); ?>};
				var getWhat = '<?php echo esc_js( $this->get ); ?>picker';

				<?php if ( isset( $this->parent_obj->field['nested-to'] ) ) { ?>
					var nestedToId = <?php echo '".' . esc_js( $this->parent_obj->field['nested-to'] ) . '"'; ?>;
					var nestedTo   = nestedToId + '-wrapper';
				<?php } ?>
				
				$.fn.AnonyDateTimePicker(fieldClass, getWhat, DateTimeOptions);

				//$.fn.AnonyObserve is defined here (assets/js/jquery.helpme.js)
				if (typeof nestedTo !== 'undefined') {
					$.fn.AnonyObserve(nestedTo, function(){
						$.fn.AnonyDateTimePicker(fieldClass, getWhat, DateTimeOptions);
					});
				}

				if (typeof nestedToId !== 'undefined') {

					$.fn.AnonyObserve(nestedToId + '-add', function(){

						$.fn.AnonyDateTimePicker(fieldClass, getWhat, DateTimeOptions);
					});
				}
			});
		</script>
		<?php
	}
}
?>
