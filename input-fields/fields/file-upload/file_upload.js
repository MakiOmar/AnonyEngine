jQuery(document).ready(function($){
	"use strict";
	$( ".file-upload" ).click( function( event ) {   
    	event.preventDefault();
    	
    	var activeFileUploadContext = $(this).parent().parent();
    	
    	var custom_file_frame, item_clicked;
    	
    	/**
	     * If an instance of file_frame already exists, then we can open it
	     * rather than creating a new instance.
	     */
	    if ( undefined !== custom_file_frame ) {
	 
	        custom_file_frame.open();
	        return;
	 
	    }
	    
	    custom_file_frame = null;
        	
        item_clicked = $(this);
        
        // Create the media frame.
        custom_file_frame = wp.media.frames.customHeader = wp.media({
        	title: $(this).data( "choose" ),
        	
            button: {
                text: $(this).data( "update" )
            },
          
        });
        
        custom_file_frame.on( "select", function() {

            	var attachment = custom_file_frame.state().get( "selection" ).first();
            	var url = attachment.changed.url;
            	var title = attachment.changed.title;
            	activeFileUploadContext.find('.attachment a').css("display", "block");
            	activeFileUploadContext.find('.attachment a').attr('href', url);
            	activeFileUploadContext.find('.attachment a').text(title);
            	activeFileUploadContext.find('.attachment input').val(attachment.id);
            	activeFileUploadContext.find('.download-file .no-file').css("display", "none");
            	activeFileUploadContext.find('.download-file .file').css("display", "none");
            	console.log(attachment);       
        });

        custom_file_frame.open();
     });
});