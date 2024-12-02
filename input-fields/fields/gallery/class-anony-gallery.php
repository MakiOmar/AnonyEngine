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
		$this->parent_obj  = $parent_obj;
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
			$this->parent_obj->id_attr_value
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
				$this->parent_obj->id_attr_value,
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
			'<input type="text" style="display:none" id="%1$s" name="%2$s" value="" class="%3$s" />',
			$this->parent_obj->id_attr_value,
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
	 * Output preview.
	 *
	 * @param string $html The output.
	 * @return void
	 */
	protected function uploads_preview( &$html ) {
		$ids       = array();
		$unique_id = $this->parent_obj->object_id ? $this->parent_obj->object_id : time() . wp_rand( 1000, 9999 );
		$html     .= '<div class="anony-gallery-thumbs-wrap" id="anony-gallery-thumbs-' . $this->parent_obj->id_attr_value . '-' . $unique_id . '">';
		$html     .= '<div class="anony-gallery-thumbs">';
		if ( ! empty( $this->parent_obj->value ) ) {
			if ( is_array( $this->parent_obj->value ) ) {
				$ids = $this->parent_obj->value;
			} elseif ( is_string( $this->parent_obj->value ) ) {
				$ids = explode( ',', $this->parent_obj->value );
			}
			foreach ( $ids as $attachment_id ) {
				$src   = wp_get_attachment_image_url( intval( $attachment_id ), 'full' );
				$html .= '<div class="gallery-item-container">';
				$html .= '<a class="anony-gallery-item" href="' . $src . '">';
				$html .= sprintf( '<img src="%s"/>', $src );
				$html .= '</a>';
				$html .= sprintf(
					'<input class="gallery-item" type="hidden" name="%1$s[]" id="anony-gallery-thumb-%2$s-%3$s" value="%2$s" />',
					$this->parent_obj->input_name,
					$attachment_id,
					$unique_id . '-' . $attachment_id
				);
				$html .= '<a href="#" class="anony_remove_gallery_image" style="display:block" data-field-id="' . $this->parent_obj->input_name . '" rel-id="' . $attachment_id . '">Remove</a>';
				$html .= '</div>';
			}
		}
		$html .= '</div>';
	}

	/**
	 * Output preview for private users.
	 *
	 * @param string $html The output.
	 * @return void
	 */
	protected function uploads_preview_priv( &$html ) {
		$this->uploads_preview( $html );
	}

	/**
	 * Output input for nonprivate users.
	 *
	 * @param string $html The output.
	 * @return void
	 */
	protected function uploads_preview_nopriv( &$html ) {
		$this->uploads_preview( $html );
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
			'<a href="javascript:void(0);" data-id="' . $this->parent_obj->id_attr_value . '" data-name="' . $this->parent_obj->input_name . '" data-choose="Choose a File" data-update="Select File" class="anony-opts-gallery button button-primary button-large"><span></span>%1$s</a>',
			$this->button_text
		);

		$html .= sprintf(
			' <a href="javascript:void(0);" data-id="' . $this->parent_obj->id_attr_value . '" data-name="' . $this->parent_obj->input_name . '" class="anony-opts-clear-gallery button button-primary button-large" style="' . $style . '"><span></span>%1$s</a>',
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
