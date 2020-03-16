<?php
/**
 * Radio img field class
 *
 * @package Anonymous theme
 * @author Makiomar
 * @link http://makiomar.com
 */

class ANONY_optf__Radio_img extends ANONY_Theme_Settings{	
	
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

		$this->value  = (isset($parent->options->$fieldID))? $parent->options->$fieldID : $fieldDefault;}

	/**
	 * Radioo img field render Function.
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

				$html .= '<div class="anony-radio-item">';

					$checked  = checked($this->value, $k, false);

					$selected = ($checked != '') ? ' anony-radio-img-selected':'';
					
					$search   = array_search(
									$k,
									array_keys($this->field['options'])
								);

					$html .= sprintf(
								'<label class="anony-radio-img%1$s anony-radio-img-%2$s" for="%2$s_%3$s">', 
								$selected, 
								$this->field['id'], 
								$search
							);
				
						$html .= sprintf(
									'<input type="radio" id="%1$s_%2$s" name="%3$s" %4$s value="%5$s" %6$s/>', 
									$this->field['id'], 
									$search, 
									$name, 
									$class, 
									$k, 
									$checked
								);
				
						$html .= sprintf(
									'<img src="%1$s" alt="%2$s" onclick="jQuery:anony_radio_img_select(\'%3$s_%4$s\', \'%3$s\');" />',
									$v['img'], 
									$v['title'], 
									$this->field['id'], 
									$search
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
		wp_enqueue_script('anony-opts-field-radio_img-js', ANONY_OPTIONS_URI.'fields/radio_img/field_radio_img.js', array('jquery'),time(),true);	
	}
	
}
?>