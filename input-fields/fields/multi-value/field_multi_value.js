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
				var dafaultHtml = dafaultHtml.replace(
					/(#index#)/g,
					function (a, b) {
						return 'item-' + targetCounter;
					}
				);
				$( '#' + targetId + '-add' ).append( dafaultHtml );

			}
		);

		$('.anony-remove-multi-value-item').on( 'click', function(e){
			e.preventDefault();
			var targetId = $(this).data( 'id' );
			// set new count.
			$( '#' + targetId + '-counter' ).val( parseInt( $( '#' + targetId + '-counter' ).val() ) - 1 );
			$(this).parent().remove();
			$( '.anony-multi-value-flex', '#fieldset_' + targetId ).each( function(index){
				// Get the container element
				var container = $( this );
				var newIndex  = 'item-' + (index + 1);
				// Update the data-index attribute for the container
				container.attr("data-index", newIndex );

				// Get the HTML content of the container
				var containerHTML = container.html();

				// Update subsequent occurrences of item-(\d+)
				containerHTML = containerHTML.replace(/item-(\d+)/g, function() {
					return newIndex;
				});

				// Set the modified HTML back to the container
				container.html(containerHTML);
			} );
		} );
	}
);
