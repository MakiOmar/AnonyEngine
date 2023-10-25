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
			'<fieldset class="anony-row anony-row-inline%3$s" id="fieldset_%1$s"%2$s>',
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
			'<input id="%1$s" type="%2$s" name="%3$s" value="%4$s" class="%5$s" %6$s/>',
			$this->parent->field['id'],
			'text',
			$this->parent->input_name,
			$this->parent->value,
			$this->parent->class_attr,
			$placeholder,
			

		);

		$html .= '<a href="#" onclick="event.preventDefault();" class="fetch-location-button" data-target="'. $this->parent->field['id'] .'">Fetch Location Address</a>';
		$html .= '<span id="'.$this->parent->field['id'].'-response"></span>';

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
						
				wp_enqueue_script( 'anony-google-map-api');
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
