<?php
/**
 * Upload field class
 *
 * @package Anonymous theme
 * @author Makiomar
 * @link http://makiomar.com
 */

class ANONY_Uploader{

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
	function render(){
		
		
		$html = '';

		if(isset($this->parent->field['note'])){
			$html .= '<p class=anony-warning>'.$this->parent->field['note'].'<p>';
		}

		$html	.= sprintf( 
						'<fieldset class="anony-row anony-row-inline" id="fieldset_%1$s">', 
						$this->parent->field['id'] 
					);
		if(($this->parent->context === 'meta' || $this->parent->context === 'form') && isset($this->parent->field['title'])){
			$html   .= sprintf(
							'<label class="anony-label" for="%1$s">%2$s</label>', 
							esc_attr($this->parent->field['id']),
							esc_html($this->parent->field['title'])
						  ) ;
		}
		
		$html .= sprintf(
				'<input type="hidden" name="%1$s" value="%2$s" class="%3$s" />', 
				$this->parent->input_name, 
				$this->parent->value, 
				$this->parent->class_attr
			);
		$html .= '<div class="uploads-wrapper">';
		$image_exts   = array( 'jpg', 'jpeg', 'jpe', 'gif', 'png', 'svg' );
		$img_ext_preg = '!\.(' . join( '|', $image_exts ) . ')$!i';
		
		if(!empty($this->parent->value) && wp_http_validate_url($this->parent->value)){
			if ( preg_match( $img_ext_preg, $this->parent->value ) ) {
				$html .= '<img class="anony-opts-screenshot" style="max-width:80px;" src="'.$this->parent->value.'" />';
			} else {
				$file_basename = wp_basename($this->parent->value);
				$html .= '<a href="'.$this->parent->value.'">';
				$html .= '<img class="anony-opts-screenshot" style="max-width:80px;" src="'. ANOE_URI . 'assets/images/placeholders/file.png'.'" /><br>';
				$html .= '<span>'.$file_basename.'</span>';
				$html .= '</a>';
			}
		}
		
		
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
					'<br><a href="javascript:void(0);" class="anony-opts-upload-remove"%1$s>%2$s</a>', 
					$remove, 
					esc_html__('Remove Upload', ANOE_TEXTDOM )
				);
		$html .= '<div>';
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
                ANONY_FIELDS_URI . 'uploader/field_upload_3_4.js', 
                array('jquery', 'thickbox', 'media-upload'),
                time(),
                true
            );
            wp_enqueue_style('thickbox');
        } else {
            wp_enqueue_script(
                'anony-opts-field-upload-js', 
                ANONY_FIELDS_URI . 'uploader/field_upload.js', 
                array('jquery'),
                time(),
                true
            );
            wp_enqueue_media();
        }
        wp_localize_script('anony-opts-field-upload-js', 'anony_upload', array('url' => ANONY_FIELDS_URI.'uploader/blank.png'));
    }
}
