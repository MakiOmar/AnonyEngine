<?php
/**
 * Color field class
 *
 * @package Anonymous theme
 * @author Makiomar
 * @link http://makiomar.com
 */
class ANONY_optf__Color_gradient extends ANONY_Theme_Settings{	
	
	/**
	 * Field Constructor.
	 *
	 * Required - must call the parent constructor, then assign field and value to vars, and obviously call the render field function
	 *
	 * @since Theme_Settings 1.0
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
	}
	
	
	
	/**
	 * Color gradient field render Function.
	 *
	 * @return void
	 */
	public function render(){
		
		$class = (isset($this->field['class']))?$this->field['class'].' ':'';

		$from_style = '';
		$from_value = '';
		
		if(isset($this->value['from'])){
			$from_value = esc_attr( $this->value['from'] );

			$from_style = 'style="background-color:'.$from_value.';"';
			
		}

		$to_style = '';
		$to_value = '';

		if(isset($this->value['to'])){

			$to_value = esc_attr( $this->value['to'] );

			$to_style = 'style="background-color:'.$to_value.';"';
			
		}

		if(isset($field['note'])){
			echo '<p class=anony-warning>'.$field['note'].'<p>';
		}
		
		$html  = '<div class="farb-popup-wrapper" id="'.$this->field['id'].'">';		

		$html .= '<fieldset>';

		//from field
		$html .= sprintf(
					'<label for="%1$s-from" class="anony-input-lable">%2$s</label>',
					$this->field['id'], 
					esc_html__( 'From', ANOE_TEXTDOM  )
				);

		$html .= sprintf(
					'<input type="text" id="%1$s-from" name="%2$s[%1$s][from]" value="%3$s" class="%4$spopup-colorpicker"/>', 
					$this->field['id'], 
					$this->args['opt_name'], 
					$from_value, 
					$class
				);
			 
		
		$html .= sprintf(
					'<div class="farb-popup"><div class="farb-popup-inside"><div id="%1$s-frompicker" class="color-picker"></div></div></div>', 
					$this->field['id']
				);

		$html .= sprintf(
					'<div class="color-prev prev-%1$s-from" %2$s rel="%1$s-from"></div>', 
					$this->field['id'], 
					$from_style
				);

		//to field
		$html .= sprintf(
					'<label for="%1$s-to" class="anony-input-lable">%2$s</label>',
					$this->field['id'], 
					esc_html__( 'To', ANOE_TEXTDOM  )
				);

		$html .= sprintf(
					'<input type="text" id="%1$s-to" name="%2$s[%1$s][to]" value="%3$s" class="%4$spopup-colorpicker"/>', 
					$this->field['id'], 
					$this->args['opt_name'], 
					$to_value, 
					$class
				);
			 
		
		$html .= sprintf(
					'<div class="farb-popup"><div class="farb-popup-inside"><div id="%1$s-topicker" class="color-picker"></div></div></div>', 
					$this->field['id']
				);

		$html .= sprintf(
					'<div class="color-prev prev-%1$s-to" %2$s rel="%1$s-to"></div>', 
					$this->field['id'], 
					$to_style
				);
		
		$html .= (isset($this->field['desc']) && !empty($this->field['desc']))?' <div class="description">'.$this->field['desc'].'</div>':'';
			
		$html .= '</fieldset>';
		
		$html .= '</div>';

		echo $html;
		
	}//function
	
	
	/**
	 * Enqueue scripts.
	 *
	 * If this field requires any scripts, or css define this function and register/enqueue the scripts/css
	 *
	 * @since Theme_Settings 1.0
	*/
	public function enqueue(){
		
		wp_enqueue_script(
			'anony-opts-field-color-js', 
			ANONY_OPTIONS_URI.'fields/color/field_color.js', 
			array('jquery', 'farbtastic'),
			time(),
			true
		);
		
	}//function
	
}//class
?>