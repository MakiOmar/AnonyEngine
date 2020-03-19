<?php
/**
 * Theme options functions
 *
 * @package Anonymous theme
 * @author Makiomar
 * @link http://makiomar.com
 */

/*---------------------------------------------------------------
 * Options configurations
 *-------------------------------------------------------------*/

/**
 * Holds directory separator
 * @const
 */
define('ANONY_DIRS', DIRECTORY_SEPARATOR );

/**
 * Holds options group name
 * @const
 */
define('ANONY_OPTIONS', "Anony_Options");

/**
 * Holds options folder URI
 * @const
 */
define('ANONY_OPTIONS_URI', ANOE_URI . "options/");

/*----------------------------------------------------------------------
* Options Autoloading
*---------------------------------------------------------------------*/


/**
 * Holds options folder path
 * @const
 */
define('ANONY_OPTIONS_DIR', wp_normalize_path(ANOE_DIR . "options/"));

/**
 * Holds options fields folder path
 * @const
 */
define('ANONY_OPTIONS_FIELDS', wp_normalize_path(ANOE_DIR . "options/fields/"));

/**
 * Holds options widgets folder path
 * @const
 */
define('ANONY_OPTIONS_WIDGETS', wp_normalize_path(ANOE_DIR . "options/widgets/"));


/*----------------------------------------------------------------------------------
*Options functions
*---------------------------------------------------------------------------------*/

/**
 * Theme Fonts list - system & Google Fonts.
 * @param mixed $type type of font ['system', 'default', 'popular', 'all']
 * @return array Array of fonts names
 */
function anony_fonts( $type = false ){
	$fonts = unserialize(ANOE_FONTS);
	
	if( $type ) {
		return $fonts[$type];
	} else {
		return $fonts;
	}
}


//controls add query strings to scripts/styles
function anony_control_query_strings($src, $handle){
	if(is_admin()) return $src;

	$anonyOptions = ANONY_Options_Model::get_instance();
	
	//Keep query string for these items
	$neglected = array();
	
	if(!empty($anonyOptions->keep_query_string)){
		$neglected = explode(',',$anonyOptions->keep_query_string);
	}
	
	if($anonyOptions->query_string != '0' && !in_array( $handle, $neglected )){
		$src = remove_query_arg('ver', $src);
	}
	return $src;
	
}


//controls add query strings to scripts
add_filter( 'script_loader_src', 'anony_control_query_strings', 15, 2 );

//controls add query strings to styles
add_filter( 'style_loader_src', 'anony_control_query_strings', 15, 2);