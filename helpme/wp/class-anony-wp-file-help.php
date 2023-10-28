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

if ( ! class_exists( 'ANONY_Wp_File_Help' ) ) {
	/**
	 * Taxonomy helpers' class
	 *
	 * @package    AnonyEngine helpers
	 * @author     Makiomar <info@makiomar.com>
	 * @license    https://makiomar.com AnonyEngine Licence
	 * @link       https://makiomar.com
	 */
	class ANONY_Wp_File_Help extends ANONY_HELP {

		public static function handle_attachments( $file_handler, $post_id ) {
			
		  // check to make sure its a successful upload
		  if (  !isset( $_FILES[$file_handler] ) || $_FILES[$file_handler]['error'] !== UPLOAD_ERR_OK || is_null($_FILES)) __return_false();

		  require_once(ABSPATH . "wp-admin" . '/includes/image.php');
		  require_once(ABSPATH . "wp-admin" . '/includes/file.php');
		  require_once(ABSPATH . "wp-admin" . '/includes/media.php');

		  $attach_id = media_handle_upload( $file_handler, $post_id );
		  return $attach_id;
		}
		
		public static function gallery_upload($input_name){
			if(!empty($_FILES) && isset($_FILES[$input_name])){
				$files = $_FILES[$input_name];
				$ids = array();

				if( is_array( $files['name'] ) ){
					foreach ($files['name'] as $key => $value) {            
						if ($files['name'][$key]) { 
							$file = array( 
								'name' => $files['name'][$key],
								'type' => $files['type'][$key], 
								'tmp_name' => $files['tmp_name'][$key], 
								'error' => $files['error'][$key],
								'size' => $files['size'][$key]
							); 
							$_FILES = array ($input_name => $file); 
							foreach ($_FILES as $file => $array) {              
								$newupload = self::handle_attachments($file,0);
								
								if(!is_wp_error($newupload)){
									$ids[] = $newupload;
								}
							}
						}
					}
				}else{
					$newupload = self::handle_attachments($files,0);
								
					if(!is_wp_error($newupload)){
						$ids[] = $newupload;
					}
				}
				
				
				if(!empty($ids)){
					return $ids;
				}
			}
			
			return false;
		}
	}
}
