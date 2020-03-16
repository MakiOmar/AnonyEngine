<?php
/**
 * Multi-input types render class. 
 *
 * Handles rendring these type ['text','number','email', 'password','url'].
 * @package Anonymous theme
 * @author Makiomar
 * @link http://makiomar.com
 */

class ANONY_optf__Mixed extends ANONY_Theme_Settings{	
	
	/**
	 * Field Constructor.
	 *
	 * Required - must call the parent constructor, then assign field and value to vars, and obviously call the render field function
	 *
	 * @param array $field Array of field's data
	 * @param object $parent Field parent object
	 */
	public function __construct( $field = array(), $parent = NULL ){
		if (!is_array($field) || empty($field)) return;

		if( is_object($parent) ) parent::__construct($parent->sections, $parent->args, $parent->widgets);

		$this->field  = $field;

		$fieldID      = $this->field['id'];
					
		$fieldDefault = isset($this->field['default']) ? $this->field['default'] : '';

		$this->value  = (isset($parent->options->$fieldID))? $parent->options->$fieldID : $fieldDefault;

		switch ($this->field['type']) {
			case 'url':
				$this->value  = esc_url($this->value);
				break;

			case 'email':
				$this->value  = sanitize_email($this->value);
				break;

			case 'password':
				$this->value  = '';//Passwords can't be visible 
				break;
			
			default:
				esc_attr($this->value);
				break;
		}
	}
	
	/**
	 * Text field render Function.
	 *
	 * @return void
	 */
	public function render( $meta = false ){
		
		$class = ( isset( $this->field['class']) ) ? $this->field['class'] : 'regular-text';
		
		$name  = ( ! $meta ) ? ( $this->args['opt_name'].'['.$this->field['id'].']' ) : $this->field['id'];

		if(isset($field['note'])){
			echo '<p class=anony-warning>'.$field['note'].'<p>';
		}
		
		$html  = sprintf(
					'<input type="%1$s" name="%2$s" value="%3$s" class="%4$s"/>', 
					$this->field['type'],
					$name, 
					$this->value, 
					$class
				 );
		
		$html .= (isset($this->field['desc']) && !empty($this->field['desc']))?' <div class="description '.$class.'">'.$this->field['desc'].'</div>':'';

		echo $html;
		
	}
	
}
?>