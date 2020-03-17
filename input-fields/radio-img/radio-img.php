<?php
/**
 * Radio img field class
 *
 * @package Anonymous theme
 * @author Makiomar
 * @link http://makiomar.com
 */

class ANONY_Radio_img{	
	
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
	 * Radioo img field render Function.
	 *
	 * @return void
	 */
	public function render( $meta = false ){
		
		$html = '';
		if(isset($this->parent->field['note'])){
			$html .= '<p class=anony-warning>'.$this->parent->field['note'].'<p>';
		}
		
		$html	.= sprintf( 
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
			foreach($this->parent->field['options'] as $k => $v){

				$html .= '<div class="anony-radio-item">';

					$checked  = checked($this->parent->value, $k, false);

					$selected = ($checked != '') ? ' anony-radio-img-selected':'';
					
					$search   = array_search(
									$k,
									array_keys($this->parent->field['options'])
								);

					$html .= sprintf(
								'<label class="anony-radio-img%1$s anony-radio-img-%2$s" for="%2$s_%3$s">', 
								$selected, 
								$this->parent->field['id'], 
								$search
							);
				
						$html .= sprintf(
									'<input type="radio" id="%1$s_%2$s" name="%3$s" %4$s value="%5$s" %6$s/>', 
									$this->parent->field['id'], 
									$search, 
									$this->parent->input_name, 
									$this->parent->class_attr, 
									$k, 
									$checked
								);
				
						$html .= sprintf(
									'<img src="%1$s" alt="%2$s" onclick="jQuery:anony_radio_img_select(\'%3$s_%4$s\', \'%3$s\');" />',
									$v['img'], 
									$v['title'], 
									$this->parent->field['id'], 
									$search
								);
				
					$html .= '</label>';
				
					$html .= '<span class="description">'.$v['title'].'</span>';

				$html .= '</div>';
			}

			$html .= (isset($this->parent->field['desc']) && !empty($this->parent->field['desc']))?'<br style="clear:both;"/><div class="description">'.$this->parent->field['desc'].'</div>':'';

		$html .= '</fieldset>';

		return $html;
		
	}
	
	/**
	 * Enqueue scripts.
	 */
	public function enqueue(){	
		wp_enqueue_script('anony-opts-field-radio_img-js', ANONY_INPUT_FIELDS_URI.'radio-img/field_radio_img.js', array('jquery'),time(),true);	
	}
	
}
?>