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

	}
}