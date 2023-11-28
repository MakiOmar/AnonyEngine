<?php
/**
 * Helpers config.
 *
 * PHP version 7.3 Or Later.
 *
 * @package  AnonyEngine.
 * @author   Makiomar <info@makiomar.com>.
 * @license  https:// makiomar.com AnonyEngine Licence.
 * @link     https:// makiomar.com/anonyengine
 */

defined( 'ABSPATH' ) || die(); // Exit if accessed direct.

/**
 * Holds a URI to Custom fields classes folder.
 *
 * @const
 */
define( 'ANONY_HLP_PATH', wp_normalize_path( ANOE_DIR . 'helpme/' ) );

/**
 * Holds a path plugin's folder.
 *
 * @const
 */
define( 'ANONY_HLP_URI', ANOE_URI . 'helpme/' );


/**
 * Holds a URI to main classes folder.
 *
 * @const
 */
define( 'ANONY_HLP_PHP', wp_normalize_path( ANONY_HLP_PATH . 'php/' ) );

/**
 * Holds a URI to Custom fields classes folder.
 *
 * @const
 */
define( 'ANONY_HLP_WP', wp_normalize_path( ANONY_HLP_PATH . 'wp/' ) );

/**
 * Holds helper classes path.
 *
 * @const
 */
define( 'ANOE_HELPER_CLASSES', wp_normalize_path( ANONY_HLP_PATH . 'helper-classes/' ) );

//require_once 'wp-hooks.php';
