<?php
/**
 * WP plugins helpers class
 *
 * @package Anonymous theme
 * @author Makiomar
 * @link http://makiomar.com
 */

if ( ! class_exists( 'ANONY_WPPLUGIN_HELP' ) ) {
	class ANONY_WPPLUGIN_HELP extends ANONY_HELP{
		/**
		 * Check if plugin is active.
		 *
		 * Detect plugin. For use on Front End and Back End.
		 * @var string $path  Path of plugin file
		 */

		static function isActive($path){
			
			$path = wp_normalize_path($path);

			if(in_array($path, apply_filters('active_plugins', get_option('active_plugins')))) return true;
			
			return false;
		}
		
	}
}