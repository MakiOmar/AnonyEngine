jQuery( document ).ready(
	function ($) {
		'use strict';
		$.fn.metaboxLayout = function (element) {
			$( element ).on(
				'click',
				function (e) {
					e.preventDefault();
					$( element ).not( this ).each(
						function () {

							var clicked = $( this );
							var target  = clicked.data( 'id' );
							clicked.removeClass( 'opened' );
							$( '#form-group-' + target ).slideUp( 'slow' );
						}
					);
					var clicked = $( this );
					var target  = clicked.data( 'id' );

					if (clicked.hasClass( 'opened' )) {
						$( '#form-group-' + target ).slideUp( 'slow' );
						clicked.removeClass( 'opened' ).addClass( 'closed' );
					} else {
						$( '#form-group-' + target ).slideDown( 'slow' );
						clicked.removeClass( 'closed' ).addClass( 'opened' );
					}
				}
			);

			$( element + ':first' ).click();
		};
		if ( $( ".anony-tabbed-metabox" ).length == 0 ) {
			$.fn.metaboxLayout( '.anony-form-group-heading' );
		} else {
			$.fn.metaboxLayout( '.anony-metabox-tab-item' );
		}

	}
);
