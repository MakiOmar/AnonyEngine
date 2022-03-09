<?php
/**
 * PHP String helpers class
 *
 * @package Anonymous theme
 * @author Makiomar
 * @link http://makiomar.com
 */

if ( ! class_exists( 'ANONY_STRING_HELP' ) ) {
	class ANONY_STRING_HELP extends ANONY_HELP {

		static function sliceText( $text, $length ) {

			$words = str_word_count( $text, 1 );

			$len = min( $length, count( $words ) );

			return join( ' ', array_slice( $words, 0, $len ) );
		}

		/**
		 * Uppercase first litter after delimiter
		 *
		 * @param string $delimiter
		 * @param string $string
		 * @return string
		 */
		static function ucAfter( $delimiter, $string ) {

			return implode( $delimiter, array_map( 'ucfirst', explode( $delimiter, $q ) ) );
		}

		/**
		 * Read textarea content line by line
		 *
		 * @param string $content
		 * @return array
		 */
		static function lineByLineTextArea( $content ) {

			return explode( "\r\n", trim( $content ) );

		}

		/**
		 * Add images missing dimensions
		 *
		 * @param string $content
		 * @return string
		 */
		static function addImagesMissingDimensions( $content ) {
			$pattern = '/<img [^>]*?src="(\w+?:\/\/[^"]+?)"[^>]*?>/iu';
			preg_match_all( $pattern, $content, $imgs );
			foreach ( $imgs[0] as $i => $img ) {

				if ( false !== strpos( $img, 'width=' ) && false !== strpos( $img, 'height=' ) ) {
					continue;
				}

				$img_url  = $imgs[1][ $i ];
				$img_size = @getimagesize( $img_url );

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
