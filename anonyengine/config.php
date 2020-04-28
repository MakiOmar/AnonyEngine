<?php

/**
 * Holds plugin text domain
 * @const
 */
define('ANOENGINE', '');

/**
 * Holds plugin text domain
 * @const
 */
define('ANOE_TEXTDOM', 'anonyengine');


/**
 * Holds plugin uri
 * @const
 */
define('ANOE_URI', plugin_dir_url( __FILE__ ));

/*----------------------required sub-configs----------------*/

require_once( ANOE_DIR . 'metaboxes/metaboxes.php');
require_once( ANOE_DIR . 'helpme/helpme.php');
require_once( ANOE_DIR . 'input-fields/index.php');
require_once( ANOE_DIR . 'options/options.php');



/*----------------------Autoloading -------------------------*/

define('ANOE_AUTOLOADS' ,serialize(array(
	/*----Metaboxes-----------*/
	ANONY_MB_FIELDS,
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
	ANONY_INPUT_FIELDS
)));


/*
*Classes Auto loader
*/
spl_autoload_register( function ( $class_name ) {

	if ( false !== strpos( $class_name, 'ANONY_' )) {

		$class_name = preg_replace('/ANONY_/', '', $class_name);

		$class_name  = strtolower(str_replace('_', '-', $class_name));

		if(file_exists($class_name)){

			require_once($class_name);
		}else{
			foreach(unserialize( ANOE_AUTOLOADS ) as $path){

				$class_file = wp_normalize_path($path) .$class_name . '.php';

				//var_dump($class_name.': '. $class_file.'<br/>') ;

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

