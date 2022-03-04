<?php

/**
 * Holds plugin text domain
 * @const
 */
define('ANOENGINE', '');

/**
 * Holds plugin uri
 * @const
 */
define('ANOE_URI', plugin_dir_url( __FILE__ ));

/**
 * Holds common classes path
 * @const
 */
define('ANOE_COMMON_CLASSES', wp_normalize_path(ANOE_DIR . 'common-classes/'));

/*----------------------required sub-configs----------------*/

require_once( wp_normalize_path( ANOE_FUNC_DIR . 'post.php' ) );
require_once( wp_normalize_path( ANOE_DIR . 'forms/forms.php' ) );
require_once( wp_normalize_path( ANOE_DIR . 'metaboxes/metaboxes.php' ) );
require_once( wp_normalize_path( ANOE_DIR . 'helpme/helpme.php') );
require_once( wp_normalize_path( ANOE_DIR . 'input-fields/index.php') );
require_once( wp_normalize_path( ANOE_DIR . 'options/options.php') );
//require_once( wp_normalize_path( ANOE_DIR . 'utilities/utilities.php') );



/*----------------------Autoloading -------------------------*/

// Holds autoload classes' paths.
$auto_load = apply_filters( 'anoe_auto_load', array(
	/*----Common Classes-----------*/
	ANOE_COMMON_CLASSES,
	/*----Metaboxes-----------*/
	ANONY_MB_CLASSES,
	/*----Helpers-----------*/
	ANONY_HLP_PHP,
	ANONY_HLP_WP,
	/*----Inputs-----------*/
	ANONY_INPUT_FIELDS,
	ANONY_FIELDS_DIR,
	ANONY_FIELDS_URI,
	/*----Options-----------*/
	ANONY_OPTIONS_DIR , 
	ANONY_OPTIONS_FIELDS, 
	ANONY_OPTIONS_WIDGETS,
	ANONY_INPUT_FIELDS,
	/*-----Forms-----*/
	ANONY_FORMS_CLASSES,
	
));

/**
 * Holds serialized autoload paths
 * @const
 */
define('ANOE_AUTOLOADS' , wp_json_encode($auto_load));


/*
*Classes Auto loader
*/
spl_autoload_register( function ( $class_name ) {

	if ( false !== strpos( $class_name, 'ANONY_' )) {

		$class_name = preg_replace('/ANONY_/', '', $class_name);

		$class_name  = strtolower(str_replace('_', '-', $class_name));
		
		$class_file = $class_name .'.php';
		
		if(file_exists($class_file)){

			require_once($class_file);
		}else{
			foreach(json_decode( ANOE_AUTOLOADS ) as $path){

				$class_file = wp_normalize_path($path) .$class_name . '.php';

				if(file_exists($class_file)){

					require_once($class_file);
				}else{

					$class_file = wp_normalize_path($path) .$class_name .'/' .$class_name . '.php';

					if(file_exists($class_file)){

						require_once($class_file);
					}
				}
			}
		}
		
	}
} );

if (!function_exists('nvd')) {
	function nvd($arr){
		echo '<pre dir="ltr">';
			var_dump($arr);
		echo '</pre>';
	}
}


