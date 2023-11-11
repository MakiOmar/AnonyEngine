<?php
/**
 * Metabox for frontend single post
 *
 * @package Anonymous theme
 * @author Makiomar
 * @link http://makiomar.com
 */

defined( 'ABSPATH' ) || die();

if ( ! class_exists( 'ANONY_Mb_Single' ) ) {
	/**
	 * Metabox for frontend single post
	 *
	 * @package Anonymous theme
	 * @author Makiomar
	 * @link http://makiomar.com
	 */
	class ANONY_Mb_Single extends ANONY_Meta_Box {

		/**
		 * Parent object metabox
		 *
		 * @var object
		 */
		private $parent_obj;

		/**
		 * Constructor
		 *
		 * @param object $parent_obj Parent metabox object.
		 * @param array  $meta_box Metabox arguments.
		 */
		public function __construct( $parent_obj, $meta_box = array() ) {

			$this->parent_obj = $parent_obj;

			$this->hooks();
		}

		/**
		 * Hooks.
		 *
		 * @return void
		 */
		public function hooks() {
			add_action( 'wp_enqueue_scripts', array( $this->parent_obj, 'front_scripts' ) );

			add_action( 'wp_head', array( $this, 'head_styles' ) );

			add_action( 'wp_enqueue_scripts', array( $this, 'wp_enque_scripts' ) );

			add_filter( 'the_content', array( $this, 'show_on_frontend' ) );

			add_action( 'wp_footer', array( $this, 'single_footer_scripts' ) );

			add_filter( 'anony_mb_loc_scripts', array( $this, 'localize_scripts' ) );
		}

		/**
		 * Renders metabox in frontend. hooked to the_content filter
		 *
		 * @param  string $content Page content.
		 * @return string
		 */
		public function show_on_frontend( $content ) {

			if ( ! is_user_logged_in() && ! is_admin() && is_singular() && isset( $_GET['action'] ) ) {
				return esc_html__( 'Sorry, you have to login first', 'anonyengine' );
			}

			global $post;

			if (
				! is_single() ||
				( is_array( $this->parent_obj->post_type ) && ! in_array( $post->post_type, $this->parent_obj->post_type, true ) ) ||
				( ! is_array( $this->parent_obj->post_type ) && $post->post_type !== $this->parent_obj->post_type )
			) {
				return $content;
			}

			$metabox_id = $this->parent_obj->id;

			$this->parent_obj->metabox = apply_filters( 'anony_post_specific_metaboxes', $this->parent_obj->metabox, $post );

			// make sure metabox has the same id to match the shortcode.
			$this->parent_obj->metabox['id'] = $metabox_id;

			// Set metabox's data.
			$this->parent_obj->set_metabox_data( $this->parent_obj->metabox );

			$this->updatePost();

			$render = $this->parent_obj->get_notices();

			do_action( $this->parent_obj->id_as_hook . '_show_on_front' );

			$render .= $this->render_for_action( $post );

			return $content . '<br/>' . $render;
		}


		/**
		 * Render frontend form according to action
		 *
		 * @param WP_Post $post Post object.
		 * @return string Rendered content
		 */
		public function render_for_action( $post ) {
			$request = wp_unslash( $_GET );

			if ( isset( $request['action'] ) && isset( $request['_wpnonce'] ) && wp_verify_nonce( $request['_wpnonce'], 'anonyinsert_' . $post->ID ) && get_current_user_id() === $post->post_author ) {

					$render = $this->render_frontend_form();

			} else {
				$render = $this->parent_obj->return_meta_fields();

				if ( get_current_user_id() === $post->post_author ) {

					$render .= sprintf( '<a href="%1$s?action=edit&_wpnonce=%2$s" class="button button-primary button-large">%3$s</a>', get_permalink(), wp_create_nonce( 'anonyinsert_' . $post->ID ), esc_html__( 'Edit' ) );
				}
			}

			return $render;
		}

		/**
		 * Render frontend form.
		 *
		 * @return string
		 */
		public function render_frontend_form() {

			global $post;

			$render = '<form method="post">';

			$render .= apply_filters( $this->id_as_hook . '_hiddens', '' );

			$render .= '<input type="hidden" id="post_type" name="postType" value="' . $post->post_type . '">';

			$render .= '<input type="hidden" id="user_ID" name="user_ID" value="' . get_current_user_id() . '">';

			$render .= '<input type="hidden" id="post_ID" name="post_ID" value="' . $post->ID . '">';

			$render .= $this->parent_obj->return_meta_fields();

			$render .= '<input name="save" type="submit" class="button button-primary button-large" id="publish" value="' . esc_html__( 'Save changes' ) . '">';

			$render .= '</form>';

			return $render;
		}

		/**
		 * Update metafields if updated from frontend. (in a single post)
		 */
		public function updatePost() {
			if ( ! is_user_logged_in() ) {
				return;
			}
			$request = wp_unslash( $_POST );
			/**
			 * Check if there are any posted data return if empty.
			 */
			if ( empty( $request['postType'] ) ) {
				return;
			}

			global $post;

			if ( ! $post || is_null( $post ) || $post->post_type !== $request['postType'] ) {
				return;
			}

			if ( ( $post->post_author !== $request['user_ID'] ) || ! current_user_can( 'manage_options' ) ) {
				return;
			}

			if ( $post->ID != $request['post_ID'] ) {
				return;
			}

			if ( ! isset( $request[ $this->parent_obj->id . '_nonce' ] ) ) {
				return;
			}

			if ( isset( $request[ $this->parent_obj->id . '_nonce' ] ) && ! wp_verify_nonce( $request[ $this->parent_obj->id . '_nonce' ], $this->parent_obj->id . '_action' ) ) {
				return;
			}

			// Can be used to validate $_POST data befoore insertion.
			do_action( $this->parent_obj->id_as_hook . '_before_update' );

			$this->parent_obj->start_update( $request, $request['post_ID'] );
		}
		/**
		 * Scripts for single post metabox
		 *
		 * @return void
		 */
		public function single_footer_scripts() {

			if ( is_single() ) {

				$post_type = get_post_type();
				if ( in_array( $post_type, $this->parent_obj->post_type, true ) || $post_type === $this->parent_obj->post_type ) {
					$this->parent_obj->footer_scripts();
				}
			}
		}

		/**
		 * Add style tag to head
		 *
		 * @return void
		 */
		public function head_styles() {
			if ( is_single() && ! is_admin() ) {?>
				<style type="text/css">
					#anony_map{
						width:100%;
						height: 480px;
					}
				</style>
				<?php
			}
		}

		/**
		 * Filter metabox localize scripts
		 *
		 * @param  array $localized_script An array of localized script.
		 * @return array
		 */
		public function localize_scripts( $localized_script ) {
			if ( is_single() ) {
				$post_id           = get_the_ID();
				$anony__entry_lat  = get_post_meta( $post_id, 'anony__entry_lat', true );
				$anony__entry_long = get_post_meta( $post_id, 'anony__entry_long', true );

				if ( ! empty( $anony__entry_lat ) && ! empty( $anony__entry_long ) ) {

					$localized_script['geolat']  = $anony__entry_lat;
					$localized_script['geolong'] = $anony__entry_long;
				}
			}

			return $localized_script;
		}
		/**
		 * Enqueue scripts
		 *
		 * @return void
		 */
		public function wp_enque_scripts() {
			if ( is_single() ) {
				$this->parent_obj->footer_scripts();
			}
		}
	}
}
