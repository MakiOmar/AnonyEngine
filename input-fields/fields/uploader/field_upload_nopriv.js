;(function ($) {
	"use strict";
	$( "body" ).on(
		'click',
		'.uploader-trigger',
		function () {
			var target = $( this ).data( 'id' );
			$( '#' + target ).trigger( 'click' );
		}
	);

	$( "body" ).on(
		'change',
		'.anony-uploader' ,
		function (event) {
			var parent           = $( this ).closest( 'fieldset' );
			var file             = event.target.files[0];
			var previewContainer = parent.find( ".anony-opts-screenshot" );

			if (file) {
				if (file.type.startsWith( "image/" )) {
					// Display image preview
					var reader = new FileReader();

					reader.onload = function (e) {
						previewContainer.attr( "src", e.target.result );
					};
					reader.readAsDataURL( file );

				} else {
					// Display file icon with file name

					previewContainer.attr( "src", anony_upload.url );
					var fileName = $( "<span>" ).text( file.name );
					parent.find( '.uploaded-file-name' ).append( fileName );
				}
			} else {
				previewContainer.attr( "src", anony_upload.browse_url );
				parent.find( '.uploaded-file-name' ).empty();
			}
		}
	);

})( jQuery );
