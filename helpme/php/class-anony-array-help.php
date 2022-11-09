<?php
/**
 * Array helpers.
 *
 * PHP version 7.3 Or Later.
 *
 * @package  AnonyEngine
 * @author   Makiomar <info@makiomar.com>
 * @license  https:// makiomar.com AnonyEngine Licence.
 * @link     https:// makiomar.com/anonyengine
 */

defined( 'ABSPATH' ) || die();

if ( ! class_exists( 'ANONY_ARRAY_HELP' ) ) {
	/**
	 * PHP Array helpers class
	 *
	 * @package    AnonyEngine
	 * @author     Makiomar <info@makiomar.com>
	 * @license    https://makiomar.com AnonyEngine Licence
	 * @link       https://makiomar.com
	 */
	class ANONY_ARRAY_HELP extends ANONY_HELP {

		/**
		 * Checks if an array is sequentially indexed
		 *
		 * @param  array $array To be checked array.
		 * @return bool
		 */
		public static function is_sequentially_indexed( array $array ) {

			if ( array() === $array ) {
				return false;
			}

			return array_keys( $array ) === range( 0, count( $array ) - 1 );
		}

		/**
		 * Checks if an array is indexed
		 *
		 * @param  array $array To be checked array.
		 * @return bool
		 */
		public static function is_indexed( $array ) {
			if ( empty( array_filter( array_keys( $array ), 'is_string' ) ) ) {
				return true;
			}
			return false;
		}

		/**
		 * Checks if an array is associative
		 *
		 * @param  array $array To be checked array.
		 * @return bool
		 */
		public static function is_assoc( $array ) {
			if ( ! self::is_indexed( $array ) ) {
				return true;
			}
			if ( ! self::is_sequentially_indexed( $array ) ) {
				return true;
			}
			return false;
		}

		/**
		 * Gets an associative array as key/value pairs from any object properties.
		 *
		 * Useful where a select input field has dynamic options.
		 *
		 * @param object $objects_array The array of objects to loop through.
		 * @param string $key           The property that should be used as a key.
		 * @param string $value         The property that should be used as a value.
		 * @param bool   $is_associative  Weather the to convert to associative array or indexed one. Default 'yes'.
		 * @return array                An array of properties as key/value pairs.
		 */
		public static function object_to_associative_array( $objects_array, $key, $value, $is_associative = 'yes' ) {

			$arr = array();

			foreach ( $objects_array as $object ) {
				if ( 'yes' === $is_associative && ! empty( $key ) ) {
					$arr[ $object->$key ] = $object->$value;
				} else {
					$arr[] = $object->$value;
				}
			}

			return $arr;
		}

		/**
		 * Same as print_r but usefull for rtl pages.
		 *
		 * @param array $array To be printed array.
		 * @return void
		 */
		public static function neat_print_r( $array ) {
			if ( defined( 'WP_BEBUG' ) && true === WP_BEBUG ) {
				echo '<pre dir="ltr" style="diretction:ltr;text-align:center">';
					// phpcs:disable WordPress.PHP.DevelopmentFunctions
					print_r( $array );
					// phpcs: enable.
				echo '</pre>';
			}

		}

		/**
		 * Insert a key/value before another in an associative array.
		 *
		 * @param array  $original_array The Array.
		 * @param strin  $original_key   The key to be iserted before.
		 * @param array  $insert_key     To be inserted key.
		 * @param string $insert_value   To be inserted value.
		 * @return array
		 */
		public static function insert_before_key( $original_array, $original_key, $insert_key, $insert_value ) {

			$new_array = array();
			$inserted  = false;

			foreach ( $original_array as $key => $value ) {

				if ( ! $inserted && $key === $original_key ) {
					$new_array[ $insert_key ] = $insert_value;
					$inserted                 = true;
				}

				$new_array[ $key ] = $value;

			}

			return $new_array;

		}

		/**
		 *  Check if an array is a multidimensional array.
		 *
		 *  @param   array $array  The array to check.
		 *  @return  boolean     Whether the the array is a multidimensional array or not.
		 */
		public static function is_multi_dimensional( $array ) {
			if ( count( array_filter( $array, 'is_array' ) ) > 0 ) {
				return true;
			}
			return false;
		}

		/**
		 *  Convert an object to an array.
		 *
		 *  @param   array $object  The object to convert.
		 *  @return  array          The converted array.
		 */
		public static function to_array( $object ) {
			if ( ! is_object( $object ) || ! is_array( $object ) ) {
				return $object;
			}
			return array_map( array( self, 'object_to_array' ), (array) $object );
		}

		public static function convert_object_to_array($data) {

		    if (is_object($data)) {
		        $data = get_object_vars($data);
		    }

		    if (is_array($data)) {
		        return array_map(__FUNCTION__, $data);
		    }
		    else {
		        return $data;
		    }
		}

		/**
		 *  Check if a value exists in the array/object.
		 *
		 *  @param   mixed   $needle    The value that you are searching for.
		 *  @param   mixed   $haystack  The array/object to search.
		 *  @param   boolean $strict    Whether to use strict search or not.
		 *  @return  boolean            Whether the value was found or not.
		 */
		public static function search_haystack( $needle, $haystack, $strict = true ) {
			$haystack = self::to_array( $haystack );

			if ( is_array( $haystack ) ) {
				if ( self::is_multi_dimensional( $haystack ) ) {   // Multidimensional array.
					foreach ( $haystack as $subhaystack ) {
						if ( self::search_haystack( $needle, $subhaystack, $strict ) ) {
							return true;
						}
					}
				} elseif ( array_keys( $haystack ) !== range( 0, count( $haystack ) - 1 ) ) {    // Associative array.
					foreach ( $haystack as $key => $val ) {
						if ( $needle === $val && ! $strict ) {
							return true;
						} elseif ( $needle === $val && $strict ) {
							return true;
						}
					}

					return false;
				} else {
					// Normal array.
					if ( ! $strict ) {
						return $needle === $haystack;
					} else {

						return $needle === $haystack;
					}
				}
			}

			return false;
		}

		/**
		 * Insertes new key/value pairs after a specific key.
		 *
		 * @param  string $key Insert after this key.
		 * @param  array  $insert_array To be inserted array.
		 * @param  array  $original_array Original array.
		 * @return array  Array after insertion.
		 */
		public static function insert_after_assoc_key( $key, $insert_array, $original_array ) {

			$offset = array_search( $key, array_keys( $original_array ), true );

			$result = array_merge(
				array_slice( $original_array, 0, $offset ),
				$insert_array,
				array_slice( $original_array, $offset, null )
			);

			return $resutl;
		}

		/**
		 * Compairs two associative arrays and replace default values of first array with new value in second array.
		 *
		 * @param  array $defaults Defaults array.
		 * @param  array $atts     New Array.
		 * @return array
		 */
		public static function defaults_mapping( array $defaults, array $atts ) {
			$out = array();
			foreach ( $defaults as $name => $default ) {

				if ( array_key_exists( $name, $atts ) ) {
					$out[ $name ] = $atts[ $name ];
				} else {
					$out[ $name ] = $default;
				}
			}
			return $out;
		}

		/**
		 * Sorts multi-dimensional array by a given key value.
		 *
		 * @param  string $key Sorting key.
		 * @param  array  $array Array to be sorted.
		 * @param  string $flag Sorting flag ( Refere to: https://www.php.net/manual/en/function.array-multisort.php ).
		 * @return array Sorted array.
		 */
		public static function array_multisort_by_key( $key, $array, $flag = SORT_DESC ) {

			$sort_country = array_column( $array, $key );

			array_multisort( $sort_country, $flag, $array );

			return $array;

		}

		/**
		 * Function that groups an array of associative arrays by some key.
		 *
		 * @param string $key Property to sort by.
		 * @param array  $data Array that stores multiple associative arrays.
		 */
		public static function group_by( $key, $data ) {
			$result = array();

			foreach ( $data as $val ) {
				if ( array_key_exists( $key, $val ) ) {
					$result[ $val[ $key ] ][] = $val;
				} else {
					$result[''][] = $val;
				}
			}

			return $result;
		}

		/**
		 * Get first key/value pair.
		 *
		 * @param array $my_array Target array.
		 * @param array
		 */
		public static function array_1st_element($my_array)
		{
			if( empty( $my_array ) ) return $my_array;
			
			list($k) = array_keys($my_array);

			$result  = array( $k => $my_array[$k] );

			unset($my_array[$k]);

			return $result;
		}
	}
}
