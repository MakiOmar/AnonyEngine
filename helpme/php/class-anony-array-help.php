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
		 * @param  array $_arr To be checked array.
		 * @return bool
		 */
		public static function is_sequentially_indexed( array $_arr ) {

			if ( array() === $_arr ) {
				return false;
			}

			return array_keys( $_arr ) === range( 0, count( $_arr ) - 1 );
		}

		/**
		 * Checks if an array is indexed
		 *
		 * @param  array $_arr To be checked array.
		 * @return bool
		 */
		public static function is_indexed( $_arr ) {
			if ( empty( array_filter( array_keys( $_arr ), 'is_string' ) ) ) {
				return true;
			}
			return false;
		}

		/**
		 * Checks if an array is associative
		 *
		 * @param  array $_arr To be checked array.
		 * @return bool
		 */
		public static function is_assoc( $_arr ) {
			if ( ! self::is_indexed( $_arr ) ) {
				return true;
			}
			if ( ! self::is_sequentially_indexed( $_arr ) ) {
				return true;
			}
			return false;
		}

		/**
		 * Gets an associative array as key/value pairs from any object properties.
		 *
		 * Useful where a select input field has dynamic options.
		 *
		 * @param object $_objs_array The array of objects to loop through.
		 * @param string $key           The property that should be used as a key.
		 * @param string $value         The property that should be used as a value.
		 * @param bool   $is_associative  Weather the to convert to associative array or indexed one. Default 'yes'.
		 * @return array                An array of properties as key/value pairs.
		 */
		public static function object_to_associative_array( $_objs_array, $key, $value, $is_associative = 'yes' ) {

			$arr = array();

			foreach ( $_objs_array as $_obj ) {
				if ( 'yes' === $is_associative && ! empty( $key ) ) {
					$arr[ $_obj->$key ] = $_obj->$value;
				} else {
					$arr[] = $_obj->$value;
				}
			}

			return $arr;
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
		 *  @param   array $_arr  The array to check.
		 *  @return  boolean     Whether the the array is a multidimensional array or not.
		 */
		public static function is_multi_dimensional( $_arr ) {
			if ( count( array_filter( $_arr, 'is_array' ) ) > 0 ) {
				return true;
			}
			return false;
		}

		/**
		 *  Convert an object to an array.
		 *
		 *  @param   array $_obj  The object to convert.
		 *  @return  array          The converted array.
		 */
		public static function to_array( $_obj ) {
			if ( ! is_object( $_obj ) || ! is_array( $_obj ) ) {
				return $_obj;
			}
			return array_map( array( 'ANONY_ARRAY_HELP', 'convert_object_to_array' ), (array) $_obj );
		}
		/**
		 * Convert object to array
		 *
		 * @param object $data Object.
		 * @return array
		 */
		public static function convert_object_to_array( $data ) {

			if ( is_object( $data ) ) {
				$data = get_object_vars( $data );
			}

			if ( is_array( $data ) ) {
				return array_map( __FUNCTION__, $data );
			} else {
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
				} elseif ( ! $strict ) {
					return $needle === $haystack;
				}
				return $needle === $haystack;
			}

			return false;
		}

		/**
		 * Insertes new key/value pairs after a specific key.
		 *
		 * @param  array  $_arr The array.
		 * @param  string $key Insert after this key.
		 * @param  array  $new_key The new key.
		 * @param  array  $new_value The new value.
		 * @return array  Array after insertion.
		 */
		public static function insert_after_assoc_key( $_arr, $key, $new_key, $new_value ) {

			$keys  = array_keys( $_arr );
			$index = array_search( $key, $keys, true );
			if ( false !== $index ) {
				$result = array_slice( $_arr, 0, $index + 1, true ) +
							array( $new_key => $new_value ) +
							array_slice( $_arr, $index + 1, count( $_arr ) - 1, true );
				return $result;
			}
			return $_arr;
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
		 * @param  array  $_arr Array to be sorted.
		 * @param  string $flag Sorting flag ( Refere to: https://www.php.net/manual/en/function.array-multisort.php ).
		 * @return array Sorted array.
		 */
		public static function array_multisort_by_key( $key, $_arr, $flag = SORT_DESC ) {

			$sort_country = array_column( $_arr, $key );

			array_multisort( $sort_country, $flag, $_arr );

			return $_arr;
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
		 * Sort assotiative array based on keys order of another array.
		 *
		 * @param array $_arr Array to be sorted.
		 * @param array $key_order Array that contains ordered keys.
		 * @return array
		 */
		public static function sort_array_by_key_order( $_arr, $key_order ) {
			// Custom sorting function using the specified key order.
			uksort(
				$_arr,
				function ( $a, $b ) use ( $key_order ) {
					$pos_a = array_search( $a, $key_order, true );
					$pos_b = array_search( $b, $key_order, true );

					// Handle cases where keys are not found in $key_order.
					if ( false === $pos_a ) {
						$pos_a = PHP_INT_MAX;
					}
					if ( false === $pos_b ) {
						$pos_b = PHP_INT_MAX;
					}

					return $pos_a - $pos_b;
				}
			);
			return $_arr;
		}

		/**
		 * Sort $_arr_b items according to their position in $_arr_a
		 *
		 * @param array $_arr_a Array A.
		 * @param array $_arr_b Array B.
		 * @return array
		 */
		public static function b_sort_from_a( $_arr_a, $_arr_b ) {
			// Custom comparison function to sort according to the position in array A.
			usort(
				$_arr_b,
				function ( $a, $b ) use ( $_arr_a ) {
					// Find the positions of $a and $b in array A.
					$pos_a = array_search( $a, $_arr_a, true );
					$pos_b = array_search( $b, $_arr_a, true );

					// Compare positions.
					return $pos_a - $pos_b;
				}
			);
			return $_arr_b;
		}

		/**
		 * Get first key/value pair.
		 *
		 * @param array $my_array Target array.
		 * @return array
		 */
		public static function array_1st_element( $my_array ) {
			if ( empty( $my_array ) ) {
				return $my_array;
			}

			list($k) = array_keys( $my_array );

			$result = array( $k => $my_array[ $k ] );

			unset( $my_array[ $k ] );

			return $result;
		}
		/**
		 * Store array in acookie
		 *
		 * @param array  $_arr The array.
		 * @param string $cookie_name Cookie's name.
		 * @param string $expiry Cookie expiry.
		 * @return void
		 */
		public static function array_to_cookie( $_arr, $cookie_name, $expiry ) {
			//phpcs:disable
			$serialized_result = serialize( $_arr );
			$encoded_values    = urlencode( $serialized_result );
			setcookie( $cookie_name, $encoded_values, $expiry, '/' );
			//phpcs:enable
		}
	}
}
