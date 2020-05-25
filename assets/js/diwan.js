jQuery(document).ready(function($){
	const ajaxUrl = diwanLoc.ajaxUrl;
	"use strict";
	
	$('.save-alt').on('click', function(e){
		
		e.preventDefault();
		var clicked = $(this);
		var action = $(this).attr('data-action');
		clicked.addClass('loading');
		clicked.find('span').addClass('save_loader');
	
		var relId     = clicked.attr('rel-id');
		var elParent  = clicked.parent();
		var elWrapper = $('#' + relId + '-wrapper');
		
		var altWord  = elParent.find('.word-element').val();
		var altIndex = elParent.find('.word-element-index').val();
		var wordAlts = $('#' + relId).val();
		var wordAltsArr = $('#' + relId).val().split(',');
		var postId   = $('#post_ID').val();		
	
		var dataString = 'action='+action+'&post_id='+postId+'&word_element_index=' + altIndex +'&word_element_alt=' + altWord +'&word_element_alternatives=' + wordAlts;
		$.ajax({
			type:'POST',
			data:dataString,
			url : ajaxUrl,
			success:function(response) {
				
				if (response.result === 'success') {
					var htmlOpt = '';

					$.each(response.alt_list, function( index, value ){
						
						htmlOpt = htmlOpt.concat('<option value=' + altIndex + '-' +index + '>' + value + '</option>');
					});
					
					elParent.find('.words-alts-select').html(htmlOpt);
					
					elWrapper.find('label > .success-msg').show();
					elWrapper.find('label > .failed-msg').hide();
					
					setTimeout(function(){
						elWrapper.find('label > .success-msg').fadeOut('slow');
						$('#' + relId).val('');
					}, 1000);
					
					setTimeout(function(){
						elWrapper.find('label > .success-msg').hide();
						clicked.removeClass('loading');
						clicked.find('span').removeClass('save_loader');
					}, 2000);
				}
				
				if (response.result === 'failed') {
					elWrapper.find('label > .success-msg').hide();
					elWrapper.find('label > .failed-msg').show();
					setTimeout(function(){
						elWrapper.find('label > .failed-msg').fadeOut('slow');
					}, 1000);
					
					setTimeout(function(){
						elWrapper.find('label > .failed-msg').hide();
						clicked.removeClass('loading');
						clicked.find('span').removeClass('save_loader');
					}, 2000);
				}
			}
    	});
	});
	
	$('.words-alts-select').change(function(){ 
		if (confirm('Are you sure you want to remove this alternative?')) {
			var clicked = $(this);
			var value = clicked.val();
		    var indexes = value.split('-');
		    var parentIndex = indexes[0];
		    var childIndex = indexes[1];
		    var metaKey = $(this).attr('data-key');
		    
		    var postId   = $('#post_ID').val();
		    
		    var dataString = 'action=keyword_alt_delete'+'&post_id='+postId+'&word_element_index=' + parentIndex +'&word_element_alt_index=' + childIndex + '&meta_key=' + metaKey;

		    $.ajax({
			type:'POST',
			data:dataString,
			url : ajaxUrl,
			success:function(response) {
				var htmlOpt = '';
				
				$.each(response.alt_list, function( index, value ){
				
					htmlOpt = htmlOpt.concat('<option value=' + parentIndex + '-' + index + '>' + value + '</option>');
				});
				
				clicked.html(htmlOpt);
				
				console.log(response.message, response.alt_list, clicked);
			
			}
    	});
		}
	    
	});
	
});