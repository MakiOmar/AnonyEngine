<?php
/**
 * Upload field class
 *
 * @package Anonymous theme
 * @author Makiomar
 * @link http://makiomar.com
 */

class ANONY_File_upload {

	/**
	 * @var object
	 */
	private $parent;
	
	/**
	 * Field Constructor.
	 *
	 * Required - must call the parent constructor, then assign field and value to vars, and obviously call the render field function
	 *
	 * @param array  $this->parent->field Array of field's data
	 * @param object $parent Field parent object
	 */
	public function __construct( $parent = null ) {

		if ( ! is_object( $parent ) ) {
			return;
		}

		$this->parent = $parent;

		$this->enqueue();
	}


	/**
	 * Upload field render Function.
	 *
	 * @return void
	 */
	function render( $meta = false ) {

		$select_text   = esc_html__( 'Select your file', 'anonyengine' );
		$no_file_text  = esc_html__( 'No selected file', 'anonyengine' );
		$current_text  = esc_html__( 'Current file:', 'anonyengine' );
		$download_text = esc_html__( 'Download', 'anonyengine' );

		$note       = isset( $this->parent->field['note'] ) ? $this->parent->field['note'] : '';
		$id         = $this->parent->field['id'];
		$is_meta    = ( $this->parent->context == 'meta' ) ? true : false;
		$has_title  = ( isset( $this->parent->field['title'] ) ) ? true : false;
		$title      = $has_title ? $this->parent->field['title'] : '';
		$name       = $this->parent->input_name;
		$class_attr = $this->parent->class_attr;
		$value      = $this->parent->value;
		$file_url   = false;
		if ( $value !== '' ) {
			$file_url = wp_get_attachment_url( intval( $value ) ) ? esc_url( wp_get_attachment_url( intval( $value ) ) ) : false;
			$basename = basename( $file_url );
		}

		$desc = ( isset( $this->parent->field['desc'] ) && ! empty( $this->parent->field['desc'] ) ) ? $this->parent->field['desc'] : '';

		ob_start();

		include 'file-upload.view.php';

		$html = ob_get_clean();

		return $html;

	}

	/**
	 * Enqueue scripts.
	 */
	public function enqueue() {
		if( is_user_logged_in() ){
			$this->logged_in_scripts();
		}else{
			$this->not_logged_in_scripts();
		}
	}

	protected function logged_in_scripts() {
		wp_enqueue_media();
		wp_enqueue_script( 
			'file_upload', 
			ANONY_FIELDS_URI . 'file-upload/file_upload.js', 
			array( 'jquery' ), 
			filemtime( ANONY_FIELDS_DIR . 'file-upload/file_upload.js' ), 
			true 
		);
	}

	protected function not_logged_in_scripts(){
		
	}
}
