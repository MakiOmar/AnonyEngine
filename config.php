<?php
/**
 * AnonyEngine configuration file.
 *
 * PHP version 7.3 Or Later.
 *
 * @package  AnonyEngine
 * @author   Makiomar <info@makiomar.com>
 * @license  GPL-2.0-or-later
 * @link     https://makiomar.com/anonyengine
 * @since    1.0.0
 */

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Define plugin constants.
if ( ! defined( 'ANOENGINE' ) ) {
	define( 'ANOENGINE', '' );
}

if ( ! defined( 'ANOE_URI' ) ) {
	define( 'ANOE_URI', plugin_dir_url( __FILE__ ) );
}

if ( ! defined( 'ANOE_COMMON_CLASSES' ) ) {
	define( 'ANOE_COMMON_CLASSES', wp_normalize_path( ANOE_DIR . 'common-classes/' ) );
}

/**
 * Load required configuration files.
 *
 * @since 1.0.0
 * @return void
 */
function anoe_load_config_files() {
	$required_files = array(
		'functions/helpers.php',
		'functions/ajax.php',
		'forms/forms.php',
		'metaboxes/metaboxes.php',
		'helpme/helpme.php',
		'input-fields/index.php',
		'options/options.php',
		'anonyengine-options.php',
	);

	foreach ( $required_files as $file ) {
		$file_path = wp_normalize_path( ANOE_DIR . $file );
		if ( file_exists( $file_path ) ) {
			require_once $file_path;
		}
	}
}

// Load configuration files.
anoe_load_config_files();

/**
 * Get autoload paths with proper filtering.
 *
 * @since 1.0.0
 * @return array
 */
function anoe_get_autoload_paths() {
	$auto_load = array(
		// Common Classes.
		ANOE_COMMON_CLASSES,
		// Metaboxes.
		defined( 'ANONY_MB_CLASSES' ) ? ANONY_MB_CLASSES : '',
		// Helpers.
		defined( 'ANONY_HLP_PHP' ) ? ANONY_HLP_PHP : '',
		defined( 'ANONY_HLP_WP' ) ? ANONY_HLP_WP : '',
		defined( 'ANOE_HELPER_CLASSES' ) ? ANOE_HELPER_CLASSES : '',
		// Inputs.
		defined( 'ANONY_INPUT_FIELDS' ) ? ANONY_INPUT_FIELDS : '',
		defined( 'ANONY_FIELDS_DIR' ) ? ANONY_FIELDS_DIR : '',
		defined( 'ANONY_FIELDS_URI' ) ? ANONY_FIELDS_URI : '',
		// Options.
		defined( 'ANONY_OPTIONS_DIR' ) ? ANONY_OPTIONS_DIR : '',
		defined( 'ANONY_OPTIONS_FIELDS' ) ? ANONY_OPTIONS_FIELDS : '',
		defined( 'ANONY_OPTIONS_WIDGETS' ) ? ANONY_OPTIONS_WIDGETS : '',
		// Forms.
		defined( 'ANONY_FORMS_CLASSES' ) ? ANONY_FORMS_CLASSES : '',
		defined( 'ANONY_FORMS_ACTIONS' ) ? ANONY_FORMS_ACTIONS : '',
	);

	// Filter out empty values.
	$auto_load = array_filter( $auto_load );

	/**
	 * Filter autoload paths.
	 *
	 * @since 1.0.0
	 * @param array $auto_load Array of autoload paths.
	 */
	return apply_filters( 'anoe_auto_load_paths', $auto_load );
}

// Get autoload paths.
$auto_load = anoe_get_autoload_paths();

// Define autoload paths constant.
if ( ! defined( 'ANOE_AUTOLOADS' ) ) {
	define( 'ANOE_AUTOLOADS', wp_json_encode( $auto_load ) );
}

/**
 * WordPress autoloader for AnonyEngine classes.
 *
 * @since 1.0.0
 * @param string $class_name Class name to autoload.
 * @return void
 */
function anoe_autoloader( $class_name ) {
	// Only handle AnonyEngine classes.
	if ( false === strpos( $class_name, 'ANONY_' ) ) {
		return;
	}

	// Convert class name to file name format.
	$wp_class_name = 'class-' . strtolower( str_replace( '_', '-', $class_name ) );

	// Get autoload paths.
	$autoload_paths = json_decode( ANOE_AUTOLOADS, true );
	if ( ! is_array( $autoload_paths ) ) {
		return;
	}

	// Search for class file in autoload paths.
	foreach ( $autoload_paths as $path ) {
		if ( empty( $path ) ) {
			continue;
		}

		$path = wp_normalize_path( $path );

		// Try direct path first.
		$class_file = $path . '/' . $wp_class_name . '.php';
		if ( file_exists( $class_file ) ) {
			require_once $class_file;
			return;
		}

		// Try subdirectory path.
		$folder_name = strtolower(
			str_replace(
				'_',
				'-',
				str_replace(
					'ANONY_',
					'',
					$class_name
				)
			)
		);

		$class_file = $path . '/' . $folder_name . '/' . $wp_class_name . '.php';
		if ( file_exists( $class_file ) ) {
			require_once $class_file;
			return;
		}
	}
}

// Register autoloader.
spl_autoload_register( 'anoe_autoloader' );
