function AnonyUpload(){
	(function($) {

		jQuery( "img[src='']" ).attr( "src", anony_upload.url );

		jQuery( ".anony-opts-upload" ).click( function( event ) {   
        	event.preventDefault();
        	
        	var activeFileUploadContext = jQuery(this).parent();
        	custom_file_frame = null;
        	
        	item_clicked = jQuery(this);

            // Create the media frame.
            custom_file_frame = wp.media.frames.customHeader = wp.media({
            	title: jQuery(this).data( "choose" ),
            	library: {
            		type: 'image'
            	},
                button: {
                    text: jQuery(this).data( "update" )
                }
            });

            custom_file_frame.on( "select", function() {
            	var attachment = custom_file_frame.state().get( "selection" ).first();

                // Update value of the targetfield input with the attachment url.
                jQuery( '.anony-opts-screenshot', activeFileUploadContext ).attr( 'src', attachment.attributes.url );
                jQuery( 'input', activeFileUploadContext )
            		.val( attachment.attributes.url )
            		.trigger( 'change' );

                jQuery( '.anony-opts-upload', activeFileUploadContext ).hide();
                jQuery( '.anony-opts-screenshot', activeFileUploadContext ).show();
                jQuery( '.anony-opts-upload-remove', activeFileUploadContext ).show();
            });

            custom_file_frame.open();
        });

	    jQuery( ".anony-opts-upload-remove" ).click( function( event ) {
	    	event.preventDefault();
	    	
	        var activeFileUploadContext = jQuery( this ).parent();
	
	        jQuery( 'input', activeFileUploadContext ).val('');
	        jQuery(this).prev().fadeIn('slow');
	        jQuery( '.anony-opts-screenshot', activeFileUploadContext ).fadeOut( 'slow' );
	        jQuery(this).fadeOut( 'slow' );
	    });

	})(jQuery);
}
	
jQuery(document).ready(function($){
	var anony_upload = new AnonyUpload();
});
