<?php
/**
 * Upload field class
 *
 * @package Anonymous theme
 * @author Makiomar
 * @link http://makiomar.com
 */

class ANONY_Upload{

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
		$this->parent->value = esc_url( $this->parent->value );
		$this->enqueue();
	}

	
	/**
	 * Upload field render Function.
	 *
	 * @return void
	 */
	function render( $meta = false ){
		
		
		$html = '';

		if(isset($this->parent->field['note'])){
			$html .= '<p class=anony-warning>'.$this->parent->field['note'].'<p>';
		}

		$html	.= sprintf( 
						'<fieldset class="anony-row anony-row-inline" id="fieldset_%1$s">', 
						$this->parent->field['id'] 
					);
		if($this->parent->context == 'meta' && isset($this->parent->field['title'])){
			$html   .= sprintf(
							'<label class="anony-label" for="%1$s">%2$s</label>', 
							$this->parent->field['id'],
							$this->parent->field['title']
						  ) ;
		}
		
		$html .= sprintf(
				'<input type="hidden" name="%1$s" value="%2$s" class="%3$s" />', 
				$this->parent->input_name, 
				$this->parent->value, 
				$this->parent->class_attr
			);

		$html .= '<img class="anony-opts-screenshot" style="max-width:180px;" src="'.$this->parent->value.'" />';

		if($this->parent->value == ''){
			$remove = ' style="display:none;"';
			$upload = '';
		}else{
			$remove = '';
			$upload = ' style="display:none;"';
		}

		$html .= sprintf(
					' <a href="javascript:void(0);" data-choose="Choose a File" data-update="Select File" class="anony-opts-upload"%1$s><span></span>%2$s</a>', 
					$upload, 
					esc_html__('Browse', ANOE_TEXTDOM )
				);

		$html .= sprintf(
					' <a href="javascript:void(0);" class="anony-opts-upload-remove"%1$s>%2$s</a>', 
					$remove, 
					esc_html__('Remove Upload', ANOE_TEXTDOM )
				);
		
		$html .= (isset($this->parent->field['desc']) && !empty($this->parent->field['desc']))?'<div class="description">'.$this->parent->field['desc'].'</div>':'';
		$html .= '</fieldset>';

		return $html;
	}

    /**
     * Enqueue scripts.
     */
    function enqueue() {
        $wp_version = floatval( get_bloginfo( 'version' ) );
        if ( $wp_version < "3.5" ) {
            wp_enqueue_script(
                'anony-opts-field-upload-js', 
                ANONY_FIELDS_URI . 'upload/field_upload_3_4.js', 
                array('jquery', 'thickbox', 'media-upload'),
                time(),
                true
            );
            wp_enqueue_style('thickbox');
        } else {
            wp_enqueue_script(
                'anony-opts-field-upload-js', 
                ANONY_FIELDS_URI . 'upload/field_upload.js', 
                array('jquery'),
                time(),
                true
            );
            wp_enqueue_media();
        }
        wp_localize_script('anony-opts-field-upload-js', 'anony_upload', array('url' => ANONY_FIELDS_URI.'upload/blank.png'));
    }
}
