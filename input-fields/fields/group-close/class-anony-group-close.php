<?php
/**
 * Group close render class.
 *
 * @package Anonymous theme
 * @author Makiomar
 * @link http://makiomar.com
 */

class ANONY_Group_Close {
	
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

		$this->parent->value = '';

	}

	/**
	 * Render input field
	 */
	public function render() {

		return '</div>';

	}
}
