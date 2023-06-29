jQuery( document ).ready(
	function($){
		'use strict';

		$( '.anony-form-group-heading' ).on(
			'click',
			function(e){
				e.preventDefault();
				$('.anony-form-group-heading').not(this).each(function(){

					var clicked = $(this);
					var target = clicked.data( 'id' );
					clicked.removeClass( 'opened' );
					$( '#form-group-' + target ).slideUp( 'slow' );
				});
				var clicked = $( this );
				var target = clicked.data( 'id' );

				if (clicked.hasClass( 'opened' )) {
					$( '#form-group-' + target ).slideUp( 'slow' );
					clicked.removeClass( 'opened' ).addClass( 'closed' );
				} else {
					$( '#form-group-' + target ).slideDown( 'slow' );
					clicked.removeClass( 'closed' ).addClass( 'opened' );
				}
			}
		);

		$( '.anony-form-group-heading:first' ).click();
	}
);
