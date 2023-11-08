;(function ($) {
	"use strict";
	$( "body" ).on(
		'click',
		'.uploader-trigger.style-two',
		function () {
			var parent = $( this ).closest( 'fieldset' );
			var target = $( this ).data( 'id' );
			parent.find( '#' + target ).trigger( 'click' );
		}
	);

	$( "body" ).on(
		'change',
		'.anony-uploader' ,
		function (event) {
			var parent           = $( this ).closest( 'fieldset' );
			var file             = event.target.files[0];
			var previewContainer = parent.find( ".uploads-wrapper.style-two" );

			if (file) {
				if (file.type.startsWith( "image/" )) {
					// Display image preview.
					var reader = new FileReader();

					reader.onload = function (e) {
						previewContainer.find('.anony-uploaded-preview').css( { 
							'background-image' : 'url("' + e.target.result + '")',
							'background-position' : 'center',
							'background-size' : 'cover',
						} );
					};
					reader.readAsDataURL( file );

				} else {
					// Display file icon with file name.
					previewContainer.find('.anony-uploaded-preview').css( { 
						'background-image' : 'url("' + anony_upload.url + '")',
						'background-position' : 'center',
						'background-size' : 'cover',
					} );
					var fileName = $( "<span>" ).text( file.name );
					parent.find( '.uploaded-file-name' ).append( fileName );
				}
			} else {
				previewContainer.find('.anony-uploaded-preview').css( { 
					'background-image' : 'none',
				} );
				parent.find( '.uploaded-file-name' ).empty();
			}
		}
	);

})( jQuery );
