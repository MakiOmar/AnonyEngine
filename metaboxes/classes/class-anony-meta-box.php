<?php

if ( ! class_exists( 'ANONY_Meta_Box' ) ) {

	class ANONY_Meta_Box {
		/**
		 * @var array Array of input fields errors. array('field_id' => 'error')
		 */
		public $errors = array();

		/**
		 * @var string metabox's ID
		 */
		public $id;

		/**
		 * @var string metabox's label
		 */
		public $label;

		/**
		 * @var string metabox's context. side|normal|advanced
		 */
		public $context;

		/**
		 * @var string metabox's priority. High|low
		 */
		public $priority;

		/**
		 * @var int metabox's hook priority. Default 10
		 */
		public $hook_priority = 10;

		/**
		 * @var string|array metabox's post types.
		 */
		public $post_type;

		/**
		 * @var array metabox's fields array.
		 */
		public $fields;

		/**
		 * @var array metabox's localize scripts.
		 */
		public $localize_scripts;

		/**
		 * @var object inputs validation object.
		 */
		private $validate;

		/**
		 * Constructor
		 */
		public function __construct( $meta_box = array() ) {

			if ( empty( $meta_box ) || ! is_array( $meta_box ) ) {
				return;
			}

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

			// Set metabox's data
			$this->setMetaboxData( $this->metabox );

			new ANONY_Mb_Admin( $this, $this->metabox );

			new ANONY_Mb_Shortcode( $this, $this->metabox );

			new ANONY_Mb_Single( $this, $this->metabox );

		}

		/**
		 * Set metabox properties.
		 *
		 * @param array $meta_box Array of meta box data
		 * @return void
		 */
		public function setMetaboxData( $metabox ) {

			$this->id            = $metabox['id'];
			$this->label         = $metabox['title'];
			$this->context       = $metabox['context'];
			$this->priority      = $metabox['priority'];
			$this->hook_priority = isset( $metabox['hook_priority'] ) ? $metabox['hook_priority'] : $this->hook_priority;
			$this->post_type     = $metabox['post_type'];

			// To use id for hooks definitions
			$this->id_as_hook = str_replace( '-', '_', $this->id );

			$this->fields = $metabox['fields'];
		}

		/**
		 * Returns metabox fields
		 *
		 * @return string
		 */
		public function returnMetaFields() {
			ob_start();

			$this->metaFieldsCallback();

			$render = ob_get_contents();

			ob_end_clean();

			return $render;
		}

		/**
		 * Render metabox' fields.
		 */
		public function metaFieldsCallback() {

			if ( ! class_exists( 'ANONY_Input_Field' ) ) {
						esc_html_e( 'Input fields plugin is required', 'anonyengine' );
						return;
			}
			global $post;

			$pID = isset( $_GET['post'] ) && ! empty( $_GET['post'] ) ? intval( $_GET['post'] ) : $post->ID;

			wp_nonce_field( $this->id . '_action', $this->id . '_nonce', false );

			// Loop through inputs to render
			foreach ( $this->fields as $field ) {
				if ( ! is_admin() && ( ! isset( $field['show_on_front'] ) || ! $field['show_on_front'] ) ) {
					continue;
				}

				$render_field = new ANONY_Input_Field( $field, $this->id, 'meta', $pID );

				echo $render_field->field_init();

				$this->enqueueFieldScripts( $field );

			}
		}

		public function enqueueFieldScripts( $field ) {
			if ( isset( $field['scripts'] ) && ! empty( $field['scripts'] ) ) {

				foreach ( $field['scripts'] as $script ) {

					$deps = ( isset( $script['dependancies'] ) && ! empty( $script['dependancies'] ) ) ? $script['dependancies'] : array();

					$deps[] = 'anony-metaboxs';

					if ( isset( $script['file_name'] ) ) {

						$url = ANONY_MB_URI . 'assets/js/' . $script['file_name'] . '.js';

					} elseif ( isset( $script['url'] ) ) {

						$url = $script['url'];
					}

					wp_enqueue_script( $script['handle'], $url, $deps, false, true );
				}
			}
		}

		/**
		 * Validate field value
		 *
		 * @param array $field     Field's data array
		 * @param mixed $new_value Field's new value
		 * @return object          Validation object
		 */
		public function validateField( $field, $new_value ) {
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
		 * @param array $sent_data An Array of sent data. Upon POST Request
		 * @param int   $post_ID   Should be the id of being updated post
		 */
		public function startUpdate( $sent_data, $post_ID = null ) {
			$postType = get_post_type( $post_ID );

			if ( empty( $sent_data ) || ! is_array( $sent_data ) || is_null( $post_ID ) ) {
				return;
			}

			// Can be used to validate $sent_data data before insertion
			do_action( $this->id_as_hook . '_before_meta_insert' );

			$metaboxOptions = get_post_meta( $post_ID, $this->id, true );

			if ( ! is_array( $metaboxOptions ) ) {
				$metaboxOptions = array();
			}

			foreach ( $this->fields as $field ) {

				if ( ! isset( $sent_data[ $this->id ][ $field['id'] ] ) ) {
					continue;
				}

				$chech_meta = ( ! empty( $metaboxOptions ) && isset( $metaboxOptions[ $field['id'] ] ) ) ? $metaboxOptions[ $field['id'] ] : '';

				if ( $chech_meta === $sent_data[ $this->id ][ $field['id'] ] ) {
					continue;
				}

				// If this field is an array of other fields values
				if ( isset( $field['fields'] ) ) {
					// $nested_field : The nested field inside the multi-value
					foreach ( $field['fields'] as  $nested_field ) {

						foreach ( $sent_data[ $this->id ][ $field['id'] ] as $field_index => $posted_field ) {

							foreach ( $posted_field as $fieldID => $value ) {

								if ( $nested_field['id'] == $fieldID ) {

									$this->validate = $this->validateField( $nested_field, $value );

									if ( ! empty( $this->validate->errors ) ) {

										$this->errors = array_merge( (array) $this->errors, (array) $this->validate->errors );

										$sent_data[ $this->id ][ $field_id ][ $field_index ][ $fieldID ] = (
											$chech_meta !== '' &&
											isset( $chech_meta[ $field_index ][ $nested_field['id'] ] )
										) ?

										$chech_meta[ $field_index ][ $nested_field['id'] ] : '';

										continue;
									}

									$sent_data[ $this->id ][ $field['id'] ][ $field_index ][ $fieldID ] = $this->validate->value;
								}
							}
						}
					}

					// For now this deals with multi values, which have been already validated individually, so the only validation required is to remove all values that are empty in one row.
					$this->validate = $this->validateField( $field, $sent_data[ $this->id ][ $field['id'] ] );

				} else {

					$this->validate = $this->validateField( $field, $sent_data[ $this->id ][ $field['id'] ] );

					if ( ! empty( $this->validate->errors ) ) {

						$this->errors = array_merge( (array) $this->errors, (array) $this->validate->errors );

						continue;
					}
				}

				$metaboxOptions[ $field['id'] ] = $this->validate->value;

			}

			update_post_meta( $post_ID, $this->id, $metaboxOptions );

			if ( ! empty( $this->errors ) ) {
				set_transient( 'ANONY_errors_' . $postType . '_' . $post_ID, $this->errors );
			}
		}

		/**
		 * Return notices
		 *
		 * @return string
		 */
		public function getNotices() {
			ob_start();

			$this->notices();

			$render = ob_get_contents();

			ob_end_clean();

			return $render;
		}

		/**
		 * Render notices
		 *
		 * @return string
		 */
		public function notices() {
			global $post;

			$postType = get_post_type();

			if ( is_single() && ! in_array( $postType, $this->post_type ) ) {
				return;
			}

			$errors = get_transient( 'ANONY_errors_' . $postType . '_' . $post->ID );

			if ( $errors ) {

				foreach ( $errors as $field => $data ) {?>

					<div class="error <?php echo $field; ?>">

						<p><?php echo ANONY_Validate_Inputs::get_error_msg( $data['code'], $field ); ?>

					</div>


					<?php
				}

				delete_transient( 'ANONY_errors_' . $postType . '_' . $post->ID );
			}
		}

		/**
		 * Enqueue needed scripts|styles
		 */
		public function enqueueMainScripts() {
			$screen = get_current_screen();
			if ( in_array( $screen->base, array( 'post' ) ) && in_array( $screen->post_type, $this->post_type ) ) {

				wp_enqueue_style( 'select2', ANONY_FIELDS_URI . 'select2/css/select2.min.css' );

				wp_enqueue_style(
					'anony-metaboxs',
					ANONY_MB_URI . 'assets/css/metaboxes.css',
					false,
					filemtime( wp_normalize_path( ANONY_MB_PATH . 'assets/css/metaboxes.css' ) )
				);

				wp_enqueue_script(
					'anony-metaboxs',
					ANONY_MB_URI . 'assets/js/metaboxes.js',
					false,
					filemtime( wp_normalize_path( ANONY_MB_PATH . 'assets/js/metaboxes.js' ) )
				);

				// Don't remove outside the if statement
				wp_localize_script( 'anony-metaboxs', 'AnonyMB', $this->localize_scripts );
			}

		}

		/**
		 * Load fields scripts on front if `show_on_front` is set to true
		 */
		public function frontScripts() {

			if ( is_home() || is_front_page() ) {
				return;
			}

			wp_enqueue_style( 'select2', ANONY_FIELDS_URI . 'select2/css/select2.min.css' );

			wp_enqueue_script( 'metaboxes-front', ANONY_MB_URI . 'assets/js/metaboxes-front.js', array( 'jquery' ), false, true );

		}

		public function footerScripts() {

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

						obj.animate(
									  {
										scrollTop: $("#" + id).offset().top - totalHeight
									  },
									  1000 //speed
									);
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
