<?php
/**
 * Debug field class
 *
 * @package Anonymous theme
 * @author Makiomar
 * @link http://makiomar.com
 */

/**
 * Debug render class
 *
 * @package Anonymous theme
 * @author Makiomar
 * @link http://makiomar.com
 */
class ANONY_Opt_Debug {

	/**
	 * Parent object
	 *
	 * @var object
	 */
	private $parent_obj;

	/**
	 * Color field Constructor.
	 *
	 * @param object $parent_obj Field parent object.
	 */
	public function __construct( $parent_obj = null ) {
		if ( ! is_object( $parent_obj ) ) {
			return;
		}

		$this->parent_obj = $parent_obj;

		$this->parent_obj->value = esc_attr( $this->parent_obj->value );
	}

	/**
	 * Info field render Function.
	 *
	 * @return string Field output.
	 */
	public function render() {
		if ( key_exists( 'callback', $this->parent_obj->field ) ) {
			$debug = call_user_func( $this->parent_obj->field['callback'] );

			ANONY_HELP::neat_var_dump( $debug );
			return;
		}
	}
}
