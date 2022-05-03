<?php
/**
 * Taxonomy helpers
 *
 * PHP version 7.3 Or Later
 *
 * @package  AnonyEngine helpers
 * @author   Makiomar <info@makior.com>
 * @license  https://makiomar.com AnonyEngine Licence
 * @link     https://makiomar.com/anonyengine
 */

defined( 'ABSPATH' ) || die(); // Exit if accessed direct.

if ( ! class_exists( 'ANONY_Taxonomy_Help' ) ) {
	/**
	 * Taxonomy helpers' class
	 *
	 * @package    AnonyEngine helpers
	 * @author     Makiomar <info@makior.com>
	 * @license    https://makiomar.com AnonyEngine Licence
	 * @link       https://makiomar.com
	 */
	class ANONY_Taxonomy_Help extends ANONY_HELP {

		/**
		 * Register taxonomies
		 */
		public static function register_taxonomies() {
			$anony_custom_taxs = apply_filters( 'anony_taxonomies', array() );

			if ( empty( $anony_custom_taxs ) ) {
				return;
			}

			foreach ( $anony_custom_taxs as $anony_custom_tax => $translatable ) {
				$singular_label = $translatable[0];
				$plural_label   = $translatable[1];

				register_taxonomy(
					$anony_custom_tax,
					self::taxonomy_posts( $anony_custom_tax ),
					array(
						'hierarchical'      => true,
						'label'             => $plural_label,
						'singular_label'    => $singular_label,
						'labels'            =>
								array(
									// Translators: Post type plural label.
									'all_items'         => sprintf( esc_html__( 'All %s', 'anonyengine' ), $plural_label ),
									// Translators: Post type singular label.
									'edit_item'         => sprintf( esc_html__( 'Edit %s', 'anonyengine' ), $singular_label ),
									// Translators: Post type singular label.
									'view_item'         => sprintf( esc_html__( 'View %s', 'anonyengine' ), $singular_label ),
									// Translators: Post type singular label.
									'update_item'       => sprintf( esc_html__( 'update %s', 'anonyengine' ), $singular_label ),
									// Translators: Post type singular label.
									'add_new_item'      => sprintf( esc_html__( 'Add new %s', 'anonyengine' ), $singular_label ),
									// Translators: Post type singular label.
									'new_item_name'     => sprintf( esc_html__( 'new %s', 'anonyengine' ), $singular_label ),
									// Translators: Post type singular label.
									'parent_item'       => sprintf( esc_html__( 'Parent %s', 'anonyengine' ), $singular_label ),
									// Translators: Post type singular label.
									'parent_item_colon' => sprintf( esc_html__( 'Parent %s:', 'anonyengine' ), $singular_label ),
									// Translators: Post type plural label.
									'search_items'      => sprintf( esc_html__( 'search %s', 'anonyengine' ), $plural_label ),
									// Translators: Post type plural label.
									'not_found'         => sprintf( esc_html__( 'No %s found', 'anonyengine' ), $plural_label ),
								),
						'show_admin_column' => true,
					)
				);
			}
		}

		/**
		 * Get post type for a taxonomy
		 *
		 * @param string $tax The taxonomy to get post types for.
		 * @return array An array of post types names.
		 */
		public static function taxonomy_posts( $tax ) {
			$tax_posts = apply_filters( 'anony_taxonomy_posts', array() );

			if ( ! empty( $tax_posts ) && array_key_exists( $tax, $tax_posts ) ) {
				return $tax_posts[ $tax ];
			}

			return array();
		}
	}
}
