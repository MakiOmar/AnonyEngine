<?php
/**
 * Plugin Name: AnonyEngine
 * Plugin URI: https://makiomar.com
 * Description: With AnonyEngine you can add any kind of metaboxes and options pages and forms easily and super fast
 * Version: 1.0.0224
 * Requires at least: 5.0
 * Tested up to: 6.4
 * Requires PHP: 7.3
 * Author: Mohammad Omar
 * Author URI: https://makiomar.com
 * Text Domain: anonyengine
 * Domain Path: /languages
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Network: false
 *
 * @package AnonyEngine
 * @version 1.0.0224
 * @author Mohammad Omar <info@makiomar.com>
 * @license GPL-2.0-or-later
 * @link https://makiomar.com
 */

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Define plugin constants.
if ( ! defined( 'ANOE_PLUGIN_SLUG' ) ) {
	define( 'ANOE_PLUGIN_SLUG', plugin_basename( __FILE__ ) );
}

if ( ! defined( 'ANOE_DIR' ) ) {
	define( 'ANOE_DIR', wp_normalize_path( plugin_dir_path( __FILE__ ) ) );
}

if ( ! defined( 'ANOE_URI' ) ) {
	define( 'ANOE_URI', plugin_dir_url( __FILE__ ) );
}

if ( ! defined( 'ANOE_VERSION' ) ) {
	define( 'ANOE_VERSION', '1.0.0224' );
}

if ( ! defined( 'ANOE_LIBS_URI' ) ) {
	define( 'ANOE_LIBS_URI', ANOE_DIR . 'libs/' );
}

if ( ! defined( 'ANOE_FUNC_DIR' ) ) {
	define( 'ANOE_FUNC_DIR', ANOE_DIR . 'functions/' );
}

/**
 * Main AnonyEngine class.
 *
 * @since 1.0.0
 */
final class AnonyEngine {

	/**
	 * Plugin instance.
	 *
	 * @since 1.0.0
	 * @var AnonyEngine
	 */
	private static $instance = null;

	/**
	 * Plugin version.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	private $version;

	/**
	 * Plugin directory.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	private $plugin_dir;

	/**
	 * Plugin URL.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	private $plugin_url;

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 */
	private function __construct() {
		$this->version    = ANOE_VERSION;
		$this->plugin_dir = ANOE_DIR;
		$this->plugin_url = ANOE_URI;

		$this->init();
	}

	/**
	 * Get plugin instance.
	 *
	 * @since 1.0.0
	 * @return AnonyEngine
	 */
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Initialize the plugin.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	private function init() {
		// Load dependencies.
		$this->load_dependencies();

		// Initialize hooks.
		$this->init_hooks();

		// Load text domain.
		add_action( 'plugins_loaded', array( $this, 'load_textdomain' ) );

		// Initialize update checker.
		$this->init_update_checker();

		// Fire action when plugin is loaded.
		do_action( 'anonyengine_loaded', $this );
	}

