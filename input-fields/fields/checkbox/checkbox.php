<?php
/**
 * Checkbox field class
 *
 * @package Anonymous theme
 * @author Makiomar
 * @link http://makiomar.com
 */

class ANONY_Checkbox
{
	public $parent;
	/**
	 * Checkbox field Constructor.
	 * @param object $parent Field parent object
	 */
	public function __construct( $parent = NULL ){

		if (!is_object($parent)) return;

		$this->parent = $parent;
	}

	public function render(){	

		$disabled = isset( $this->parent->field['disabled'] ) && ( $this->parent->field['disabled'] == true ) ? " disabled = 'disabled' " : "";

		// fix for value "off = 0"
		if( ! $this->parent->value ) $this->parent->value = 0;

		if(isset($this->parent->field['note'])){
			echo '<p class=anony-warning>'.$field['note'].'<p>';
		}

		$html	= sprintf( 
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

		// fix for WordPress 3.6 meta options
		if(strpos( $this->parent->field['id'] ,'[]') === false) 

			$html .= sprintf(
						'<input type="hidden" name="%1$s" value="0" />', 
						$this->parent->input_name
					);
		$html .= '<div class="anony-metabox-col">';

		if(isset($this->parent->field['options']) && is_array($this->parent->field['options'])){
			foreach($this->parent->field['options'] as $opt => $title){

				$checked = (is_array($this->parent->value[0]) && in_array($opt, $this->parent->value[0])) ? ' checked="checked"' : '';

				$html   .= sprintf(
							'<label class="anony-inputs-row">%1$s', 
							$title
						  ) ;

				$html   .= sprintf('<input type="checkbox" class="checkbox %1$s" id="%2$s[%3$s]" name="%4$s[]" value="%3$s"%5$s%6$s/></label>',
							$this->parent->class_attr,
							$this->parent->field['id'],
							$opt, 
							$this->parent->input_name,  
							$checked,
							$disabled
						  ) ;
			}

		$html .= '</div>';

		}else{

			$html .= sprintf('<input type="checkbox" id="%1$s" name="%2$s" class="checkbox %3$s" value="1"%4$s%5$s/>',$this->parent->field['id'], $this->parent->input_name, $this->parent->class_attr, checked($this->parent->value, 1, false), $disabled);
		}
		
		
		$html .= (isset($this->parent->field['desc']) && !empty($this->parent->field['desc'])) ? '&nbsp;&nbsp;<div class="description btn-desc">'.$this->parent->field['desc'].'</div>':'';

		$html	.= '</fieldset>';

		return $html;
	}


}