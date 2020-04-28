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

/**
 * Holds libraries directory
 * @const
 */
define('ANOE_LIBS_URI', ANOE_DIR . 'libs/');


require_once( ANOE_LIBS_URI . 'fonts.php');

require_once (wp_normalize_path( ANOE_DIR . 'config.php' ));

/**
 * Enqueue admin/frontend common scripts.
 * 
 * Looping through custom arrays of styles/scripts, and consider using filemtime
 */
function anonyCommonScripts(){

	//Enqueue styles
	$styles = ['anonyengine'];
	foreach($styles as $style){
		wp_enqueue_style( $style , ANOE_URI .'assets/css/'.$style.'.css' , false, filemtime(wp_normalize_path(ANOE_DIR .'assets/css/'.$style.'.css')) );
	}


	$scripts = ['jquery.helpme'];
	foreach($scripts as $script){
		wp_enqueue_script( $script , ANOE_URI . 'assets/js/'.$script.'.js' ,array('jquery'),filemtime(wp_normalize_path( ANOE_DIR .'/assets/js/'.$script.'.js')),true);
	}

	/**
	 * Register styles/Scripts
	 */ 

	//Register styles
	$styles = ['jquery.ui.slider-rtl'];
	foreach($styles as $style){
		wp_register_style( $style , ANOE_URI .'assets/css/'.$style.'.css' , false, filemtime(wp_normalize_path(ANOE_DIR .'assets/css/'.$style.'.css')) );
	}

	//Register scripts
	$scripts = ['jquery.ui.slider-rtl.min'];
	foreach($scripts as $script){
		wp_enqueue_script( $script , ANOE_URI . 'assets/js/'.$script.'.js' ,array('jquery', 'jquery-ui-slider'),filemtime(wp_normalize_path( ANOE_DIR .'/assets/js/'.$script.'.js')),true);
	}



}

/**
 * AnonyEngine common scripts
 */
add_action('wp_enqueue_scripts','anonyCommonScripts');
add_action('admin_enqueue_scripts','anonyCommonScripts');


add_action( 'activated_plugin', function(){
	flush_rewrite_rules();
} );