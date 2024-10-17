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
		 * Flag to determine if options should be read from JSON.
		 *
		 * @var bool
		 */
		protected $read_from_json = false;

		/**
		 * Directory for JSON file storage.
		 *
		 * @var string
		 */
		private $json_dir;

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
			$this->set_read_from_json_flag();
			$this->set_json_directory();
			// Load options based on the flag.
			if ( $this->read_from_json ) {
				$this->options_arr = $this->load_options_from_json();
			} else {
				$this->options_arr = get_option( $this->option_group, array() );
			}
		}
		/**
		 * Set the flag for reading options from JSON using a filter.
		 */
		private function set_read_from_json_flag() {
			$hook_name            = strtolower( "anony_read_from_json_{$this->option_group}" );
			$this->read_from_json = apply_filters( $hook_name, false );
		}

		/**
		 * Set the directory for JSON file storage.
		 */
		private function set_json_directory() {
			$upload_dir     = wp_upload_dir();
			$this->json_dir = $upload_dir['basedir'] . '/anony-options';
		}

		/**
		 * Load options from the JSON file.
		 *
		 * @return array
		 */
		private function load_options_from_json() {
			global $wp_filesystem;

			// Initialize the WP_Filesystem API.
			if ( ! function_exists( 'WP_Filesystem' ) ) {
				require_once ABSPATH . 'wp-admin/includes/file.php';
			}

			WP_Filesystem();

			// Construct the file path.
			$file_path = $this->json_dir . '/' . $this->option_group . '.json';

			// Check if the file exists.
			if ( ! $wp_filesystem->exists( $file_path ) ) {
				return array(); // Return an empty array if the file does not exist.
			}

			// Get the contents of the file.
			$json_data = $wp_filesystem->get_contents( $file_path );

			// Decode the JSON data into an associative array.
			$options = json_decode( $json_data, true );

			// Ensure the result is an array before returning.
			return is_array( $options ) ? $options : array();
		}



		// [Other existing methods remain unchanged]

		/**
		 * Save options to the database if modified and optionally to JSON.
		 *
		 * @return bool
		 */
		public function save() {
			if ( $this->dirty ) {
				$this->dirty = false;
				$result      = update_option( $this->option_group, $this->options_arr );

				// Save to JSON if the flag is enabled.
				if ( $this->read_from_json ) {
					$this->save_options_to_json();
				}

				return $result;
			}
			return false;
		}

		/**
		 * Save options to a JSON file.
		 */
		private function save_options_to_json() {
			global $wp_filesystem;

			// Initialize the WP_Filesystem.
			if ( ! function_exists( 'WP_Filesystem' ) ) {
				require_once ABSPATH . 'wp-admin/includes/file.php';
			}

			WP_Filesystem();

			// Ensure the directory exists.
			if ( ! $wp_filesystem->is_dir( $this->json_dir ) ) {
				$wp_filesystem->mkdir( $this->json_dir, FS_CHMOD_DIR );
			}

			$file_path = $this->json_dir . '/' . $this->option_group . '.json';
			$json_data = wp_json_encode( $this->options_arr, JSON_PRETTY_PRINT );

			// Write the JSON file using WP_Filesystem.
			$wp_filesystem->put_contents( $file_path, $json_data, FS_CHMOD_FILE );
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
