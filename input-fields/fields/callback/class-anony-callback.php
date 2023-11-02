<?php
/**
 * Callback field class.
 *
 * @package Anonymous theme
 * @author Makiomar
 * @link http://makiomar.com
 */

/**
 * Callback field class.
 *
 * @package Anonymous theme
 * @author Makiomar
 * @link http://makiomar.com
 */
class ANONY_Callback {
	/**
	 * Parent object.
	 *
	 * @var object
	 */
	private $parent_obj_obj;

	/**
	 * Color field Constructor.
	 *
	 * @param object $parent_obj_obj Field parent object.
	 */
	public function __construct( $parent_obj_obj = null ) {
		if ( ! is_object( $parent_obj_obj ) ) {
			return;
		}

		$this->parent_obj_obj = $parent_obj_obj;

		$this->parent_obj_obj->value = esc_attr( $this->parent_obj_obj->value );
	}

	/**
	 * Callback field render Function.
	 *
	 * @return mixed
	 */
	public function render() {
		if ( key_exists( 'callback', $this->parent_obj_obj->field ) ) {
			return call_user_func( $this->parent_obj_obj->field['callback'], $this->parent_obj_obj );
		}
	}
}
