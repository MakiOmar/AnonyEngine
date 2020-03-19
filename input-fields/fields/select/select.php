<?php
/**
 * Select field class
 *
 * @package Anonymous theme
 * @author Makiomar
 * @link http://makiomar.com
 */

class ANONY_Select{	
	
	/**
	 * Color field Constructor.
	 * @param object $parent Field parent object
	 */
	public function __construct($parent = NULL ){
		if (!is_object($parent)) return;

		$this->parent = $parent;
	}
	
	/**
	 * Select field render Function.
	 *
	 * @return void
	 */
	public function render( $meta = false ){

		$disabled     = isset( $this->parent->field['disabled'] ) && ( $this->parent->field['disabled'] == true ) ? " disabled" : "";
		
		$autocomplete = (isset( $this->parent->field['auto-complete']) && $this->parent->field['auto-complete'] == 'on') ? 'autocomplete="on"' : 'autocomplete="off"';

		if(isset($this->parent->field['multiple']) && $this->parent->field['multiple']){
			$multiple  = " multiple ";
			$this->parent->input_name = $this->parent->input_name.'[]';

		}else{
			$multiple  = "";
		}

		$html = sprintf( 
					'<fieldset class="anony-row anony-row-inline" id="fieldset_%1$s">', 
					$this->parent->field['id'] 
				);

				
		if(isset($this->parent->field['note'])){
			echo '<p class=anony-warning>'.$this->parent->field['note'].'<p>';
		}
		
		if($this->parent->context == 'meta' && isset($this->parent->field['title'])){
			$html .= sprintf( 
						'<label class="anony-label" for="%1$s">%2$s</label>', 
						$this->parent->field['id'], 
						$this->parent->field['title']
					);
		}

		$html   .= sprintf( 
					'<select class="%1$s" name="%2$s" id="'.$this->parent->field['id'].'" %3$s %4$s %5$s>', 
					$this->parent->class_attr, 
					$this->parent->input_name, 
					$disabled, 
					$multiple,
					$autocomplete
				);

			if( is_array( $this->parent->field['options'] ) && !empty($this->parent->field['options']) ){
				$html .= sprintf('<option value="">%1$s</option>', esc_html__( 'Select', ANOE_TEXTDOM  ));
				
				if( empty($multiple) ) :

			        foreach ( $this->parent->field['options'] as $key => $label ) {
			            $html .= sprintf( 
									'<option value="%1$s"%2$s>%3$s</option>',
									$key, 
									selected( $this->parent->value, $key, false ), 
									$label 
								);
			        }

			    else:
			        foreach ( $this->parent->field['options'] as $key => $label ) {
						
			        	$selected = is_array($this->parent->value) && in_array( $key, $this->parent->value ) && $key != '' ? ' selected' : '';

			            $html .= sprintf( 
									'<option value="%1$s"%2$s>%3$s</option>', 
									$key, 
									$selected, 
									$label 
								);
			        }

			    endif;
			}else{
				$html .= sprintf(
								'<option value="">%1$s</option>', 
								esc_html__( 'No options', ANOE_TEXTDOM  )
							);
			}

		$html .= '</select>';
		
		$html .= (isset($this->parent->field['desc']) && !empty($this->parent->field['desc']))?' <div class="description">'.$this->parent->field['desc'].'</div>':'';

		$html .= '</fieldset>';
		
		return $html;
	}
	
}
?>