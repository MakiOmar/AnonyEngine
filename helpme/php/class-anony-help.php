<?php
/**
 * AnonyEngine PHP Main helpers.
 *
 * PHP version 7.3 Or Later.
 *
 * @package  AnonyEngine
 * @author   Makiomar <info@makiomar.com>
 * @license  https://makiomar.com AnonyEngine Licence
 * @link     https://makiomar.com/anonyengine
 */

defined( 'ABSPATH' ) || die(); // Exit if accessed directly.

if ( ! class_exists( 'ANONY_HELP' ) ) {
	/**
	 * PHP Main helpers class
	 *
	 * @package AnonyEngine
	 * @author Makiomar
	 * @link http://makiomar.com
	 */
	class ANONY_HELP {

		/**
		 * Buffer output of included file.
		 *
		 * @param  string $file_path File to be buffered.
		 * @return string
		 */
		public static function ob_include( $file_path ) {
			ob_start();

			include $file_path;

			return ob_get_clean();
		}

		/**
		 * Buffer output of a function.
		 *
		 * @param string $function Function name.
		 * @param array  $args Function arguments.
		 * @return string
		 */
		public static function ob_get( $function, $args = array() ) {
			ob_start();
			call_user_func_array( $function, $args );
			return ob_get_clean();
		}

		/**
		 * Trims a string to a custom number of words.
		 *
		 * @param string $text Text to be trimmed.
		 * @param int    $length Trim length.
		 * @return string
		 */
		public static function slice_text( $text, $length ) {

			$words = str_word_count( $text, 1 );

			$len = min( $length, count( $words ) );

			return join( ' ', array_slice( $words, 0, $len ) );
		}

		/**
		 * Remove script tags with REGEX.
		 *
		 * @param string $string String to be cleaned.
		 * @return string Cleaned string.
		 */
		public static function remove_script_tag_regx( $string ) {
			return preg_replace( '#<script(.*?)>(.*?)</script>#mis', '', $string );
		}
		/**
		 * Remove specific tags with DOMDocument.
		 *
		 * **Description: ** Will remove all supplied tags and automatically remove DOCTYPE, body and html.
		 *
		 * @param string       $html String to be cleaned.
		 * @param array|string $remove Tag or array of tags to be removed.
		 * @param boolean      $cleanest If <code>true</code> removes DOCTYPE, body and html automatically. default <code>true</code>.
		 * @return string Cleaned string.
		 */
		public static function remove_tags_dom( $html, $remove, $cleanest = true ) {
			$dom = new DOMDocument();
			$dom->loadHTML( $html, LIBXML_HTML_NODEFDTD | LIBXML_HTML_NOIMPLIED );
			if ( is_array( $remove ) ) {
				foreach ( $remove as $tag ) {
					$element = $dom->getElementsByTagName( $tag );
					foreach ( $element  as $item ) {
						$item->parent_node->removeChild( $item );
					}
				}
			} else {
				$element = $dom->getElementsByTagName( $remove );
				foreach ( $element  as $item ) {
						$item->parent_node->removeChild( $item );
				}
			}

			if ( $cleanest ) {
				$html = preg_replace( '/^<!DOCTYPE.+?>/', '', str_replace( array( '<html>', '</html>', '<body>', '</body>' ), array( '', '', '', '' ), $dom->saveHTML() ) );
			}

			if ( ( is_array( $remove ) && in_array( 'script', $remove, true ) ) || 'script' === $remove ) {
				$html = self::remove_script_tag_regx( $html );
			}

			return $html;
		}

		/**
		 * Check if checkbox is checked in a form.
		 *
		 * @param array  $sent_data Data sent.
		 * @param string $chkname Checkbox's input name.
		 * @param string $value Checkbox's value.
		 *
		 * @return bool True if checked, otherwise false.
		 */
		public static function is_checked( $sent_data, $chkname, $value ) {
			if ( self::isset_not_empty( $sent_data[ $chkname ] ) ) {
				foreach ( $sent_data[ $chkname ] as $chkval ) {
					if ( $chkval === $value ) {
						return true;
					}
				}
			}
			return false;
		}

		/**
		 * For debugging. used when page direction is rtl.
		 *
		 * @param mixed $data To be dumpped variable.
		 */
		public static function neat_var_dump( $data ) {
			echo '<pre styel="direction:ltr;text-align:left">';
				// phpcs:disable WordPress.PHP.DevelopmentFunctions
				var_dump( $data );
				// phpcs:enable
			echo '</pre>';
		}

		/**
		 * Check is a variable is set and not empty.
		 *
		 * @param mixed $variable To be checked variable.
		 * @return bool True if a variable is set and not empty, otherwise false.
		 */
		public static function isset_not_empty( $variable ) {

			if ( isset( $variable ) && ! empty( $variable ) ) {
				return true;
			}

			return false;

		}

		/**
		 * Check if any of supplied variables is empty.
		 *
		 * @param mixed ...$arguments Any number of variables.
		 * @return bool Returns true on success, otherwise false
		 */
		public static function empty( ...$arguments ) {
			foreach ( $arguments as $argument ) {
				if ( empty( $argument ) ) {
					return true;
				}
			}
			return false;
		}

	}
}
