<?php
/**
 * AnonyEngine Helpers
 *
 * @package AnonyEngine
 * @since 1.0.0
 */

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Enqueue AnonyEngine styles.
 *
 * @since 1.0.0
 * @return void
 */
function anoe_enqueue_styles() {
	// Enqueue metaboxes styles.
	$metaboxes_css_path = ANOE_DIR . 'metaboxes/assets/css/metaboxes.css';
	if ( file_exists( $metaboxes_css_path ) ) {
		wp_enqueue_style(
			'anony-metaboxs',
			ANOE_URI . 'metaboxes/assets/css/metaboxes.css',
			array(),
			filemtime( $metaboxes_css_path )
		);
	}

	// Enqueue input fields styles.
	$inputs_css_path = ANOE_DIR . 'input-fields/assets/css/inputs-fields.css';
	if ( file_exists( $inputs_css_path ) ) {
		wp_enqueue_style(
			'anony-inputs',
			ANOE_URI . 'input-fields/assets/css/inputs-fields.css',
			array(),
			filemtime( $inputs_css_path )
		);
	}

	// Enqueue RTL styles if needed.
	if ( is_rtl() ) {
		$inputs_rtl_css_path = ANOE_DIR . 'input-fields/assets/css/inputs-fields-rtl.css';
		if ( file_exists( $inputs_rtl_css_path ) ) {
			wp_enqueue_style(
				'anony-inputs-rtl',
				ANOE_URI . 'input-fields/assets/css/inputs-fields-rtl.css',
				array( 'anony-inputs' ),
				filemtime( $inputs_rtl_css_path )
			);
		}
	}
}

/**
 * Initialize Google Maps callback function.
 *
 * @since 1.0.0
 * @return void
 */
function anoe_init_map_callback() {
	?>
	<script>
		if ( typeof initMap !== 'function' ) {
			function initMap() {
				console.log('%cGoogle map api has been called for a location field', 'color: green');
			}
		}
	</script>
	<?php
}

/**
 * Add head scripts.
 *
 * @since 1.0.0
 * @return void
 */
function anoe_head_scripts() {
	anoe_init_map_callback();
}

/**
 * Remove gallery item from comma-separated list.
 *
 * @since 1.0.0
 * @param string $gallery_items Comma separated attachments' IDs.
 * @param mixed  $attachment_id To be unset attachment ID. int or string.
 * @return void
 */
function anoe_unset_gallery_item( &$gallery_items, $attachment_id ) {
	$attachment_id = absint( $attachment_id );
	
	if ( empty( $gallery_items ) ) {
		return;
	}

	$attachments_ids = array_map( 'absint', array_filter( explode( ',', $gallery_items ) ) );
	
	if ( in_array( $attachment_id, $attachments_ids, true ) ) {
		$index = array_search( $attachment_id, $attachments_ids, true );

		if ( false !== $index ) {
			unset( $attachments_ids[ $index ] );
		}
	}

	$gallery_items = implode( ',', $attachments_ids );
}

/**
 * Sanitize and validate file upload.
 *
 * @since 1.0.0
 * @param array $file $_FILES array element.
 * @param array $allowed_types Allowed file types.
 * @param int   $max_size Maximum file size in bytes.
 * @return array|WP_Error Sanitized file data or WP_Error on failure.
 */
function anoe_sanitize_file_upload( $file, $allowed_types = array(), $max_size = 0 ) {
	if ( ! is_array( $file ) || empty( $file['name'] ) ) {
		return new WP_Error( 'invalid_file', __( 'Invalid file data.', 'anonyengine' ) );
	}

	// Check file size.
	if ( $max_size > 0 && $file['size'] > $max_size ) {
		return new WP_Error( 'file_too_large', __( 'File size exceeds maximum allowed size.', 'anonyengine' ) );
	}

	// Check file type.
	if ( ! empty( $allowed_types ) ) {
		$file_extension = strtolower( pathinfo( $file['name'], PATHINFO_EXTENSION ) );
		if ( ! in_array( $file_extension, $allowed_types, true ) ) {
			return new WP_Error( 'invalid_file_type', __( 'File type not allowed.', 'anonyengine' ) );
		}
	}

	// Sanitize file data.
	$sanitized_file = array(
		'name'     => sanitize_file_name( $file['name'] ),
		'type'     => sanitize_mime_type( $file['type'] ),
		'tmp_name' => $file['tmp_name'],
		'error'    => absint( $file['error'] ),
		'size'     => absint( $file['size'] ),
	);

	return $sanitized_file;
}

