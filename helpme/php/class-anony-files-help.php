<?php
/**
 * PHP Files helpers.
 *
 * PHP version 7.3 Or Later.
 *
 * @package  AnonyEngine
 * @author   Makiomar <info@makiomar.com>
 * @license  https:// makiomar.com AnonyEngine Licence.
 * @link     https:// makiomar.com/anonyengine
 */

defined( 'ABSPATH' ) || die();

if ( ! class_exists( 'ANONY_FILES_HELP' ) ) {

	/**
	 * PHP Files helpers class.
	 *
	 * PHP version 7.3 Or Later.
	 *
	 * @package  AnonyEngine
	 * @author   Makiomar <info@makiomar.com>
	 * @license  https:// makiomar.com AnonyEngine Licence.
	 * @link     https:// makiomar.com/anonyengine
	 */
	class ANONY_FILES_HELP extends ANONY_HELP {
		/**
		 * Helper that require all files in a folder/subfolders once.
		 *
		 * @param string $dir Directory path.
		 * @return void
		 */
		public static function require_all_files( $dir ) {
			foreach ( glob( "$dir/*" ) as $path ) {
				if ( preg_match( '/\.php$/', $path ) ) {
					require_once $path; // It's a PHP file, so just require it.
				} elseif ( is_dir( $path ) ) {
					atrn_require_all_files( $path ); // It's a subdir, so call the same function for this subdir.
				}
			}
		}
	}
}
