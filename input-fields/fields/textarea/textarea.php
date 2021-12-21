<?php 
/**
 * Textarea render class. 
 *
 * @package Anonymous theme
 * @author Makiomar
 * @link http://makiomar.com
 */

class ANONY_Textarea{
	/**
	 * Color field Constructor.
	 * @param object $parent Field parent object
	 */
	public function __construct($parent = NULL ){
		if (!is_object($parent)) return;

		$this->parent = $parent;

		
		$this->parent->value = esc_textarea($this->parent->value );

		
	}
	
	/**
	 * Render input field
	 */
	public function render(){
		$placeholder = (isset($this->parent->field['placeholder'])) ? 'placeholder="'.$this->parent->field['placeholder'].'"' : '';
						
		$class  = isset( $this->parent->field['class'] ) && ! is_null( $this->parent->field['class'] ) ? $this->parent->field['class'] : 'anony-meta-field';
		
		$readonly  = isset( $this->parent->field['readonly'] ) && ( $this->parent->field['readonly'] == true ) ? " readonly" : "";
		
		$disabled  = isset( $this->parent->field['disabled'] ) && ( $this->parent->field['disabled'] == true ) ? " disabled" : "";
		
		$cols  = isset( $this->parent->field['columns'] ) ? $this->parent->field['columns'] : 24;
		
		$rows  = isset( $this->parent->field['rows'] ) ? $this->parent->field['rows'] : 5;		
		$text_align  = isset( $this->parent->field['text-align'] ) ? $this->parent->field['text-align'] : 'initial';
		
		if ($this->parent->as_template) {
			$html	= sprintf( 
						'<fieldset class="anony-row">', 
						$this->parent->field['id']
					);
			$html  .= sprintf( 
						'<textarea style="text-align:%1$s" class="%2$s anony-row" rows="'.$rows.'" cols="'.$cols.'" name="%3$s" %4$s %5$s %5$s></textarea>', 
						$text_align,
						$class,
						$this->parent->input_name, 
						$readonly,
						$disabled,
						$placeholder
					);
			$html	.= '</fieldset>';
			return $html;
		}
		
		$html	= sprintf( 
						'<fieldset class="anony-row" id="fieldset_%1$s">', 
						$this->parent->field['id']
					);
		if($this->parent->context == 'meta' && isset($this->parent->field['title'])){
			$html	.= sprintf( 
							'<label class="anony-label" for="anony_%1$s">%2$s</label>', 
							$this->parent->field['id'], 
							$this->parent->field['title']
						);
		}

		$html  .= sprintf( 
						'<textarea style="text-align:%1$s" class="%2$s" rows="'.$rows.'" cols="'.$cols.'" id="%3$s" name="%3$s" %4$s %5$s %6$s>%7$s</textarea>',
						$text_align,
						$class,
						$this->parent->input_name, 
						$readonly,
						$disabled,
						$placeholder,
						$this->parent->value
					);
		
		$html	.= '</fieldset>';
		
		return $html;

	}
} 