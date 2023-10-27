<?php

defined( 'ABSPATH' ) || die(); // Exit if accessed directly.


function anony_enqueue_styles(){
    wp_enqueue_style(
        'anony-metaboxs',
        ANOE_URI . 'metaboxes/assets/css/metaboxes.css',
        false,
        // phpcs:disable WordPress.WP.EnqueuedResourceParameters.MissingVersion
        time()
        // phpcs:enable.
    );

    wp_enqueue_style( 'anony-inputs',  wp_normalize_path( ANOE_URI . 'input-fields/' ) . 'assets/css/inputs-fields.css', array(), time(), 'all' );


    if ( is_rtl() ) {
        wp_enqueue_style( 'anony-inputs-rtl',  wp_normalize_path( ANOE_URI . 'input-fields/' ) . 'assets/css/inputs-fields-rtl.css', array( 'anony-inputs' ), time(), 'all' );
    }
}