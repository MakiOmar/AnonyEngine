<?php
/**
 * Plugin Name: AnonyEngine
 * Plugin URI: https://makiomar.com
 * Description: With AnonyEngine you can add any kind of metaboxes and options pages and forms easily and supper fast
 * Version: 1.0.0161
 *
 * @package  AnonyEngine
 * Author: Mohammad Omar
 * Author URI: https://makiomar.com
 * Text Domain: anonyengine
 * License: GPL2
 */

defined( 'ABSPATH' ) || die(); // Exit if accessed direct.

/**
 * Holds plugin's slug
 *
 * @const
 */
define( 'ANOE_PLUGIN_SLUG', plugin_basename(__FILE__) );

/**
 * Holds plugin PATH
 *
 * @const
 */
define( 'ANOE_DIR', wp_normalize_path( plugin_dir_path( __FILE__ ) ) );

/**
 * Holds libraries directory
 *
 * @const
 */
define( 'ANOE_LIBS_URI', ANOE_DIR . 'libs/' );

/**
 * Holds functions directory
 *
 * @const
 */
define( 'ANOE_FUNC_DIR', ANOE_DIR . 'functions/' );


require ANOE_DIR . 'vendor/autoload.php';

require ANOE_DIR . 'plugin-update-checker/plugin-update-checker.php';

require_once ANOE_LIBS_URI . 'fonts.php';

require_once wp_normalize_path( ANOE_DIR . 'config.php' );

$anonyengine_update_checker = Puc_v4_Factory::buildUpdateChecker(
    'https://github.com/MakiOmar/AnonyEngine/',
    __FILE__,
    ANOE_PLUGIN_SLUG
);

//Set the branch that contains the stable release.
$anonyengine_update_checker->setBranch('master');

/**
 * Enqueue admin/frontend common scripts.
 *
 * Looping through custom arrays of styles/scripts, and consider using filemtime
 */
function anony_common_scripts() {

	$scripts = array( 'jquery.helpme' );
	foreach ( $scripts as $script ) {
		wp_enqueue_script( $script, ANOE_URI . 'assets/js/' . $script . '.js', array( 'jquery' ), filemtime( wp_normalize_path( ANOE_DIR . '/assets/js/' . $script . '.js' ) ), true );
	}

	// Register styles.
	$styles_libs = array( 'jquery.ui.slider-rtl' );

	$styles = array( 'responsive', 'anonyengine' );

	$styles = array_merge( $styles, $styles_libs );

	foreach ( $styles as $style ) {

		$handle = in_array( $style, $styles_libs, true ) ? $style : 'anony-' . $style;

		wp_enqueue_style( $handle, ANOE_URI . 'assets/css/' . $style . '.css', false, filemtime( wp_normalize_path( ANOE_DIR . 'assets/css/' . $style . '.css' ) ) );
	}

	// equeue scripts.
	$scripts = array( 'jquery.ui.slider-rtl.min' );
	foreach ( $scripts as $script ) {
		wp_register_style( $script, ANOE_URI . 'assets/js/' . $script . '.js', array( 'jquery', 'jquery-ui-slider' ), filemtime( wp_normalize_path( ANOE_DIR . '/assets/js/' . $script . '.js' ) ), true );
	}
}

/**
 * AnonyEngine common scripts.
 */
// add_action( 'wp_enqueue_scripts', 'anony_common_scripts' );
add_action( 'admin_enqueue_scripts', 'anony_common_scripts' );


add_action(
	'activated_plugin',
	function() {
		flush_rewrite_rules();
	}
);


add_filter(
	'plugin_action_links_' . plugin_basename( __FILE__ ),
	function ( $links ) {
			$links[] = sprintf(
				'<a href="admin.php?page=Anony_Engine_Options">%s</a>',
				esc_html__( 'Settings', 'anonyengine' )
			);
			return $links;
	}
);


/**
 * Load plugin textdomain.
 *
 * @since 1.0.0
 */
add_action(
	'plugins_loaded',
	function() {
		load_plugin_textdomain( 'anonyengine', false, basename( dirname( __FILE__ ) ) . '/languages' );
	}
);

// Register Post Types.
add_action( 'init', array ( 'ANONY_Post_Help', 'register_post_types') );

// Register Taxonomies.
add_action( 'init', array ( 'ANONY_Taxonomy_Help', 'register_taxonomies' ));

do_action('anonyengine_loaded');