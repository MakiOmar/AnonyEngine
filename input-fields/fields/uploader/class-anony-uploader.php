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
	 * @var object
	 */
	private $parent_obj;

	/**
	 * Field Constructor.
	 *
	 * Required - must call the parent constructor, then assign field and value to vars,
	 * and obviously call the render field function.
	 *
	 * @param object $parent_obj Field parent object.
	 */
	public function __construct( $parent_obj = null ) {

		if ( ! is_object( $parent_obj ) ) {
			return;
		}

		$this->parent_obj = $parent_obj;
		$this->enqueue();
	}


	/**
	 * Upload field render Function.
	 *
	 * @access public
	 *
	 * @return string HTML output
	 */
	public function render() {
		if ( current_user_can( 'upload_files' ) ) {
			return $this->render_priv();
		} else {
			return $this->render_nopriv();
		}
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
		if ( ( 'meta' === $this->parent_obj->context || 'form' === $this->parent_obj->context ) && isset( $this->parent_obj->field['title'] ) ) {
			$html .= sprintf(
				'<label class="anony-label" for="%1$s">%2$s</label>',
				esc_attr( $this->parent_obj->field['id'] ),
				esc_html( $this->parent_obj->field['title'] )
			);
		}
	}

	protected function input_priv( &$html ) {
		$html .= sprintf(
			'<input id="%4$s" type="hidden" name="%1$s" value="%2$s" class="%3$s" />',
			$this->parent_obj->input_name,
			$this->parent_obj->value,
			$this->parent_obj->class_attr,
			esc_attr( $this->parent_obj->field['id'] )
		);
	}

	protected function input_nopriv( &$html ) {
		$html .= sprintf(
			'<input type="file" id="%1$s" class="anony-uploader" name="%1$s" style="display:none"/>',
			esc_attr( $this->parent_obj->field['id'] ),
			$this->parent_obj->input_name,
		);
	}

	protected function uploads_preview_priv( &$html ) {
		$html        .= '<div class="uploads-wrapper">';
		$image_exts   = array( 'jpg', 'jpeg', 'jpe', 'gif', 'png', 'svg' );
		$img_ext_preg = '!\.(' . join( '|', $image_exts ) . ')$!i';
		$src          = wp_get_attachment_url( $this->parent_obj->value );
		if ( ! empty( $this->parent_obj->value ) && wp_http_validate_url( $src ) ) {
			if ( preg_match( $img_ext_preg, $this->parent_obj->value ) ) {
				$html .= '<img class="anony-opts-screenshot" style="max-width:80px;" src="' . $src . '" />';
			} else {
				$file_basename = wp_basename( $src );
				$html         .= '<a href="' . $src . '">';
				$html         .= '<img class="anony-opts-screenshot" style="max-width:80px;" src="' . ANOE_URI . 'assets/images/placeholders/file.png"/><br>';
				$html         .= '<span class="uploaded-file-name">' . $file_basename . '</span>';
				$html         .= '</a>';
			}
		} else {
			$html .= '<img class="anony-opts-screenshot" style="max-width:80px;" src="' . ANOE_URI . 'assets/images/placeholders/browse.png"/>';
			$html .= '<span class="uploaded-file-name"></span>';
		}
	}

	protected function uploads_preview_nopriv( &$html ) {
		$html        .= '<div class="uploads-wrapper">';
		$image_exts   = array( 'jpg', 'jpeg', 'jpe', 'gif', 'png', 'svg' );
		$img_ext_preg = '!\.(' . join( '|', $image_exts ) . ')$!i';
		$src          = wp_get_attachment_url( $this->parent_obj->value );
		if ( ! empty( $this->parent_obj->value ) && wp_http_validate_url( $src ) ) {
			if ( preg_match( $img_ext_preg, $this->parent_obj->value ) ) {
				$html .= '<img class="anony-opts-screenshot" style="max-width:80px;" src="' . $src . '" />';
			} else {
				$file_basename = wp_basename( $src );
				$html         .= '<a href="' . $src . '">';
				$html         .= '<img class="anony-opts-screenshot" style="max-width:80px;" src="' . ANOE_URI . 'assets/images/placeholders/file.png"/><br>';
				$html         .= '<span class="uploaded-file-name">' . $file_basename . '</span>';
				$html         .= '</a>';
			}
		} else {
			$html .= '<img class="anony-opts-screenshot" style="max-width:80px;" src="' . ANOE_URI . 'assets/images/placeholders/browse.png"/>';
			$html .= '<span class="uploaded-file-name"></span>';
		}
	}

	protected function button( &$html ) {
		if ( '' === $this->parent_obj->value ) {
			$remove = ' style="display:none;"';
			$upload = '';
		} else {
			$remove = '';
			$upload = ' style="display:none;"';
		}

		$html .= sprintf(
			' <a href="javascript:void(0);" data-id="%3$s" data-choose="Choose a File" data-update="Select File" class="anony-opts-upload uploader-trigger"%1$s><span></span>%2$s</a>',
			$upload,
			esc_html__( 'Browse', 'anonyengine' ),
			esc_attr( $this->parent_obj->field['id'] )
		);

		$html .= sprintf(
			'<br><a href="javascript:void(0);" data-id="%3$s" class="anony-opts-upload-remove"%1$s>%2$s</a>',
			$remove,
			esc_html__( 'Remove Upload', 'anonyengine' ),
			esc_attr( $this->parent_obj->field['id'] )
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
	 *
	 * @access public
	 *
	 * @return void.
	 */
	public function enqueue() {
		if ( current_user_can( 'upload_files' ) ) {
			$this->user_can_upload_files_scripts();
			$handle = 'anony-opts-field-upload-js';
		} else {
			$this->user_can_not_upload_files_scripts();
			$handle = 'anony-opts-field-upload-nopriv-js';
		}
		global $localized_uploader;
		if ( ! isset( $localized_uploader ) ) {
			wp_localize_script(
				$handle,
				'anony_upload',
				array(
					'url'        => ANOE_URI . 'assets/images/placeholders/file.png',
					'browse_url' => ANOE_URI . 'assets/images/placeholders/browse.png',
				)
			);

			$localized_uploader = true;
		}
	}
	protected function user_can_upload_files_scripts() {
		$wp_version = floatval( get_bloginfo( 'version' ) );
		if ( $wp_version < '3.5' ) {
			wp_enqueue_script(
				'anony-opts-field-upload-js',
				ANONY_FIELDS_URI . 'uploader/field_upload_3_4.js',
				array( 'jquery', 'thickbox', 'media-upload' ),
				time(),
				true
			);
			wp_enqueue_style( 'thickbox' );
		} else {
			wp_enqueue_script(
				'anony-opts-field-upload-js',
				ANONY_FIELDS_URI . 'uploader/field_upload.js',
				array( 'jquery' ),
				time(),
				true
			);
			wp_enqueue_media();
		}
	}

	protected function user_can_not_upload_files_scripts() {
		wp_enqueue_script(
			'anony-opts-field-upload-nopriv-js',
			ANONY_FIELDS_URI . 'uploader/field_upload_nopriv.js',
			array( 'jquery' ),
			time(),
			true
		);
	}
}
