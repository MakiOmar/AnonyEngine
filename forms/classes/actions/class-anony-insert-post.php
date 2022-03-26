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
    class ANONY_Insert_Post{

        /**
         * Constructor.
         *
         * @param array $validated_data $_POST after validation.
         * @param array $action_data Fields mapping as ( 'wp_key' => 'form_key' ). While wp_key stands for the original key that should be passed to wp_inser_post arguments.
         */
        public function __construct( $validated_data, $action_data ) {

            error_log( print_r( $action_data, true ) );

        }
    }

}