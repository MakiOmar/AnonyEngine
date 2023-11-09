<?php
/**
 * Term metaboxes class file.
 *
 * PHP version 7.3 Or Later.
 *
 * @package  AnonyEngine.
 * @author   Makiomar <info@makiomar.com>.
 * @license  https:// makiomar.com AnonyEngine Licence.
 * @link     https:// makiomar.com/anonyengine.
 */

defined( 'ABSPATH' ) || die(); // Exit if accessed direct.

if ( ! class_exists( 'ANONY_Term_Metabox' ) ) {
	/**
	 * Term metaboxes class file.
	 *
	 * PHP version 7.3 Or Later.
	 *
	 * @package  AnonyEngine.
	 * @author   Makiomar <info@makiomar.com>.
	 * @license  https:// makiomar.com AnonyEngine Licence.
	 * @link     https:// makiomar.com/anonyengine.
	 */
	class ANONY_Term_Metabox extends ANONY_Meta_Box {
		/**
		 * Taxonomy slug.
		 *
		 * @var string
		 */

		public $taxonomy;

		/**
		 * Constructor
		 *
		 * @param array $meta_box Metabox arguments.
		 */
		public function __construct( $meta_box = array() ) {

			if ( empty( $meta_box ) || ! is_array( $meta_box ) ) {
				return;
			}

			$this->metabox = $meta_box;

			// Set metabox's data.
			$this->set_metabox_data( $this->metabox );

			add_action( 'init', array( $this, 'register_term_meta_key' ) );
			add_action( $this->taxonomy . '_add_form_fields', array( $this, 'add_form_fields' ) );
			add_action( $this->taxonomy . '_edit_form_fields', array( $this, 'editFormFields' ) );
			add_action( 'edited_' . $this->taxonomy, array( $this, 'save_term_meta' ) );
			add_action( 'create_' . $this->taxonomy, array( $this, 'save_term_meta' ) );
		}

		/**
		 * Set metabox data.
		 *
		 * @param array $metabox Metabox arguments.
		 */
		public function set_metabox_data( $metabox ) {
			$this->id       = $metabox['id'];
			$this->taxonomy = $metabox['taxonomy'];
			$this->fields   = $metabox['fields'];
			$this->context  = $metabox['context'];
		}

		/**
		 * Save term meta value
		 *
		 * @param int $term_id Term's ID.
		 */
		public function save_term_meta( $term_id ) {
			if ( isset( $_POST ) ) {
				$submitted_data = wp_unslash( $_POST );
			}
			if ( ! isset( $submitted_data[ $this->id . '_nonce' ] ) || ! wp_verify_nonce( $submitted_data[ $this->id . '_nonce' ], $this->id . '_action' ) ) {
				return;
			}

			$old_value = get_term_meta( $term_id, $this->id, true );

			if ( $old_value === $submitted_data[ $this->id ] ) {
				return;
			}

			$term_value[ $this->id ] = $submitted_data[ $this->id ];

			if ( $old_value && '' === $submitted_data[ $this->id ] ) {
				delete_term_meta( $term_id, $this->id );

			} elseif ( $old_value !== $submitted_data[ $this->id ] ) {
				update_term_meta( $term_id, $this->id, $term_value );
			}
		}

		/**
		 * Register term meta key
		 */
		public function register_term_meta_key() {
			register_meta( 'term', $this->id, array( $this, 'validateTermMeta' ) );
		}

		/**
		 * Validate metabox
		 *
		 * @param mixed $value term value.
		 */
		public function validateTermMeta( $value ) {

			return $value;
		}

		/**
		 * Add metabox on add term page
		 */
		public function add_form_fields() {
			if ( 'edit-tags.php' !== $GLOBALS['pagenow'] ) {
				return;
			}
			echo '<div class="form-field anony-term-meta-wrap">';
			$this->meta_fields_callback();
			echo '</div>';
		}

		/**
		 * Add metabox on edit term page
		 *
		 * @param object $term Term's ID.
		 */
		public function editFormFields( $term ) {
			if ( 'term.php' !== $GLOBALS['pagenow'] ) {
				return;
			}
			echo '<div class="form-field anony-term-meta-wrap">';
			$this->meta_fields_callback( $term->term_id );
			echo '</div>';
		}

		/**
		 * Render metabox' fields.
		 *
		 * @param mixed $object_id Object ID or null.
		 */
		public function meta_fields_callback( $object_id = null ) {

			if ( ! class_exists( 'ANONY_Input_Field' ) ) {
				esc_html_e( 'Input fields plugin is required', 'anonyengine' );
				return;
			}

			wp_nonce_field( $this->id . '_action', $this->id . '_nonce', false );

			// Loop through inputs to render.
			foreach ( $this->fields as $field ) {
				if ( ! is_null( $object_id ) ) {
					$render_field = new ANONY_Input_Field( $field, $this->id, 'term', intval( $object_id ) );
				} else {
					$render_field = new ANONY_Input_Field( $field, $this->id, 'term' );
				}

				//phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped
				echo $render_field->field_init();
				//phpcs:enable.

				$this->enqueue_field_scripts( $field );
			}
		}
	}
}
