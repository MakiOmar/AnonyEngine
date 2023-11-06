<?php
/**
 * Ajax handler
 *
 * @package AnonyEngine
 */

defined( 'ABSPATH' ) || die(); // Exit if accessed directly.

add_action( 'wp_ajax_get_term_children_options', 'anony_get_term_children_options' );
add_action( 'wp_ajax_nopriv_get_term_children_options', 'anony_get_term_children_options' );

/**
 * Render term children options
 *
 * @return void
 */
function anony_get_term_children_options() {
	$html = '';
	if ( ! empty( $_POST ) ) {
		//phpcs:disable WordPress.Security.NonceVerification.Missing
		$data = $_POST;
		//phpcs:enable.
		if ( ! isset( $data['nonce'] ) || ! wp_verify_nonce( $data['nonce'], 'term-children' ) ) {
			die();
		}
		if ( ! ANONY_HELP::empty( $data['taxonomy'], $data['term_id'], $data['target'] ) ) {
			$terms = get_terms(
				array(
					'taxonomy'   => wp_strip_all_tags( $data['taxonomy'] ),
					'fields'     => 'id=>name',
					'hide_empty' => false,
					'parent'     => absint( wp_strip_all_tags( $data['term_id'] ) ),
				)
			);
			if ( is_array( $terms ) && ! empty( $terms ) ) {
				ob_start();
				foreach ( $terms as $id => $name ) {
					?>
						<option value="<?php echo esc_attr( $id ); ?>"><?php echo esc_html( $name ); ?></option>
					<?php

				}

				$html .= ob_get_clean();
			}
		}
	}
	// Make your array as json.
	wp_send_json(
		array(
			'html'        => $html,
			'firstOption' => apply_filters( $data['target'] . '_first_option', esc_html__( 'Select option', 'anonyengine' ) ),
		)
	);
	// Don't forget to stop execution afterward.
	die();
}
