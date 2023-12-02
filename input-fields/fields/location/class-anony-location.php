<?php
/**
 * Location class.
 *
 * @package Anonymous plugin
 * @author Makiomar
 * @link http://makiomar.com
 */

/**
 * Location render class.
 *
 * @package Anonymous plugin
 * @author Makiomar
 * @link http://makiomar.com
 */
class ANONY_Location {

	/**
	 * Parent object
	 *
	 * @var object
	 */
	private $parent_obj;

	/**
	 * Color field Constructor.
	 *
	 * @param object $parent_obj Field parent object.
	 */
	public function __construct( $parent_obj = null ) {
		if ( ! is_object( $parent_obj ) ) {
			return;
		}

		$this->parent_obj = $parent_obj;
	}

	/**
	 * Text field render Function.
	 *
	 * Suitable if editing/submitting is enabled
	 *
	 * @return string Field output.
	 */
	public function render() {

		$placeholder = ( isset( $this->parent_obj->field['placeholder'] ) ) ? 'placeholder="' . $this->parent_obj->field['placeholder'] . '"' : '';

		$html = sprintf(
			'<fieldset class="anony-row anony-row-inline%3$s fieldset-location" id="fieldset_%1$s"%2$s>',
			$this->parent_obj->id_attr_value,
			'hidden' === $this->parent_obj->field['type'] ? ' style="display:none"' : '',
			$this->parent_obj->width
		);

		if ( isset( $this->parent_obj->field['note'] ) ) {
			$html .= '<p class=anony-warning>' . esc_html( $this->parent_obj->field['note'] ) . '<p>';
		}

		if ( in_array( $this->parent_obj->context, array( 'meta', 'form' ), true ) && isset( $this->parent_obj->field['title'] ) ) {
			$html .= sprintf(
				'<label class="anony-label" for="%1$s">%2$s</label>',
				$this->parent_obj->id_attr_value,
				$this->parent_obj->field['title']
			);
		}

		$html .= sprintf(
			'<div style="position:relative"><input id="%1$s" type="%2$s" name="%3$s" value="%4$s" class="%5$s" %6$s/>',
			$this->parent_obj->id_attr_value,
			'text',
			$this->parent_obj->input_name,
			$this->parent_obj->value,
			$this->parent_obj->class_attr,
			$placeholder,
		);
		$icon  = '<?xml version="1.0" encoding="utf-8"?><!-- Uploaded to: SVG Repo, www.svgrepo.com, Generator: SVG Repo Mixer Tools -->
		<svg width="25px" height="25px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
		<path d="M4.20404 15C3.43827 15.5883 3 16.2714 3 17C3 19.2091 7.02944 21 12 21C16.9706 21 21 19.2091 21 17C21 16.2714 20.5617 15.5883 19.796 15M12 6.5V11.5M9.5 9H14.5M18 9.22222C18 12.6587 15.3137 15.4444 12 17C8.68629 15.4444 6 12.6587 6 9.22222C6 5.78578 8.68629 3 12 3C15.3137 3 18 5.78578 18 9.22222Z" stroke="#fff" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
		</svg>';
		$html .= '<a href="#" onclick="event.preventDefault();" class="fetch-location-button" data-target="' . $this->parent_obj->id_attr_value . '">' . apply_filters( 'anony_location_icon', $icon ) . '</a></div>';
		$html .= '<span id="' . $this->parent_obj->id_attr_value . '-response"></span>';

		$html .= ( isset( $this->parent_obj->field['desc'] ) && ! empty( $this->parent_obj->field['desc'] ) ) ? ' <div class="description ' . $this->parent_obj->class_attr . '">' . $this->parent_obj->field['desc'] . '</div>' : '';

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
