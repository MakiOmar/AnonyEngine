<?php
/**
 * AnonyEngine uploader input field
 *
 * PHP version 7.3 Or Later
 *
 * @package  AnonyEngine
 * @author   Makiomar <info@makiomar.com>
 * @license  https://makiomar.com AnonyEngine Licence
 * @link     https://makiomar.com/anonyengine
 */

defined( 'ABSPATH' ) || die(); // Exit if accessed direct.

/**
 * AnonyEngine uploader input field class
 *
 * @package AnonyEngine
 * @author Makiomar
 * @link http://makiomar.com
 */
class ANONY_Uploader {

	/**
	 * Parent object.
	 *
	 * @var object
	 */
	public $parent_obj;

	/**
	 * Output style.
	 *
	 * @var string
	 */
	private $style = 'default';

	/**
	 * Output style class.
	 *
	 * @var string
	 */
	private $style_class;

	/**
	 * Field Constructor.
	 *
	 * Required - must call the parent constructor, then assign field and value to vars,
	 * and obviously call the render field function.
	 *
	 * @param object      $parent_obj Field parent object.
	 * @param bool|string $field Field arguments.
	 */
	public function __construct( $parent_obj = null, $field = false ) {
		if ( $field ) {
			$this->style = ! empty( $field['style'] ) ? $field['style'] : 'default';
		} else {
			$this->style = ! empty( $parent_obj->field['style'] ) ? $parent_obj->field['style'] : 'default';
		}
		if ( ! is_object( $parent_obj ) ) {
			return;
		}

		$this->parent_obj = $parent_obj;

		$this->style_class = 'ANONY_Uploader_Style_' . ucfirst( $this->style );

		require_once ANONY_FIELDS_DIR . 'uploader/class-anony-uploader-style-' . $this->style . '.php';
	}


	/**
	 * Upload field render Function.
	 *
	 * @access public
	 *
	 * @return string HTML output
	 */
	public function render() {

		if ( $this->parent_obj->as_template ) {
			return $this->render_as_template();
		} elseif ( current_user_can( 'upload_files' ) ) {
			return $this->render_priv();
		} else {
			return $this->render_nopriv();
		}
	}

	/**
	 * Output field note.
	 *
	 * @param string $html The output.
	 * @return void
	 */
	protected function note( &$html ) {
		if ( isset( $this->parent_obj->field['note'] ) ) {
			$html .= '<p class=anony-warning>' . esc_html( $this->parent_obj->field['note'] ) . '<p>';
		}
	}

	/**
	 * Output fieldset open tag.
	 *
	 * @param string $html The output.
	 * @return void
	 */
	protected function fieldset_open( &$html ) {
		$html .= sprintf(
			'<fieldset id="fieldset_%1$s" class="anony-row %2$s">',
			$this->parent_obj->id_attr_value,
			$this->parent_obj->width
		);
	}

	/**
	 * Output the label.
	 *
	 * @param string $html The output.
	 * @return void
	 */
	protected function label( &$html ) {
		if ( ( 'meta' === $this->parent_obj->context || 'form' === $this->parent_obj->context ) && isset( $this->parent_obj->field['title'] ) ) {
			$html .= sprintf(
				'<label class="anony-label" for="%1$s">%2$s</label>',
				esc_attr( $this->parent_obj->id_attr_value ),
				esc_html( $this->parent_obj->field['title'] )
			);
		}
	}

	/**
	 * Output input for private users.
	 *
	 * @param string $html The output.
	 * @return void
	 */
	protected function input_priv( &$html ) {
		$html .= sprintf(
			'<input id="%4$s" type="hidden" name="%1$s" value="%2$s" class="%3$s" />',
			$this->parent_obj->input_name,
			$this->parent_obj->value,
			$this->parent_obj->class_attr,
			esc_attr( $this->parent_obj->id_attr_value )
		);
	}

	/**
	 * Output input for private users.
	 *
	 * @param string $html The output.
	 * @return void
	 */
	protected function input_as_template( &$html ) {
		$html .= sprintf(
			'<input id="%4$s" type="hidden" name="%1$s" value="%2$s" class="%3$s" />',
			$this->parent_obj->input_name,
			'',
			$this->parent_obj->class_attr,
			esc_attr( $this->parent_obj->id_attr_value )
		);
	}

	/**
	 * Output input for nonprivate users.
	 *
	 * @param string $html The output.
	 * @return void
	 */
	protected function input_nopriv( &$html ) {
		$html .= sprintf(
			'<input type="file" id="%1$s" class="anony-uploader" name="%2$s" style="display:none"/>',
			esc_attr( $this->parent_obj->id_attr_value ),
			$this->parent_obj->input_name,
		);
	}
	/**
	 * Load style method
	 *
	 * @param string $html Html output.
	 * @param string $method Corresponding method.
	 * @return void
	 */
	protected function load_style_html_method( &$html, $method ) {
		if ( class_exists( $this->style_class ) && method_exists( $this->style_class, $method ) ) {
			$class_name = $this->style_class;
			$style      = new $class_name( $this );
			$html      .= $style->$method();
		}
	}

