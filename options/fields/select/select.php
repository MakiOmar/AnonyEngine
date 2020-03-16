<?php
/**
 * Select field class
 *
 * @package Anonymous theme
 * @author Makiomar
 * @link http://makiomar.com
 */

class ANONY_optf__Select extends ANONY_Theme_Settings{	
	
	/**
	 * Color field Constructor.
	 * @param object $parent Field parent object
	 */
	public function __construct($parent = NULL ){
		if (!is_object($parent)) return;

		$this->parent = $parent;
		
		$this->parent->value = esc_attr($this->parent->value );
	}
	
	/**
	 * Select field render Function.
	 *
	 * @return void
	 */
	public function render( $meta = false ){
				
		if(isset($field['note'])){
			echo '<p class=anony-warning>'.$field['note'].'<p>';
		}
		
		$html = sprintf(
					'<select name="%1$s" %2$s autocomplete="off">', 
					$this->parent->input_name, 
					$this->parent->class_attr
				);

			if( is_array( $this->field['options'] ) && !empty($this->field['options']) ){
				foreach( $this->field['options'] as $k => $v ){

					$html .= sprintf(
								'<option value="%1$s"%2$s>%3$s</option>', 
								$k, 
								selected($this->value, $k, false), 
								$v
							);
				}
			}else{
				$html .= sprintf(
								'<option value="">%1$s</option>', 
								esc_html__( 'No options', ANOE_TEXTDOM  )
							);
			}

		$html .= '</select>';
		
		$html .= (isset($this->field['desc']) && !empty($this->field['desc']))?' <div class="description">'.$this->field['desc'].'</div>':'';
		
		echo  $html;
	}
	
}
?>