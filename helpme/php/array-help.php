<?php
/**
 * PHP helpers class
 *
 * @package Anonymous theme
 * @author Makiomar
 * @link http://makiomar.com
 */

if ( ! class_exists( 'ANONY_ARRAY_HELP' ) ) {
	class ANONY_ARRAY_HELP extends ANONY_HELP{
		/**
		 * Gets an associative array as key/value pairs from any object properties.
		 * 
	 	 * Useful where a select input field has dynamic options.
		 * @param object $objects_array The array of objects to loop through
		 * @param string $key           The property that should be used as a key
		 * @param string $value         The property that should be used as a value
		 * @return array                An array of properties as key/value pairs    
		 */

		static function ObjToAssoc($objects_array, $key, $value, $assoc = true){

			$arr = [];

			foreach ($objects_array as $object) {
				if($assoc && !empty($key)){
					$arr[$object->$key] = $object->$value;
				}else{
					$arr[] = $object->$value;
				}
				
			}

			return $arr;
		}

		/**
		 * Same as print_r but usefull for rtl pages
		 * @param type $array 
		 * @return type
		 */
		static function neatPrintR($array){
			echo '<pre dir="ltr">';
				print_r($array);
			echo '</pre>';
		}

		/**
		 * Insert a key/value before another in an associative array
		 * @param array $originalArray 
		 * @param strin $originalKey 
		 * @param array $insertKey 
		 * @param string $insertValue 
		 * @return array
		 */
		static function insertBeforeKey( $originalArray, $originalKey, $insertKey, $insertValue ) {

		    $newArray = array();
		    $inserted = false;

		    foreach( $originalArray as $key => $value ) {

		        if( !$inserted && $key === $originalKey ) {
		            $newArray[ $insertKey ] = $insertValue;
		            $inserted = true;
		        }

		        $newArray[ $key ] = $value;

		    }

		    return $newArray;

		}

		/**
		 *  Check if an array is a multidimensional array.
		 *
		 *  @param   array   $arr  The array to check
		 *  @return  boolean       Whether the the array is a multidimensional array or not
		 */
		static function isMultiDimensional( $x ) {
			if( count( array_filter( $x,'is_array' ) ) > 0 ) return true;
			return false;
		}

		/**
		 *  Convert an object to an array.
		 *
		 *  @param   array   $object  The object to convert
		 *  @return  array            The converted array
		 */
		static function ToArray( $object ) {
			if( !is_object( $object ) || !is_array( $object ) ) return $object;
			return array_map( 'object_to_array', (array) $object );
		}

		/**
		 *  Check if a value exists in the array/object.
		 *
		 *  @param   mixed    $needle    The value that you are searching for
		 *  @param   mixed    $haystack  The array/object to search
		 *  @param   boolean  $strict    Whether to use strict search or not
		 *  @return  boolean             Whether the value was found or not
		 */
		static function searchHaystack( $needle, $haystack, $strict=true ) {
			$haystack = self::ToArray( $haystack );

			if( is_array( $haystack ) ) {
				if( self::isMultiDimensional( $haystack ) ) {   // Multidimensional array
					foreach( $haystack as $subhaystack ) {
						if( self::searchHaystack( $needle, $subhaystack, $strict ) ) {
							return true;
						}
					}
				} elseif( array_keys( $haystack ) !== range( 0, count( $haystack ) - 1 ) ) {    // Associative array
					foreach( $haystack as $key => $val ) {
						if( $needle == $val && !$strict ) {
							return true;
						} elseif( $needle === $val && $strict ) {
							return true;
						}
					}

					return false;
				} else {    
					// Normal array
					if(!$strict){
						return $needle == $haystack;
					}else
					
					return $needle === $haystack;
				}
			}

			return false;
		}

	}
}