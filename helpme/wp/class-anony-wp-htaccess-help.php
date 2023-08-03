<?php
/**
 * WP miscellaneous helpers.
 *
 * PHP version 7.3 Or Later.
 *
 * @package  AnonyEngine.
 * @author   Makiomar <info@makiomar.com>.
 * @license  https:// makiomar.com AnonyEngine Licence.
 * @link     https:// makiomar.com/anonyengine
 */

defined( 'ABSPATH' ) || die(); // Exit if accessed direct.

if ( ! class_exists( 'ANONY_Wp_Htaccess_Help' ) ) {

	/**
	 * WP Htaccess helpers.
	 *
	 * PHP version 7.3 Or Later.
	 *
	 * @package  AnonyEngine.
	 * @author   Makiomar <info@makiomar.com>.
	 * @license  https:// makiomar.com AnonyEngine Licence.
	 * @link     https:// makiomar.com/anonyengine
	 */
	class ANONY_Wp_Htaccess_Help extends ANONY_HELP {
		
		public static function maintenance_mode(){ ?>
			<IfModule mod_rewrite.c>
			RewriteEngine on

			RewriteCond %{REQUEST_URI} !^/maintenance.html$
			RewriteCond %{REQUEST_URI} !^/wp-admin/.*$ [NC]
			RewriteCond %{REQUEST_URI} !^/wp-includes/.*$ [NC]
			RewriteCond %{REQUEST_URI} !^/wp-content/plugins/.*$ [NC]
			RewriteCond %{REQUEST_URI} !^/wp-content/uploads/.*$ [NC]
			RewriteCond %{REQUEST_URI} !^/wp-login.php.*$ [NC]

			RewriteRule ^(.*)$ /maintenance.html [R=permanent,L]
			</IfModule>
		<?php }

	}
}
