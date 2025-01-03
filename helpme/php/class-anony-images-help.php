<?php
/**
 * PHP images helpers.
 *
 * PHP version 7.3 Or Later.
 *
 * @package  AnonyEngine
 * @author   Makiomar <info@makiomar.com>
 * @license  https:// makiomar.com AnonyEngine Licence.
 * @link     https:// makiomar.com/anonyengine
 */

defined( 'ABSPATH' ) || die();

if ( ! class_exists( 'ANONY_IMAGES_HELP' ) ) {

	/**
	 * PHP images helpers class.
	 *
	 * PHP version 7.3 Or Later.
	 *
	 * @package  AnonyEngine
	 * @author   Makiomar <info@makiomar.com>
	 * @license  https:// makiomar.com AnonyEngine Licence.
	 * @link     https:// makiomar.com/anonyengine
	 */
	class ANONY_IMAGES_HELP extends ANONY_HELP {

		/**
		 * Get dimensions from image url.
		 * Example: /image-60x60.png. Dimensions are 60x60
		 *
		 * @param string $image_url The URL.
		 * @return array|bool Dimesions array if URL has dimensions otherwise false.
		 */
		public static function thumb_get_dimensions( $image_url ) {
			$pattern = '/-(\d+)x(\d+)\.(?:jpg|jpeg|png|gif|webp)$/i';
			preg_match( $pattern, $image_url, $matches );

			if ( count( $matches ) === 3 ) {
				$width  = intval( $matches[1] );
				$height = intval( $matches[2] );

				return array(
					'width'  => $width,
					'height' => $height,
				);
			}

			return false;
		}
		/**
		 * Generate svg placeholder
		 *
		 * @param string $image_url Thumbnail URL.
		 * @param string $type Placeholder type.
		 * @return string
		 */
		public static function create_base64_string( $image_url, $type = 'svg' ) {
			list( $width, $height ) = self::thumb_get_dimensions( $image_url );

			$svg  = '<svg';
			$svg .= ' viewBox="0 0 ' . $width . ' ' . $height . '"';
			$svg .= ' xmlns="http://www.w3.org/2000/svg"';
			$svg .= '></svg>';
			if ( 'base64' === $type ) {
				//phpcs:disable WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode
				return 'data:image/svg+xml;base64,' . base64_encode( $svg );
			}
			return 'data:image/svg+xml,' . rawurlencode( $svg );
		}
		/**
		 * Extract image dimentions
		 *
		 * @param string $html Image html.
		 * @return mixed
		 */
		public static function extract_img_dimentions( $html ) {
			$pattern = '/<img[^>]+width=["\']([^"\']+)["\'][^>]+height=["\']([^"\']+)["\']/i';
			preg_match( $pattern, $html, $matches );

			if ( count( $matches ) === 3 ) {
				$width  = $matches[1];
				$height = $matches[2];
				return array(
					'width'  => $width,
					'height' => $height,
				);
			}
			return false;
		}
		/**
		 * Add images missing dimensions.
		 *
		 * @param string $content HTML content that contains images.
		 * @param bool   $lazyload Weather to enable lazyload or not. Default False.
		 * @return string
		 */
		public static function add_missing_dimensions( $content, $lazyload = false ) {
			$pattern = '/<img [^>]*?src="(\w+?:\/\/[^"]+?)"[^>]*?>/iu';
			preg_match_all( $pattern, $content, $imgs );
			$no_dimensions = array();
			foreach ( $imgs[0] as $i => $img ) {

				$img_url    = $imgs[1][ $i ];
				$dimensions = self::thumb_get_dimensions( $img_url );
				if ( ! $dimensions && function_exists( 'getimagesize' ) ) {
					$no_dimensions[] = $img;
					if ( false === strpos( $imgs[0][ $i ], 'width' ) && false === strpos( $imgs[0][ $i ], 'height' ) ) {
						//phpcs:disable WordPress.PHP.NoSilencedErrors.Discouraged
						$img_size = @getimagesize( $img_url );
					} else {
						$img_size = is_array( self::extract_img_dimentions( $imgs[0][ $i ] ) ) ? array_values( self::extract_img_dimentions( $imgs[0][ $i ] ) ) : false;
					}
				} elseif ( is_array( $dimensions ) ) {

					$img_size = array_values( $dimensions );

				} else {

					$img_size = false;
				}
				$replaced_img = $imgs[0][ $i ];
				if ( false !== $img_size ) {
					$dimension_attributes = 'width="' . $img_size[0] . 'px" height="' . $img_size[1] . 'px"';
					if ( empty( $img_size[3] ) ) {
						$img_size[3] = $dimension_attributes;
					}

					if ( false === strpos( $replaced_img, 'width' ) && false === strpos( $replaced_img, 'height' ) ) {
						$replaced_img = str_replace( '<img ', '<img style="width:' . $img_size[0] . 'px;max-height:' . $img_size[1] . 'px"' . $img_size[3] . ' ', $replaced_img );

					} elseif ( preg_match( '/<img[^>]+style=["\']([^"\']+)["\']/', $replaced_img, $matches ) ) {

						// The img element has a style attribute.
						$style_attribute = $matches[1];
						if ( ! preg_match( '/\bwidth\s*:\s*[^;]+/', $style_attribute ) ) {
							// Width is not set in style attribute, add it.
							$style_attribute .= ' width: ' . $img_size[0] . 'px;';
						}

						if ( ! preg_match( '/\bmax-height\s*:\s*[^;]+/', $style_attribute ) ) {
							// Height is not set in style attribute, add it.
							$style_attribute .= ';max-height: ' . $img_size[1] . 'px;';
						}
						// Replace the updated style attribute in the HTML.
						$replaced_img = str_replace( $matches[1], $style_attribute, $replaced_img );
					} else {
						$replaced_img = str_replace( '<img ', '<img style="width:' . $img_size[0] . 'px;max-height:' . $img_size[1] . 'px" ', $replaced_img );
					}
					if ( $lazyload ) {
						if ( false === strpos( $replaced_img, 'no-lazyload' ) ) {
							// Use Defer.js to lazyload.
							// https://github.com/shinsenter/defer.js/#Defer.lazy.
							if ( false === strpos( $imgs[0][ $i ], 'data-src' ) ) {
								$replaced_img = preg_replace(
									'/<img([^>]*)src=("|\')([^"\']*)(\2)([^\/>]*)\s*\/?>/',
									'<img$1data-src=$2$3$4$5 src="data:image/svg+xml,%3Csvg%20xmlns=\'http://www.w3.org/2000/svg\'%20viewBox=\'0%200%20225%20225\'%3E%3C/svg%3E" />',
									$replaced_img
								);
								error_log( $replaced_img );
							}
							if ( false === strpos( $replaced_img, 'data-srcset' ) ) {
								$replaced_img = preg_replace( '/<img([^>]*)srcset=("|\')([^"\']*)(\2)([^>]*)>/', '<img$1data-srcset=$2$3$4$5>', $replaced_img );
							}

							$replaced_img = str_replace( '<img ', '<img loading="lazy" ', $replaced_img );
							$replaced_img = str_replace( 'decoding="async"', '', $replaced_img );
						} elseif ( false !== strpos( $replaced_img, 'no-lazyload' ) ) {
							// Disable wp lazyload.
							$replaced_img = str_replace( ' loading="lazy"', '', $replaced_img );
						}
					}
					$content = str_replace( $imgs[0][ $i ], $replaced_img, $content );
				}
			}
			return $content;
		}
		/**
		 * Get the MIME type of a file from its URL.
		 *
		 * @param string $url The URL of the file.
		 * @return string|null The MIME type, or null if it cannot be determined.
		 */
		public static function get_mime_type_from_url( string $url ): ?string {
			try {
				$file_info     = new finfo( FILEINFO_MIME_TYPE );
				$file_contents = file_get_contents( $url );
				if ( ! $file_contents ) {
					return null; // Failed to fetch the file contents.
				}
				return $file_info->buffer( $file_contents );
			} catch ( Exception $e ) {
				return null; // Handle any exceptions gracefully.
			}
		}
		/**
		 * Get the dimensions of an image from its URL.
		 *
		 * @param string $url The URL of the image.
		 * @return array|null An array with 'width' and 'height', or null if the dimensions cannot be determined.
		 */
		function get_image_dimensions_from_url( string $url ): ?array {
			try {
				$image_data = file_get_contents( $url );
				if ( false === $image_data ) {
					return null; // Failed to fetch the image.
				}

				$image = imagecreatefromstring( $image_data );
				if ( ! $image ) {
					return null; // Invalid image data.
				}

				$width  = imagesx( $image );
				$height = imagesy( $image );

				imagedestroy( $image ); // Free up memory.

				return array(
					'width'  => $width,
					'height' => $height,
				);
			} catch ( Exception $e ) {
				return null; // Handle any exceptions gracefully.
			}
		}
	}
}
