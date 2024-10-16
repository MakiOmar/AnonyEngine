<?php
/**
 * Theme options class
 *
 * @package Anonymous theme
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
		 * Indicates if the options have been modified.
		 *
		 * @var bool
		 */
		protected $dirty = false;

		/**
		 * Singleton instance
		 *
		 * @var self|null
		 */
		private static $instance = null;

		/**
		 * Option group tracking for instance change
		 *
		 * @var string|null
		 */
		private static $object_changed_to = null;

		/**
		 * Get singleton instance
		 *
		 * @param string $option_name Option group name.
		 * @return self
		 */
		public static function get_instance( $option_name = ANONY_OPTIONS ) {
			if ( null === self::$instance || self::$object_changed_to !== $option_name ) {
				self::$object_changed_to = $option_name;
				self::$instance          = new self( $option_name );
			}
			return self::$instance;
		}

		/**
		 * Constructor
		 *
		 * @param string $option_name Option group name.
		 */
		private function __construct( $option_name ) {
			$this->option_group = trim( $option_name );
			$this->options_arr  = get_option( $this->option_group, array() );
		}

		/**
		 * Set option value
		 *
		 * @param string $option_name Option name.
		 * @param mixed  $option_value Option value.
		 */
		public function __set( $option_name, $option_value ) {
			if ( ! isset( $this->options_arr[ $option_name ] ) || $this->options_arr[ $option_name ] !== $option_value ) {
				$this->options_arr[ $option_name ] = $option_value;
				$this->dirty                       = true;
			}
		}

		/**
		 * Get option value
		 *
		 * @param string $option_name Option name.
		 * @return mixed|null
		 */
		public function __get( $option_name ) {
			return $this->options_arr[ $option_name ] ?? null;
		}

		/**
		 * Check if an option exists
		 *
		 * @param string $option_name Option name.
		 * @return bool
		 */
		public function __isset( $option_name ) {
			return isset( $this->options_arr[ $option_name ] );
		}

		/**
		 * Unset an option
		 *
		 * @param string $option_name Option name.
		 */
		public function __unset( $option_name ) {
			if ( isset( $this->options_arr[ $option_name ] ) ) {
				unset( $this->options_arr[ $option_name ] );
				$this->dirty = true;
			}
		}

		/**
		 * Save options to the database if modified
		 *
		 * @return bool
		 */
		public function save() {
			if ( $this->dirty ) {
				$this->dirty = false;
				return update_option( $this->option_group, $this->options_arr );
			}
			return false;
		}

		/**
		 * Reset options to a new set of values
		 *
		 * @param array $option_arr New options array.
		 * @return bool
		 */
		public function reset( $option_arr = array() ) {
			$this->options_arr = $option_arr;
			$this->dirty       = true;
			return $this->save();
		}

		/**
		 * Get an option with a default value fallback
		 *
		 * @param string $option_name Option name.
		 * @param mixed  $default_value Default value.
		 * @return mixed
		 */
		public function get_option( $option_name, $default_value = null ) {
			return $this->options_arr[ $option_name ] ?? $default_value;
		}

		/**
		 * Update an existing option
		 *
		 * @param string $option_name Option name.
		 * @param mixed  $new_value New value for the option.
		 * @return bool
		 */
		public function update_option( $option_name, $new_value ) {
			if ( current_user_can( 'manage_options' ) ) {
				$this->$option_name = $new_value;
				return $this->save();
			}
			return false;
		}

		/**
		 * Add a new option
		 *
		 * @param string $option_name Option name.
		 * @param mixed  $value Option value.
		 * @return bool
		 */
		public function add_option( $option_name, $value ) {
			return $this->update_option( $option_name, $value );
		}

		/**
		 * Delete an option
		 *
		 * @param string $option_name Option name.
		 * @return bool
		 */
		public function delete_option( $option_name ) {
			if ( current_user_can( 'manage_options' ) && isset( $this->options_arr[ $option_name ] ) ) {
				unset( $this->options_arr[ $option_name ] );
				$this->dirty = true;
				return $this->save();
			}
			return false;
		}

		/**
		 * Get all current options
		 *
		 * @return array
		 */
		public function get_all_current_options() {
			return $this->options_arr;
		}

		/**
		 * Get all options from the database
		 *
		 * @return array
		 */
		public function get_all_options_in_database() {
			return get_option( $this->option_group, array() );
		}
	}
}
