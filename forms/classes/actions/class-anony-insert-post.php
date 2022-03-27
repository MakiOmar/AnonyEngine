<?php
/**
 * AnonyEngine post insertion.
 *
 * PHP version 7.3 Or Later.
 *
 * @package  AnonyEngine
 * @author   Makiomar <info@makior.com>
 * @license  https://makiomar.com AnonyEngine Licence
 * @link     https://makiomar.com/anonyengine_elements
 */

defined( 'ABSPATH' ) || die(); // Exit if accessed directly.

if ( ! class_exists( 'ANONY_Insert_Post' ) ) {

	/**
	 * AnonyEngine post insertion class.
	 *
	 * @package    AnonyEngine
	 * @author     Makiomar <info@makior.com>
	 * @license    https:// makiomar.com AnonyEngine Licence.
	 * @link       https:// makiomar.com
	 */
	class ANONY_Insert_Post {

		/**
		 * Required arguments for post insertion.
		 */
		const REQUIRED_ARGUMENTS = array( 'post_type', 'post_status', 'post_title' );

		/**
		 * Constructor.
		 *
		 * @param array $validated_data $_POST after validation.
		 * @param array $action_data Fields mapping as ( 'wp_key' => 'form_key' ). While wp_key stands for the original key that should be passed to wp_inser_post arguments.
		 */
		public function __construct( $validated_data, $action_data ) {

			// Argumnets sent from the form.
			$posted_required_arguments = array_intersect_key( $validated_data, $action_data );

			if ( ! ANONY_HELP::isset_not_empty( $posted_required_arguments['post_type'] ) || ! ANONY_HELP::isset_not_empty( $posted_required_arguments['post_status'] ) || ! ANONY_HELP::isset_not_empty( $posted_required_arguments['post_title'] ) ) {

			}

		}




	}

}