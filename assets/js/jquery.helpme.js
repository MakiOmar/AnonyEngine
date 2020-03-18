jQuery(document).ready(function($){
	'use strict';
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
});