/**
 * Validate and sanitize email address.
 *
 * @since 1.0.0
 * @param string $email Email address to validate.
 * @return string|false Sanitized email or false if invalid.
 */
function anoe_validate_email( $email ) {
	$email = sanitize_email( $email );
	
	if ( ! is_email( $email ) ) {
		return false;
	}

	return $email;
}

/**
 * Validate and sanitize URL.
 *
 * @since 1.0.0
 * @param string $url URL to validate.
 * @return string|false Sanitized URL or false if invalid.
 */
function anoe_validate_url( $url ) {
	$url = esc_url_raw( $url );
	
	if ( empty( $url ) || ! filter_var( $url, FILTER_VALIDATE_URL ) ) {
		return false;
	}

	return $url;
}

/**
 * Create nonce for AnonyEngine forms.
 *
 * @since 1.0.0
 * @param string $action Action name for the nonce.
 * @return string Nonce value.
 */
function anoe_create_nonce( $action = 'anonyengine_action' ) {
	return wp_create_nonce( 'anonyengine_' . $action );
}

/**
 * Verify nonce for AnonyEngine forms.
 *
 * @since 1.0.0
 * @param string $nonce Nonce value to verify.
 * @param string $action Action name for the nonce.
 * @return bool True if nonce is valid, false otherwise.
 */
function anoe_verify_nonce( $nonce, $action = 'anonyengine_action' ) {
	return wp_verify_nonce( $nonce, 'anonyengine_' . $action );
}

/**
 * Check if user has required capability.
 *
 * @since 1.0.0
 * @param string $capability Capability to check.
 * @return bool True if user has capability, false otherwise.
 */
function anoe_user_can( $capability = 'manage_options' ) {
	return current_user_can( $capability );
}

/**
 * Sanitize text field with proper escaping.
 *
 * @since 1.0.0
 * @param string $text Text to sanitize.
 * @return string Sanitized text.
 */
function anoe_sanitize_text( $text ) {
	return sanitize_text_field( wp_unslash( $text ) );
}

/**
 * Sanitize textarea field.
 *
 * @since 1.0.0
 * @param string $text Text to sanitize.
 * @return string Sanitized text.
 */
function anoe_sanitize_textarea( $text ) {
	return sanitize_textarea_field( wp_unslash( $text ) );
}

/**
 * Escape HTML output safely.
 *
 * @since 1.0.0
 * @param string $html HTML to escape.
 * @return string Escaped HTML.
 */
function anoe_escape_html( $html ) {
	return wp_kses_post( $html );
}

/**
 * Get plugin option with default fallback.
 *
 * @since 1.0.0
 * @param string $option_name Option name.
 * @param mixed  $default Default value if option doesn't exist.
 * @return mixed Option value or default.
 */
function anoe_get_option( $option_name, $default = null ) {
	$option_value = get_option( $option_name, $default );
	
	// Sanitize based on type.
	if ( is_string( $option_value ) ) {
		return sanitize_text_field( $option_value );
	}
	
	return $option_value;
}

/**
 * Update plugin option with proper sanitization.
 *
 * @since 1.0.0
 * @param string $option_name Option name.
 * @param mixed  $option_value Option value.
 * @return bool True on success, false on failure.
 */
function anoe_update_option( $option_name, $option_value ) {
	// Sanitize value based on type.
	if ( is_string( $option_value ) ) {
		$option_value = sanitize_text_field( $option_value );
	}
	
	return update_option( $option_name, $option_value );
}

/**
 * Log debug information if WP_DEBUG is enabled.
 *
 * @since 1.0.0
 * @param mixed  $data Data to log.
 * @param string $context Context for the log entry.
 * @return void
 */
function anoe_debug_log( $data, $context = 'AnonyEngine' ) {
	if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
		error_log( sprintf( '[%s] %s: %s', $context, date( 'Y-m-d H:i:s' ), print_r( $data, true ) ) );
	}
}