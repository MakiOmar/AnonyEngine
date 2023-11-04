<?php
/**
 * Utilitis
 *
 * @package AnonyEngine
 */

defined( 'ABSPATH' ) || die();

define( 'ANOE_UTLS_DIR', ANOE_DIR . 'utilities/' );
define( 'ANOE_UTLS_URI', ANOE_URI . 'utilities/' );

require_once wp_normalize_path( ANOE_UTLS_DIR . 'share-by-email/share-data.php' );
