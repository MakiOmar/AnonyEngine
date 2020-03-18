<?php
/**
 * Plugin Name: AnonyEngine
 * Plugin URI: https://makiomar.com
 * Description: With AnonyEngine you can add any kind of metaboxes and options pages easily and supper fast
 * Version: 1.0.0
 * Author: Mohammad Omar
 * Author URI: https://makiomar.com
 * Text Domain: anonyengine
 * License: GPL2
*/

/**
 * Holds plugin PATH
 * @const
 */ 
define('ANOE_DIR', wp_normalize_path(plugin_dir_path( __FILE__ )));

require_once (wp_normalize_path( ANOE_DIR . 'config.php' ));