<?php
/**
 * AnonyEngine style two uploader
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
 * AnonyEngine style two uploader input field class
 *
 * @package AnonyEngine
 * @author Makiomar
 * @link http://makiomar.com
 */
class ANONY_Uploader_Style_Two {
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
		return '<div class="uploads-wrapper">';
	}

	/**
	 * Output input for nonprivate users.
	 *
	 * @return string Output input for nonprivate users.
	 */
	public function uploads_preview_nopriv() {
		return '<div class="uploads-wrapper style-two">';
	}

	/**
	 * Output button.
	 *
	 * @return string Output button.
	 */
	public function button() {
		$image_exts   = array( 'jpg', 'jpeg', 'jpe', 'gif', 'png', 'svg', 'webp' );
		$img_ext_preg = '/\.(' . join( '|', $image_exts ) . ')$/i';
		$src          = wp_get_attachment_url( $this->uploader->parent_obj->value );
		$src_exists   = ! empty( $this->uploader->parent_obj->value ) && wp_http_validate_url( $src );
		$is_image     = preg_match( $img_ext_preg, $src );
		if ( $src_exists ) {
			if ( $is_image ) {
				$style = ' style="background-image:url(' . $src . ')"';
			} else {
				$style = ' style="background-image:url(' . ANOE_URI . 'assets/images/placeholders/file.png)"';
			}
		} else {
			$style = '';
		}
		return sprintf(
			'<a href="javascript:void(0);" data-id="%1$s" data-choose="Choose a File" data-update="Select File" class="anony-opts-upload uploader-trigger style-two"><span class="anony-uploaded-preview"' . $style . '><?xml version="1.0" encoding="utf-8"?><!-- Uploaded to: SVG Repo, www.svgrepo.com, Generator: SVG Repo Mixer Tools -->
			<svg fill="#ffffff" width="25px" height="25px" viewBox="0 0 30 30" xmlns="http://www.w3.org/2000/svg"><path d="M15.5 16c-.13.002-.26.055-.353.146l-3.994 3.995c-.464.446.26 1.17.706.707l3.64-3.642 3.64 3.642c.454.472 1.175-.257.707-.706l-3.994-3.994c-.096-.095-.218-.148-.353-.146zm0 3c.277 0 .5.223.5.5v7c0 .277-.223.5-.5.5s-.5-.223-.5-.5v-7c0-.277.223-.5.5-.5zm7 4H25c2.756 0 5-2.244 5-5 0-2.398-1.734-4.373-4.04-4.836.016-.22.04-.494.04-.664C26 7.28 21.74 3 16.5 3c-3.51.005-6.686 1.973-8.33 5.05C7.948 8.03 7.726 8 7.5 8 3.352 8 0 11.364 0 15.5S3.364 23 7.5 23h1c.663 0 .668-1 0-1h-1C3.904 22 1 19.096 1 15.5 1 11.906 3.902 9.002 7.496 9c.285.002.57.023.852.063.214.03.424-.08.52-.276C10.287 5.862 13.247 4.005 16.5 4c4.7 0 8.5 3.8 8.5 8.5-.002.322-.022.643-.06.963-.035.28.167.53.447.558C27.44 14.22 29 15.938 29 18c0 2.215-1.785 4-4 4h-2.5c-.685 0-.638 1 0 1z"/></svg></span></a>',
			esc_attr( $this->uploader->parent_obj->field['id'] )
		);
	}

	/**
	 * Scripts for nonprivate input.
	 *
	 * @return void
	 */
	public function user_can_not_upload_files_scripts() {
		wp_enqueue_script(
			'anony-opts-field-upload-two-nopriv-js',
			ANONY_FIELDS_URI . 'uploader/js/two/field_upload_nopriv.js',
			array( 'jquery' ),
			time(),
			true
		);
	}
}
