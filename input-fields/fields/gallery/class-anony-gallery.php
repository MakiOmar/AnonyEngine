<?php
/**
 * Upload field class
 *
 * @package Anonymous theme
 * @author Makiomar
 * @link http://makiomar.com
 */

/**
 * Upload field render class
 *
 * @package Anonymous theme
 * @author Makiomar
 * @link http://makiomar.com
 */
class ANONY_Gallery {

	/**
	 * Parent object
	 *
	 * @var object
	 */
	private $parent_obj;

	/**
	 * Button text
	 *
	 * @var object
	 */
	private $button_text;

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

		$this->button_text = ! empty( $this->parent_obj->field['button_text'] ) ? $this->parent_obj->field['button_text'] : esc_html__( 'Browse', 'anonyengine' );
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
			'<fieldset class="anony-row anony-row-inline" id="fieldset_%1$s">',
			$this->parent_obj->field['id']
		);
	}

	/**
	 * Output the label.
	 *
	 * @param string $html The output.
	 * @return void
	 */
	protected function label( &$html ) {
		if ( 'option' !== $this->parent_obj->context && isset( $this->parent_obj->field['title'] ) ) {
			$html .= sprintf(
				'<label class="anony-label" for="%1$s">%2$s</label>',
				$this->parent_obj->field['id'],
				$this->parent_obj->field['title']
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
			'<input type="hidden" name="%1$s" value="" class="%2$s" />',
			$this->parent_obj->input_name,
			$this->parent_obj->class_attr
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
			'<input type="file" name="%1$s[]" class="%2$s anony_gallery" style="display:none" multiple="multiple"/>',
			$this->parent_obj->input_name,
			$this->parent_obj->class_attr
		);
	}

	/**
	 * Output preview for private users.
	 *
	 * @param string $html The output.
	 * @return void
	 */
	protected function uploads_preview_priv( &$html ) {
		$ids   = array();
		$html .= '<div class="anony-gallery-thumbs-wrap" id="anony-gallery-thumbs-' . $this->parent_obj->field['id'] . '">';
		$html .= '<div class="anony-gallery-thumbs">';
		if ( is_array( $this->parent_obj->value ) && ! empty( $this->parent_obj->value ) ) {
			$ids = $this->parent_obj->value;
		} elseif ( is_string( $this->parent_obj->value ) ) {
			$ids = explode( ',', $this->parent_obj->value );
		}
		foreach ( $ids as $attachment_id ) {

			$html .= '<div class="gallery-item-container" style="display:inline-flex; flex-direction:column; align-items: center;margin-left:15px;"><a href="#" style="display:block; width:50px; height:50px;background-color: #d2d2d2;border-radius: 3px;padding:5px"><img src="' . wp_get_attachment_image_url( intval( $attachment_id ), 'full' ) . '" alt="" style="width:100%;height:100%;display:block;"/></a><input class="gallery-item" type="hidden" name="' . $this->parent_obj->input_name . '[]" id="anony-gallery-thumb-' . $attachment_id . '" value="' . $attachment_id . '" /><a href="#" class="anony_remove_gallery_image" style="display:block" rel-id="' . $attachment_id . '">Remove</a></div>';
		}
		$html .= '</div>';
	}

	/**
	 * Output input for nonprivate users.
	 *
	 * @param string $html The output.
	 * @return void
	 */
	protected function uploads_preview_nopriv( &$html ) {
		$ids   = array();
		$html .= '<div class="anony-gallery-thumbs-wrap" id="anony-gallery-thumbs-' . $this->parent_obj->field['id'] . '">';
		$html .= '<div class="anony-gallery-thumbs">';
		if ( is_array( $this->parent_obj->value ) && ! empty( $this->parent_obj->value ) ) {
			$ids = $this->parent_obj->value;
		} elseif ( is_string( $this->parent_obj->value ) ) {
			$ids = explode( ',', $this->parent_obj->value );
		}
		foreach ( $ids as $attachment_id ) {

			$html .= '<div class="gallery-item-container" style="display:inline-flex; flex-direction:column; align-items: center;margin-left:15px;"><a href="#" style="display:block; width:50px; height:50px;background-color: #d2d2d2;border-radius: 3px;padding:5px"><img src="' . wp_get_attachment_image_url( intval( $attachment_id ), 'full' ) . '" alt="" style="width:100%;height:100%;display:block;"/></a><input class="gallery-item" type="hidden" name="' . $this->parent_obj->input_name . '[]" id="anony-gallery-thumb-' . $attachment_id . '" value="' . $attachment_id . '" /><a href="#" class="anony_remove_gallery_image" style="display:block" rel-id="' . $attachment_id . '">Remove</a></div>';
		}
		$html .= '</div>';
	}

	/**
	 * Output button.
	 *
	 * @param string $html The output.
	 * @return void
	 */
	protected function button( &$html ) {
		$style = 'display:none;';
		$html .= '<div class="anony-gallery-buttons">';
		$html .= sprintf(
			'<a href="javascript:void(0);" data-choose="Choose a File" data-update="Select File" class="anony-opts-gallery button button-primary button-large"><span></span>%1$s</a>',
			$this->button_text
		);

		$html .= sprintf(
			' <a href="javascript:void(0);" class="anony-opts-clear-gallery button button-primary button-large" style="' . $style . '"><span></span>%1$s</a>',
			esc_html__( 'Remove all', 'anonyengine' )
		);
		$html .= '</div>';
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
	 * Upload field render Function.
	 *
	 * @return string Field output.
	 */
	public function render() {
		if ( current_user_can( 'upload_files' ) ) {
			return $this->render_priv();
		} else {
			return $this->render_nopriv();
		}
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
	 */
	public function enqueue() {
		if ( current_user_can( 'upload_files' ) ) {
			$this->user_can_upload_files_scripts();
			$handle = 'anony-opts-field-gallery-js';
		} else {
			$this->user_can_not_upload_files_scripts();
			$handle = 'anony-opts-field-gallery-nopriv-js';
		}
		global $localized_gallery;
		if ( ! isset( $localized_gallery ) ) {
			wp_localize_script(
				$handle,
				'anony_gallery',
				array(
					'blank_icon_url' => ANONY_FIELDS_URI . 'gallery/blank.png',
					'file_icon_url'  => ANOE_URI . 'assets/images/placeholders/file.png',
				)
			);

			$localized_gallery = true;
		}
	}

	/**
	 * Scripts for private input.
	 *
	 * @return void
	 */
	protected function user_can_upload_files_scripts() {
		$wp_version = floatval( get_bloginfo( 'version' ) );
		if ( $wp_version < '3.5' ) {
			wp_enqueue_script(
				'anony-opts-field-gallery-js',
				ANONY_FIELDS_URI . 'gallery/field_upload_3_4.js',
				array( 'jquery', 'thickbox', 'media-upload' ),
				time(),
				true
			);
			wp_enqueue_style( 'thickbox' );
		} else {
			wp_enqueue_script(
				'anony-opts-field-gallery-js',
				ANONY_FIELDS_URI . 'gallery/field_upload.js',
				array( 'jquery' ),
				time(),
				true
			);
			wp_enqueue_media();
		}
	}

	/**
	 * Scripts for nonprivate input.
	 *
	 * @return void
	 */
	protected function user_can_not_upload_files_scripts() {
		wp_enqueue_script(
			'anony-opts-field-gallery-nopriv-js',
			ANONY_FIELDS_URI . 'gallery/field_upload_nopriv.js',
			array( 'jquery' ),
			time(),
			true
		);
	}
}
