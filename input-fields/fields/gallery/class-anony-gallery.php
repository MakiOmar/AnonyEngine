<?php
/**
 * Upload field class
 *
 * @package Anonymous theme
 * @author Makiomar
 * @link http://makiomar.com
 */

class ANONY_Gallery {

	/**
	 * @var object
	 */
	private $parent_obj;

	/**
	 * Field Constructor.
	 *
	 * Required - must call the parent constructor, then assign field and value to vars, and obviously call the render field function
	 *
	 * @param array  $this->parent_obj->field Array of field's data
	 * @param object $parent_obj Field parent object
	 */
	public function __construct( $parent_obj = null ) {

		if ( ! is_object( $parent_obj ) ) {
			return;
		}

		$this->parent_obj = $parent_obj;
	}
	protected function note( &$html ) {
		if ( isset( $this->parent_obj->field['note'] ) ) {
			$html .= '<p class=anony-warning>' . $this->parent_obj->field['note'] . '<p>';
		}
	}

	protected function fieldset_open( &$html ) {
		$html .= sprintf(
			'<fieldset class="anony-row anony-row-inline" id="fieldset_%1$s">',
			$this->parent_obj->field['id']
		);
	}

	protected function label( &$html ) {
		if ( $this->parent_obj->context != 'option' && isset( $this->parent_obj->field['title'] ) ) {
			$html .= sprintf(
				'<label class="anony-label" for="%1$s">%2$s</label>',
				$this->parent_obj->field['id'],
				$this->parent_obj->field['title']
			);
		}
	}
	protected function input_priv( &$html ) {
		$html .= sprintf(
			'<input type="hidden" name="%1$s" value="" class="%2$s" />',
			$this->parent_obj->input_name,
			$this->parent_obj->class_attr
		);
	}

	protected function input_nopriv( &$html ) {
		$html .= sprintf(
			'<input type="file" name="%1$s[]" class="%2$s anony_gallery" multiple="multiple"/>',
			$this->parent_obj->input_name,
			$this->parent_obj->class_attr
		);
	}

	protected function uploads_preview_priv( &$html ) {
		$html .= '<div class="anony-gallery-thumbs-wrap" id="anony-gallery-thumbs-' . $this->parent_obj->field['id'] . '">';
		$style = 'display:none;';
		if ( is_array( $this->parent_obj->value ) && ! empty( $this->parent_obj->value ) ) {
			$style = 'display:inline-block;';
			$html .= '<div class="anony-gallery-thumbs">';
			foreach ( $this->parent_obj->value as $attachment_id ) {

				$html .= '<div class="gallery-item-container" style="display:inline-flex; flex-direction:column; align-items: center;margin-left:15px;"><a href="#" style="display:block; width:50px; height:50px;background-color: #d2d2d2;border-radius: 3px;padding:5px"><img src="' . wp_get_attachment_url( intval( $attachment_id ) ) . '" alt="" style="width:100%;height:100%;display:block;"/></a><input class="gallery-item" type="hidden" name="' . $this->parent_obj->input_name . '[]" id="anony-gallery-thumb-' . $attachment_id . '" value="' . $attachment_id . '" /><a href="#" class="anony_remove_gallery_image" style="display:block" rel-id="' . $attachment_id . '">Remove</a></div>';
			}

			$html .= '</div>';
		} else {
			$html .= '<div class="anony-gallery-thumbs"></div>';
		}
	}

	protected function uploads_preview_nopriv( &$html ) {
		$html .= '<div class="anony-gallery-thumbs-wrap" id="anony-gallery-thumbs-' . $this->parent_obj->field['id'] . '">';

		if ( is_array( $this->parent_obj->value ) && ! empty( $this->parent_obj->value ) ) {
			$style = 'display:inline-block;';
			$html .= '<div class="anony-gallery-thumbs">';
			foreach ( $this->parent_obj->value as $attachment_id ) {

				$html .= '<div class="gallery-item-container" style="display:inline-flex; flex-direction:column; align-items: center;margin-left:15px;"><a href="#" style="display:block; width:50px; height:50px;background-color: #d2d2d2;border-radius: 3px;padding:5px"><img src="' . wp_get_attachment_url( intval( $attachment_id ) ) . '" alt="" style="width:100%;height:100%;display:block;"/></a><input class="gallery-item" type="hidden" name="' . $this->parent_obj->input_name . '[]" id="anony-gallery-thumb-' . $attachment_id . '" value="' . $attachment_id . '" /><a href="#" class="anony_remove_gallery_image" style="display:block" rel-id="' . $attachment_id . '">Remove</a></div>';
			}

			$html .= '</div>';
		} else {
			$html .= '<div class="anony-gallery-thumbs"></div>';
		}
	}

	protected function button( &$html ) {
		$style = 'display:none;';
		$html .= sprintf(
			'<a href="javascript:void(0);" data-choose="Choose a File" data-update="Select File" class="anony-opts-gallery button button-primary button-large"><span></span>%1$s</a>',
			esc_html__( 'Browse', 'anonyengine' )
		);

		$html .= sprintf(
			' <a href="javascript:void(0);" class="anony-opts-clear-gallery button button-primary button-large" style="' . $style . '"><span></span>%1$s</a>',
			esc_html__( 'Remove all', 'anonyengine' )
		);
	}

	protected function close_preview( &$html ) {
		$html .= '<div>';
	}

	protected function description( &$html ) {
		$html .= ( isset( $this->parent_obj->field['desc'] ) && ! empty( $this->parent_obj->field['desc'] ) ) ? '<div class="description">' . $this->parent_obj->field['desc'] . '</div>' : '';
	}

	protected function close_fieldset( &$html ) {
		$html .= '</fieldset>';
	}
	/**
	 * Upload field render Function.
	 *
	 * @return void
	 */
	function render( $meta = false ) {
		if ( current_user_can( 'upload_files' ) ) {
			return $this->render_priv();
		} else {
			return $this->render_nopriv();
		}
	}
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
	function enqueue() {
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
