<?php
/**
 * AnonyEngine uploader input field
 *
 * PHP version 7.3 Or Later
 *
 * @package  AnonyEngine
 * @author   Makiomar <info@makior.com>
 * @license  https://makiomar.com AnonyEngine Licence
 * @link     https://makiomar.com/anonyengine_elements
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
	 * Field Constructor.
	 *
	 * Required - must call the parent constructor, then assign field and value to vars,
	 * and obviously call the render field function.
	 *
	 * @param object $parent Field parent object.
	 */
	public function __construct( $parent = null ) {

		if ( ! is_object( $parent ) ) {
			return;
		}

		$this->parent        = $parent;
		$this->parent->value = esc_url( $this->parent->value );
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

		$html = '';

		if ( isset( $this->parent->field['note'] ) ) {
			$html .= '<p class=anony-warning>' . $this->parent->field['note'] . '<p>';
		}

		$html .= sprintf(
			'<fieldset class="anony-row anony-row-inline" id="fieldset_%1$s">',
			$this->parent->field['id']
		);
		if ( ( 'meta' === $this->parent->context || 'form' === $this->parent->context ) && isset( $this->parent->field['title'] ) ) {
			$html .= sprintf(
				'<label class="anony-label" for="%1$s">%2$s</label>',
				esc_attr( $this->parent->field['id'] ),
				esc_html( $this->parent->field['title'] )
			);
		}

		$html        .= sprintf(
			'<input id="%4$s" type="hidden" name="%1$s" value="%2$s" class="%3$s" />',
			$this->parent->input_name,
			$this->parent->value,
			$this->parent->class_attr,
			esc_attr( $this->parent->field['id'] )
		);
		$html        .= '<div class="uploads-wrapper">';
		$image_exts   = array( 'jpg', 'jpeg', 'jpe', 'gif', 'png', 'svg' );
		$img_ext_preg = '!\.(' . join( '|', $image_exts ) . ')$!i';

		if ( ! empty( $this->parent->value ) && wp_http_validate_url( $this->parent->value ) ) {
			if ( preg_match( $img_ext_preg, $this->parent->value ) ) {
				$html .= '<img class="anony-opts-screenshot" style="max-width:80px;" src="' . $this->parent->value . '" />';
			} else {
				$file_basename = wp_basename( $this->parent->value );
				$html         .= '<a href="' . $this->parent->value . '">';
				$html         .= '<img class="anony-opts-screenshot" style="max-width:80px;" src="' . ANOE_URI . 'assets/images/placeholders/file.png"/><br>';
				$html         .= '<span class="uploaded-file-name">' . $file_basename . '</span>';
				$html         .= '</a>';
			}
		}else{
			$html .= '<img class="anony-opts-screenshot" style="max-width:80px;" src="'.ANOE_URI . 'assets/images/placeholders/browse.png"/>';
			$html .= '<span class="uploaded-file-name"></span>';
		}

		if ( '' === $this->parent->value ) {
			$remove = ' style="display:none;"';
			$upload = '';
		} else {
			$remove = '';
			$upload = ' style="display:none;"';
		}

		$html .= sprintf(
			' <a href="javascript:void(0);" data-id="%3$s" data-choose="Choose a File" data-update="Select File" class="anony-opts-upload"%1$s><span></span>%2$s</a>',
			$upload,
			esc_html__( 'Browse', 'anonyengine' ),
			esc_attr($this->parent->field['id'])
		);

		$html .= sprintf(
			'<br><a href="javascript:void(0);" data-id="%3$s" class="anony-opts-upload-remove"%1$s>%2$s</a>',
			$remove,
			esc_html__( 'Remove Upload', 'anonyengine' ),
			esc_attr($this->parent->field['id'])
		);
		$html .= '<div>';
		$html .= ( isset( $this->parent->field['desc'] ) && ! empty( $this->parent->field['desc'] ) ) ? '<div class="description">' . $this->parent->field['desc'] . '</div>' : '';
		$html .= '</fieldset>';

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
		wp_localize_script( 'anony-opts-field-upload-js', 'anony_upload', array( 'url' => ANOE_URI . 'assets/images/placeholders/file.png' ) );
	}
}
