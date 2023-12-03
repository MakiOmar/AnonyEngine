jQuery(document).ready(function($) {
	"use strict";
	function AnonyUploader() {
		$( "img[src='']" ).attr( "src", anony_upload.url );
		var custom_file_frame = null;
		$('fieldset').on( "click", ".anony-opts-upload", function ( event ) {
			event.preventDefault();
			
			var activeFileUploadContext = $( this ).parent();
			var clicked                 = $( this ).data( 'id' );
			
			if (custom_file_frame) {
				custom_file_frame.open();
				return;
			}
			// Create the media frame.
			custom_file_frame = wp.media.frames.customHeader = wp.media(
				{
					title: $( this ).data( "choose" ),
					library: {
						type: 'image'
					},
					button: {
						text: $( this ).data( "update" )
					}
				}
			);
			custom_file_frame.on(
				"select",
				function () {
					var attachment = custom_file_frame.state().get( "selection" ).first();

					var imageExtensions = [ 'jpg', 'jpeg', 'jpe', 'gif', 'png', 'svg' ];

					if (imageExtensions.includes( attachment.attributes.subtype )) {
							// Update value of the targetfield input with the attachment url.
							$('#fieldset_' + clicked).find( '.anony-opts-screenshot', activeFileUploadContext ).attr( 'src', attachment.attributes.url );
					} else {
						$('#fieldset_' + clicked).find( '.anony-opts-screenshot', activeFileUploadContext ).attr( 'src', anony_upload.url );
						$('#fieldset_' + clicked).find( '.uploaded-file-name', activeFileUploadContext ).text( attachment.attributes.filename );
						$('#fieldset_' + clicked).find( '.uploaded-file-name', activeFileUploadContext ).show();
					}

					$( '#' + clicked ).val( attachment.attributes.id ).trigger( 'change' );

					$('#fieldset_' + clicked).find( '.anony-opts-upload', activeFileUploadContext ).hide();
					$('#fieldset_' + clicked).find( '.anony-opts-screenshot', activeFileUploadContext ).show();
					$('#fieldset_' + clicked).find( '.anony-opts-screenshot' ).parent().show();
					$('#fieldset_' + clicked).find( '.anony-opts-upload-remove', activeFileUploadContext ).show();
				}
			);
			custom_file_frame.open();

		});

		$('fieldset').on( "click", ".anony-opts-upload-remove" , function ( event ) {
				event.preventDefault();

				var activeFileUploadContext = $( this ).parent();

				var clicked = $( this ).data( 'id' );

				$( '#' + clicked ).val( '' ).trigger( 'change' );
				$('#fieldset_' + clicked).find('.anony-opts-upload').fadeIn( 'slow' );
				$('#fieldset_' + clicked).find( '.anony-opts-screenshot', activeFileUploadContext ).fadeOut( 'slow' );
				$('#fieldset_' + clicked).find( '.uploaded-file-name', activeFileUploadContext ).fadeOut( 'slow' );
				$( this ).fadeOut( 'slow' );
			}
		);
	}
	AnonyUploader();
});