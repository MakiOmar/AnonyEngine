jQuery( document ).ready(
	function ($) {
		'use strict';

		$( '.multi-value-btn' ).on(
			'click',
			function () {
				var thisBtn = $( this );

				var targetId = thisBtn.attr( 'rel-id' );

				// Target element to be duplicated
				var targetClass = thisBtn.attr( 'rel-class' );

				// set new count
				$( '#' + targetId + '-counter' ).val( parseInt( $( '#' + targetId + '-counter' ).val() ) + 1 );

				// Counter for duplication
				var targetCounter = $( '#' + targetId + '-counter' ).val();

				// Template id
				var defaultId = targetId + '-default';

				var dafaultHtml = $( '#' + defaultId ).html();

				var x = dafaultHtml.replace(
					/(\w+)\[(\d+)\]/g,
					function (a, b, c) {
						return b + '[' + targetCounter + ']';
					}
				);

				$( '#' + targetId + '-add' ).append( x );

			}
		);
	}
);
