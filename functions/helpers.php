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

/**
 * Unset gallery item
 *
 * @param string $gallery_items Comma separated attachments' IDs.
 * @param mixed  $attachment_id To be un set attachment ID. int or string.
 * @return void
 */
function anony_unset_gallery_item( &$gallery_items, $attachment_id ) {
	$attachment_id = absint( $attachment_id );
	if ( $gallery_items && ! empty( $gallery_items ) ) {
		$attachments_ids = array_map( 'absint', array_filter( explode( ',', $gallery_items ) ) );
		if ( in_array( $attachment_id, $attachments_ids, true ) ) {
			$index = array_search( $attachment_id, $attachments_ids, true );

			if ( false !== $index ) {
				unset( $attachments_ids[ $index ] );
			}
		}

		$gallery_items = implode( ',', $attachments_ids );
	}
}