<?php
/**
 * Location render class.
 *
 * @package Anonymous plugin
 * @author Makiomar
 * @link http://makiomar.com
 */

class ANONY_Location {

	/**
	 * @var object
	 */
	private $parent;

	/**
	 * Color field Constructor.
	 *
	 * @param object $parent Field parent object
	 */
	public function __construct( $parent = null ) {
		if ( ! is_object( $parent ) ) {
			return;
		}

		$this->parent = $parent;
		$this->enqueue();
	}

	/**
	 * Text field render Function.
	 *
	 * Suitable if editing/submitting is enabled
	 *
	 * @return void
	 */
	public function render() {

		$placeholder = ( isset( $this->parent->field['placeholder'] ) ) ? 'placeholder="' . $this->parent->field['placeholder'] . '"' : '';

		$html = sprintf(
			'<fieldset class="anony-row anony-row-inline%3$s fieldset-location" id="fieldset_%1$s"%2$s>',
			$this->parent->field['id'],
			$this->parent->field['type'] == 'hidden' ? ' style="display:none"' : '',
			$this->parent->width
		);

		if ( isset( $this->parent->field['note'] ) ) {
			$html .= '<p class=anony-warning>' . $this->parent->field['note'] . '<p>';
		}

		if ( in_array( $this->parent->context, array( 'meta', 'form' ) ) && isset( $this->parent->field['title'] ) ) {
			$html .= sprintf(
				'<label class="anony-label" for="%1$s">%2$s</label>',
				$this->parent->field['id'],
				$this->parent->field['title']
			);
		}

		$html .= sprintf(
			'<div style="position:relative"><input id="%1$s" type="%2$s" name="%3$s" value="%4$s" class="%5$s" %6$s/>',
			$this->parent->field['id'],
			'text',
			$this->parent->input_name,
			$this->parent->value,
			$this->parent->class_attr,
			$placeholder,
		);

		$html .= '<a href="#" onclick="event.preventDefault();" class="fetch-location-button" data-target="' . $this->parent->field['id'] . '"><?xml version="1.0" encoding="utf-8"?><!-- Uploaded to: SVG Repo, www.svgrepo.com, Generator: SVG Repo Mixer Tools -->
		<svg width="25px" height="25px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
		<path d="M4.20404 15C3.43827 15.5883 3 16.2714 3 17C3 19.2091 7.02944 21 12 21C16.9706 21 21 19.2091 21 17C21 16.2714 20.5617 15.5883 19.796 15M12 6.5V11.5M9.5 9H14.5M18 9.22222C18 12.6587 15.3137 15.4444 12 17C8.68629 15.4444 6 12.6587 6 9.22222C6 5.78578 8.68629 3 12 3C15.3137 3 18 5.78578 18 9.22222Z" stroke="#fff" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
		</svg></a></div>';
		$html .= '<span id="' . $this->parent->field['id'] . '-response"></span>';

		$html .= ( isset( $this->parent->field['desc'] ) && ! empty( $this->parent->field['desc'] ) ) ? ' <div class="description ' . $this->parent->class_attr . '">' . $this->parent->field['desc'] . '</div>' : '';

		$html .= '</fieldset>';

		return $html;
	}

	/**
	 * Enqueue scripts.
	 */
	public function enqueue() {

		$engine_options = ANONY_Options_Model::get_instance( ANONY_ENGINE_OPTIONS );

		if ( ! empty( $engine_options->google_maps_api_key ) && '1' === $engine_options->enable_google_maps_script ) {

				wp_enqueue_script( 'anony-google-map-api' );
				wp_enqueue_script(
					'anony-field-location-js',
					ANONY_FIELDS_URI . 'location/location.js',
					array( 'anony-google-map-api' ),
					time(),
					array( 'in_footer' => true )
				);

		}
	}
}
