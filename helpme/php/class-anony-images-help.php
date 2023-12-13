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
					if ( false === strpos( $img, ' width' ) && false === strpos( $img, ' height' ) ) {
						$img_size = @getimagesize( $img_url );
					} else {
						$img_size = false;
					}
				} elseif ( is_array( $dimensions ) ) {

					$img_size = array_values( $dimensions );

				} else {

					$img_size = false;
				}

				if ( false !== $img_size ) {
					$replaced_img = $imgs[0][ $i ];
					if ( $lazyload ) {
						// Use Defer.js to lazyload.
						// https://github.com/shinsenter/defer.js/#Defer.lazy.
						if ( false === strpos( $imgs[0][ $i ], 'data-src' ) ) {
							$replaced_img = preg_replace( '/<img([^>]*)src=("|\')([^"\']*)(\2)([^>]*)>/', '<img$1data-src=$2$3$4$5 src="data:image/svg+xml,%3Csvg%20xmlns=\'http://www.w3.org/2000/svg\'%20viewBox=\'0%200%20225%20225\'%3E%3C/svg%3E">', $imgs[0][ $i ] );
						}

						if ( false === strpos( $replaced_img, 'data-srcset' ) ) {
							$replaced_img = preg_replace( '/<img([^>]*)srcset=("|\')([^"\']*)(\2)([^>]*)>/', '<img$1data-srcset=$2$3$4$5>', $replaced_img );
						}

						$replaced_img = str_replace( '<img ', '<img loading="lazy" ', $replaced_img );

					}

					$dimension_attributes = 'width="' . $img_size[0] . '" height="' . $img_size[1] . '"';
					if ( empty( $img_size[3] ) ) {
						$img_size[3] = $dimension_attributes;
					}

					if ( false === strpos( $img, ' width' ) && false === strpos( $img, ' height' ) ) {
						$replaced_img = str_replace( '<img ', '<img style="width:' . $img_size[0] . ';max-height:' . $img_size[1] . '"' . $img_size[3] . ' ', $replaced_img );

					} elseif ( preg_match( '/<img[^>]+style=["\']([^"\']+)["\']/', $replaced_img, $matches ) ) {

						// The img element has a style attribute.
						$style_attribute = $matches[1];

						if ( ! preg_match( '/\bwidth\s*:\s*[^;]+/', $style_attribute ) ) {
							// Width is not set in style attribute, add it.
							$style_attribute .= ' width: ' . $img_size[0] . 'px;';
						}

						if ( ! preg_match( '/\bmax-height\s*:\s*[^;]+/', $style_attribute ) ) {
							// Height is not set in style attribute, add it.
							$style_attribute .= ' max-height: ' . $img_size[1] . 'px;';
						}

						// Replace the updated style attribute in the HTML.
						$replaced_img = str_replace( $matches[1], $style_attribute, $replaced_img );
					} else {
						$replaced_img = str_replace( '<img ', '<img style="width:' . $img_size[0] . 'px;max-height:' . $img_size[1] . 'px" ', $replaced_img );
					}
					$content = str_replace( $img, $replaced_img, $content );
				}
			}
			return $content;
		}
	}
}
