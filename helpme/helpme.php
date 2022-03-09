<?php
/**
 * Holds a URI to Custom fields classes folder
 *
 * @const
 */
define( 'ANONY_HLP_PATH', wp_normalize_path( ANOE_DIR . 'helpme/' ) );

/**
 * Holds a path plugin's folder
 *
 * @const
 */
define( 'ANONY_HLP_URI', ANOE_URI . 'helpme/' );


/**
 * Holds a URI to main classes folder
 *
 * @const
 */
define( 'ANONY_HLP_PHP', ANONY_HLP_PATH . 'php/' );

/**
 * Holds a URI to Custom fields classes folder
 *
 * @const
 */
define( 'ANONY_HLP_WP', ANONY_HLP_PATH . 'wp/' );


require_once 'wp-hooks.php';
