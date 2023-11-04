<?php
/**
 * Theme options functions
 *
 * @package Anonymous theme
 * @author Makiomar
 * @link http://makiomar.com
 */

defined( 'ABSPATH' ) || die();

/**
 * ---------------------------------------------------------------
 * Options configurations
 * -------------------------------------------------------------
 */

/**
 * Holds directory separator
 *
 * @const
 */
define( 'ANONY_DIRS', DIRECTORY_SEPARATOR );

/**
 * Holds options group name
 *
 * @const
 */
define( 'ANONY_OPTIONS', 'Anony_Options' );

/**
 * Holds options folder URI
 *
 * @const
 */
define( 'ANONY_OPTIONS_URI', ANOE_URI . 'options/' );

/**
 * ----------------------------------------------------------------------
 * Options Autoloading
 * ----------------------------------------------------------------------
 */


/**
 * Holds options folder path
 *
 * @const
 */
define( 'ANONY_OPTIONS_DIR', wp_normalize_path( ANOE_DIR . 'options/' ) );

/**
 * Holds options fields folder path
 *
 * @const
 */
define( 'ANONY_OPTIONS_FIELDS', wp_normalize_path( ANOE_DIR . 'options/fields/' ) );

/**
 * Holds options widgets folder path
 *
 * @const
 */
define( 'ANONY_OPTIONS_WIDGETS', wp_normalize_path( ANOE_DIR . 'options/widgets/' ) );

/**
 * ----------------------------------------------------------------------
 * Options functions
 * ----------------------------------------------------------------------
 */

/**
 * Theme Fonts list - system & Google Fonts.
 *
 * @param mixed $type type of font ['system', 'default', 'popular', 'all'].
 * @return array Array of fonts names.
 */
function anony_fonts( $type = false ) {
	$fonts = json_decode( ANOE_FONTS );

	if ( $type ) {
		return $fonts[ $type ];
	} else {
		return $fonts;
	}
}
