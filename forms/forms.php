<?php
/**
 * AnonyEngine forms' configuration file.
 *
 * PHP version 7.3 Or Later.
 *
 * @package  AnonyEngine
 * @author   Makiomar <info@makiomar.com>
 * @license  https://makiomar.com AnonyEngine Licence
 * @link     https://makiomar.com/anonyengine
 */

defined( 'ABSPATH' ) || die(); // Exit if accessed directly.


/**
 * Holds a path to forms folder.
 *
 * @const
 */
define( 'ANONY_FORMS_PATH', wp_normalize_path( ANOE_DIR . 'forms/' ) );


/**
 * Holds forms' URI.
 *
 * @const
 */
define( 'ANONY_FORMS_URI', ANOE_URI . 'forms/' );

/**
 * Holds a URI to main classes folder.
 *
 * @const
 */
define( 'ANONY_FORMS_CLASSES', ANONY_FORMS_PATH . 'classes/' );

/**
 * Holds a URI to actions classes folder.
 *
 * @const
 */
define( 'ANONY_FORMS_ACTIONS', ANONY_FORMS_CLASSES . 'actions/' );
