<?php
/**
 * PHP Main helpers class
 *
 * @package Anonymous theme
 * @author Makiomar
 * @link http://makiomar.com
 */
if ( ! class_exists( 'ANONY_HELP' ) ) {
	class ANONY_HELP {

		/**
		 * Output buffer included file
		 *
		 * @param  string $file_path
		 * @return string
		 */
		static function obInclude( $file_path ) {
			ob_start();

			include $file_path;

			return ob_get_clean();
		}

		/**
		 * Output buffer function
		 *
		 * @param string $function
		 * @param array  $args
		 * @return string
		 */
		static function obGet( $function, $args = array() ) {
			ob_start();
			call_user_func_array( $function, $args );
			return ob_get_clean();
		}

		/**
		 * trims a string to a custom number of words
		 *
		 * @param string $text
		 * @param int    $length
		 * @return string
		 */

		static function sliceText( $text, $length ) {

			$words = str_word_count( $text, 1 );

			$len = min( $length, count( $words ) );

			return join( ' ', array_slice( $words, 0, $len ) );
		}

		/**
		 * Remove script tags with REGEX.
		 *
		 * @param string $string String to be cleaned
		 * @return string Cleaned string
		 */
		static function removeScriptTagRegx( $string ) {
			return preg_replace( '#<script(.*?)>(.*?)</script>#mis', '', $string );
		}
		/**
		 * Remove specific tags with DOMDocument.
		 *
		 * **Description: ** Will remove all supplied tags and automatically remove DOCTYPE, body and html.
		 *
		 * @param string                                                                    $html String to be cleaned
		 * @param array|string                                                              $remove Tag or array of tags to be removed
		 * @param boolean If <code>true</code> removes DOCTYPE, body and html automatically. default <code>true</code>
		 * @return string Cleaned string
		 */
		static function removeTagsDom( $html, $remove, $cleanest = true ) {
			$dom = new DOMDocument();
			$dom->loadHTML( $html, LIBXML_HTML_NODEFDTD | LIBXML_HTML_NOIMPLIED );
			if ( is_array( $remove ) ) {
				foreach ( $remove as $tag ) {
					$element = $dom->getElementsByTagName( $tag );
					foreach ( $element  as $item ) {
						$item->parentNode->removeChild( $item );
					}
				}
			} else {
				$element = $dom->getElementsByTagName( $remove );
				foreach ( $element  as $item ) {
						$item->parentNode->removeChild( $item );
				}
			}

			if ( $cleanest ) {
				$html = preg_replace( '/^<!DOCTYPE.+?>/', '', str_replace( array( '<html>', '</html>', '<body>', '</body>' ), array( '', '', '', '' ), $dom->saveHTML() ) );
			}

			if ( ( is_array( $remove ) && in_array( 'script', $remove ) ) || $remove == 'script' ) {
				$html = self::removeScriptTagRegx( $html );
			}

			return $html;
		}

		/**
		 * Check if checkbox is checked in a form
		 */
		static function isChecked( $chkname, $value ) {
			if ( isset( $_POST[ $chkname ] ) && ! empty( $_POST[ $chkname ] ) ) {
				foreach ( $_POST[ $chkname ] as $chkval ) {
					if ( $chkval == $value ) {
						return true;
					}
				}
			}
			return false;
		}

		// For debugging. used when page direction is rtl.
		static function neatVarDump( $r ) {
			echo '<pre styel="direction:ltr;text-align:left">';
				// phpcs:disable WordPress.PHP.DevelopmentFunctions
				var_dump( $r );
				// phpcs:enable
			echo '</pre>';
		}

		/**
		 * Check is a variable is set and not empty.
		 * 
		 * @param mixed $variable To be checked variable.
		 * @return bool True if a variable is set and not empty, otherwise false.
		 */ 
		static function isset_not_empty( $variable ){

			if ( isset( $variable ) && !empty( $variable ) ) {
				return true;
			}

			return false;

		}

	}
}
