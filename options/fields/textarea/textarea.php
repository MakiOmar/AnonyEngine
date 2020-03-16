<?php
/**
 * Textarea field class
 *
 * @package Anonymous theme
 * @author Makiomar
 * @link http://makiomar.com
 */

class ANONY_optf__Textarea extends ANONY_Theme_Settings{	
	
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
		
		$this->value  = esc_textarea($this->value);
	}
	
	/**
	 * Textarea field render Function.
	 *
	 * @return void
	 */
	public function render( $meta = false ){
		
		$class = ( isset($this->field['class']) ) ? $this->field['class'] : 'large-text';

		$name  = ( ! $meta ) ? ( $this->args['opt_name'].'['.$this->field['id'].']' ) : $this->field['id'];
		
		if(isset($field['note'])){
			echo '<p class=anony-warning>'.$field['note'].'<p>';
		}
		
		$html = sprintf(
					'<textarea name="%1$s" class="%2$s" rows="6" >%3$s</textarea>', 
					$name, 
					$class, 
					$this->value
				);

		$html .= (isset($this->field['desc']) && !empty($this->field['desc']))?'<br/><div class="description">'.$this->field['desc'].'</div>':'';

		echo $html;
		
	}
	
}
?>