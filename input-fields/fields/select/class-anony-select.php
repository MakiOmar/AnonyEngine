<?php
/**
 * Select field class
 *
 * @package Anonymous theme
 * @author Makiomar
 * @link http://makiomar.com
 */

/**
 * Select render class.
 *
 * @package    Fields inputs
 * @author     Makiomar <info@makiomar.com>
 * @license    https://makiomar.com AnonyEngine Licence
 * @link       https://makiomar.com
 */
class ANONY_Select {

	/**
	 * Parent object
	 *
	 * @var object
	 */
	private $parent_obj;

	/**
	 * Option number
	 *
	 * @var object
	 */
	private $numbered;

	/**
	 * First option label
	 *
	 * @var object
	 */
	private $first_option;

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

		$this->numbered = isset( $this->parent_obj->field['numbered'] ) && 'yes' === $this->parent_obj->field['numbered'] ? true : false;

		$this->first_option = ! empty( $this->parent_obj->field['first_option'] ) ? $this->parent_obj->field['first_option'] : esc_html__( 'Select', 'anonyengine' );

		add_action(
			'wp_print_footer_scripts',
			function () {
				if ( ! empty( $this->parent_obj->field['on_change'] ) ) :
					$data = array(
						'action' => esc_js( $this->parent_obj->field['on_change']['action'] ),
						'target' => esc_js( $this->parent_obj->field['on_change']['target'] ),
						'nonce'  => wp_create_nonce( 'term-children' ),
					);

					if ( ! empty( $this->parent_obj->field['on_change']['data'] ) ) {
						foreach ( $this->parent_obj->field['on_change']['data'] as $k => $v ) {
							$data[ $k ] = esc_js( $v );
						}
					}
					?>
					<script>
						jQuery( document ).ready( function($) {
							var dataObject = <?php echo wp_json_encode( $data ); ?>;

							$( 'body' ).on( 'change', '#<?php echo esc_js( $this->parent_obj->id_attr_value ); ?>', function() {
								var selectedId = $( this ).val();
								dataObject.term_id = selectedId;

								$.ajax({
									type : "POST",
									data: dataObject,
									url : '<?php echo esc_url( ANONY_Wpml_Help::get_ajax_url() ); ?>',
									beforeSend: function( jqXHR, settings ) {
										$( '#<?php echo esc_js( $this->parent_obj->field['on_change']['target'] ); ?>' ).prop( 'disabled', true );
									},
									success: function( response ) {
										var firstOption = '<option value="">' + response.firstOption + '</option>';
										$( '#<?php echo esc_js( $this->parent_obj->field['on_change']['target'] ); ?>' ).html( firstOption + response.html );
									},

									complete: function( jqXHR, textStatus ) {
										$( '#<?php echo esc_js( $this->parent_obj->field['on_change']['target'] ); ?>' ).prop( 'disabled', false );
									},

									error: function( jqXHR, textStatus, errorThrown ) {
										
									}
								} );
							} );
						} );
					</script>
					<?php
				endif;
			}
		);
	}

	/**
	 * Select field render Function.
	 *
	 * @return string Field output.
	 */
	public function render() {

		$disabled = isset( $this->parent_obj->field['disabled'] ) && ( true === $this->parent_obj->field['disabled'] ) ? ' disabled' : '';

		$autocomplete = ( isset( $this->parent_obj->field['auto-complete'] ) && 'on' === $this->parent_obj->field['auto-complete'] ) ? 'autocomplete="on"' : 'autocomplete="off"';

		if ( isset( $this->parent_obj->field['multiple'] ) && $this->parent_obj->field['multiple'] ) {
			$multiple                     = ' multiple ';
			$this->parent_obj->input_name = $this->parent_obj->input_name . '[]';

		} else {
			$multiple = '';
		}

		$html = sprintf(
			'<fieldset class="anony-row anony-row-inline" id="fieldset_%1$s">',
			$this->parent_obj->id_attr_value
		);

		if ( isset( $this->parent_obj->field['note'] ) ) {
			echo '<p class=anony-warning>' . esc_html( $this->parent_obj->field['note'] ) . '<p>';
		}

		if ( in_array( $this->parent_obj->context, array( 'meta', 'form' ), true ) && isset( $this->parent_obj->field['title'] ) ) {
			$html .= sprintf(
				'<label class="anony-label" for="%1$s">%2$s</label>',
				$this->parent_obj->id_attr_value,
				$this->parent_obj->field['title']
			);
		}

		$html .= sprintf(
			'<select class="%1$s" name="%2$s" id="' . $this->parent_obj->id_attr_value . '" %3$s %4$s %5$s>',
			$this->parent_obj->class_attr,
			$this->parent_obj->input_name,
			$disabled,
			$multiple,
			$autocomplete
		);

		if ( is_array( $this->parent_obj->field['options'] ) && ! empty( $this->parent_obj->field['options'] ) ) {

			$html .= sprintf(
				'<option value="">%1$s</option>',
				apply_filters(
					'anony_select_first_option_label',
					$this->first_option,
					$this->parent_obj->field['id']
				)
			);

			$option_number = 1;

			if ( empty( $multiple ) ) :

				if ( ANONY_ARRAY_HELP::is_assoc( $this->parent_obj->field['options'] ) ) {

					foreach ( $this->parent_obj->field['options'] as $key => $label ) {

						$label = $this->numbered ? $option_number . '- ' . $label : $label;

						++$option_number;

						$html .= sprintf(
							'<option value="%1$s"%2$s>%3$s</option>',
							$key,
							selected( $this->parent_obj->value, $key, false ),
							$label
						);
					}
				} else {
					foreach ( $this->parent_obj->field['options'] as $value ) {

						$html .= sprintf(
							'<option value="%1$s"%2$s>%1$s</option>',
							$value,
							selected( $this->parent_obj->value, $value, false )
						);
					}
				}

				elseif ( ANONY_ARRAY_HELP::is_assoc( $this->parent_obj->field['options'] ) ) :
					foreach ( $this->parent_obj->field['options'] as $key => $label ) {

						$label = $this->numbered ? $option_number . '- ' . $label : $label;
						++$option_number;

						$selected = is_array( $this->parent_obj->value ) && in_array( $key, $this->parent_obj->value, true ) && '' !== $key ? ' selected' : '';

						$html .= sprintf(
							'<option value="%1$s"%2$s>%3$s</option>',
							$key,
							$selected,
							$label
						);
					}
					else :
						foreach ( $this->parent_obj->field['options'] as $value ) {

							$selected = is_array( $this->parent_obj->value ) && in_array( $value, $this->parent_obj->value, true ) && '' !== $value ? ' selected' : '';

							$html .= sprintf(
								'<option value="%1$s"%2$s>%1$s</option>',
								$value,
								$selected
							);
						}

				endif;
		} else {

			$html .= sprintf(
				'<option value="">%1$s</option>',
				$this->first_option
			);
		}

		$html .= '</select>';

		$html .= ( isset( $this->parent_obj->field['desc'] ) && ! empty( $this->parent_obj->field['desc'] ) ) ? ' <div class="description">' . $this->parent_obj->field['desc'] . '</div>' : '';

		$html .= '</fieldset>';

		return $html;
	}
}
