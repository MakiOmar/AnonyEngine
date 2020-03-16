<?php
/**
 * Font select field class
 *
 * @package Anonymous theme
 * @author Makiomar
 * @link http://makiomar.com
 */

class ANONY_optf__Font_select extends ANONY_Theme_Settings{	
	
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
	 * Font select field render Function.
	 *
	 * @return void
	 */
	public function render( $meta = false ){
		
		$class = ( isset( $this->field['class']) ) ? 'class="'.$this->field['class'].'" ' : '';
		$name = ( ! $meta ) ? ( $this->args['opt_name'].'['.$this->field['id'].']' ) : $this->field['id'];
		
		$fonts = anony_fonts();

		$opts_groups = 
		[
			'default' => esc_html__('Default Webfont',ANOE_TEXTDOM ),
			'system'  => esc_html__('System',ANOE_TEXTDOM ),
			'popular' => esc_html__('Popular Google Fonts',ANOE_TEXTDOM ),
			'all'     => esc_html__('Google Fonts',ANOE_TEXTDOM ),
		];
		
		if(isset($field['note'])){
			echo '<p class=anony-warning>'.$field['note'].'<p>';
		}

		$html  =  '<select name="'. $name .'" '.$class.'rows="6" >';	
		
		$html .= ANONY_POST_HELP::renderHtmlOptsGroups( $fonts, $opts_groups, $this->value );

		$html .= '</select>';
		
		$html .= (isset($this->field['desc']) && !empty($this->field['desc']))?' <div class="description">'.$this->field['desc'].'</div>':'';
		
		echo $html;
	}
	
}
?>