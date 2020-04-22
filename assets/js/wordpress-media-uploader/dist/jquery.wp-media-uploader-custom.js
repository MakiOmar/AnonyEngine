jQuery(document).ready(function($){
	"use strict";
	var mediaUploader = $("#media-caller").mediaUploader({
		editor: "my-editor",
		target: "#upload-result"
	});
	mediaUploader.add([]);
	$('.insert-media').click(function(){
		$('.current-upload').hide();
		$('#download-file a').hide();
	});
	$('#upload-result').bind("DOMSubtreeModified",function(){
		var imgUrl = $( ".attachment input[type=hidden]" ).val();
		var slashIndex = imgUrl.lastIndexOf("/") + 1;
		var fullfilename = imgUrl.substr(slashIndex);
		$('#download-file p span').text(fullfilename);
	});
});