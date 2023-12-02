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
class ANONY_File_Upload {

	/**
	 * Parent object
	 *
	 * @var object
	 */
	private $parent_obj;

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
	}


	/**
	 * Upload field render Function.
	 *
	 * @return string Field output.
	 */
	public function render() {

		$select_text   = __( 'Select your file', 'anonyengine' );
		$no_file_text  = __( 'No selected file', 'anonyengine' );
		$current_text  = __( 'Current file:', 'anonyengine' );
		$download_text = __( 'Download', 'anonyengine' );

		$note       = isset( $this->parent_obj->field['note'] ) ? $this->parent_obj->field['note'] : '';
		$id         = $this->parent_obj->id_attr_value;
		$is_meta    = ( 'meta' === $this->parent_obj->context ) ? true : false;
		$has_title  = ( isset( $this->parent_obj->field['title'] ) ) ? true : false;
		$label      = $has_title ? $this->parent_obj->field['title'] : '';
		$name       = $this->parent_obj->input_name;
		$class_attr = $this->parent_obj->class_attr;
		$value      = $this->parent_obj->value;
		$file_url   = false;
		if ( '' !== $value ) {
			$file_url = wp_get_attachment_url( intval( $value ) ) ? esc_url( wp_get_attachment_url( intval( $value ) ) ) : false;
			$basename = basename( $file_url );
		}

		$desc = ( isset( $this->parent_obj->field['desc'] ) && ! empty( $this->parent_obj->field['desc'] ) ) ? $this->parent_obj->field['desc'] : '';

		ob_start();

		include 'file-upload-view.php';

		$html = ob_get_clean();

		return $html;
	}

	/**
	 * Enqueue scripts.
	 */
	public function enqueue() {
		if ( current_user_can( 'upload_files' ) ) {
			$this->user_can_upload_files_scripts();
		} else {
			$this->user_can_not_upload_files_scripts();
		}
	}

	/**
	 * Scripts for private input.
	 *
	 * @return void
	 */
	protected function user_can_upload_files_scripts() {
		wp_enqueue_media();
		wp_enqueue_script(
			'file_upload',
			ANONY_FIELDS_URI . 'file-upload/file_upload.js',
			array( 'jquery' ),
			filemtime( ANONY_FIELDS_DIR . 'file-upload/file_upload.js' ),
			true
		);
	}
	/**
	 * Scripts for nonprivate input.
	 *
	 * @return void
	 */
	protected function user_can_not_upload_files_scripts() {
	}
}
