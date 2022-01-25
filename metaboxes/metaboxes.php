<?php
/**
 * Holds google map api key
 * @const
 */
define('ANONY_GOOGLE_MAP_API','AIzaSyB6ieIzFEqkoEHM9mP1Iq5bt9HR__F-04E');


/**
 * Holds a path to metaboxes folder
 * @const
 */
define('ANONY_MB_PATH', wp_normalize_path(ANOE_DIR . 'metaboxes/')); 


/**
 * Holds a URI plugin's folder
 * @const
 */
define('ANONY_MB_URI', ANOE_URI . 'metaboxes/');

/**
 * Holds a URI to main classes folder
 * @const
 */
define('ANONY_MB_CLASSES', ANONY_MB_PATH .'classes/' );


/**
 * Holds a serialized array of all pathes to classes folders
 * @const
 */

$GLOBALS['anoe_metaboxes'] = [];

add_action( 'init', function(){

	$metaboxes = apply_filters( 'anony_metaboxes', [] );

	if (!is_array($metaboxes) || empty($metaboxes)) return;

	foreach ($metaboxes as $metabox) {
		$mbObj = new ANONY_Meta_Box($metabox);
	}
});

