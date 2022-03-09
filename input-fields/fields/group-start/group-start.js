jQuery( document ).ready(
	function($){
		'use strict';

		$( '.anony-form-group-heading > i' ).on(
			'click',
			function(){
				var parent = $( this ).parent();
				var target = parent.data( 'id' );

				if ($( this ).hasClass( 'fa-chevron-down' )) {
					$( '#form-group-' + target ).slideDown( 'slow' );
					$( this ).removeClass( 'fa-chevron-down' ).addClass( 'fa-chevron-up' );
				} else {
					$( '#form-group-' + target ).slideUp( 'slow' );
					$( this ).removeClass( 'fa-chevron-up' ).addClass( 'fa-chevron-down' );
				}
			}
		);
	}
);
