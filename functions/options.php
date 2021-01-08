<?php
/**
 * UC options fields and navigation
 *
 * @package Anonymous theme
 * @author Makiomar
 * @link http://makiomar.com
 */

if(get_option('Diwan_Options')){
	$diwanOptions = ANONY_Options_Model::get_instance('Diwan_Options');
}

// Navigation elements
$options_nav = array(
	// General --------------------------------------------
	'diwan_options' => array(
		'title' => esc_html__('Diwan options', ANOE_TEXTDOM),
		'sections' => array('general-options'),
	),
);

$anoucsections['general-options']= array(
		'title' => esc_html__('General options', ANOE_TEXTDOM),
		'icon' => 'x',
		'fields' => array(
						array(
							'id'      => 'activate_publish',
							'title'   => esc_html__('Publish active', ANOE_TEXTDOM),
							'type'    => 'switch',
							'validate'=> 'no_html',
							'desc'    => esc_html('If checked, it will start publishing posts with cron job', ANOE_TEXTDOM),
							
						),
						
						array(
							'id'      => 'test_mode',
							'title'   => esc_html__('Test mode', ANOE_TEXTDOM),
							'type'    => 'switch',
							'validate'=> 'no_html',
							'desc'    => esc_html('If checked, it will publish contents to a none visible/not indexed post type (diwan_test)', ANOE_TEXTDOM),
							
						),						
					)
);


$diwanOptionsPage['opt_name'] = 'Diwan_Options';		
$diwanOptionsPage['menu_title'] = esc_html__('Auto-poster Options', ANOE_TEXTDOM);
$diwanOptionsPage['page_title'] = esc_html__('Auto-poster Options', ANOE_TEXTDOM);
$diwanOptionsPage['menu_slug'] = 'Diwan_Options';
$diwanOptionsPage['page_cap'] = 'manage_options';
$diwanOptionsPage['icon_url'] = 'dashicons-admin-settings';
$diwanOptionsPage['page_position'] = 5;
$diwanOptionsPage['page_type'] = 'submenu';
$diwanOptionsPage['parent_slug'] = 'options-general.php';



$Diwan_Options_Page = new ANONY_Theme_Settings( $options_nav, $anoucsections, [], $diwanOptionsPage);

$diwanOptions = ANONY_Options_Model::get_instance('Diwan_Options');
