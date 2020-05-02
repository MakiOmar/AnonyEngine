<?php
/**
 * Upload field class
 *
 * @package Anonymous theme
 * @author Makiomar
 * @link http://makiomar.com
 */

class ANONY_File_upload{

	/**
	 * Field Constructor.
	 *
	 * Required - must call the parent constructor, then assign field and value to vars, and obviously call the render field function
	 *
	 * @param array $this->parent->field Array of field's data
	 * @param object $parent Field parent object
	 */
	public function __construct( $parent = NULL ){

		if (!is_object($parent)) return;

		$this->parent = $parent;
		
		$this->enqueue();
	}

	
	/**
	 * Upload field render Function.
	 *
	 * @return void
	 */
	function render( $meta = false ){
		
		$select_text   = esc_html__('Select your file',ANOE_TEXTDOM);
		$no_file_text  = esc_html__('No selected file',ANOE_TEXTDOM);
		$current_text  = esc_html__('Current file:',ANOE_TEXTDOM);
		$download_text = esc_html__('Download',ANOE_TEXTDOM);
		
		
		$note = isset($this->parent->field['note']) ? $this->parent->field['note'] : '';
		$id = $this->parent->field['id'];
		$is_meta = ($this->parent->context == 'meta') ? true : false;
		$has_title = (isset($this->parent->field['title'])) ? true : false;
		$title = $has_title ? $this->parent->field['title'] : '';
		$name = $this->parent->input_name;
		$class_attr = $this->parent->class_attr;
		$value  = $this->parent->value;
		$file_url = false;
		if($value !== ''){
			$file_url = wp_get_attachment_url( intval($value) ) ?  esc_url( wp_get_attachment_url( intval($value) ) )  : flase;
			$basename = basename($file_url);
		}
		
		$desc  = (isset($this->parent->field['desc']) && !empty($this->parent->field['desc'])) ? $this->parent->field['desc'] : '';


		ob_start();

		include 'file-upload.view.php';

		$html = ob_get_clean();

		return $html;

	}

    /**
     * Enqueue scripts.
     */
    function enqueue() {
		wp_enqueue_media();
		$scripts = array('file_upload');

		foreach($scripts as $script){

			wp_register_script( $script ,ANONY_FIELDS_URI.'file-upload/'.$script.'.js' ,array('jquery'),filemtime(ANONY_FIELDS_URI.'file-upload/'.$script.'.js'),true);

			wp_enqueue_script($script);
		}
    }
}
