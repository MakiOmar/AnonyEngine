jQuery( document ).ready(
	function ($) {
		'use strict';

		$( '.multi-value-btn' ).on(
			'click',
			function () {
				var thisBtn = $( this );

				var targetId   = thisBtn.attr( 'rel-id' );
				var targetName = thisBtn.attr( 'rel-name' );

				// set new count.
				$( '#' + targetId + '-counter' ).val( parseInt( $( '#' + targetId + '-counter' ).val() ) + 1 );

				// Counter for duplication.
				var targetCounter = $( '#' + targetId + '-counter' ).val();

				var targetName = targetName.replace(
					/(#index#)/g,
					function (a, b) {
						return 'item-' + targetCounter;
					}
				);

				// Template id.
				var defaultId = targetId + '-default';

				var dafaultHtml = $( '#' + defaultId ).html();

				var x = dafaultHtml.replace(
					/\[(\w+)\]/g,
					function ( a, b ) {
						return targetName + '[' + b + ']';
					}
				);

				$( '#' + targetId + '-add' ).append( x );

			}
		);
	}
);
