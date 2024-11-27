function AnonyUpload(){
	(function ($) {

		jQuery( "img[src='']" ).attr( "src", anony_gallery.url );

		jQuery( document ).on(
			'click',
			".anony-opts-gallery",
			function ( event ) {
				event.preventDefault();

				var activeFileUploadContext = jQuery( this ).closest('fieldset');
				var targetID                = $(this).data( 'id' );
				var targetFieldName         = $(this).data( 'name' );
				var custom_file_frame = null;
				// Create the media frame.
				custom_file_frame = wp.media.frames.customHeader = wp.media(
					{
						title: jQuery( this ).data( "choose" ),
						library: {
							type: 'image'
						},
						button: {
							text: jQuery( this ).data( "update" )
						},
						multiple: true
					}
				);

				custom_file_frame.on(
					"select",
					function () {

						var attachment_ids  = new Array();
						var attachment_urls = new Array();

						var attachments = custom_file_frame.state().get( "selection" );

						i = 0;
						var attachment_ids_string = '';
						attachments.each(
							function (attachment) {
								attachment_ids_string += attachment['id'] + ',';
								attachment_ids[i] = attachment['id'];
								$( activeFileUploadContext ).find( '.anony-gallery-thumbs' ).append(
									'<div class="gallery-item-container" style="display:inline-flex; flex-direction:column; align-items: center;margin-left:15px;"><a href="#" style="display:block; width:50px; height:50px;background-color: #d2d2d2;border-radius: 3px;padding:5px"><img src="' + attachment.attributes.url + '" alt="" style="width:100%;height:100%;display:block;"/></a><input type="hidden" name="' + targetFieldName + '[]" class="gallery-item" id="anony-gallery-thumb-' + attachment.id + '" value="' + attachment.id + '" /><a href="#" class="anony_remove_gallery_image" style="display:block">Remove</a></div>'
								);
								i++;
							}
						);
						attachment_ids_string = attachment_ids_string.replace(/,\s*$/, "");
						activeFileUploadContext.find( '#' + targetID ).val(attachment_ids_string).trigger('change');
						( '.anony-opts-clear-gallery', activeFileUploadContext ).attr( 'style', 'display:inline-block!important' );

					}
				);

				custom_file_frame.open();
			}
		);

		jQuery( document ).on(
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

		jQuery( document ).on(
			"click",
			".anony-opts-clear-gallery",
			function (event) {
				event.preventDefault();

				if (confirm( 'Are you sure you want to remove all images?' )) {

					var activeFileUploadContext = jQuery( this ).parent();

					activeFileUploadContext.find( '.gallery-item' ).val( '' );
					activeFileUploadContext.find( '.anony-gallery-thumbs div' ).fadeOut( 'slow' );
					setTimeout(
						function () {
							activeFileUploadContext.find( '.anony-gallery-thumbs' ).empty();
						},
						500
					);
					jQuery( this ).fadeOut( 'slow' );
					jQuery( this ).attr( 'style', 'display:none!important' );

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
