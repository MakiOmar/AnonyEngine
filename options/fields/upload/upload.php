<?php
/**
 * Upload field class
 *
 * @package Anonymous theme
 * @author Makiomar
 * @link http://makiomar.com
 */

class ANONY_optf__Upload extends ANONY_Theme_Settings{

	/**
	 * Field Constructor.
	 *
	 * Required - must call the parent constructor, then assign field and value to vars, and obviously call the render field function
	 *
	 * @param array $field Array of field's data
	 * @param object $parent Field parent object
	 */
	function __construct( $field = array(), $parent = NULL ){
		if (!is_array($field) || empty($field)) return;

		if( is_object($parent) ) parent::__construct($parent->sections, $parent->args, $parent->widgets);

		$this->field  = $field;

		$fieldID      = $this->field['id'];
					
		$fieldDefault = isset($this->field['default']) ? $this->field['default'] : '';

		$this->value  = (isset($parent->options->$fieldID))? $parent->options->$fieldID : $fieldDefault;

		$this->value  = esc_url( $this->value );
	}

	
	/**
	 * Upload field render Function.
	 *
	 * @return void
	 */
	function render( $meta = false ){
		
		$class = ( isset($this->field['class']) ) ? $this->field['class'] : 'regular-text';
		
		$name  = ( ! $meta ) ? ( $this->args['opt_name'].'['.$this->field['id'].']' ) : $this->field['id'];
		
		if(isset($field['note'])){
			echo '<p class=anony-warning>'.$field['note'].'<p>';
		}
		
		$html = sprintf(
				'<input type="hidden" name="%1$s" value="%2$s" class="%3$s" />', 
				$name, 
				$this->value, 
				$class
			);

		$html .= '<img class="anony-opts-screenshot" style="max-width:180px;" src="'.$this->value.'" />';

		if($this->value == ''){
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
		
		$html .= (isset($this->field['desc']) && !empty($this->field['desc']))?'<div class="description">'.$this->field['desc'].'</div>':'';

		echo $html;
	}

    /**
     * Enqueue scripts.
     */
    function enqueue() {
        $wp_version = floatval( get_bloginfo( 'version' ) );
        if ( $wp_version < "3.5" ) {
            wp_enqueue_script(
                'anony-opts-field-upload-js', 
                ANONY_OPTIONS_URI . '/fields/upload/field_upload_3_4.js', 
                array('jquery', 'thickbox', 'media-upload'),
                time(),
                true
            );
            wp_enqueue_style('thickbox');
        } else {
            wp_enqueue_script(
                'anony-opts-field-upload-js', 
                ANONY_OPTIONS_URI . '/fields/upload/field_upload.js', 
                array('jquery'),
                time(),
                true
            );
            wp_enqueue_media();
        }
        wp_localize_script('anony-opts-field-upload-js', 'anony_upload', array('url' => ANONY_OPTIONS_URI.'/fields/upload/blank.png'));
    }
}
