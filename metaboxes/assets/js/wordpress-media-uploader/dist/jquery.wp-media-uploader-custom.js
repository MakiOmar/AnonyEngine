jQuery(document).ready(function($){
	"use strict";
	var mediaUploader = $("#anony-media-caller").mediaUploader({
		editor: "anony-my-editor",
		target: "#anony-upload-result"
	});
	mediaUploader.add([]);
	/*insert-media name shouldn't be changed (It is a WordPress built-in class)*/
	$('.insert-media').click(function(e){
		e.preventDefault();
		$('.anony-current-upload').hide();
		$('.anony-upload').hide();
	});
	$('#anony-upload-result').bind("DOMSubtreeModified",function(){
		var imgUrl = $( ".attachment input[type=hidden]" ).val();
		var slashIndex = imgUrl.lastIndexOf("/") + 1;
		var fullfilename = imgUrl.substr(slashIndex);
		$('#anony-file-name span').text(fullfilename);
	});
});