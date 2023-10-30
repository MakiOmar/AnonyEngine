<?php
/**
 * Telephone render.
 *
 * PHP version 7.3 Or Later
 *
 * @package  AnonyEngine elements
 * @author   Makiomar <info@makiomar.com>
 * @license  https://makiomar.com AnonyEngine Licence
 * @link     https://makiomar.com/anonyengine
 */

defined( 'ABSPATH' ) || die(); // Exit if accessed direct.

if ( ! class_exists( 'ANONY_Tel' ) ) {

	/**
	 * Telephone render class.
	 *
	 * @package    AnonyEngine fields
	 * @author     Makiomar <info@makiomar.com>
	 * @license    https://makiomar.com AnonyEngine Licence
	 * @link       https://makiomar.com
	 */
	class ANONY_Tel {

		/**
		 * @var object
		 */
		private $parent_obj;

		/**
		 * @var string
		 */
		private $with_dial_codes;

		/**
		 * @var string
		 */
		private $pattern;

		/**
		 * Tel field Constructor.
		 *
		 * @param object $parent_obj Field parent object
		 */
		public function __construct( $parent_obj = null ) {

			if ( ! is_object( $parent_obj ) ) {
				return;
			}

			$this->parent_obj = $parent_obj;

			$this->parent_obj->value = esc_attr( $this->parent_obj->value );

			$this->parent_obj->placeholder = ( isset( $this->parent_obj->field['placeholder'] ) ) ? ' placeholder="' . $this->parent_obj->field['placeholder'] . '"' : '';

			if ( isset( $this->parent_obj->field['desc'] ) && ! empty( $this->parent_obj->field['desc'] ) ) {

				$this->parent_obj->description = $this->parent_obj->field['desc'];
			}

			$this->with_dial_codes = 'no';

			if ( isset( $this->parent_obj->field['with-dial-codes'] ) ) {
				$this->with_dial_codes = $this->parent_obj->field['with-dial-codes'];
			}
			if ( 'yes' === $this->with_dial_codes ) {
				$this->dial_init();
			}

			if ( isset( $this->parent_obj->field['pattern'] ) ) {
				$this->pattern = $this->parent_obj->field['pattern'];
			}

			$this->enqueue();
		}

		/**
		 * Text field render Function.
		 *
		 * @return void
		 */
		public function render() {

			if ( $this->parent_obj->as_template ) {

				return $this->as_template_field();

			}

			$html = sprintf(
				'<fieldset class="anony-row anony-row-inline%2$s" id="fieldset_%1$s">',
				$this->parent_obj->field['id'],
				$this->parent_obj->width
			);

			if ( isset( $this->parent_obj->field['note'] ) ) {
				$html .= '<p class=anony-warning>' . $this->parent_obj->field['note'] . '<p>';
			}

			if ( in_array( $this->parent_obj->context, array( 'meta', 'form' ) ) && isset( $this->parent_obj->field['title'] ) ) {
				$html .= sprintf(
					'<label class="anony-label" for="%1$s">%2$s</label>',
					$this->parent_obj->field['id'],
					$this->parent_obj->field['title']
				);
			}

			$html .= sprintf(
				'<input id="%1$s" type="%2$s" name="%3$s" value="%4$s" class="%5$s"%6$s%7$s/>',
				$this->parent_obj->field['id'],
				$this->parent_obj->field['type'],
				$this->parent_obj->input_name,
				$this->parent_obj->value,
				$this->parent_obj->class_attr,
				$this->parent_obj->placeholder,
				isset( $this->pattern ) ? 'pattern ="' . $this->pattern . '"' : ''
			);

			if ( isset( $this->parent_obj->description ) ) {
				$html .= ' <div class="description ' . $this->parent_obj->class_attr . '">' . $this->parent_obj->description . '</div>';
			}

			$html .= '</fieldset>';

			return $html;
		}

		/**
		 * Render as template.
		 *
		 * @return string Field's output.
		 */
		public function as_template_field() {

			$html  = sprintf(
				'<fieldset class="anony-row anony-row-inline%2$s">',
				$this->parent_obj->field['id'],
				$this->parent_obj->width
			);
			$html .= sprintf(
				'<input  type="%1$s" name="%2$s" class="%3$s anony-row" %4$s/>',
				$this->parent_obj->field['type'],
				$this->parent_obj->input_name,
				$this->parent_obj->class_attr,
				$this->parent_obj->placeholder
			);

			$html .= '</fieldset>';

			return $html;
		}
		/**
		 * Text field render Function.
		 *
		 * Suitable if editing/submitting is enabled
		 *
		 * @return string Field's output.
		 */
		public function renderDisplay() {

			$html = sprintf(
				'<div class="anony-row anony-row-inline" id="fieldset_%1$s">',
				$this->parent_obj->field['id']
			);

			if ( $this->parent_obj->context == 'meta' && isset( $this->parent_obj->field['title'] ) ) {
				$html .= sprintf(
					'<label class="anony-label" for="%1$s">%2$s</label>',
					$this->parent_obj->field['id'],
					$this->parent_obj->field['title']
				);
			}

			$html .= sprintf(
				'<span id="%1$s" class="%2$s">%3$s</span>',
				$this->parent_obj->field['id'],
				$this->parent_obj->class_attr,
				$this->parent_obj->value
			);

			$html .= '</div>';

			return $html;
		}

		/**
		 * Enqueue scripts.
		 *
		 * @return void
		 */
		public function enqueue() {
			if ( 'yes' === $this->with_dial_codes ) {
				wp_enqueue_style( 'intlTelInput', ANONY_FIELDS_URI . 'tel/css/intlTelInput.css' );
				// wp_enqueue_script('intlTelInput', ANONY_FIELDS_URI.'tel/js/intlTelInput-jquery.min.js', array('jquery' ), time(), true);
				wp_enqueue_script( 'intlTelInput', ANONY_FIELDS_URI . 'tel/js/intlTelInput.min.js', array(), time(), true );
			}
		}


		/**
		 * Initialize dial code.
		 *
		 * @return void
		 */
		public function dial_init() {

			$hook = is_admin() ? 'admin_print_footer_scripts' : 'wp_print_footer_scripts';

			add_action(
				$hook,
				function (){ ?>

				<script type="text/javascript">
					/*jQuery(document).ready(function(e) {
						e("#<?php echo $this->parent_obj->field['id']; ?>").intlTelInput();
					});*/
					// Vanilla Javascript.
					var input = document.querySelector("#<?php echo $this->parent_obj->field['id']; ?>");
					window.intlTelInput(input,({
						// specify the path to the libphonenumber script to enable validation/formatting.
						utilsScript: '<?php echo ANONY_FIELDS_URI; ?>tel/js/utils.js',
						autoHideDialCode: false,
						nationalMode: false
					}));
				</script>

					<?php
				}
			);
		}
	}
}
