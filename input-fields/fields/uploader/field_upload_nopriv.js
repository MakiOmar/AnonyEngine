;(function($) {
	"use strict";
	$("body").on('click', '.uploader-trigger', function(){
		var target = $(this).data('id');
		$('#' + target).trigger('click');
	});

	$("body").on('change', '#' + anony_upload.target_id, function(event){
		var file = event.target.files[0];
		var previewContainer = $(".anony-opts-screenshot");

		if (file) {
			if (file.type.startsWith("image/")) {
				// Display image preview
				var reader = new FileReader();
				
				reader.onload = function (e) {
					previewContainer.attr("src", e.target.result);
				};
				reader.readAsDataURL(file);

			} else {
				// Display file icon with file name
				
				previewContainer.attr("src", anony_upload.url);
				var fileName = $("<span>").text(file.name);
				$('.uploaded-file-name').append(fileName);
			}
		} else {
			previewContainer.attr("src", anony_upload.browse_url);
			$('.uploaded-file-name').empty();
		}
	});

	

})( jQuery );
