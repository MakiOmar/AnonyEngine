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
		public static function add_missing_dimensions( $content, $lazyload = false ) {
			$pattern = '/<img [^>]*?src="(\w+?:\/\/[^"]+?)"[^>]*?>/iu';
			preg_match_all( $pattern, $content, $imgs );
			if(!function_exists('getimagesize')){
				return $content;
			}
			foreach ( $imgs[0] as $i => $img ) {
				
				if( $lazyload ){
					// Use Defer.js to lazyload.
					// https://github.com/shinsenter/defer.js/#Defer.lazy.
					if( false === strpos($imgs[0][ $i ], 'data-src') ){
						$replaced_img = preg_replace('/<img([^>]*)src=("|\')([^"\']*)(\2)([^>]*)>/', '<img$1data-src=$2$3$4$5 src="' . ANOE_URI . 'assets/images/placeholders/lazyload-placeholder.svg">', $imgs[0][ $i ]);
					}else{
						$replaced_img = $imgs[0][ $i ];
					}
					
					if( false === strpos( $replaced_img, 'data-srcset') ){
						$replaced_img = preg_replace('/<img([^>]*)srcset=("|\')([^"\']*)(\2)([^>]*)>/', '<img$1data-srcset=$2$3$4$5>', $replaced_img);
					}
					
					$replaced_img = str_replace( '<img ', '<img loading="lazy" ' , $replaced_img );
					
				}else{
					$replaced_img = $imgs[0][ $i ];
				}

				if ( false === strpos( $img, ' width' ) && false === strpos( $img, ' height' ) ) {
					$img_url  = $imgs[1][ $i ];
					$img_size = getimagesize( $img_url );



					if ( false !== $img_size ) {
						$replaced_img = str_replace( '<img ', '<img style="width:'. $img_size[0] .';max-height:'. $img_size[1] .'"' . $img_size[3] . ' ', $replaced_img );
					}

				}else{
					$img_url  = $imgs[1][ $i ];
					$img_size = getimagesize( $img_url );



					if ( false !== $img_size ) {
						
						if (preg_match('/<img[^>]+style=["\']([^"\']+)["\']/', $replaced_img, $matches)) {
							// The img element has a style attribute
							$style_attribute = $matches[1];

							if (!preg_match('/\bwidth\s*:\s*[^;]+/', $style_attribute)) {
								// Width is not set in style attribute, add it
								$style_attribute .= ' width: '.$img_size[0].'px;';
							}
							
							if (!preg_match('/\bmax-height\s*:\s*[^;]+/', $style_attribute)) {
								// Height is not set in style attribute, add it
								$style_attribute .= ' max-height: '.$img_size[1].'px;';
							}
							
							// Replace the updated style attribute in the HTML
							$replaced_img = str_replace($matches[1], $style_attribute , $replaced_img);
						}else{

							$replaced_img = str_replace( '<img ', '<img style="width:'. $img_size[0] .'px;max-height:'. $img_size[1] .'px" ', $replaced_img );
						}
						
					}
				}

				$content      = str_replace( $img, $replaced_img, $content );
			}
			return $content;
		}

	}
}