<?php
/**
 * Metaboxes class file.
 *
 * PHP version 7.3 Or Later.
 *
 * @package  AnonyEngine.
 * @author   Makiomar <info@makiomar.com>.
 * @license  https:// makiomar.com AnonyEngine Licence.
 * @link     https:// makiomar.com/anonyengine.
 */

defined( 'ABSPATH' ) || die(); // Exit if accessed direct.

if ( ! class_exists( 'ANONY_Meta_Box' ) ) {

	/**
	 * Metaboxes class file.
	 *
	 * PHP version 7.3 Or Later.
	 *
	 * @package  AnonyEngine.
	 * @author   Makiomar <info@makiomar.com>.
	 * @license  https:// makiomar.com AnonyEngine Licence.
	 * @link     https:// makiomar.com/anonyengine.
	 */
	class ANONY_Meta_Box {
		/**
		 * Array of input fields errors. array('field_id' => 'error').
		 *
		 * @var array
		 */
		public $errors = array();

		/**
		 * Metabox's ID.
		 *
		 * @var string
		 */
		public $id;

		/**
		 * Metabox's label.
		 *
		 * @var string
		 */
		public $label;

		/**
		 * Metabox's context. side|normal|advanced.
		 *
		 * @var string
		 */
		public $context;

		/**
		 * Metabox's priority. High|low.
		 *
		 * @var string
		 */
		public $priority;

		/**
		 * Metabox's hook priority. Default 10.
		 *
		 * @var int
		 */
		public $hook_priority = 10;

		/**
		 * Metabox's post types.
		 *
		 * @var string|array
		 */
		public $post_type;

		/**
		 * Metabox's fields array.
		 *
		 * @var array
		 */
		public $fields;

		/**
		 * Metabox's localize scripts.
		 *
		 * @var array
		 */
		public $localize_scripts;

		/**
		 * Inputs validation object.
		 *
		 * @var object
		 */
		private $validate;

		/**
		 * Meta box array.
		 *
		 * @var array
		 */
		public $metabox;

		/**
		 * ID used for metabox's actions.
		 *
		 * @var string
		 */
		public $id_as_hook;

		/**
		 * If we should use tabs.
		 *
		 * @var bool
		 */
		public $taps;

		/**
		 * Constructor
		 *
		 * @param array $meta_box Metabox's data.
		 */
		public function __construct( array $meta_box ) {

			global $anoe_metaboxes;

			$this->metabox = $meta_box;

			$anoe_metaboxes[] = $this->metabox;

			$localize_scripts = array(
				'ajaxURL'   => ANONY_Wpml_Help::get_ajax_url(),
				'textDir'   => ( is_rtl() ? 'rtl' : 'ltr' ),
				'themeLang' => get_bloginfo( 'language' ),
				'MbUri'     => ANONY_MB_URI,
				'MbPath'    => ANONY_MB_PATH,

			);

			$this->localize_scripts = apply_filters( 'anony_mb_loc_scripts', $localize_scripts );

			// Set metabox's data.
			$this->set_metabox_data( $this->metabox );

			new ANONY_Mb_Admin( $this, $this->metabox );

			new ANONY_Mb_Shortcode( $this, $this->metabox );

			new ANONY_Mb_Single( $this, $this->metabox );

		}

		/**
		 * Set metabox properties.
		 *
		 * @param array $metabox Array of meta box data.
		 * @return void.
		 */
		public function set_metabox_data( $metabox ) {

			$this->id            = $metabox['id'];
			$this->label         = $metabox['title'];
			$this->context       = $metabox['context'];
			$this->priority      = $metabox['priority'];
			$this->hook_priority = isset( $metabox['hook_priority'] ) ? $metabox['hook_priority'] : $this->hook_priority;
			$this->post_type     = $metabox['post_type'];
			$this->taps          = !empty($metabox['taps'])  ? $metabox['taps'] : false;

			// To use id for hooks definitions.
			$this->id_as_hook = str_replace( '-', '_', $this->id );

			$this->fields = $metabox['fields'];
		}

		/**
		 * Returns metabox fields
		 *
		 * @return string.
		 */
		public function return_meta_fields() {
			ob_start();

			$this->meta_fields_callback();

			$render = ob_get_clean();

			return $render;
		}

		/**
		 * Render metabox' fields.
		 */
		public function meta_fields_callback() {

			global $post;

			// phpcs:disable WordPress.Security.NonceVerification.Recommended
			$p_id = ! empty( $_GET['post'] ) ? intval( $_GET['post'] ) : $post->ID;
			// phpcs:enable
			wp_nonce_field( $this->id . '_action', $this->id . '_nonce', false );

			// Loop through inputs to render.
			foreach ( $this->fields as $field ) {
				if ( ! is_admin() && ( ! isset( $field['show_on_front'] ) || ! $field['show_on_front'] ) ) {
					continue;
				}

				$render_field = new ANONY_Input_Field( $field, $this->id, 'meta', $p_id );

				// phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped
				echo $render_field->field_init();
				// phpcs:enable.
				$this->enqueue_field_scripts( $field );

			}
		}

		/**
		 * Enqueue field script.
		 *
		 * @param array $field Field's Data.
		 */
		public function enqueue_field_scripts( $field ) {
			if ( isset( $field['scripts'] ) && ! empty( $field['scripts'] ) ) {

				foreach ( $field['scripts'] as $script ) {

					$deps = ( isset( $script['dependancies'] ) && ! empty( $script['dependancies'] ) ) ? $script['dependancies'] : array();

					$deps[] = 'anony-metaboxs';

					if ( isset( $script['file_name'] ) ) {

						$url = ANONY_MB_URI . 'assets/js/' . $script['file_name'] . '.js';

					} elseif ( isset( $script['url'] ) ) {

						$url = $script['url'];
					}

					wp_enqueue_script( $script['handle'], $url, $deps, time(), true );
				}
			}
		}

		/**
		 * Validate field value
		 *
		 * @param array $field     Field's data array.
		 * @param mixed $new_value Field's new value.
		 * @return object          Validation object.
		 */
		public function validate_field( $field, $new_value ) {
			$args     = array(
				'field'     => $field,
				'new_value' => $new_value,
			);
			$validate = new ANONY_Validate_Inputs( $args );

			return $validate;
		}

		/**
		 * Updates post meta
		 *
		 * @param array $sent_data An Array of sent data. Upon POST Request.
		 * @param int   $post_ID   Should be the id of being updated post.
		 */
		public function start_update( $sent_data, $post_ID = null ) {
			$post_type = get_post_type( $post_ID );

			if ( empty( $sent_data ) || ! is_array( $sent_data ) || is_null( $post_ID ) ) {
				return;
			}

			// Can be used to validate $sent_data data before insertion.
			do_action( $this->id_as_hook . '_before_meta_insert' );

			$metabox_options = get_post_meta( $post_ID, $this->id, true );

			if ( ! is_array( $metabox_options ) ) {
				$metabox_options = array();
			}

			foreach ( $this->fields as $field ) {

				if ( ! isset( $sent_data[ $this->id ][ $field['id'] ] ) ) {
					continue;
				}

				$chech_meta = ( ! empty( $metabox_options ) && isset( $metabox_options[ $field['id'] ] ) ) ? $metabox_options[ $field['id'] ] : '';

				if ( $chech_meta === $sent_data[ $this->id ][ $field['id'] ] ) {
					continue;
				}

				// If this field is an array of other fields values.
				if ( isset( $field['fields'] ) ) {
					// $nested_field : The nested field inside the multi-value.
					foreach ( $field['fields'] as  $nested_field ) {

						foreach ( $sent_data[ $this->id ][ $field['id'] ] as $field_index => $posted_field ) {

							foreach ( $posted_field as $field_id => $value ) {

								if ( $nested_field['id'] === $field_id ) {

									$this->validate = $this->validate_field( $nested_field, $value );

									if ( ! empty( $this->validate->errors ) ) {

										$this->errors = array_merge( (array) $this->errors, (array) $this->validate->errors );

										$sent_data[ $this->id ][ $field_id ][ $field_index ][ $field_id ] = (
											'' !== $chech_meta &&
											isset( $chech_meta[ $field_index ][ $nested_field['id'] ] )
										) ?

										$chech_meta[ $field_index ][ $nested_field['id'] ] : '';

										continue;
									}

									$sent_data[ $this->id ][ $field['id'] ][ $field_index ][ $field_id ] = $this->validate->value;
								}
							}
						}
					}

					// For now this deals with multi values, which have been already validated individually, so the only validation required is to remove all values that are empty in one row.
					$this->validate = $this->validate_field( $field, $sent_data[ $this->id ][ $field['id'] ] );

				} else {

					$this->validate = $this->validate_field( $field, $sent_data[ $this->id ][ $field['id'] ] );

					if ( ! empty( $this->validate->errors ) ) {

						$this->errors = array_merge( (array) $this->errors, (array) $this->validate->errors );

						continue;
					}
				}

				$metabox_options[ $field['id'] ] = $this->validate->value;

			}

			update_post_meta( $post_ID, $this->id, $metabox_options );

			if ( ! empty( $this->errors ) ) {
				set_transient( 'ANONY_errors_' . $post_type . '_' . $post_ID, $this->errors );
			}
		}

		/**
		 * Return notices.
		 *
		 * @return string Returns notices.
		 */
		public function get_notices() {
			ob_start();

			$this->notices();

			return ob_get_clean();
		}

		/**
		 * Render notices
		 */
		public function notices() {
			global $post;

			$post_type = get_post_type();

			if ( is_single() && ! in_array( $post_type, $this->post_type, true ) ) {
				return;
			}

			$errors = get_transient( 'ANONY_errors_' . $post_type . '_' . $post->ID );

			if ( $errors ) {

				foreach ( $errors as $field => $data ) {?>

					<div class="error <?php echo esc_attr( $field ); ?>">

						<p>
							<?php
							// phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped
							echo ANONY_Validate_Inputs::get_error_msg( $data['code'], $field );
							// phpcs:enable. 
							?>
						</p>

					</div>


					<?php
				}

				delete_transient( 'ANONY_errors_' . $post_type . '_' . $post->ID );
			}
		}

		/**
		 * Enqueue needed scripts|styles
		 */
		public function enqueue_main_scripts() {
			$screen = get_current_screen();
			if ( in_array( $screen->base, array( 'post' ), true ) && in_array( $screen->post_type, $this->post_type, true ) ) {

				wp_enqueue_style( 'select2', ANONY_FIELDS_URI . 'select2/css/select2.min.css', false, time() );

				wp_enqueue_style(
					'anony-metaboxs',
					ANONY_MB_URI . 'assets/css/metaboxes.css',
					false,
					// phpcs:disable WordPress.WP.EnqueuedResourceParameters.MissingVersion
					filemtime( wp_normalize_path( ANONY_MB_PATH . 'assets/css/metaboxes.css' ) )
					// phpcs:enable.
				);

				wp_enqueue_script(
					'anony-metaboxs',
					ANONY_MB_URI . 'assets/js/metaboxes.js',
					false,
					filemtime( wp_normalize_path( ANONY_MB_PATH . 'assets/js/metaboxes.js' ) ),
					true
				);

				// Don't remove outside the if statement.
				wp_localize_script( 'anony-metaboxs', 'AnonyMB', $this->localize_scripts );
			}

		}

		/**
		 * Load fields scripts on front if `show_on_front` is set to true
		 */
		public function front_scripts() {

			if ( is_home() || is_front_page() ) {
				return;
			}

			wp_enqueue_style( 'select2', ANONY_FIELDS_URI . 'select2/css/select2.min.css', false, time() );

			wp_enqueue_script( 'metaboxes-front', ANONY_MB_URI . 'assets/js/metaboxes-front.js', array( 'jquery' ), time(), true );

		}

		/**
		 * Render required scripts for metabox
		 */
		public function footer_scripts() {

			?>
			<script type="text/javascript">
				jQuery(document).ready(function($){
					'use strict';

					$('.meta-error').on('click', function(e){
						e.preventDefault();
						var obj = $.browser.webkit ? $('body') : $('html');
						var id = $(this).attr('rel-id');
						var elHeight = $('#fieldset_' + id).height();
						var paddingTop = parseInt($('#fieldset_' + id).css("padding-top"));
						var paddingBottom = parseInt($('#fieldset_' + id).css("padding-bottom"));

						var totalHeight = elHeight + paddingTop + paddingBottom;

						obj.animate({ scrollTop: $("#" + id).offset().top - totalHeight }, 1000 );
					});

					$('.meta-error').each(function(){
						var metaFieldId = $(this).attr('rel-id');
						$('#fieldset_' + metaFieldId).css('border', '1px solid red');
					});
				});
			</script>
			<?php
		}
	}
}
