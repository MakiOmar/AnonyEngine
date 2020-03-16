<?php
/**
 * Switch field class
 *
 * @package Anonymous theme
 * @author Makiomar
 * @link http://makiomar.com
 */
class ANONY_optf__Switch extends ANONY_Theme_Settings{	
	
	/**
	 * Field Constructor.
	 *
	 * Required - must call the parent constructor, then assign field and value to vars, and obviously call the render field function
	 *
	 * @param array $field Array of field's data
	 * @param object $parent Field parent object
	 */
	public function __construct( $field , $parent = NULL ){
		
		if (!is_array($field) || empty($field)) return;

		if( is_object($parent) ) parent::__construct($parent->sections, $parent->args, $parent->widgets);

		$this->field  = $field;

		$fieldID      = $this->field['id'];
					
		$fieldDefault = isset($this->field['default']) ? $this->field['default'] : '';

		$this->value  = (isset($parent->options->$fieldID))? $parent->options->$fieldID : $fieldDefault;
	}

	/**
	 * Switch field render Function.
	 *
	 * @return void
	 */
	public function render( $meta = false ){
		
		$class = ( isset( $this->field['class']) ) ? 'class="'.$this->field['class'].'" ' : '';	

		$name  = ( ! $meta ) ? ( $this->args['opt_name'].'['.$this->field['id'].']' ) : $this->field['id'];
		
		$html = '';
		// fix for value "off = 0"
		if( ! $this->value ) $this->value = 0;
		// fix for WordPress 3.6 meta options
		if(strpos( $this->field['id'] ,'[]') === false) $html = '<input type="hidden" name="'. $name .'" value="0" />';

		if(isset($field['note'])){
			echo '<p class=anony-warning>'.$field['note'].'<p>';
		}
		
		$html .= sprintf(
					'<input type="checkbox" data-toggle="switch" id="%1$s" name="%2$s" %3$s value="1" %4$s />',
					$this->field['id'], 
					$name, 
					$class, 
					checked($this->value, 1, false)
				);
		
		$html .= (isset($this->field['desc']) && !empty($this->field['desc']))?'&nbsp;&nbsp;<div class="description btn-desc">'.$this->field['desc'].'</div>':'';

		echo $html;	
	}
	
	/**
	 * Enqueue scripts.
	 */
	public function enqueue(){		
		wp_enqueue_script('anony-opts-field-switch-js', ANONY_OPTIONS_URI.'fields/switch/field_switch.js', array('jquery'),time(),true);
	}
	
}
?>