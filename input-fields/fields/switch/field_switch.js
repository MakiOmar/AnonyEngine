/* ============================================================
 * bootstrapSwitch v1.3 by Larentis Mattia @spiritualGuru
 * http://www.larentis.eu/switch/
 * ============================================================
 * Licensed under the Apache License, Version 2.0
 * http://www.apache.org/licenses/LICENSE-2.0
 * ============================================================ */
!function(e){"use strict";e.fn["bootstrapSwitch"]=function(t){var n={init:function(){return this.each(function(){var t=e(this),n,r,i,s,o="",u=t.attr("class"),a,f,l="ON",c="OFF",h=false;e.each(["switch-mini","switch-small","switch-large"],function(e,t){if(u.indexOf(t)>=0)o=t});t.addClass("has-switch");if(t.data("on")!==undefined)a="switch-"+t.data("on");if(t.data("on-label")!==undefined)l=t.data("on-label");if(t.data("off-label")!==undefined)c=t.data("off-label");if(t.data("icon")!==undefined)h=t.data("icon");r=e("<span>").addClass("switch-left").addClass(o).addClass(a).html(l);a="";if(t.data("off")!==undefined)a="switch-"+t.data("off");i=e("<span>").addClass("switch-right").addClass(o).addClass(a).html(c);s=e("<label>").html(" ").addClass(o).attr("for",t.find("input").attr("id"));if(h){s.html('<i class="'+h+'"></i>')}n=t.find(":checkbox").wrap(e("<div>")).parent().data("animated",false);if(t.data("animated")!==false)n.addClass("switch-animate").data("animated",true);n.append(r).append(s).append(i);t.find(">div").addClass(t.find("input").is(":checked")?"switch-on":"switch-off");if(t.find("input").is(":disabled"))e(this).addClass("deactivate");var p=function(e){e.siblings("label").trigger("mousedown").trigger("mouseup").trigger("click")};t.on("keydown",function(t){if(t.keyCode===32){t.stopImmediatePropagation();t.preventDefault();p(e(t.target).find("span:first"))}});r.on("click",function(t){p(e(this))});i.on("click",function(t){p(e(this))});t.find("input").on("change",function(t){var n=e(this),r=n.parent(),i=n.is(":checked"),s=r.is(".switch-off");t.preventDefault();r.css("left","");if(s===i){if(i)r.removeClass("switch-off").addClass("switch-on");else r.removeClass("switch-on").addClass("switch-off");if(r.data("animated")!==false)r.addClass("switch-animate");r.parent().trigger("switch-change",{el:n,value:i})}});t.find("label").on("mousedown touchstart",function(t){var n=e(this);f=false;t.preventDefault();t.stopImmediatePropagation();n.closest("div").removeClass("switch-animate");if(n.closest(".has-switch").is(".deactivate"))n.unbind("click");else{n.on("mousemove touchmove",function(t){var n=e(this).closest(".switch"),r=(t.pageX||t.originalEvent.targetTouches[0].pageX)-n.offset().left,i=r/n.width()*100,s=25,o=75;f=true;if(i<s)i=s;else if(i>o)i=o;n.find(">div").css("left",i-o+"%")});n.on("click touchend",function(t){var n=e(this),r=e(t.target),i=r.siblings("input");t.stopImmediatePropagation();t.preventDefault();n.unbind("mouseleave");if(f)i.prop("checked",!(parseInt(n.parent().css("left"))<-25));else i.prop("checked",!i.is(":checked"));f=false;i.trigger("change")});n.on("mouseleave",function(t){var n=e(this),r=n.siblings("input");t.preventDefault();t.stopImmediatePropagation();n.unbind("mouseleave");n.trigger("mouseup");r.prop("checked",!(parseInt(n.parent().css("left"))<-25)).trigger("change")});n.on("mouseup",function(t){t.stopImmediatePropagation();t.preventDefault();e(this).unbind("mousemove")})}})})},toggleActivation:function(){e(this).toggleClass("deactivate")},is_active:function(){return!e(this).hasClass("deactivate")},setActive:function(t){if(t)e(this).removeClass("deactivate");else e(this).addClass("deactivate")},toggleState:function(t){var n=e(this).find("input:checkbox");n.prop("checked",!n.is(":checked")).trigger("change",t)},setState:function(t,n){e(this).find("input:checkbox").prop("checked",t).trigger("change",n)},status:function(){return e(this).find("input:checkbox").is(":checked")},destroy:function(){var t=e(this).find("div"),n;t.find(":not(input:checkbox)").remove();n=t.children();n.unwrap().unwrap();n.unbind("change");return n}};if(n[t])return n[t].apply(this,Array.prototype.slice.call(arguments,1));else if(typeof t==="object"||!t)return n.init.apply(this,arguments);else e.error("Method "+t+" does not exist!")}}(jQuery);
jQuery(document).ready(function($) {
	// Function to apply the code to the switch elements
	function applySwitch() {
		
		/*if ( jQuery("[data-toggle='switch']").parent('.switch-animate').length === 0 ) {
			jQuery("[data-toggle='switch']").wrap('<div class="switch" />').parent().bootstrapSwitch();
		}*/

		jQuery("[data-toggle='switch']").each( function() {
			var currentSwitch = $( this );
			if ( currentSwitch.parent('.switch-animate').length === 0 ) {
				currentSwitch.wrap('<div class="switch" />').parent().bootstrapSwitch();
			}
		} );
	}

	// Apply the code initially
	applySwitch();
	$.fn.AnonyObserve('.anony-multi-values-wrapper', applySwitch);
});