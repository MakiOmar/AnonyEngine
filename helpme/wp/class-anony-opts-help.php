<?php
/**
 * WP Options/settings helpers.
 *
 * PHP version 7.3 Or Later.
 *
 * @package  AnonyEngine
 * @author   Makiomar <info@makiomar.com>
 * @license  https:// makiomar.com AnonyEngine Licence.
 * @link     https:// makiomar.com/anonyengine
 */

defined( 'ABSPATH' ) || die();

if ( ! class_exists( 'ANONY_OPTS_HELP' ) ) {

	/**
	 * WP Options/settings helpers class.
	 *
	 * PHP version 7.3 Or Later.
	 *
	 * @package  AnonyEngine
	 * @author   Makiomar <info@makiomar.com>
	 * @license  https:// makiomar.com AnonyEngine Licence.
	 * @link     https:// makiomar.com/anonyengine
	 */
	class ANONY_OPTS_HELP extends ANONY_HELP {

		/**
		 * Print out the settings fields for a particular settings section.
		 *
		 * Part of the Settings API. Use this in a settings page to output
		 * a specific section. Should normally be called by do_settings_sections()
		 * rather than directly.
		 *
		 * @global $wp_settings_fields Storage array of settings fields and their pages/sections.
		 *
		 * @since 2.7.0
		 *
		 * @param string $page Slug title of the admin page whose settings fields you want to show.
		 * @param string $section Slug title of the settings section whose fields you want to show.
		 */
		public static function do_settings_fields( $page, $section ) {
			global $wp_settings_fields;

			if ( ! isset( $wp_settings_fields[ $page ][ $section ] ) ) {
				return;
			}

			foreach ( (array) $wp_settings_fields[ $page ][ $section ] as $field ) {
				$class = '';

				if ( ! empty( $field['args']['class'] ) ) {
					$class = ' class="' . esc_attr( $field['args']['class'] ) . '"';
				}
				echo '<table class="form-table" role="presentation">';
				echo '<tr' . esc_attr( $class ) . '>';

				if ( ! empty( $field['args']['label_for'] ) ) {
					echo '<th scope="row"><label for="' . esc_attr( $field['args']['label_for'] ) . '">' . esc_html( $field['title'] ) . '</label></th>';
				} else {
					echo '<th scope="row">' . esc_html( $field['title'] ) . '</th>';
				}

				echo '<td>';
				call_user_func( $field['callback'], $field['args'] );
				echo '</td>';
				echo '</tr>';
				echo '</table>';

			}
		}

		/**
		 * Prints out all settings sections added to a particular settings page
		 *
		 * Part of the Settings API. Use this in a settings page callback function
		 * to output all the sections and fields that were added to that $page with
		 * add_settings_section() and add_settings_field()
		 *
		 * @global $wp_settings_sections Storage array of all settings sections added to admin pages.
		 * @global $wp_settings_fields Storage array of settings fields and info about their pages/sections.
		 * @since 2.7.0
		 *
		 * @param string $page The slug name of the page whose settings sections you want to output.
		 */
		public static function do_settings_sections( $page ) {
			global $wp_settings_sections, $wp_settings_fields;

			if ( ! isset( $wp_settings_sections[ $page ] ) ) {
				return;
			}

			foreach ( (array) $wp_settings_sections[ $page ] as $section ) {

				if ( ! isset( $wp_settings_fields ) || ! isset( $wp_settings_fields[ $page ] ) || ! isset( $wp_settings_fields[ $page ][ $section['id'] ] ) ) {
					continue;
				}
				?>

				<div id="<?php echo esc_attr( $section['id'] ); ?>" class="anony-section-group">
					<?php

					if ( $section['title'] ) {
						echo '<h2>' . esc_html( $section['title'] ) . "</h2>\n";
					}

					if ( $section['callback'] ) {
						call_user_func( $section['callback'], $section );
					}
						self::do_settings_fields( $page, $section['id'] );
					?>
				</div>
				<?php
			}
		}
	}
}
