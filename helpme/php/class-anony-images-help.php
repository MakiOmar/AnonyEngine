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
		 * Add images missing dimensions.
		 *
		 * @param string $content HTML content that contains images.
		 * @return string
		 */
		public static function add_missing_dimensions( $content ) {
			$pattern = '/<img [^>]*?src="(\w+?:\/\/[^"]+?)"[^>]*?>/iu';
			preg_match_all( $pattern, $content, $imgs );
			foreach ( $imgs[0] as $i => $img ) {

				if ( false !== strpos( $img, 'width=' ) && false !== strpos( $img, 'height=' ) ) {
					continue;
				}

				$img_url  = $imgs[1][ $i ];
				$img_size = getimagesize( $img_url );

				if ( false === $img_size ) {
					continue;
				}

				$replaced_img = str_replace( '<img ', '<img ' . $img_size[3] . ' ', $imgs[0][ $i ] );
				$content      = str_replace( $img, $replaced_img, $content );
			}

			return $content;
		}

	}
}
