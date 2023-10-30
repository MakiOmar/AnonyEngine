function AnonyUpload(){
	(function ($) {

		$( "img[src='']" ).attr( "src", anony_gallery.url );

		$( "body" ).on(
			'click',
			'.anony-opts-gallery',
			function ( event ) {
				event.preventDefault();
				item_clicked = $( this );
				var parent   = $( this ).closest( 'fieldset' );

				parent.find( '.anony_gallery' ).trigger( 'click' );

			}
		);

		$( "body" ).on(
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
				}
			}
		);

		jQuery( document.body ).on(
			"click",
			".anony_remove_gallery_image" ,
			function ( event ) {
				event.preventDefault();
				if (confirm( 'Are you sure you want to remove this image?' )) {
					$attachment_id = jQuery( this ). attr( 'rel-id' );

					var activeFileUploadContext = jQuery( this ).parent();

					activeFileUploadContext.find( '#anony-gallery-thumb-' + $attachment_id ).val( '' );

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

			}
		);

	})( jQuery );
}

jQuery( document ).ready(
	function ($) {
		var anony_gallery = new AnonyUpload();
	}
);
