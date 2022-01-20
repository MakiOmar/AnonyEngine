<?php
/**
 * PHP String helpers class
 *
 * @package Anonymous theme
 * @author Makiomar
 * @link http://makiomar.com
 */

if ( ! class_exists( 'ANONY_STRING_HELP' ) ) {
	class ANONY_STRING_HELP extends ANONY_HELP{
		
		static  function sliceText($text, $length){

			$words = str_word_count($text, 1);

			$len = min($length, count($words));

			return join(' ', array_slice($words, 0, $len));	
		}
		
		/**
		 * Uppercase first litter after delimiter
		 * @param string $delimiter 
		 * @param string $string 
		 * @return string
		 */
		static  function ucAfter($delimiter, $string){

			return implode($delimiter, array_map('ucfirst', explode($delimiter, $q)));
		}

		/**
		 * Read textarea content line by line
		 * @param string content
		 * @return array
		 */
		static function lineByLineTextArea($content){

			return explode("\r\n", trim($content));

		}

	}
}