	/**
	 * Output preview for private users.
	 *
	 * @param string $html The output.
	 * @return void
	 */
	protected function uploads_preview_priv( &$html ) {

		$this->load_style_html_method( $html, 'uploads_preview_priv' );
	}

	/**
	 * Output input for nonprivate users.
	 *
	 * @param string $html The output.
	 * @return void
	 */
	protected function uploads_preview_nopriv( &$html ) {

		$this->load_style_html_method( $html, 'uploads_preview_nopriv' );
	}

	/**
	 * Output button.
	 *
	 * @param string $html The output.
	 * @return void
	 */
	protected function button( &$html ) {

		$this->load_style_html_method( $html, 'button' );
	}

	/**
	 * Output button as template.
	 *
	 * @param string $html The output.
	 * @return void
	 */
	protected function button_as_template( &$html ) {

		$this->load_style_html_method( $html, 'button_as_template' );
	}
	/**
	 * Output close preview markup.
	 *
	 * @param string $html The output.
	 * @return void
	 */
	protected function close_preview( &$html ) {
		$html .= '<div>';
	}

	/**
	 * Output Description.
	 *
	 * @param string $html The output.
	 * @return void
	 */
	protected function description( &$html ) {
		$html .= ( isset( $this->parent_obj->field['desc'] ) && ! empty( $this->parent_obj->field['desc'] ) ) ? '<div class="description">' . $this->parent_obj->field['desc'] . '</div>' : '';
	}

	/**
	 * Output fieldset close.
	 *
	 * @param string $html The output.
	 * @return void
	 */
	protected function close_fieldset( &$html ) {
		$html .= '</fieldset>';
	}

	/**
	 * Final output for nonprivate users.
	 *
	 * @return string The final output.
	 */
	protected function render_nopriv() {
		$html = '';

		$this->note( $html );
		$this->fieldset_open( $html );
		$this->label( $html );
		$this->input_nopriv( $html );
		$this->uploads_preview_nopriv( $html );
		$this->button( $html );
		$this->close_preview( $html );
		$this->description( $html );
		$this->close_fieldset( $html );

		return $html;
	}
	/**
	 * Render as a template
	 *
	 * @return string
	 */
	protected function render_as_template() {
		$html = '';
		$this->note( $html );
		$this->fieldset_open( $html );
		$this->label( $html );
		$this->input_as_template( $html );
		$this->uploads_preview_priv( $html );
		$this->button_as_template( $html );
		$this->close_preview( $html );
		$this->description( $html );
		$this->close_fieldset( $html );
		return $html;
	}

	/**
	 * Final output for private users.
	 *
	 * @return string The final output.
	 */
	protected function render_priv() {

		$html = '';

		$this->note( $html );
		$this->fieldset_open( $html );
		$this->label( $html );
		$this->input_priv( $html );
		$this->uploads_preview_priv( $html );
		$this->button( $html );
		$this->close_preview( $html );
		$this->description( $html );
		$this->close_fieldset( $html );

		return $html;
	}

	/**
	 * Enqueue scripts.
	 *
	 * @access public
	 *
	 * @return void.
	 */
	public function enqueue() {
		if ( current_user_can( 'upload_files' ) ) {
			$this->user_can_upload_files_scripts();
		} else {
			$this->user_can_not_upload_files_scripts();
		}
		global $localized_uploader;
		if ( ! isset( $localized_uploader ) ) {
			wp_localize_script(
				'jquery',
				'anony_upload',
				array(
					'url'        => ANOE_URI . 'assets/images/placeholders/file.png',
					'browse_url' => ANOE_URI . 'assets/images/placeholders/browse.png',
				)
			);

			$localized_uploader = true;
		}
	}

	/**
	 * Load style scripts method
	 *
	 * @param string $method Corresponding method.
	 * @return void
	 */
	protected function load_style_scripts_method( $method ) {
		if ( ! $this->style || '' === $this->style ) {
			$field_style = 'default';
		} else {
			$field_style = $this->style;
		}
		require_once ANONY_FIELDS_DIR . 'uploader/class-anony-uploader-style-' . $field_style . '.php';
		$class_name = 'ANONY_Uploader_Style_' . ucfirst( $field_style );
		if ( class_exists( $class_name ) && method_exists( $class_name, $method ) ) {
			$style = new $class_name( $this );
			$style->$method();
		}
	}

	/**
	 * Scripts for private input.
	 *
	 * @return void
	 */
	public function user_can_upload_files_scripts() {
		$this->load_style_scripts_method( 'user_can_upload_files_scripts' );
	}
	/**
	 * Scripts for nonprivate input.
	 *
	 * @return void
	 */
	public function user_can_not_upload_files_scripts() {
		$this->load_style_scripts_method( 'user_can_not_upload_files_scripts' );
	}
}
