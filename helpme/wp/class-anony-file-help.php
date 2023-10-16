<?php
/**
 * Wp fiel helpers
 *
 * PHP version 7.3 Or Later
 *
 * @package  AnonyEngine helpers
 * @author   Makiomar <info@makiomar.com>
 * @license  https://makiomar.com AnonyEngine Licence
 * @link     https://makiomar.com/anonyengine
 */

defined( 'ABSPATH' ) || die(); // Exit if accessed direct.

if ( ! class_exists( 'ANONY_File_Help' ) ) {
	/**
	 * Taxonomy helpers' class
	 *
	 * @package    AnonyEngine helpers
	 * @author     Makiomar <info@makiomar.com>
	 * @license    https://makiomar.com AnonyEngine Licence
	 * @link       https://makiomar.com
	 */
	class ANONY_File_Help extends ANONY_HELP {

		function handle_attachments($file_handler,$post_id,$set_thu=false) {
		  // check to make sure its a successful upload
		  if ( $_FILES[$file_handler]['error'] !== UPLOAD_ERR_OK ) __return_false();

		  require_once(ABSPATH . "wp-admin" . '/includes/image.php');
		  require_once(ABSPATH . "wp-admin" . '/includes/file.php');
		  require_once(ABSPATH . "wp-admin" . '/includes/media.php');

		  $attach_id = media_handle_upload( $file_handler, $post_id );

		  return $attach_id;
		}
	}
}
