jQuery(document).ready(function($){
	'use strict';

	//Apply mutation observer on a querySelector and apply a callback function
	$.fn.AnonyObserve = function(querySelector, callback){

		var selectedObserve = new MutationObserver(function(mutations){

	        mutations.forEach(function(mutation) {

	          if(mutation.addedNodes.length){

	          	if (typeof callback === "function")  callback();

	          }
	    
	        });  
	    });

		if($(querySelector).length !== 0){

			const subSelected = document.querySelector(querySelector);

	        selectedObserve.observe(subSelected,{

	            childList: true
	            
	        });
		}
    	
	};

	$.fn.AnonyDateTimePicker = function(fieldId, getWhat, DateTimeOptions){
	    $(fieldId)[getWhat](DateTimeOptions);
	};
});

