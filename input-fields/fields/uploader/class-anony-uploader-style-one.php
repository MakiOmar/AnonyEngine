<?php
/**
 * AnonyEngine style one uploader
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
 * AnonyEngine style one uploader input field class
 *
 * @package AnonyEngine
 * @author Makiomar
 * @link http://makiomar.com
 */
class ANONY_Uploader_Style_One {
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
		$image_exts   = array( 'jpg', 'jpeg', 'jpe', 'gif', 'png', 'svg', 'webp' );
		$img_ext_preg = '/\.(' . join( '|', $image_exts ) . ')$/i';
		if ( is_numeric( $this->uploader->parent_obj->value ) ) {
			$src = wp_get_attachment_url( $this->uploader->parent_obj->value );
			if ( ! $src ) {
				$src = '';
			}
		} elseif ( filter_var( $this->uploader->parent_obj->value, FILTER_VALIDATE_URL ) !== false ) {
			$src = $this->uploader->parent_obj->value;
		} else {
			return '';
		}
		$is_image = preg_match( $img_ext_preg, $src );
		if ( $is_image ) {
			$style = ' style="background-image:url(' . $src . ')"';
		} else {
			$style = ' style="background-image:url(' . ANOE_URI . 'assets/images/placeholders/file.png)"';
		}
		return '<div class="uploads-wrapper style-one"' . $style . '>';
	}

	/**
	 * Output input for nonprivate users.
	 *
	 * @return string Output input for nonprivate users.
	 */
	public function uploads_preview_nopriv() {
		return self::uploads_preview_priv();
	}

	/**
	 * Output button.
	 *
	 * @return string Output button.
	 */
	public function button() {
		return sprintf(
			'<a href="javascript:void(0);" data-id="%1$s" data-choose="Choose a File" data-update="Select File" class="anony-opts-upload uploader-trigger style-one"><span class="anony-upload-plus">+</span></a>',
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
			'anony-opts-field-upload-one-nopriv-js',
			ANONY_FIELDS_URI . 'uploader/js/one/field_upload_nopriv.js',
			array( 'jquery' ),
			time(),
			true
		);
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

		wp_enqueue_script(
			'anony-opts-field-upload-one-js',
			ANONY_FIELDS_URI . 'uploader/js/one/field_upload.js',
			array( 'jquery' ),
			time(),
			true
		);
	}
}
