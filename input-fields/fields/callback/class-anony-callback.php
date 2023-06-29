<?php
/**
 * Callback field class
 *
 * @package Anonymous theme
 * @author Makiomar
 * @link http://makiomar.com
 */
class ANONY_Callback {
	
	/**
	 * @var object
	 */
	private $parent;
	
	/**
	 * Color field Constructor.
	 *
	 * @param object $parent Field parent object
	 */
	public function __construct( $parent = null ) {
		if ( ! is_object( $parent ) ) {
			return;
		}

		$this->parent = $parent;

		$this->parent->value = esc_attr( $this->parent->value );
	}

	/**
	 * Callback field render Function.
	 *
	 * @return mixed
	 */
	public function render( $meta = false ) {
		if ( key_exists( 'callback', $this->parent->field ) ) {
			return call_user_func($this->parent->field['callback'], $this->parent);
		}
	}

}

