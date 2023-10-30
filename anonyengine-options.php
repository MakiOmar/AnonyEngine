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
 * Holds Main plugin options
 *
 * @const
 */
define( 'ANONY_ENGINE_OPTIONS', 'Anony_Engine_Options' );

/**
 * Callback for registering main plugin options and their page.
 */
function anony_engine_options() {

	if ( get_option( ANONY_ENGINE_OPTIONS ) ) {
		$engine_options = ANONY_Options_Model::get_instance( ANONY_ENGINE_OPTIONS );
	}

	// Navigation elements.
	$options_nav = array(
		/** -------------------------- APIs -----------------------------*/
		'anoe-apis' => array(
			'title'    => esc_html__( 'APIs', 'anonyengine' ),
			'sections' => array( 'anoe-apis' ),
		),
	);

	$anoe_sections['anoe-apis'] = array(
		'title'  => esc_html__( 'APIs&IDs', 'anonyengine' ),
		'icon'   => 'x',
		'fields' => array(

			array(
				'id'       => 'enable_google_maps_script',
				'title'    => esc_html__( 'Enable google maps\' script', 'anonyengine' ),
				'type'     => 'switch',
				'desc'     => esc_html__( 'Should be disabled if the script is loaded from other source.', 'anonyengine' ),
				'validate' => 'no_html',

			),
			array(
				'id'       => 'google_maps_api_key',
				'title'    => esc_html__( 'Google maps\' api key', 'anonyengine' ),
				'type'     => 'text',
				'validate' => 'no_html',

			),
		),
	);

	$anoe_options_page['opt_name']      = ANONY_ENGINE_OPTIONS;
	$anoe_options_page['menu_title']    = esc_html__( 'Engine options', 'anonyengine' );
	$anoe_options_page['page_title']    = esc_html__( 'Anonymous engine options', 'anonyengine' );
	$anoe_options_page['menu_slug']     = ANONY_ENGINE_OPTIONS;
	$anoe_options_page['page_cap']      = 'manage_options';
	$anoe_options_page['icon_url']      = 'dashicons-admin-generic';
	$anoe_options_page['page_position'] = 100;
	$anoe_options_page['page_type']     = 'menu';

	new ANONY_Theme_Settings( $options_nav, $anoe_sections, array(), $anoe_options_page );
}


add_action( 'init', 'anony_engine_options' );
