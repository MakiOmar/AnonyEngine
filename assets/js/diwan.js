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
		var wordAltsArr = $('#' + relId).val().split(',');
		var postId   = $('#post_ID').val();
		
		var htmlOpt = '';
		
		if(Array.isArray(wordAltsArr) && wordAltsArr.length){
			$.each(wordAltsArr, function( index, value ){
				
				htmlOpt = htmlOpt.concat('<option value=' + value + '>' + value + '</option>');
			});
		}		
	
		var dataString = 'action=diwan_update_alts&post_id='+postId+'&word_element_index=' + altIndex +'&word_element_alt=' + altWord +'&word_element_alternatives=' + wordAlts;
		$.ajax({
			type:'POST',
			data:dataString,
			url : ajaxUrl,
			success:function(response) {
				
				if (response.result === 'success') {
					elParent.find('.success-msg').show();
					elParent.find('.failed-msg').hide();
					
					elParent.find('.words-alts-select').append(htmlOpt);
					
					setTimeout(function(){
						elParent.find('.success-msg').fadeOut('slow');
						$('#' + relId).val('');
					}, 1000);
					
					setTimeout(function(){
						elParent.find('.success-msg').hide();
					}, 2000);
				}
				
				if (response.result === 'failed') {
					elParent.find('.success-msg').hide();
					elParent.find('.failed-msg').show();
					setTimeout(function(){
						elParent.find('.failed-msg').fadeOut('slow');
					}, 1000);
					
					setTimeout(function(){
						elParent.find('.failed-msg').fadeOut('slow');
						elParent.find('.failed-msg').hide();
					}, 2000);
				}
				console.log(response.result);
			}
    	});
	});
	
});