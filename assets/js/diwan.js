jQuery(document).ready(function($){
	const ajaxUrl = diwanLoc.ajaxUrl;
	"use strict";
	
	$('.save-alt').on('click', function(e){
		
		e.preventDefault();
		var relId = $(this).attr('rel-id');
		var elParent = $(this).parent();
		
		var altWord  = elParent.find('.word-element').val();
		var altIndex = elParent.find('.word-element-index').val();
		var wordAlts = $('#' + relId).val();
		var postId   = $('#post_ID').val();		
	
		var dataString = 'action=diwan_update_alts&post_id='+postId+'&word_element_index=' + altIndex +'&word_element_alt=' + altWord +'&word_element_alternatives=' + wordAlts;
		$.ajax({
			type:'POST',
			data:dataString,
			url : ajaxUrl,
			success:function(response) {
				console.log(response.result);
			}
    	});
	});
	
});