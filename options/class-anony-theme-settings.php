<?php
/**
 * Options page.
 *
 * PHP version 7.3 Or Later.
 *
 * @package  AnonyEngine
 * @author   Makiomar <info@makiomar.com>
 * @license  https:// makiomar.com AnonyEngine Licence..
 * @link     https:// makiomar.com/anonyengine_elements..
 */

defined( 'ABSPATH' ) || die();

if ( ! class_exists( 'ANONY_Theme_Settings' ) ) {

	/**
	 * Options page class.
	 *
	 * PHP version 7.3 Or Later.
	 *
	 * @package  AnonyEngine
	 * @author   Makiomar <info@makiomar.com>
	 * @license  https:// makiomar.com AnonyEngine Licence..
	 * @link     https:// makiomar.com/anonyengine_elements..
	 */
	class ANONY_Theme_Settings {

		/**
		 * Array of input fields errors. array('field_id' => 'error').
		 *
		 * @var array
		 */
		public $errors = array();

		/**
		 * Holds the resulting page's hook_suffix from add_theme_page.
		 *
		 * @var string
		 */
		public $page;

		/**
		 * Holds the option group name for register_setting.
		 *
		 * @var string
		 */
		public $option_group;

		/**
		 * Holds An array of options page's sections.
		 *
		 * @var array
		 */
		public $sections = array();

		/**
		 * Holds An array of options page's data.
		 *
		 * @var array
		 */
		public $args = array();

		/**
		 * Holds An array of widgets to be registered.
		 *
		 * @var array
		 */
		public $widgets = array();

		/**
		 * Holds an object from ANONY_Options_Model.
		 *
		 * @var object
		 */
		public $options;

		/**
		 * Holds an object from ANONY_Validate_Inputs.
		 *
		 * @var object
		 */
		public $validate;

		/**
		 * Holds an array of options page's menu items.
		 *
		 * @var array
		 */
		public $menu;

		/**
		 * Holds an array of options default values.
		 *
		 * @var array
		 */
		public $default_options;

		/**
		 * Used as a hack to prevent error messages duplication.
		 *
		 * @var integer Holds a number that represents how many a function is called
		 */
		public static $called = 0;

		/**
		 * Holds options page argument.
		 *
		 * @var array
		 */
		public $options_page;

		/**
		 * Holds page add function name.
		 *
		 * @var string
		 */
		public $page_func_name = 'add_theme_page';

		/**
		 * Class Constructor. Defines the args for the theme options class
		 *
		 * @param array $menu Array of options page's menu items.
		 * @param array $sections Array of options page's sections.
		 * @param array $widgets Array of widgets to be registered.
		 * @param array $options_page Options page argument.
		 */
		public function __construct( $menu = array(), $sections = array(), $widgets = array(), $options_page = null ) {

			$this->options_page = $options_page;

			$this->menu = $menu;

			// get page defaults.
			$this->args = $this->opt_page_defaults();

			$this->options = ANONY_Options_Model::get_instance( $this->args['opt_name'] );

			// Set option groups.
			$this->option_group = $this->args['opt_name'] . '_group';

			// Options page sections.
			$this->sections = $sections;

			// Options related widgets.
			$this->widgets = $widgets;

			// set default values.
			$this->default_values();

			$this->hooks();
		}

		/**
		 * Set options page defaults
		 *
		 * @return array An array of page's defaults e.g. [menu_title, page_title, menu_slug, etc]
		 */
		public function opt_page_defaults() {

			$defaults['opt_name'] = ANONY_OPTIONS;

			$defaults['parent_slug'] = '';

			$defaults['page_title'] = esc_html__( 'Anonymous Theme Options', 'anonyengine' );

			$defaults['menu_title'] = esc_html__( 'Anonymous Theme Options', 'anonyengine' );

			$defaults['page_cap'] = 'manage_options';

			$defaults['menu_slug'] = ANONY_OPTIONS;

			$defaults['icon_url'] = 'dashicons-welcome-widgets-menus';

			$defaults['page_position'] = 100;

			$defaults['page_type'] = 'theme';

			if ( ! is_null( $this->options_page ) && is_array( $this->options_page ) && ! empty( $this->options_page ) ) {
				if ( ! isset( $this->options_page['menu_slug'] ) || $this->options_page['menu_slug'] === $defaults['menu_slug'] ) {
					return $defaults;
				}

				$defaults = wp_parse_args( $this->options_page, $defaults );
			}

			return $defaults;
		}

		/**
		 * Theme options hooks
		 */
		public function hooks() {
			// Styles for options in front end.
			add_action( 'wp_head', array( &$this, 'frontend_styles' ) );

			// Load page scripts.
			add_action( 'admin_enqueue_scripts', array( &$this, 'page_scripts' ) );

			// options page.
			add_action( 'admin_menu', array( &$this, 'options_page' ) );

			// register settings_init to the admin_init action hook.
			add_action( 'admin_init', array( &$this, 'settings_init' ) );

			// set option with defaults.
			add_action( 'admin_init', array( &$this, 'set_default_options' ) );

			// Show admin notices.
			add_action( 'admin_notices', array( &$this, 'admin_notices' ) );

			add_action( 'widgets_init', array( &$this, 'register_widgets' ), 999 );
		}

		/**
		 * Get page fields
		 *
		 * @return array An array of fields;
		 */
		public function get_fields() {
			$fields = array();
			foreach ( $this->sections as $section ) {

				if ( isset( $section['fields'] ) ) {

					foreach ( $section['fields'] as $field ) {
						$fields[] = $field;
					}
				}
			}

			return $fields;
		}

		/**
		 * Get default options into an array suitable for the settings API
		 */
		public function default_values() {
			$defaults = array();

			foreach ( $this->sections as $sec_key => $section ) {

				if ( isset( $section['fields'] ) ) {

					foreach ( $section['fields'] as $fieldk => $field ) {
						if ( ! isset( $field['default'] ) ) {
							$field['default'] = '';
						}

						if ( ! empty( $field['id'] ) ) {
							$defaults[ $field['id'] ] = $field['default'];
						}
					}
				}
			}

			$this->default_options = $defaults;
		}

		/**
		 * Set default options if option doesnt exist
		 */
		public function set_default_options() {

			if ( empty( get_option( $this->args['opt_name'] ) ) ) {

				delete_option( $this->args['opt_name'] );

			}

			if ( ! get_option( $this->args['opt_name'] ) ) {

				add_option( $this->args['opt_name'], $this->default_options );

			}
		}

		/**
		 * Class Theme Options Page Function, creates main options page.
		 */
		public function options_page() {
			$screen = get_current_screen();

			if ( ! current_user_can( 'manage_options' ) ) {
				return;
			}

			$page_func_name = $this->page_func_name;

			$args = $this->args;

			$args = ANONY_ARRAY_HELP::insert_before_key( $args, 'icon_url', 'function', array( &$this, 'options_page_html' ) );

			if ( isset( $this->args['page_type'] ) ) {

				unset( $args['opt_name'], $args['page_type'] );

				$page_func_name = 'add_' . $this->args['page_type'] . '_page';

				switch ( $this->args['page_type'] ) {

					case 'submenu':
						if ( isset( $this->args['parent_slug'] ) && ! empty( $this->args['parent_slug'] ) ) {

							unset( $args['icon_url'], $args['page_position'] );
						}

						break;

					case 'menu':
						unset( $args['parent_slug'] );

						break;

					default:
						if ( function_exists( $page_func_name ) ) {
							unset( $args['icon_url'], $args['page_position'], $args['parent_slug'] );
						}

						break;
				}
			} else {
				unset( $args['opt_name'], $args['parent_slug'], $args['icon_url'], $args['page_position'], $args['page_type'] );
			}

			$this->page = call_user_func_array( $page_func_name, array_values( $args ) );

			// Head styles.
			add_action( 'admin_print_styles-' . $this->page, array( &$this, 'admin_styles' ) );
		}

		/**
		 * Class register settings function
		 */
		public function settings_init() {
			// register a new setting for "Anonymous" page.
			register_setting(
				$this->option_group,
				$this->args['opt_name'],
				array( 'sanitize_callback' => array( $this, 'options_validate' ) )
			);

			foreach ( $this->sections as $sec_key => $section ) {

				add_settings_section(
					'anony_' . $sec_key . '_section',
					$section['title'],
					array( &$this, 'section_cb' ),
					// Make sure to add the same in add_settings_field.
					$this->args['menu_slug']
				);

				if ( isset( $section['fields'] ) ) {

					foreach ( $section['fields'] as $field_key => $field ) {

						if ( isset( $field['title'] ) ) {

							$field_title = ( isset( $field['sub_desc'] ) ) ? $field['title'] . '<span class="description">' . $field['sub_desc'] . '</span>' : $field['title'];

						} else {

							$field_title = '';

						}

							add_settings_field(
								$field_key . '_field',
								$field_title,
								array( &$this, 'field_input' ),
								// You should pass the page passed to add_settings_section.
								$this->args['menu_slug'],
								'anony_' . $sec_key . '_section',
								$field
							);

					}
				}
			}
		}

		/**
		 * Class settings' section's callback function.
		 *
		 * @param array $section An array of section's arguments.
		 */
		public function section_cb( $section ) {

			$id = preg_match( '/anony_(.*)_section/', $section['id'], $m );

			$id = $m[1];

			if ( isset( $this->sections[ $id ]['note'] ) ) {
				echo '<p class=anony-section-warning>' . esc_html( $this->sections[ $id ]['note'] ) . '<p>';
			}
		}

		/**
		 * Class section fields callback function.
		 *
		 * @param array $field An array of field's argumnets.
		 */
		public function field_input( $field ) {

			if ( isset( $field['type'] ) ) {

				$field['option_name'] = $this->args['opt_name'];
				if ( class_exists( 'ANONY_Input_Base' ) && class_exists( 'ANONY_Option_Input_Field' ) ) {
					$args = array(
						'field'   => $field,
						'form_id' => $this->args['opt_name'],
					);

					$render_field = new ANONY_Option_Input_Field( $args );
				} else {
					$render_field = new ANONY_Input_Field( $field );
				}

				//phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped
				echo $render_field->field_init();
				//phpcs:enable.

				if ( isset( $field['callback'] ) && function_exists( $field['callback'] ) ) {
					call_user_func_array( $field['callback'], $field );
				}
			}
		}

		/**
		 * Validation function.
		 *
		 * @param  array $not_validated  array of not validated options sent by options form.
		 * @return  array An array of form values after validation.
		 */
		public function options_validate( $not_validated ) {

			++self::$called;

			// Make sure this code runs once to prevent error messages duplication.
			if ( self::$called <= 1 ) {

				$validated = array();
				// phpcs:disable WordPress.Security.NonceVerification.Missing
				$req = $_POST;
				// phpcs:enable.
				foreach ( $this->sections as $sec_key => $section ) {

					if ( isset( $section['fields'] ) ) {

						foreach ( $section['fields'] as $field_key => $field ) {
							if ( empty( $field['id'] ) ) {
								continue;
							}
							$field_i_d = $field['id'];

							// Current value in database.
							$current_value = $this->options->$field_i_d;

							// Something like a checkbox is not set if unchecked.
							if ( ! isset( $not_validated[ $field_i_d ] ) ) {
								$this->options->delete_option( $field_i_d );
								continue;
							}
							// multi input field defaults to an empty value and second condition my be met so we need to skip this type from this check.
							if ( ! isset( $field['fields'] ) && $current_value === $not_validated[ $field_i_d ] ) {

								$validated[ $field_i_d ] = $current_value;

								continue;
							}

							if ( isset( $field['fields'] ) ) {

								foreach ( $field['fields'] as  $nested_field ) {
									if ( empty( $req[ $nested_field['id'] ] ) ) {
										continue;
									}
									// Check if validation required.
									if ( isset( $field['validate'] ) ) {
										$args = array(
											'field'     => $nested_field,
											'new_value' => $req[ $nested_field['id'] ],
										);

										$this->validate = new ANONY_Validate_Inputs( $args );

										// Add to errors if not valid.
										if ( ! empty( $this->validate->errors ) ) {

											$this->errors = array_merge( (array) $this->errors, (array) $this->validate->errors );

											continue;// We will not add to $validated.
										}

										$validated[ $field_i_d ][ $nested_field['id'] ] = $this->validate->value;
									} else {
										$validated[ $field_i_d ][ $nested_field['id'] ] = $req[ $nested_field['id'] ];
									}
								}
								// Check if validation required.
							} elseif ( isset( $field['validate'] ) ) {

									$args = array(
										'field'     => $field,
										'new_value' => $not_validated[ $field_i_d ],
									);

									$this->validate = new ANONY_Validate_Inputs( $args );

									// Add to errors if not valid.
									if ( ! empty( $this->validate->errors ) ) {

										$this->errors = array_merge( (array) $this->errors, (array) $this->validate->errors );

										continue;// We will not add to $validated.
									}

									$validated[ $field_i_d ] = $this->validate->value;
							} else {

								$validated[ $field_i_d ] = $not_validated[ $field_i_d ];
							}
						}
					}
				}

				if ( ! empty( $this->errors ) ) {

					// add settings saved message with the class of "updated".
					add_settings_error(
						$this->args['opt_name'],
						esc_attr( $this->args['opt_name'] ),
						esc_html__( 'Options are saved except those with the following errors', 'anonyengine' ),
						'error'
					);

					foreach ( $this->errors as $field_id => $data ) {

						add_settings_error(
							$this->args['opt_name'],
							esc_attr( $field_id ),
							$this->validate->get_error_msg( $data['code'], $field_id, $data['title'] ),
							'error'
						);

					}
				} else {
					// add settings saved message with the class of "updated".
					add_settings_error(
						$this->args['opt_name'],
						esc_attr( $this->args['opt_name'] . '_updated' ),
						esc_html__( 'Options saved', 'anonyengine' ),
						'updated'
					);
				}
				return $validated;
			}
		}

		/**
		 * HTML OUTPUT.
		 */
		public function options_page_html() {
			// phpcs:disable WordPress.Security.NonceVerification.Recommended
			if ( ! isset( $_GET['page'] ) || $_GET['page'] !== $this->args['opt_name'] ) {
				return;
			}
			// phpcs:enable.

			// check user capabilities.
			if ( ! current_user_can( 'manage_options' ) ) {
				return;
			}?>

			<div id="anony-options-wrapper" class="anony-form">
				<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>

				<form action="options.php" method="post" enctype="multipart/form-data" autocomplete="off">

				<?php
				// output security fields for the registered setting.
				settings_fields( $this->option_group );
				if ( has_custom_logo() ) {
					$option_logo = get_custom_logo();
				} else {
					$option_logo = '<img src="' . ANONY_OPTIONS_URI . 'imgs/logo-orange.png"/>';
				}

				// phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped
				echo '<div id="options-wrap"><div id="anony-options-nav"><div id="anony-logo">' . $option_logo . '</div><ul>';
				// phpcs:enable.

				foreach ( $this->menu as $nav => $details ) {
					if ( isset( $details['sections'] ) ) {
							echo '<li><div><a id="' . esc_attr( $nav ) . '-nav" href="#"  class="anony-nav-item nav-toggle" role="' . esc_attr( $nav ) . '">' . esc_html( $details['title'] ) . '<span class="icon" data-icon="y"></a></div>';
							echo '<ul id="' . esc_attr( $nav ) . '-dropdown" class="anony-dropdown">';

						foreach ( $details['sections'] as $sec ) {
							echo '<li class="anony-nav-item"><a id="' . esc_attr( $sec ) . '" href="#anony-section/' . esc_attr( $sec ) . '" class="anony-nav-link">' . ( isset( $this->sections[ $sec ] ) ? esc_html( $this->sections[ $sec ]['title'] ) : esc_html( ucfirst( str_replace( '-', ' ', $sec ) ) ) ) . '<span class="icon" data-icon="' . esc_attr( $this->sections[ $sec ]['icon'] ) . '"></span></a></li>';
						}

							echo '</ul></li>';

					} else {
						echo '<li class="anony-nav-item"><a id="' . esc_attr( $nav ) . '" href="#anony-section/' . esc_attr( $nav ) . '" class="anony-nav-link">' . ( isset( $this->sections[ $nav ] ) ? esc_html( $this->sections[ $nav ]['title'] ) : esc_html( ucfirst( str_replace( '-', ' ', $nav ) ) ) ) . '<span class="icon" data-icon="' . esc_attr( $this->sections[ $nav ]['icon'] ) . '"></span></a></li>';

					}
				}

					echo '</ul></div><div id="options-sections">';
					/**
					 * Output setting sections and their fields.
					 */
					ANONY_OPTS_HELP::do_settings_sections( $this->args['menu_slug'] );

					submit_button( 'Save Settings', 'primary', 'submit', true, array( 'role' => 'anony-options' ) );

					echo '</div></div>';

				?>

				</form>
			</div>
			<?php
		}

		/**
		 * Page scripts registration.
		 */
		public function page_scripts() {
			// phpcs:disable WordPress.Security.NonceVerification.Recommended
			if ( ! isset( $_GET['page'] ) || $_GET['page'] !== $this->args['opt_name'] ) {
				return;
			}
			// phpcs:enable.

			// Enqueue fields scripts.
			new ANONY_Fields_Scripts( $this->get_fields() );

			wp_enqueue_style( 'select_2', ANONY_FIELDS_URI . 'select2/css/select2.min.css', array(), time(), 'all' );

			wp_register_style( 'anony-options-css', ANONY_OPTIONS_URI . 'css/options.css', array( 'farbtastic' ), time(), 'all' );

			wp_enqueue_style( 'anony-options-css' );

			if ( is_rtl() ) {
				wp_register_style( 'anony-options-rtl-css', ANONY_OPTIONS_URI . 'css/options-rtl.css', array(), time(), 'all' );
				wp_enqueue_style( 'anony-options-rtl-css' );
			}

			if ( ! is_rtl() ) {
				$en_google_fonts = array(
					'Gugi'  => 'https://fonts.googleapis.com/css?family=Gugi',
					'Anton' => 'https://fonts.googleapis.com/css?family=Anton',
					'Exo'   => 'https://fonts.googleapis.com/css?family=Exo',
				);

				foreach ( $en_google_fonts as $name => $link ) {
					wp_enqueue_style( $name, $link, array(), time(), 'all' );
				}
			}

			wp_enqueue_script( 'anony-options-js', ANONY_OPTIONS_URI . 'js/options.js', array( 'jquery', 'backbone' ), time(), true );
		}

		/**
		 * Registers option related widgets
		 */
		public function register_widgets() {
			foreach ( $this->widgets as $widget ) {
				register_widget( $widget );
			}
		}

		/**
		 * Adds styles related to some options in front end
		 */
		public function frontend_styles() {
			?>
			<style type="text/css">
				#anony-ads{
					display: flex;
					justify-content: center;
					align-items: center;
				}
			</style>
			<?php
		}

		/**
		 * Adds styles related to some options in admin
		 */
		public function admin_styles() {
			if ( get_current_screen()->id === 'appearance_page_' . $this->args['opt_name'] ) {

				echo '<style>
					#setting-error-' . esc_html( $this->args['opt_name'] ) . '{
						background-color: #d_1354b;
						color: #fff;
					}
					#setting-error-' . esc_html( $this->args['opt_name'] ) . ' .notice-dismiss, #setting-error-' . esc_html( $this->args['opt_name'] ) . ' .notice-dismiss:before{
						color: #fff;
					}
				</style>';
			}
		}

		/**
		 * Display settings errors
		 */
		public function admin_notices() {

			settings_errors( $this->args['opt_name'] );
		}
	}
}
?>
