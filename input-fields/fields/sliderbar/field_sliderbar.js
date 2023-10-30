jQuery( document ).ready(
	function ($) {

		$( '.sliderbar' ).each(
			function () {

				var field_id     = $( this ).attr( 'rel' );
				var currentValue = $( this ).siblings( '#' + field_id ).attr( 'value' );

				var siderValues;

				if (currentValue !== '') {
					currentValue = currentValue.replace( /\$/g, '' ).replace( /\s+/gm,'' );

					siderValues = currentValue.split( '-' );
				} else {
					siderValues = [100 , 400];
				}

				$( this ).slider(
					{
						isRTL: true,
						range: true,
						min:0,
						max:1000,
						values: siderValues,
						slide: function (event, ui) {
							$( this ).siblings( '#' + field_id ).val( "$" + ui.values[ 0 ] + " - $" + ui.values[ 1 ] );
						}
					}
				);
			}
		);

	}
);
