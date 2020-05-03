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
	
	
	/**
	 * This is then function used to detect if the element is scrolled into view
	 *
	 * @param object elem Jquery object of an element
	 */
	$.fn.elementScrolled = function(elem){

		var docViewTop = $(window).scrollTop();

		var docViewBottom = docViewTop + $(window).height();

		var elemTop = elem.offset().top;

		return ((elemTop <= docViewBottom) && (elemTop >= docViewTop));

	}
	/**
	 * Slide element to top if been into view.
	 *
	 * @param string     element Element selector
	 * @param slideClass Reposition class
	 */
	$.fn.slideToTop = function (element, slideClass){
		$(element).each(function(){

			if($.fn.elementScrolled($(this))) {

			  if(!$(this).hasClass(slideClass)){

				  $(this).addClass(slideClass);

			  }
			}
		});
	}
});

