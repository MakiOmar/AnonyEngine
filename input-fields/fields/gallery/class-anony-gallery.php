<?php
/**
 * Upload field class
 *
 * @package Anonymous theme
 * @author Makiomar
 * @link http://makiomar.com
 */

class ANONY_Gallery{

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
		
		
		$html = '';

		if(isset($this->parent->field['note'])){
			$html .= '<p class=anony-warning>'.$this->parent->field['note'].'<p>';
		}

		$html	.= sprintf( 
						'<fieldset class="anony-row anony-row-inline" id="fieldset_%1$s">', 
						$this->parent->field['id'] 
					);
		if($this->parent->context != 'option' && isset($this->parent->field['title'])){
			$html   .= sprintf(
							'<label class="anony-label" for="%1$s">%2$s</label>', 
							$this->parent->field['id'],
							$this->parent->field['title']
						  ) ;
		}
		
		$html .= sprintf(
				'<input type="hidden" name="%1$s" value="" class="%2$s" />', 
				$this->parent->input_name, 
				$this->parent->class_attr
			);
		$html .= '<div class="anony-gallery-thumbs-wrap" id="anony-gallery-thumbs-'.$this->parent->field['id'].'">';
		$style = 'display:none;';
		if (is_array($this->parent->value) && !empty($this->parent->value)) {
			$style = 'display:inline-block;';
			$html .= '<div class="anony-gallery-thumbs">';
			foreach ($this->parent->value as $attachment_id) {

				$html .= '<div class="gallery-item-container" style="display:inline-flex; flex-direction:column; align-items: center;margin-left:15px;"><a href="#" style="display:block; width:50px; height:50px;background-color: #d2d2d2;border-radius: 3px;padding:5px"><img src="'.wp_get_attachment_url( intval($attachment_id)).'" alt="" style="width:100%;height:100%;display:block;"/></a><input class="gallery-item" type="hidden" name="' . $this->parent->input_name . '[]" id="anony-gallery-thumb-'.$attachment_id.'" value="'.$attachment_id.'" /><a href="#" class="anony_remove_gallery_image" style="display:block" rel-id="'.$attachment_id.'">Remove</a></div>';
			}

			$html .= '</div>';
		}else{
			$html .= '<div class="anony-gallery-thumbs"></div>';
		}

		
		$html .= sprintf(
					'<a href="javascript:void(0);" data-choose="Choose a File" data-update="Select File" class="anony-opts-gallery button button-primary button-large"><span></span>%1$s</a>', 
					esc_html__('Browse', 'anonyengine' )
				);

		$html .= sprintf(
					' <a href="javascript:void(0);" class="anony-opts-clear-gallery button button-primary button-large" style="'.$style.'"><span></span>%1$s</a>', 
					esc_html__('Remove all', 'anonyengine' )
				);
		$html .= '</div>';

		
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
                ANONY_FIELDS_URI . 'gallery/field_upload_3_4.js', 
                array('jquery', 'thickbox', 'media-upload'),
                time(),
                true
            );
            wp_enqueue_style('thickbox');
        } else {
            wp_enqueue_script(
                'anony-opts-field-upload-js', 
                ANONY_FIELDS_URI . 'gallery/field_upload.js', 
                array('jquery'),
                time(),
                true
            );
            wp_enqueue_media();
        }
        wp_localize_script(
        	'anony-opts-field-upload-js', 
        	'anony_gallery', 
        	array(
        		'url'  => ANONY_FIELDS_URI.'gallery/blank.png',
        		'name' => $this->parent->input_name,
        	)
        );
    }
}
