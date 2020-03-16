<?php
/**
 * Sliderbar field class
 *
 * @package Anonymous theme
 * @author Makiomar
 * @link http://makiomar.com
 */
class ANONY_optf__Sliderbar extends ANONY_Theme_Settings{	
	
	
	/**
	 * Field Constructor.
	 *
	 * Required - must call the parent constructor, then assign field and value to vars, and obviously call the render field function
	 *
	 * @param array $field Array of field's data
	 * @param object $parent Field parent object
	 */
	public function __construct($field = array(), $parent = NULL ){
		if (!is_array($field) || empty($field)) return;

		if( is_object($parent) ) parent::__construct($parent->sections, $parent->args, $parent->widgets);

		$this->field  = $field;

		$fieldID      = $this->field['id'];
					
		$fieldDefault = isset($this->field['default']) ? $this->field['default'] : '';

		$this->value  = (isset($parent->options->$fieldID))? $parent->options->$fieldID : $fieldDefault;
		
		$this->value  = esc_attr($this->value);
	}

	
	/**
	 * Slidebar field render Function.
	 *
	 * @return void
	 */
	public function render(){
		$class = (isset($this->field['class']))?'class="'.$this->field['class'].'" ':'';

		$default = isset($this->field['default']) ? $this->field['default'] : '';

		$this->value = !empty($this->value) ? $this->value : $default;

		if(isset($field['note'])){
			echo '<p class=anony-warning>'.$field['note'].'<p>';
		}

		$html = sprintf(
					'<div class="anony-options-row"><div id="%1$s_sliderbar" class="sliderbar %2$s" rel="%1$s"></div>', 
					$this->field['id'],
					$class
				);

		$html .= sprintf(
					'<input type="text" id="%1$s" name="%2$s[%1$s]" value="%3$s" class="sliderbar_input %4$s" readonly="readonly"/></div>', 
					$this->field['id'], 
					$this->args['opt_name'], 
					$this->value, 
					$class
				);	

		$html .= (isset($this->field['desc']) && !empty($this->field['desc']))?' <div class="description sliderbar_desc'.$class.'">'.$this->field['desc'].'</div>':'';

		echo $html;
	}
	
	
	/**
	 * Enqueue scripts.
	 */
	public function enqueue(){
		
		wp_enqueue_style('anony-opts-jquery-ui-css');
		
		wp_enqueue_script(
			'jquery-slider', 
			ANONY_OPTIONS_URI.'/fields/sliderbar/jquery.ui.slider.js', 
			array('jquery', 'jquery-ui-core', 'jquery-ui-slider'), 
			time(), 
			true
		);

		wp_enqueue_script(
			'anony-opts-field-sliderbar-js', 
			ANONY_OPTIONS_URI.'/fields/sliderbar/field_sliderbar.js', 
			array('jquery', 'jquery-ui-core', 'jquery-ui-dialog'),
			time(),
			true
		);
		

	}
	
}
?>