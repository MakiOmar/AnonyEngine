<?php
/**
 * Info field class
 *
 * @package Anonymous theme
 * @author Makiomar
 * @link http://makiomar.com
 */
class ANONY_Opt_debug{	
	
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
	 * Info field render Function.
	 *
	 * @return void
	 */
	public function render( $meta = false ){
		if( key_exists('callback', $this->parent->field) ){
			$debug = call_user_func($this->parent->field['callback']);

			ANONY_HELP::neatVarDump($debug);
			return ;
		}
	}
	
}
?>