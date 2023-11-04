<?php
/**
 * Ajax calls prossecing
 *
 * @package AnonyEngine
 */

defined( 'ABSPATH' ) || die();

// Share by email.
add_action( 'wp_ajax_nopriv_anony_share_by_email', 'anony_share_by_email' );
add_action( 'wp_ajax_anony_share_by_email', 'anony_share_by_email' );

/**
 * Share by email ajax calback
 *
 * @return void
 */
function anony_share_by_email() {
	if ( empty( $_POST ) ) {
		die();
	}
	// Check for nonce security.
	if ( isset( $_POST['nonce'] ) && ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), 'anony_share_by_email_nonce' ) ) {
		die( 'Busted!' );
	}
	$posted = wp_unslash( $_POST );

	if ( ! isset( $posted['anony_share_email'] ) || empty( $posted['anony_share_email'] ) ) {
		return;
	}

	if ( ! is_email( sanitize_email( $posted['anony_share_email'] ) ) ) {

		wp_send_json(
			array(
				'error'  => 'no_email',
				'report' => esc_html__( 'Please make sure you have entered a valid email address', 'anonyengine' ),
			)
		);
		die();
	}

	$check_dir = ( function_exists( 'is_rtl' ) && is_rtl() );

	$dir = $check_dir ? 'rtl' : 'ltr';

	$align = $check_dir ? 'right' : 'left';

	ob_start();
	include wp_normalize_path( ANOE_UTLS_DIR . 'share-by-email/share-template.php' );
	$body = ob_get_contents();
	ob_end_clean();

	$to      = $posted['anony_share_email'];
	$subject = esc_html__( 'anony Real Estate', 'anonyengine' );

	$email_sent = wp_mail( $to, $subject, $body, array( 'Content-Type: text/html; charset=UTF-8' ) );

	if ( ! $email_sent ) {

		wp_send_json(
			array(
				'error'  => 'not_sent',
				'report' => esc_html__( 'Email can\'t be sent', 'anonyengine' ),
			)
		);
		die();
	}

	wp_send_json(
		array(
			'msg'    => 'email_sent',
			'report' => esc_html__( 'Email has been be sent successfully', 'anonyengine' ),
		)
	);
	die();
}
