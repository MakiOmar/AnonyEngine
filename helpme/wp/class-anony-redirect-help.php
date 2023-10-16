<?php
/**
 * Wp redirect helpers
 *
 * PHP version 7.3 Or Later
 *
 * @package  AnonyEngine helpers
 * @author   Makiomar <info@makiomar.com>
 * @license  https://makiomar.com AnonyEngine Licence
 * @link     https://makiomar.com/anonyengine
 */

defined( 'ABSPATH' ) || die(); // Exit if accessed direct.

if ( ! class_exists( 'ANONY_Redirect_Help' ) ) {
	/**
	 * Redirect helpers' class
	 *
	 * @package    AnonyEngine helpers
	 * @author     Makiomar <info@makiomar.com>
	 * @license    https://makiomar.com AnonyEngine Licence
	 * @link       https://makiomar.com
	 */
	class ANONY_Redirect_Help extends ANONY_HELP {

		function login_redirect( $url ) {
		  add_action('init',function () use($url){
				global $pagenow;
				if( 'wp-login.php' == $pagenow ) {
					wp_redirect($url);
					exit();
				}
			});

		}
	}
}
