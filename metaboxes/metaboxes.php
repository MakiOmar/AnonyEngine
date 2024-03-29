<?php
/**
 * Metaboxes configuration file.
 *
 * PHP version 7.3 Or Later.
 *
 * @package  AnonyEngine
 * @author   Makiomar <info@makiomar.com>
 * @license  https://makiomar.com AnonyEngine Licence
 * @link     https://makiomar.com/anonyengine
 */

defined( 'ABSPATH' ) || die(); // Exit if accessed direct.

/**
 * Holds a path to metaboxes folder
 *
 * @const
 */
define( 'ANONY_MB_PATH', wp_normalize_path( ANOE_DIR . 'metaboxes/' ) );


/**
 * Holds a URI plugin's folder
 *
 * @const
 */
define( 'ANONY_MB_URI', ANOE_URI . 'metaboxes/' );

/**
 * Holds a URI to main classes folder
 *
 * @const
 */
define( 'ANONY_MB_CLASSES', ANONY_MB_PATH . 'classes/' );


/**
 * Holds a serialized array of all pathes to classes folders
 *
 * @const
 */

$GLOBALS['anoe_metaboxes'] = array();

/**
 * Initialize metaboxes
 */
function anony_init_metaboxes() {
	$metaboxes = apply_filters( 'anony_metaboxes', array() );

	if ( ! is_array( $metaboxes ) || empty( $metaboxes ) ) {
		return;
	}

	foreach ( $metaboxes as $metabox ) {
		new ANONY_Meta_Box( $metabox );
	}
}
add_action( 'init', 'anony_init_metaboxes' );
