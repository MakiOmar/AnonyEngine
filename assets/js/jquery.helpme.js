jQuery(document).ready(function($){
	'use strict';
	$.fn.AnonyCreateCookie = function(name,value,minutes) {
        if (minutes) {
            var date = new Date();
            date.setTime(date.getTime()+(minutes*60*1000));
            var expires = "; expires="+date.toGMTString();
        } else {
            var expires = "";
        }
        document.cookie = name+"="+value+expires+"; path=/";
    }
    $.fn.AnonyReadCookie = function(name) {
        var nameEQ = name + "=";
        var ca = document.cookie.split(';');
        for(var i=0;i < ca.length;i++) {
            var c = ca[i];
            while (c.charAt(0)==' ') c = c.substring(1,c.length);
           if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
    
        }
        return null;
    }
    $.fn.AnonyEraseCookie = function(name) {
	    createCookie(name,"",-1);
	}​
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

	};
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
		
	};
	
	/**
	 * Check if element is empty
	 *
	 * @param object el  Element object
	 * @return bool
	 */
	$.fn.isEmpty = function( el ){
		
      return !$.trim(el.html());
		
	};
	
	/**
	 * Find largest integer in an array
	 *
	 * @param array arr
	 * @return int
	 */
	$.fn.findLargest = function( arr ){
		
		var largest= 0;

		for (i=0; i<=largest;i++){
			if (arr[i]>largest) {
				var largest=arr[i];
			}
		}
		
		return largest;
		
	};
	
	/**
	 * Converts an array to json object
	 *
	 * @param array arr
	 * @return object
	 */
	$.fn.arrayToJson = function( arr ){
		
		var arrayToString = JSON.stringify(Object.assign({}, arr));
		var stringToJsonObject = JSON.parse(arrayToString);  // convert string to json object
		
		return stringToJsonObject;
	};
});
