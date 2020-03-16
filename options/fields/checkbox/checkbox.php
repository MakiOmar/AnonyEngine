<?php
/**
 * Checkbox field class
 *
 * @package Anonymous theme
 * @author Makiomar
 * @link http://makiomar.com
 */

class ANONY_optf__Checkbox extends ANONY_Theme_Settings{	
	
	/**
	 * Checkbox field Constructor.
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
	}

	/**
	 * Checkbox field render Function.
	 *
	 * @param boolean $meta To decide field name attribute.
	 * @return void
	 */
	public function render( $meta = false ){
		
		$class    = ( isset( $this->field['class']) ) ? 'class="'.$this->field['class'].'" ' : '';	

		$name     = ( ! $meta ) ? ( $this->args['opt_name'].'['.$this->field['id'].']' ) : $this->field['id'];

		$disabled = isset( $this->field['disabled'] ) && ( $this->field['disabled'] == true ) ? " disabled = 'disabled' " : "";
		
		// fix for value "off = 0"
		if( ! $this->value ) $this->value = 0;

		if(isset($field['note'])){
			echo '<p class=anony-warning>'.$field['note'].'<p>';
		}

		$html = '';

		// fix for WordPress 3.6 meta options
		if(strpos( $this->field['id'] ,'[]') === false) 

			$html .= sprintf(
						'<input type="hidden" name="%1$s" value="0" />', 
						$name
					);
		
		if(isset($this->field['options']) && is_array($this->field['options'])){

			foreach($this->field['options'] as $opt => $title){

				$checked = (is_array($this->value) && in_array($opt, $this->value)) ? ' checked="checked"' : '';

				$html   .= sprintf(
							'<label class="anony-options-row">%1$s', 
							$title
						  ) ;

				$html   .= sprintf('<input type="checkbox" id="%1$s" name="%2$s[]" %3$s value="%4$s"%5$s%6$s/></label>', 
							$opt, 
							$name, 
							$class, 
							$opt, 
							$checked,
							$disabled
						  ) ;
			}
			
		}else{
			$html .= sprintf('<input type="checkbox" id="%1$s" name="%2$s" %3$s value="1"%4$s%5$s/>',$this->field['id'], $name, $class, checked($this->value, 1, false), $disabled);
		}
		
		
		$html .= (isset($this->field['desc']) && !empty($this->field['desc'])) ? '&nbsp;&nbsp;<div class="description btn-desc">'.$this->field['desc'].'</div>':'';	

		echo $html;
	}
	
}
?>