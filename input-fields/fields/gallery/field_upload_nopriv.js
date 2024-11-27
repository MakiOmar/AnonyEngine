function AnonyUpload(){
	(function ($) {

		$( "img[src='']" ).attr( "src", anony_gallery.url );

		$( document ).on(
			'click',
			'.anony-opts-gallery',
			function ( event ) {
				event.preventDefault();
				item_clicked = $( this );
				var parent   = $( this ).closest( 'fieldset' );

				parent.find( '.anony_gallery' ).trigger( 'click' );

			}
		);

		$( document ).on(
			'change',
			'.anony_gallery' ,
			function (event) {
				var parent = $( this ).closest( 'fieldset' );

				var files         = event.target.files;
				var thumbsWrapper = $( '.anony-gallery-thumbs' );
				if (files) {
					for (let i = 0; i < files.length; i++) {
						var file = files[i];
						if (file.type.startsWith( "image/" )) {
							// Display image preview.
							var reader = new FileReader();

							reader.onload = function (e) {

								var src = e.target.result;
								var img = $(
									"<img />",
									{
										src: src,
										width: 60
									}
								);
								thumbsWrapper.append( img );

							};
							reader.readAsDataURL( file );

						} else {
							// Display file icon with file name.
							var fileName = $( "<span>" ).text( file.name );
							var img      = $(
								"<img />",
								{
									src: anony_gallery.file_icon_url,
									width: 60
								}
							);

							var itemWrapper = $( '<span>', {class : 'file-preview-wrapper'} );
							itemWrapper.append( img );
							itemWrapper.append( fileName );
							thumbsWrapper.append( itemWrapper );

						}
					}

					$('.anony-opts-clear-gallery').show();
				} else {
					$('.anony-opts-clear-gallery').hide();
				}
			}
		);
		jQuery( document.body ).on(
			'click',
			'.anony-opts-clear-gallery',
			function(){
				var thumbsWrapper = $( '.anony-gallery-thumbs' );
				thumbsWrapper.html('');
				var parent   = $( this ).closest( 'fieldset' );
				parent.find( '.anony_gallery' ).val( null );
				$(this).hide();
			}
		)
		jQuery( document.body ).on(
			"click",
			".anony_remove_gallery_image" ,
			function ( event ) {
				event.preventDefault();
				if (confirm( 'Are you sure you want to remove this image?' )) {
					var attachmentID = jQuery( this ). attr( 'rel-id' );
					var fieldID      = $(this).data('field-id');
					
					var parentForm = $(this).closest('.anony-form');
					var objectType = parentForm.data('object-type');
					var objectID   = parentForm.data('object-id');
					var formID     = parentForm.attr('id');
					var nonce      = $( '#anony_form_submit_nonce_' + formID ).val();
					var formData   = JSON.parse( decodeURIComponent( $('#data-' + formID ).data('value') ) );

					if ( parentForm.data('object-type') !== undefined && parentForm.data('object-id') !== undefined ) {
						$.ajax({
							type : "POST",
							data: {
								object_type   : objectType,
								object_id     : objectID,
								form_id       : formID,
								attachment_id : attachmentID,
								action        : 'remove_gallery_item',
								field_config  : formData[fieldID],
								nonce         : nonce,
							},
							url : AnonyLoc.ajaxUrl,
							beforeSend: function( jqXHR, settings ) {
								
							},
							success: function( response ) {
								if ( true === response.status ) {
									var activeFileUploadContext = jQuery( this ).parent();

									activeFileUploadContext.find( '#anony-gallery-thumb-' + attachmentID ).val( '' );
				
									jQuery( this ).prev().fadeIn( 'slow' );
									activeFileUploadContext.fadeOut( 'slow' );
									jQuery( this ).fadeOut( 'slow' );
									setTimeout(
										function () {
											activeFileUploadContext.remove();
										},
										500
									);
				
									setTimeout(
										function () {
											if ( jQuery( '.gallery-item-container' ).length == 0 ) {
												jQuery( '.anony-opts-clear-gallery' ).attr( 'style', 'display:none!important' );
											}
										},
										502
									);
								}
							},

							complete: function( jqXHR, textStatus ) {
								
							},

							error: function( jqXHR, textStatus, errorThrown ) {
								
							}
						} );
					}
				}

			}
		);

	})( jQuery );
}

jQuery( document ).ready(
	function ($) {
		var anony_gallery = new AnonyUpload();
	}
);
