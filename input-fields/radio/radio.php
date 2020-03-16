<?php
/**
 * Radio field class
 *
 * @package Anonymous theme
 * @author Makiomar
 * @link http://makiomar.com
 */
class ANONY_Radio{	
	
	/**
	 * Field Constructor.
	 *
	 * Required - must call the parent constructor, then assign field and value to vars, and obviously call the render field function
	 *
	 * @param array $this->parent->field Array of field's data
	 * @param object $parent Field parent object
	 */
	public function __construct( $parent = NULL ){

		if (!is_object($parent)) return;

		$this->parent = $parent;
		$this->enqueue();
	}

	/**
	 * Radio field render Function.
	 *
	 * @return void
	 */
	public function render(){
		


		if(isset($this->parent->field['note'])){
			echo '<p class=anony-warning>'.$this->parent->field['note'].'<p>';
		}
		
		$html = '<fieldset id="'.$this->parent->field['id'].'">';

			foreach($this->parent->field['options'] as $k => $v){
				
				$radioClass =  isset($v['class']) ? 'class="'.$v['class'].'"'  : '';

				$html .= '<div class="anony-radio-item">';

					$checked  = checked($this->parent->value, $k, false);

					$search   = array_search(
									$k,
									array_keys($this->parent->field['options'])
								);

					$selected = ( $checked != '' ) ? ' anony-radio-img-selected' : '';
				
					$html .= sprintf(
								'<label class="anony-radio%1$s anony-radio-%2$s" for="%2$s_%3$s">', 
								$selected, 
								$this->parent->field['id'], 
								$search
							);
				
						$html .= sprintf(
									'<input %1$s type="radio" id="%2$s_%3$s" name="%4$s" %5$s value="%6$s" %7$s onclick="anony_radio_select(\'%2$s_%3$s\', \'%2$s\');"/>',
									 $radioClass, 
									 $this->parent->field['id'], 
									 $search, 
									 $this->parent->input_name, 
									 $this->parent->class_attr, 
									 $k, 
									 $checked
								);
				
					$html .= '</label>';
				
					$html .= '<span class="description">'.$v['title'].'</span>';
					
				$html .= '</div>';
			}

			$html .= (isset($this->parent->field['desc']) && !empty($this->parent->field['desc']))?'<br style="clear:both;"/><div class="description">'.$this->parent->field['desc'].'</div>':'';

		$html .= '</fieldset>';

		echo $html;
		
	}
	
	/**
	 * Enqueue scripts.
	 */
	public function enqueue(){	
		wp_enqueue_script('anony-opts-field-radio-js', ANONY_INPUT_FIELDS_URI.'radio/field_radio.js', array('jquery'),time(),true);	
	}
	
}
?>