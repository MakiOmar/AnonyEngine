<?php
if ( !defined('ABSPATH') ) exit();
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

/**
 * Holds libraries directory
 * @const
 */
define('ANOE_LIBS_URI', ANOE_DIR . 'libs/');

/**
 * Holds functions directory
 * @const
 */
define('ANOE_FUNC_DIR', ANOE_DIR . 'functions/');


require ANOE_DIR.'vendor/autoload.php';


require_once( ANOE_LIBS_URI . 'fonts.php');

require_once (wp_normalize_path( ANOE_DIR . 'config.php' ));
require_once (wp_normalize_path( ANOE_FUNC_DIR . 'phpoffice.php' ));

/**
 * Enqueue admin/frontend common scripts.
 * 
 * Looping through custom arrays of styles/scripts, and consider using filemtime
 */
function anonyCommonScripts(){

	

	$scripts = ['jquery.helpme'];
	foreach($scripts as $script){
		wp_enqueue_script( $script , ANOE_URI . 'assets/js/'.$script.'.js' ,array('jquery'),filemtime(wp_normalize_path( ANOE_DIR .'/assets/js/'.$script.'.js')),true);
	}

	//Register styles
	$styles = ['jquery.ui.slider-rtl'];
	foreach($styles as $style){
		wp_register_style( $style , ANOE_URI .'assets/css/'.$style.'.css' , false, filemtime(wp_normalize_path(ANOE_DIR .'assets/css/'.$style.'.css')) );
	}

	//equeue scripts
	$scripts = ['jquery.ui.slider-rtl.min'];
	foreach($scripts as $script){
		wp_register_style( $script , ANOE_URI . 'assets/js/'.$script.'.js' ,array('jquery', 'jquery-ui-slider'),filemtime(wp_normalize_path( ANOE_DIR .'/assets/js/'.$script.'.js')),true);
	}
}

/**
 * AnonyEngine common scripts
 */
add_action('wp_enqueue_scripts','anonyCommonScripts');
add_action('admin_enqueue_scripts','anonyCommonScripts');
add_action('admin_enqueue_scripts',function(){
	//Enqueue styles
	$styles = ['anonyengine'];
	foreach($styles as $style){
		wp_enqueue_style( $style , ANOE_URI .'assets/css/'.$style.'.css' , false, filemtime(wp_normalize_path(ANOE_DIR .'assets/css/'.$style.'.css')) );
	}

});


add_action( 'activated_plugin', function(){
	flush_rewrite_rules();
} );