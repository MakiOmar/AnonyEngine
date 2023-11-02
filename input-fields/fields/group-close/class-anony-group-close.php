<?php
/**
 * Group close render class.
 *
 * @package Anonymous theme
 * @author Makiomar
 * @link http://makiomar.com
 */

/**
 * Group close render class.
 *
 * @package Anonymous theme
 * @author Makiomar
 * @link http://makiomar.com
 */
class ANONY_Group_Close {

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

		$this->parent_obj->value = '';
	}

	/**
	 * Render input field
	 */
	public function render() {

		return '</div>';
	}
}
