<?php
/**
 * Theme options class
 *
 * @package Anonymous theme
 * @author Makiomar
 * @link http://makiomar.com
 */

defined( 'ABSPATH' ) || die();

if ( ! class_exists( 'ANONY_Options_Model' ) ) {
	/**
	 * Group many options of our themes into 1
	 * Create an option group in wp_options using the name provided when construct the object, e.g.:
	 *      $anony_option = new ANONY_Options_Model("anony_option");
	 * Get & Set new option in this group using assignments & save() function, e.g.:
	 *      $anony_option->logo = {url};
	 *      $anony_option->save();
	 *      echo $anony_option->logo;
	 * Options in this group will be stored as an array, however to get an option, use -> instead of [], since this class use __get & __set methods.
	 * This class keeps the old ways of retrieving options, so you can also use $anony_option->get_option() & update_options(), add_options()
	 */
	class ANONY_Options_Model {

		/**
		 * Option group
		 *
		 * @var string
		 */
		protected $option_group;

		/**
		 * Options array
		 *
		 * @var array
		 */
		protected $options_arr = array();

		/**
		 * This will not instantiate new object if option_name is not changed
		 *
		 * @var object
		 */
		public static $instance = null;

		/**
		 * This will help instantiate new object if option_name is changed
		 *
		 * @var string
		 */
		public static $object_changed_to = null;

		/**
		 * Get option object instance
		 *
		 * @param string $option_name Option name.
		 * @return object Option object instance.
		 */
		public static function get_instance( $option_name = ANONY_OPTIONS ) {
			if ( null === self::$instance ) {
				self::$instance = new ANONY_Options_Model( $option_name );
			} elseif ( null !== self::$instance && self::$object_changed_to !== $option_name ) {

				self::$object_changed_to = $option_name;
				self::$instance          = new ANONY_Options_Model( $option_name );
			}
			return self::$instance;
		}

		/**
		 * Construct the option using the provided option_name,
		 *
		 * @param string $option_name Option name.
		 */
		public function __construct( $option_name = ANONY_OPTIONS . ' ' ) {
			$this->option_group      = trim( $option_name );
			self::$object_changed_to = trim( $option_name );
			// get the current value of this option.
			$existed = get_option( $this->option_group );

			// if there is an existed value, assign it to the array.
			if ( $existed ) {
				$this->options_arr = $existed;
			}
		}

		/**
		 * Create or update an option name if not existed in this option group
		 * this function is run when trying to write to an inaccessible property
		 *
		 * @param string $option_name Option name.
		 * @param string $option_value Option value.
		 * @return void
		 */
		public function __set( $option_name, $option_value ) {
			$this->options_arr[ $option_name ] = $option_value;
			$this->save();
		}

		/**
		 * Read an option in this option group
		 * this function is run when trying to read an inaccessible property
		 *
		 * @param string $option_name Option name.
		 * @return mixed Value of an option.
		 */
		public function __get( $option_name ) {

			if ( array_key_exists( $option_name, $this->options_arr ) ) {

				return $this->options_arr[ $option_name ];
			}

			return false;
		}

		/**
		 * Check if an option is existed or not
		 * this function is run when trying to isset() or empty() an inaccessible property.
		 *
		 * @param string $option_name Option name.
		 * @return bool true if isset and false if is not set.
		 */
		public function __isset( $option_name ) {
			return isset( $this->options_arr[ $option_name ] );
		}

		/**
		 * Unset an option if existed
		 * this function is run when trying to unset() an inaccessible property
		 *
		 * @param string $option_name Option name.
		 */
		public function __unset( $option_name ) {
			unset( $this->options_arr[ $option_name ] );
		}

		/**
		 * Save the current option values into database
		 */
		public function save() {
			return update_option( $this->option_group, $this->options_arr );
		}
		/**
		 * Save the current option values into database
		 *
		 * @param array $option_arr Options array.
		 * @return bool result of update option function
		 * @since 1.0
		 * @package Appengine
		 * @category void
		 * @author Daikachi
		 */
		public function reset( $option_arr = array() ) {
			$this->options_arr = $option_arr;
			return update_option( $this->option_group, $option_arr );
		}

		/**
		 * Print all options of the theme
		 */
		public function __toString() {
			return ANONY_Wp_Debug_Help::neat_print_r( $this->options_arr );
		}

		/**
		 * Get an option.
		 *
		 * @param string $option_name Option name.
		 * @param mixed  $default_value Default value.
		 * @return mixed Value of option.
		 */
		public function get_option( $option_name, $default_value = false ) {
			if ( ! isset( $this->options_arr[ $option_name ] ) ) {
				return $default_value;
			}
			return $this->options_arr[ $option_name ];
		}

		/**
		 * Update option value
		 *
		 * @param string $option_name Option name.
		 * @param mixed  $new_value Option new value.
		 * @return bool
		 */
		public function update_option( $option_name, $new_value ) {
			if ( current_user_can( 'manage_options' ) ) {
				$this->options_arr[ $option_name ] = $new_value;
				return update_option( $this->option_group, $this->options_arr );
			} else {
				return false;
			}
		}

		/**
		 * Add new option
		 *
		 * @param string $option_name Option name.
		 * @param mixed  $value Option value.
		 * @return bool
		 */
		public function add_option( $option_name, $value ) {
			if ( current_user_can( 'manage_options' ) ) {
				return self::update_option( $option_name, $value );
			} else {
				return false;
			}
		}

		/**
		 * Delete option
		 *
		 * @param string $option_name Option name.
		 * @return bool
		 */
		public function delete_option( $option_name ) {
			if ( current_user_can( 'manage_options' ) && isset( $this->options_arr[ $option_name ] ) ) {
				unset( $this->options_arr[ $option_name ] );
				return update_option( $this->option_group, $this->options_arr );
			} else {
				return false;
			}
		}

		/**
		 *  Get all current option values of this object
		 *
		 * @return array
		 */
		public function get_all_current_options() {
			return $this->options_arr;
		}

		/**
		 * Get all option values of this object in database
		 *
		 * @return array
		 */
		public function get_all_options_in_database() {
			return get_option( $this->option_group );
		}

		/**
		 * Validate option
		 *
		 * @param string $type data type.
		 * @param mixed  $value value to be validated.
		 * @return bool
		 */
		protected static function validate( $type, $value ) {
			return $value;
		}
	}
}
