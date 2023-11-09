<?php
/**
 * AnonyEngine Helpers
 *
 * @package AnonyEngine
 */

defined( 'ABSPATH' ) || die(); // Exit if accessed directly.

/**
 * Enqueue scripts
 *
 * @return void
 */
function anony_enqueue_styles() {
	wp_enqueue_style(
		'anony-metaboxs',
		ANOE_URI . 'metaboxes/assets/css/metaboxes.css',
		false,
        // phpcs:disable WordPress.WP.EnqueuedResourceParameters.MissingVersion
		time()
        // phpcs:enable.
	);

	wp_enqueue_style( 'anony-inputs', wp_normalize_path( ANOE_URI . 'input-fields/' ) . 'assets/css/inputs-fields.css', array(), time(), 'all' );

	if ( is_rtl() ) {
		wp_enqueue_style( 'anony-inputs-rtl', wp_normalize_path( ANOE_URI . 'input-fields/' ) . 'assets/css/inputs-fields-rtl.css', array( 'anony-inputs' ), time(), 'all' );
	}
}

/**
 * Load footer_scripts
 *
 * @return void
 */
function anony_init_map_cb() {
	?>
	<script>
		if ( typeof initMap !== 'function' ) {
			function initMap(){
				console.log('%cGoogle map api has been called for a location field', 'color: green');
			}
		}
	</script>
	<?php
}

/**
 * Load head scripts
 *
 * @return void
 */
function anony_head_scripts() {
	anony_init_map_cb();
}
