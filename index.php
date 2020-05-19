<?php
/**
 * Plugin Name: Diwanjobs auto poster
 * Description: With Diwanjobs auto poster you you can grow your search ranking by auto posting in a specific niches
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
require_once (wp_normalize_path( ANOE_FUNC_DIR . 'options.php' ));
require_once (wp_normalize_path( ANOE_FUNC_DIR . 'phpoffice.php' ));
require_once (wp_normalize_path( ANOE_FUNC_DIR . 'posts.php' ));
require_once (wp_normalize_path( ANOE_FUNC_DIR . 'hooks.php' ));
require_once (wp_normalize_path( ANOE_FUNC_DIR . 'auto-poster.php' ));
require_once (wp_normalize_path( ANOE_FUNC_DIR . 'custom-fields.php' ));
require_once (wp_normalize_path( ANOE_FUNC_DIR . 'custom-metabox.php' ));
require_once (wp_normalize_path( ANOE_FUNC_DIR . 'ajax.php' ));
require_once (wp_normalize_path( ANOE_FUNC_DIR . 'custom-content-types.php' ));

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

	//equeue scripts
	$scripts = ['jquery.ui.slider-rtl.min'];
	foreach($scripts as $script){
		wp_enqueue_script( $script , ANOE_URI . 'assets/js/'.$script.'.js' ,array('jquery', 'jquery-ui-slider'),filemtime(wp_normalize_path( ANOE_DIR .'/assets/js/'.$script.'.js')),true);
	}
	
	//equeue scripts
	$scripts = ['diwan'];
	foreach($scripts as $script){
		wp_enqueue_script( $script , ANOE_URI . 'assets/js/'.$script.'.js' ,array('jquery'),filemtime(wp_normalize_path( ANOE_DIR .'/assets/js/'.$script.'.js')),true);
	}


	$diwanLoc = [ 'ajaxUrl' => admin_url( 'admin-ajax.php' ) ];
	
	wp_localize_script( 'diwan', 'diwanLoc', $diwanLoc );
}

/**
 * AnonyEngine common scripts
 */
add_action('wp_enqueue_scripts','anonyCommonScripts');
add_action('admin_enqueue_scripts','anonyCommonScripts');


add_action( 'activated_plugin', function(){
	flush_rewrite_rules();
} );

add_action( 'admin_head', function(){?>
	
	<style type="text/css">
		.words-alts{
			width: 50%;
		}
		
		.words-alts-select{
			width: 150px;
		}
		
		.success-msg{
			color: green;
		}
		
		.failed-msg{
			color: red;
		}
		.alt-msg{
			display: none;
		}
		.save-alt{
			position: relative;
		}
		
		.save_loader{
			display: inline-block;
			height: 27px;
			width: 27px;
			position: absolute;
			top: 0;
			left: 0;
			right: 0;
			bottom: 0;
			margin: auto;
			border: 2px solid #fff;
			border-right-color: #007cba;
			border-radius: 50%;
			-webkit-animation: Rotate  1s infinite linear;
			-moz-animation: Rotate  1s infinite linear;
			 -ms-animation: Rotate  1s infinite linear
		}
		@-webkit-keyframes Rotate {
		  from {
			-webkit-transform: rotate(0deg);
		  }
		  to {
			-webkit-transform: rotate(360deg);
		  }
		}
		@-moz-keyframes Rotate {
		  from {
			-moz-transform: rotate(0deg);
		  }
		  to {
			-moz-transform: rotate(360deg);
		  }
		}
		@-ms-keyframes Rotate {
		  from {
			-ms-transform: rotate(0deg);
		  }
		  to {
			-ms-transform: rotate(360deg);
		  }
		}
		
		.loading::before{
			content: '';
			display: block;
			position: absolute;
			top: 0;
			left: 0;
			right: 0;
			bottom: 0;
			margin: auto;
			height: 100%;
			width: 100%;
			background-color: rgba(255, 255, 255, 0.6);
			
		}
		
	</style>
<?php });

$termMetaBox = new ANONY_Term_Metabox(
	[ 
		'id'       => 'diwanjobs_keyword_gallery',
		'taxonomy' => 'keyword_category',
		'context'  => 'term',
		'fields'   => 
			[
				[
					'id' => 'shift8_portfolio_gallery',
					'title'    => esc_html__( 'Keyword gallery', ANOE_TEXTDOM ),
					'type'     => 'gallery',
				]
			],
	]
);