	/**
	 * Load plugin dependencies.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	private function load_dependencies() {
		// Load Composer autoloader.
		if ( file_exists( $this->plugin_dir . 'vendor/autoload.php' ) ) {
			require_once $this->plugin_dir . 'vendor/autoload.php';
		}

		// Load plugin update checker.
		if ( file_exists( $this->plugin_dir . 'plugin-update-checker/plugin-update-checker.php' ) ) {
			require_once $this->plugin_dir . 'plugin-update-checker/plugin-update-checker.php';
		}

		// Load fonts library.
		if ( file_exists( ANOE_LIBS_URI . 'fonts.php' ) ) {
			require_once ANOE_LIBS_URI . 'fonts.php';
		}

		// Load configuration.
		if ( file_exists( $this->plugin_dir . 'config.php' ) ) {
			require_once $this->plugin_dir . 'config.php';
		}
	}

	/**
	 * Initialize WordPress hooks.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	private function init_hooks() {
		// Enqueue scripts and styles.
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

		// Add plugin action links.
		add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), array( $this, 'add_plugin_action_links' ) );

		// Handle plugin activation.
		add_action( 'activated_plugin', array( $this, 'on_plugin_activated' ) );

		// Handle plugin deactivation.
		add_action( 'deactivate_plugin', array( $this, 'on_plugin_deactivated' ), 10, 2 );

		// Register post types and taxonomies.
		add_action( 'init', array( 'ANONY_Post_Help', 'register_post_types' ) );
		add_action( 'init', array( 'ANONY_Taxonomy_Help', 'register_taxonomies' ) );

		// Load Google Maps API.
		add_action( 'wp_enqueue_scripts', array( $this, 'load_google_maps_api' ) );

		// Add head scripts.
		add_action( 'wp_head', array( $this, 'add_head_scripts' ) );
	}

	/**
	 * Initialize update checker.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	private function init_update_checker() {
		if ( class_exists( 'Puc_v4_Factory' ) ) {
			$update_checker = Puc_v4_Factory::buildUpdateChecker(
				'https://github.com/MakiOmar/AnonyEngine/',
				__FILE__,
				ANOE_PLUGIN_SLUG
			);

			// Set the branch that contains the stable release.
			$update_checker->setBranch( 'master' );
		}
	}

	/**
	 * Load plugin text domain.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function load_textdomain() {
		load_plugin_textdomain(
			'anonyengine',
			false,
			dirname( plugin_basename( __FILE__ ) ) . '/languages'
		);
	}

	/**
	 * Enqueue scripts and styles.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function enqueue_scripts() {
		// Enqueue JavaScript files.
		$scripts = array( 'jquery.helpme' );
		foreach ( $scripts as $script ) {
			$script_path = $this->plugin_dir . 'assets/js/' . $script . '.js';
			if ( file_exists( $script_path ) ) {
				wp_enqueue_script(
					$script,
					$this->plugin_url . 'assets/js/' . $script . '.js',
					array( 'jquery' ),
					filemtime( $script_path ),
					true
				);
			}
		}

		// Enqueue CSS files.
		$styles_libs = array( 'jquery.ui.slider-rtl' );
		$styles      = array( 'responsive', 'anonyengine' );
		$styles      = array_merge( $styles, $styles_libs );

		foreach ( $styles as $style ) {
			$style_path = $this->plugin_dir . 'assets/css/' . $style . '.css';
			if ( file_exists( $style_path ) ) {
				$handle = in_array( $style, $styles_libs, true ) ? $style : 'anony-' . $style;
				wp_enqueue_style(
					$handle,
					$this->plugin_url . 'assets/css/' . $style . '.css',
					array(),
					filemtime( $style_path )
				);
			}
		}

		// Enqueue additional scripts.
		$additional_scripts = array( 'jquery.ui.slider-rtl.min' );
		foreach ( $additional_scripts as $script ) {
			$script_path = $this->plugin_dir . 'assets/js/' . $script . '.js';
			if ( file_exists( $script_path ) ) {
				wp_register_script(
					$script,
					$this->plugin_url . 'assets/js/' . $script . '.js',
					array( 'jquery', 'jquery-ui-slider' ),
					filemtime( $script_path ),
					true
				);
			}
		}

		// Localize script.
		if ( class_exists( 'ANONY_Wpml_Help' ) ) {
			wp_localize_script(
				'jquery',
				'AnonyLoc',
				array(
					'ajaxUrl' => ANONY_Wpml_Help::get_ajax_url(),
					'nonce'   => wp_create_nonce( 'anonyengine_nonce' ),
				)
			);
		}
	}

	/**
	 * Load Google Maps API.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function load_google_maps_api() {
		if ( ! class_exists( 'ANONY_Options_Model' ) || ! defined( 'ANONY_ENGINE_OPTIONS' ) ) {
			return;
		}

		$engine_options = ANONY_Options_Model::get_instance( ANONY_ENGINE_OPTIONS );
		
		if ( empty( $engine_options->google_maps_api_key ) || '1' !== $engine_options->enable_google_maps_script ) {
			return;
		}

		$region   = ! empty( $engine_options->google_maps_region ) ? sanitize_text_field( $engine_options->google_maps_region ) : 'EG';
		$language = ! empty( $engine_options->google_maps_language ) ? sanitize_text_field( $engine_options->google_maps_language ) : 'ar';

		$script_src = add_query_arg(
			array(
				'v'         => '3.exp',
				'key'       => sanitize_text_field( $engine_options->google_maps_api_key ),
				'language'  => $language,
				'region'    => $region,
				'libraries' => 'places',
				'callback'  => 'initMap',
			),
			'https://maps.googleapis.com/maps/api/js'
		);

		wp_register_script(
			'anony-google-map-api',
			esc_url( $script_src ),
			array(),
			'4.9.10',
			array(
				'in_footer' => true,
				'strategy'  => 'async',
			)
		);
	}

	/**
	 * Add head scripts.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function add_head_scripts() {
		?>
		<script>
		if ( typeof initMap !== 'function' ) {
			function initMap() {
				console.log('%cGoogle map api has been called for a location field', 'color: green');
			}
		}
		</script>
		<?php
	}

	/**
	 * Add plugin action links.
	 *
	 * @since 1.0.0
	 * @param array $links Plugin action links.
	 * @return array
	 */
	public function add_plugin_action_links( $links ) {
		$settings_link = sprintf(
			'<a href="%s">%s</a>',
			esc_url( admin_url( 'admin.php?page=Anony_Engine_Options' ) ),
			esc_html__( 'Settings', 'anonyengine' )
		);
		$links[] = $settings_link;
		return $links;
	}

	/**
	 * Handle plugin activation.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function on_plugin_activated() {
		flush_rewrite_rules();
	}

	/**
	 * Handle plugin deactivation.
	 *
	 * @since 1.0.0
	 * @param string $plugin Plugin file.
	 * @param bool   $network_wide Whether the plugin is network-wide.
	 * @return void
	 */
	public function on_plugin_deactivated( $plugin, $network_wide ) {
		if ( 'anonyengine/anonyengine.php' === $plugin ) {
			$template = get_option( 'template' );
			if ( 'smartpage' === $template ) {
				wp_die(
					esc_html__( 'Sorry, you cannot deactivate this plugin. Because it is mandatory for SmartPage theme.', 'anonyengine' ),
					esc_html__( 'Plugin Deactivation Error', 'anonyengine' ),
					array(
						'response' => 403,
						'back_link' => true,
					)
				);
			}
		}
	}

	/**
	 * Get plugin version.
	 *
	 * @since 1.0.0
	 * @return string
	 */
	public function get_version() {
		return $this->version;
	}

	/**
	 * Get plugin directory.
	 *
	 * @since 1.0.0
	 * @return string
	 */
	public function get_plugin_dir() {
		return $this->plugin_dir;
	}

	/**
	 * Get plugin URL.
	 *
	 * @since 1.0.0
	 * @return string
	 */
	public function get_plugin_url() {
		return $this->plugin_url;
	}
}

// Initialize the plugin.
AnonyEngine::get_instance();
