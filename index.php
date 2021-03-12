<?php
/**
 * Plugin Name: Diwanjobs auto poster
 * Description: With Diwanjobs auto poster you can grow your search ranking by auto posting in specific niches
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
		.wp-person a:focus .gravatar, a:focus, a:focus .media-icon img {
	box-shadow: none;
	outline: none;
}
		.diwan-page-sections{
			padding: 20px;
			background-color: #fff;
			border-radius: 25px;
			margin: 10px;
		}
		.diwan-page-section{
			margin: 10px;
			width: 48%;
			display: inline-block;
		}
		.diwan-page-section h1{
			color: #797979;
			border-bottom: 1px solid #d2d2d2;
			padding: 10px;
		}
		.diwan-page-section ul{
			display: flex
		}
		.diwan-page-section ul li a{
			text-decoration:none;
			text-align: center;
			color:#fff;
			font-weight:bold;
			display: flex;
			height: 100%;
			width: 100%;
			justify-content: center;
			align-items: center;
			padding: 10px;
		}
		.diwan-page-section ul li{
			display: inline-flex;
			margin: 10px;
			height: 80px;
			width: 120px;	
			justify-content: center;
			align-items: center;
			border-radius:10px;
			-webkit-box-shadow: 0px 0px 5px 0px rgba(168,168,168,1);
			-moz-box-shadow: 0px 0px 5px 0px rgba(168,168,168,1);
			box-shadow: 0px 0px 5px 0px rgba(168,168,168,1);
		}
		.diwan-page-section ul li:nth-child(odd){
			background-color: #d74106;
		}
		.diwan-page-section ul li:nth-child(even){
			background-color: #444;
		}
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
		'id'       => 'diwanjobs_keyword_meta',
		'taxonomy' => 'keyword_category',
		'context'  => 'term',
		'fields'   => 
			[
				[
					'id' => 'gallery',
					'title'    => esc_html__( 'Keyword gallery', ANOE_TEXTDOM ),
					'type'     => 'gallery',
				]
			],
	]
);

add_action( 'admin_head', function() {
	// check user permissions
    if ( !current_user_can( 'edit_posts' ) &&  !current_user_can( 'edit_pages' ) ) return;
    // check if WYSIWYG is enabled
    if ( 'true' == get_user_option( 'rich_editing' ) ) {           
    add_filter( "mce_external_plugins", "diwan_add_buttons" );
    add_filter( 'mce_buttons', 'diwan_register_buttons' );
	}
} );

function diwan_add_buttons( $plugin_array ) {
    $plugin_array['keywords'] = ANOE_URI . 'assets/js/tinymce.js?ver=' . time();
    return $plugin_array;
}
function diwan_register_buttons( $buttons ) {
	$mybuttons = array('patterns_menu');
	foreach($mybuttons as $b){
		$buttons[] = $b;
	}
    return $buttons;
}

add_action('wp_footer', function(){
	if(!current_user_can('administrator')) return;
	//nvd(get_option('diwan_alts_main_store'));
});

add_action('admin_head', function(){?>
	<style>
		#keywords-editor {
  font-family: Arial, Helvetica, sans-serif;
  border-collapse: collapse;
  width: 100%;
}

#keywords-editor td, #keywords-editor th {
  border: 1px solid #ddd;
  padding: 8px;
}

#keywords-editor td{
	max-width: 120px;
}

#keywords-editor td input{
	max-width: 100%;
}

#keywords-editor tr:nth-child(even){background-color: #f2f2f2;}

#keywords-editor tr:hover {background-color: #ddd;}

#keywords-editor th {
  padding-top: 12px;
  padding-bottom: 12px;
  text-align: center;
  background-color: #4CAF50;
  color: white;
}
.delete-keyword{
	color: red
}
	</style>
}
<?php });