<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}
/**
 * Meta boxes registration
 *
 * @package Anonymous Meta box
 * @author Makiomar
 * @link http://makiomar.com
 */


//Array of metaboxes to register
add_filter('anony_metaboxes', function($metaboxes){
	$metaboxes[] = 
		[
			'id'            => 'diwanjobs_keyword_gallery',//Meta box ID
			'title'         => esc_html__( 'Keyword gallery', ANOE_TEXTDOM ),
			'context'       => 'normal',
			'priority'      =>  'high', // high|low
	        'hook_priority' =>  '10', // Default 10
			'post_type'     => array('keyword'),
			'fields'        => 
				[
					[
						'id'       => 'shift8_portfolio_gallery',
						'title'    => esc_html__( 'Keyword gallery', ANOE_TEXTDOM ),
						'type'     => 'gallery',
						//'validate' => 'url',
					]
				]
		];

	return $metaboxes;
});