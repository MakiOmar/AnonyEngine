<?php
/**
 * PHP Main helpers class
 *
 * @package Anonymous theme
 * @author Makiomar
 * @link http://makiomar.com
 */
if ( ! class_exists( 'ANONY_HELP' ) ) {
	class ANONY_HELP{

		/**
		 * trims a string to a custom number of words
		 * @param string $text 
		 * @param int    $length 
		 * @return string
		 */

		static function sliceText($text, $length){

			$words = str_word_count($text, 1);

			$len = min($length, count($words));

			return join(' ', array_slice($words, 0, $len));	
		}

		/**
		 * Remove script tags with REGEX.
		 *
		 * @param string $string String to be cleaned
		 * @return string Cleaned string
		 */
		static function removeScriptTagRegx($string){
			return preg_replace('#<script(.*?)>(.*?)</script>#mis', '', $string);
		}
		/**
		 * Remove specific tags with DOMDocument.
		 *
		 * **Description: ** Will remove all supplied tags and automatically remove DOCTYPE, body and html.
		 *
		 * @param string $html String to be cleaned
		 * @param array|string $remove Tag or array of tags to be removed
		 * @param boolean If <code>true</code> removes DOCTYPE, body and html automatically. default <code>true</code>
		 * @return string Cleaned string
		 */
		static function removeTagsDom($html, $remove, $cleanest = true){
			$dom = new DOMDocument();
			$dom->loadHTML($html, LIBXML_HTML_NODEFDTD | LIBXML_HTML_NOIMPLIED);
			if(is_array($remove)){
				foreach($remove as $tag){
					$element = $dom->getElementsByTagName($tag);
					foreach($element  as $item){
						$item->parentNode->removeChild($item);
					}
				}
			}else{
				$element = $dom->getElementsByTagName($remove);
				foreach($element  as $item){
						$item->parentNode->removeChild($item);
				}
			}
			
			if($cleanest){
				$html = preg_replace('/^<!DOCTYPE.+?>/', '', str_replace( array('<html>', '</html>', '<body>', '</body>'), array('', '', '', ''), $dom->saveHTML()));
			}
			
			if((is_array($remove) && in_array('script',$remove)) || $remove == 'script'){
				$html = self::removeScriptTagRegx($html);
			}
			
			return $html;
		}

		/**
		 * Check if checkbox is checked in a form
		 */
		static function isChecked($chkname,$value)
		{
		    if(isset($_POST[$chkname]) && !empty($_POST[$chkname]))
		    {
		        foreach($_POST[$chkname] as $chkval)
		        {
		            if($chkval == $value)
		            {
		                return true;
		            }
		        }
		    }
		    return false;
		}

		//For testing
		static function neatVarDump($r){

				echo '<pre dir="ltr">';
					var_dump($r);
				echo '</pre>';
		}

	}
}