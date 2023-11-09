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
		 * Parent object
		 *
		 * @var object
		 */
		private $parent_obj;

		/**
		 * Show/hide dial codes
		 *
		 * @var string
		 */
		private $with_dial_codes = 'no';

		/**
		 * Input pattern
		 *
		 * @var string
		 */
		private $pattern;
		
		/**
		 * Input icon
		 *
		 * @var string
		 */
		private $icon;

		/**
		 * Tel field Constructor.
		 *
		 * @param object      $parent_obj Field parent object.
		 * @param bool|string $field Field arguments.
		 */
		public function __construct( $parent_obj = null, $field = false ) {

			if ( $field && isset( $field['with-dial-codes'] ) ) {

				$this->with_dial_codes = $field['with-dial-codes'];
				$this->dial_init( $field['id'] );

			}

			if ( ! is_object( $parent_obj ) ) {
				return;
			}

			$this->parent_obj = $parent_obj;

			$this->parent_obj->value = esc_attr( $this->parent_obj->value );

			$this->parent_obj->placeholder = ( isset( $this->parent_obj->field['placeholder'] ) ) ? ' placeholder="' . $this->parent_obj->field['placeholder'] . '"' : '';

			if ( isset( $this->parent_obj->field['desc'] ) && ! empty( $this->parent_obj->field['desc'] ) ) {

				$this->parent_obj->description = $this->parent_obj->field['desc'];
			}

			if ( isset( $this->parent_obj->field['with-dial-codes'] ) ) {
				$this->with_dial_codes = $this->parent_obj->field['with-dial-codes'];
			}
			if ( 'yes' === $this->with_dial_codes ) {
				$this->dial_init( $this->parent_obj->field['id'] );
			}

			if ( isset( $this->parent_obj->field['pattern'] ) ) {
				$this->pattern = $this->parent_obj->field['pattern'];
			}

			$this->icon = apply_filters(
				'anony_tel_icon',
				'<svg width="18" height="22" viewBox="0 0 18 22" fill="none" xmlns="http://www.w3.org/2000/svg">
				<path d="M17 6V16C17 20 16 21 12 21H6C2 21 1 20 1 16V6C1 2 2 1 6 1H12C16 1 17 2 17 6Z" stroke="#000000" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
				<path d="M11 4.5H7" stroke="#000000" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
				<path d="M9.0002 18.1C9.85624 18.1 10.5502 17.406 10.5502 16.55C10.5502 15.694 9.85624 15 9.0002 15C8.14415 15 7.4502 15.694 7.4502 16.55C7.4502 17.406 8.14415 18.1 9.0002 18.1Z" stroke="#000000" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
				</svg>'
			);
		}

		/**
		 * Text field render Function.
		 *
		 * @return string Field output.
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
				$html .= '<p class=anony-warning>' . esc_html( $this->parent_obj->field['note'] ) . '<p>';
			}

			if ( in_array( $this->parent_obj->context, array( 'meta', 'form' ), true ) && isset( $this->parent_obj->field['title'] ) ) {
				$html .= sprintf(
					'<label class="anony-label" for="%1$s">%2$s</label>',
					$this->parent_obj->field['id'],
					$this->parent_obj->field['title']
				);
			}

			$html .= sprintf(
				'<div style="position:relative"><input id="%1$s" type="%2$s" name="%3$s" value="%4$s" class="%5$s"%6$s%7$s/>',
				$this->parent_obj->field['id'],
				$this->parent_obj->field['type'],
				$this->parent_obj->input_name,
				$this->parent_obj->value,
				$this->parent_obj->class_attr,
				$this->parent_obj->placeholder,
				isset( $this->pattern ) ? 'pattern ="' . $this->pattern . '"' : ''
			);

			if ( ! empty( $this->icon ) ) {
				$html .= '<span class="anony-field-icon">' . $this->icon . '</span></div>';
			} else {
				$html .= '</div>';
			}

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
				'<div style="position:relative"><input  type="%1$s" name="%2$s" class="%3$s anony-row" %4$s/>',
				$this->parent_obj->field['type'],
				$this->parent_obj->input_name,
				$this->parent_obj->class_attr,
				$this->parent_obj->placeholder
			);

			if ( ! empty( $this->icon ) ) {
				$html .= '<span class="anony-field-icon">' . $this->icon . '</span></div>';
			} else {
				$html .= '</div>';
			}

			$html .= '</fieldset>';

			return $html;
		}
		//phpcs:disable WordPress.NamingConventions.ValidFunctionName.MethodNameInvalid
		/**
		 * Text field render Function.
		 *
		 * Suitable if editing/submitting is enabled
		 *
		 * @return string Field's output.
		 */
		public function renderDisplay() {
			//phpcs:enable.

			$html = sprintf(
				'<div class="anony-row anony-row-inline" id="fieldset_%1$s">',
				$this->parent_obj->field['id']
			);

			if ( 'meta' === $this->parent_obj->context && isset( $this->parent_obj->field['title'] ) ) {
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
				wp_enqueue_style( 'intlTelInput', ANONY_FIELDS_URI . 'tel/css/intlTelInput.css', array(), '1.0' );
				wp_enqueue_script( 'intlTelInput', ANONY_FIELDS_URI . 'tel/js/intlTelInput.js', array(), time(), true );
			}
		}


		/**
		 * Initialize dial code.
		 *
		 * @param string $target_id Target field id.
		 * @return void
		 */
		public function dial_init( $target_id ) {

			$hook = is_admin() ? 'admin_print_footer_scripts' : 'wp_print_footer_scripts';

			add_action(
				$hook,
				function () use ( $target_id ) { ?>

				<script type="text/javascript">
					// Vanilla Javascript.
					var input = document.querySelector("#<?php echo esc_attr( $target_id ); ?>");
					if ( input ) {
						window.intlTelInput(input,({
							// specify the path to the libphonenumber script to enable validation/formatting.
							utilsScript: '<?php echo esc_url( ANONY_FIELDS_URI ); ?>tel/js/utils.js',
							autoHideDialCode: false,
							nationalMode: false
						}));
					}
					
				</script>

					<?php
				}
			);
		}
	}
}
