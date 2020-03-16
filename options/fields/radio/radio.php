<?php
/**
 * Radio field class
 *
 * @package Anonymous theme
 * @author Makiomar
 * @link http://makiomar.com
 */
class ANONY_optf__Radio extends ANONY_Theme_Settings{	
	
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
	}

	/**
	 * Radio field render Function.
	 *
	 * @return void
	 */
	public function render( $meta = false ){
		
		$class = ( isset( $this->field['class'])) ? 'class="'.$this->field['class'].'" ' : '';

		$name  = ( ! $meta ) ? ( $this->args['opt_name'].'['.$this->field['id'].']' ) : $this->field['id'];

		if(isset($field['note'])){
			echo '<p class=anony-warning>'.$field['note'].'<p>';
		}
		
		$html = '<fieldset id="'.$this->field['id'].'">';

			foreach($this->field['options'] as $k => $v){
				
				$radioClass =  isset($v['class']) ? 'class="'.$v['class'].'"'  : '';

				$html .= '<div class="anony-radio-item">';

					$checked  = checked($this->value, $k, false);

					$search   = array_search(
									$k,
									array_keys($this->field['options'])
								);

					$selected = ( $checked != '' ) ? ' anony-radio-img-selected' : '';
				
					$html .= sprintf(
								'<label class="anony-radio%1$s anony-radio-%2$s" for="%2$s_%3$s">', 
								$selected, 
								$this->field['id'], 
								$search
							);
				
						$html .= sprintf(
									'<input %1$s type="radio" id="%2$s_%3$s" name="%4$s" %5$s value="%6$s" %7$s onclick="jQuery:anony_radio_select(\'%2$s_%3$s\', \'%2$s\');"/>',
									 $radioClass, 
									 $this->field['id'], 
									 $search, 
									 $name, 
									 $class, 
									 $k, 
									 $checked
								);
				
					$html .= '</label>';
				
					$html .= '<span class="description">'.$v['title'].'</span>';
					
				$html .= '</div>';
			}

			$html .= (isset($this->field['desc']) && !empty($this->field['desc']))?'<br style="clear:both;"/><div class="description">'.$this->field['desc'].'</div>':'';

		$html .= '</fieldset>';

		echo $html;
		
	}
	
	/**
	 * Enqueue scripts.
	 */
	public function enqueue(){	
		wp_enqueue_script('anony-opts-field-radio-js', ANONY_OPTIONS_URI.'fields/radio/field_radio.js', array('jquery'),time(),true);	
	}
	
}
?>