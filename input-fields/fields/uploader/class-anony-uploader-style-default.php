<?php
/**
 * AnonyEngine style defaultdefault uploader
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
 * AnonyEngine style default uploader input field class
 *
 * @package AnonyEngine
 * @author Makiomar
 * @link http://makiomar.com
 */
class ANONY_Uploader_Style_Default {

	/**
	 * Uploader object
	 *
	 * @var object
	 */

	protected $uploader;

	/**
	 * Field Constructor.
	 *
	 * @param object $uploader Uploader object.
	 */
	public function __construct( $uploader ) {
		$this->uploader = $uploader;
	}

	/**
	 * Output preview for private users.
	 *
	 * @return string Preview output.
	 */
	public function uploads_preview_priv() {
		$html         = '<div class="uploads-wrapper">';
		$image_exts   = array( 'jpg', 'jpeg', 'jpe', 'gif', 'png', 'svg', 'webp' );
		$img_ext_preg = '/\.(' . join( '|', $image_exts ) . ')$/i';
		$src          = wp_get_attachment_url( $this->uploader->parent_obj->value );
		if ( ! empty( $this->uploader->parent_obj->value ) && wp_http_validate_url( $src ) ) {
			if ( preg_match( $img_ext_preg, $this->uploader->parent_obj->value ) ) {
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
		return $html;
	}

	/**
	 * Output input for nonprivate users.
	 *
	 * @return string Output input for nonprivate users.
	 */
	public function uploads_preview_nopriv() {
		$image_exts   = array( 'jpg', 'jpeg', 'jpe', 'gif', 'png', 'svg', 'webp' );
		$img_ext_preg = '/\.(' . join( '|', $image_exts ) . ')$/i';
		$src          = wp_get_attachment_url( $this->uploader->parent_obj->value );
		$src_exists   = ! empty( $this->uploader->parent_obj->value ) && wp_http_validate_url( $src );
		$is_image     = preg_match( $img_ext_preg, $src );
		$html         = '<div class="uploads-wrapper">';
		if ( $src_exists ) {
			if ( $is_image ) {
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

		return $html;
	}

	/**
	 * Output button.
	 *
	 * @return string Output button.
	 */
	public function button() {
		if ( '' === $this->uploader->parent_obj->value ) {
			$remove = ' style="display:none;"';
			$upload = '';
		} else {
			$remove = '';
			$upload = ' style="display:none;"';
		}

		return $this->buttons( $remove, $upload );
	}

	/**
	 * Output button as template.
	 *
	 * @return string Output button.
	 */
	public function button_as_template() {

		$remove = ' style="display:none;"';
		$upload = '';

		return $this->buttons( $remove, $upload );
	}

	/**
	 * Render upload buttons
	 *
	 * @param string $remove Style attribute for remove button.
	 * @param string $upload Style attribute for upload button.
	 * @return string
	 */
	protected function buttons( $remove, $upload ) {
		$html = sprintf(
			' <a href="javascript:void(0);" data-id="%3$s" data-choose="Choose a File" data-update="Select File" class="anony-opts-upload uploader-trigger"%1$s><span></span>%2$s</a>',
			$upload,
			esc_html__( 'Browse', 'anonyengine' ),
			esc_attr( $this->uploader->parent_obj->id_attr_value )
		);

		$html .= sprintf(
			'<br><a href="javascript:void(0);" data-id="%3$s" class="anony-opts-upload-remove"%1$s>%2$s</a>',
			$remove,
			esc_html__( 'Remove Upload', 'anonyengine' ),
			esc_attr( $this->uploader->parent_obj->id_attr_value )
		);

		return $html;
	}

	/**
	 * Scripts for private input.
	 *
	 * @return void
	 */
	public function user_can_upload_files_scripts() {
		$wp_version = floatval( get_bloginfo( 'version' ) );
		if ( $wp_version < '3.5' ) {
			wp_enqueue_script(
				'anony-opts-field-upload-js',
				ANONY_FIELDS_URI . 'uploader/js/default/field_upload_3_4.js',
				array( 'jquery', 'thickbox', 'media-upload' ),
				time(),
				true
			);
			wp_enqueue_style( 'thickbox' );
		} else {
			wp_enqueue_script(
				'anony-opts-field-upload-js',
				ANONY_FIELDS_URI . 'uploader/js/default/field_upload.js',
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
	public function user_can_not_upload_files_scripts() {
		wp_enqueue_script(
			'anony-opts-field-upload-nopriv-js',
			ANONY_FIELDS_URI . 'uploader/js/default/field_upload_nopriv.js',
			array( 'jquery' ),
			time(),
			true
		);
	}
}
