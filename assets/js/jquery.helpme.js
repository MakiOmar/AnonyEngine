jQuery( document ).ready(
	function ($) {
		'use strict';
		// Please make sure to add body{-webkit-print-color-adjust:exact;color-adjust:exact}
		// Page should have this: <iframe src="about:blank" name="print_frame" width="0" height="0" frameborder="0"></iframe>
		$.fn.printDiv = function (divId, css) {
			var printDivCSS = "<style>@media print{body{-webkit-print-color-adjust:exact;color-adjust:exact}" + css.replace( /\s/g, "" ) + "}</style>";

			window.frames["print_frame"].document.body.innerHTML =
			printDivCSS + document.getElementById( divId ).innerHTML;
			window.frames["print_frame"].window.focus();
			window.frames["print_frame"].window.print();
		}

		/**
		 * PHP json encoded sample:
		 * $_json_ = json_encode([
	
							'customer_name' => $customer_name,
							'visit_type' => $service,
							'appointment_date' => $date,
							'appointment_time' => $time,
							'join_url' => $join_url,
							'join_pass' => $join_pass,
							'is_mobile' => wp_is_mobile(),
    
						]);
		*/

		/**
		 * @var string jsonString JSON string
		 * @return string
		 */
		$.fn.WhatsapShareLink = function (jsonString ) {

			var _json_ = JSON.parse( jsonString );

			var sub_domain = 'api';

			if (_json_.is_mobile === false) {

				sub_domain = 'web';
			}

			var whatsAppText = 'Zoom URL : ' + _json_.join_url + "\r\n\r\n"
				+ 'Zoom password : ' + _json_.join_pass + "\r\n\r\n"
				+ 'Customer name : ' + _json_.customer_name + "\r\n\r\n"
				+ 'Visit type : ' + _json_.visit_type + "\r\n\r\n"
				+ 'Date : ' + _json_.appointment_date + "\r\n\r\n"
				+ 'Time : ' + _json_.appointment_time;

			var _text_ = window.encodeURIComponent( whatsAppText );

			return 'https://' + sub_domain + '.whatsapp.com/send?text=' + _text_;

		}

		$.fn.AnonyCreateCookie = function (name,value,minutes) {
			if (minutes) {
				var date = new Date();
				date.setTime( date.getTime() + (minutes * 60 * 1000) );
				var expires = "; expires=" + date.toGMTString();
			} else {
				var expires = "";
			}
			document.cookie = name + "=" + value + expires + "; path=/";
		}
		$.fn.AnonyReadCookie   = function (name) {
			var nameEQ = name + "=";
			var ca     = document.cookie.split( ';' );
			for (var i = 0;i < ca.length;i++) {
				var c = ca[i];
				while (c.charAt( 0 ) == ' ') {
					c = c.substring( 1,c.length );
				}
				if (c.indexOf( nameEQ ) == 0) {
					return c.substring( nameEQ.length,c.length );
				}

			}
			return null;
		}
		$.fn.AnonyEraseCookie  = function (name) {
			createCookie( name,"",-1 );
		}
		// Apply mutation observer on a querySelector and apply a callback function
		$.fn.AnonyObserve = function (querySelector, callback) {

			var selectedObserve = new MutationObserver(
				function (mutations) {

					mutations.forEach(
						function (mutation) {

							if (mutation.addedNodes.length) {
								/*
								* You should define a function that will accept a querySelector to manipulate.
								* This functions's name should replace the callback paramenter of $.fn.AnonyObserve. Note that the name should be passed without quotes.
								*/
								if (typeof callback === "function") {
									callback( querySelector );
								}

							}

						}
					);
				}
			);

			if ($( querySelector ).length !== 0) {

				const subSelected = document.querySelector( querySelector );

				selectedObserve.observe(
					subSelected,
					{

						childList: true

					}
				);
			}

		};

		$.fn.AnonyDateTimePicker = function (fieldId, getWhat, DateTimeOptions) {
			$( fieldId )[getWhat]( DateTimeOptions );
		};

		/**
		 * This is then function used to detect if the element is scrolled into view
		 *
		 * @param object elem Jquery object of an element
		 */
		$.fn.elementScrolled = function (elem) {

			var docViewTop = $( window ).scrollTop();

			var docViewBottom = docViewTop + $( window ).height();

			var elemTop = elem.offset().top;

			return ((elemTop <= docViewBottom) && (elemTop >= docViewTop));

		};
		/**
		 * Slide element to top if been into view.
		 *
		 * @param string     element Element selector
		 * @param slideClass Reposition class
		 */
		$.fn.slideToTop = function (element, slideClass) {
			$( element ).each(
				function () {

					if ($.fn.elementScrolled( $( this ) )) {

						if ( ! $( this ).hasClass( slideClass )) {

							$( this ).addClass( slideClass );

						}
					}
				}
			);

		};

		/**
		 * Check if element is empty
		 *
		 * @param object el  Element object
		 * @return bool
		 */
		$.fn.isEmpty = function ( el ) {

			return ! $.trim( el.html() );

		};

		/**
		 * Find largest integer in an array
		 *
		 * @param array arr
		 * @return int
		 */
		$.fn.findLargest = function ( arr ) {

			var largest = 0;

			for (i = 0; i <= largest;i++) {
				if (arr[i] > largest) {
					var largest = arr[i];
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
		$.fn.arrayToJson = function ( arr ) {

			var arrayToString      = JSON.stringify( Object.assign( {}, arr ) );
			var stringToJsonObject = JSON.parse( arrayToString );  // convert string to json object

			return stringToJsonObject;
		};

		/**
		 * Get Query variable value from a URL by it's name.
		 *
		 * @param string name query variable name.
		 * @param string url Queried URL.
		 * @return string
		 */
		$.fn.getParameterByName = function (name, url = window.location.href) {
			name      = name.replace( /[\[\]]/g, '\\$&' );
			var regex = new RegExp( '[?&]' + name + '(=([^&#]*)|&|#|$)' ),
			results   = regex.exec( url );
			if ( ! results) {
				return null;
			}
			if ( ! results[2]) {
				return '';
			}
			return decodeURIComponent( results[2].replace( /\+/g, ' ' ) );
		}

		/**
		 * Matches simple emoji | emoji with modifiers (skin tones) | country flags | region flags | emoji presentation sequences.
		 *
		 * @param string sentence Sentence to be searched.
		 * @return array
		 */
		$.fn.matchEmoji = function ( sentence ) {
			const regexpUnicodeModified = /\p{RI}\p{RI}|\p{Emoji}(\p{EMod}+|\u{FE0F}\u{20E3}?|[\u{E0020}-\u{E007E}]+\u{E007F})?(\u{200D}\p{Emoji}(\p{EMod}+|\u{FE0F}\u{20E3}?|[\u{E0020}-\u{E007E}]+\u{E007F})?)+|\p{EPres}(\p{EMod}+|\u{FE0F}\u{20E3}?|[\u{E0020}-\u{E007E}]+\u{E007F})?|\p{Emoji}(\p{EMod}+|\u{FE0F}\u{20E3}?|[\u{E0020}-\u{E007E}]+\u{E007F})/gu
			return sentence.match( regexpUnicodeModified );
		}

		$.fn.dateTimeInputNextDay = function (element) {
			// Using Date.parse to covert datetime string to timestamp.
			var selectedDate = Date.parse( element.val() );
			var currentDate  = new Date( selectedDate + 24 * 60 * 60 * 1000 );
			var day          = currentDate.getDate(),
			month            = currentDate.getMonth() + 1,
			year             = currentDate.getFullYear(),
			hour             = currentDate.getHours(),
			min              = currentDate.getMinutes();

			month = (month < 10 ? "0" : "") + month;
			day   = (day < 10 ? "0" : "") + day;
			hour  = (hour < 10 ? "0" : "") + hour;
			min   = (min < 10 ? "0" : "") + min;

			var today   = year + "-" + month + "-" + day,
			displayTime = hour + ":" + min;

			return today + 'T' + displayTime;
		}

		$.fn.jsonObjectHighlight = function (json) {
			if (typeof json != 'string') {
				json = JSON.stringify( json, undefined, 2 );
			}
			json = json.replace( /&/g, '&amp;' ).replace( /</g, '&lt;' ).replace( />/g, '&gt;' );
			return json.replace(
				/("(\\u[a-zA-Z0-9]{4}|\\[^u]|[^\\"])*"(\s*:)?|\b(true|false|null)\b|-?\d+(?:\.\d*)?(?:[eE][+\-]?\d+)?)/g,
				function (match) {
					var cls = 'number';
					if (/^"/.test( match )) {
						if (/:$/.test( match )) {
							cls = 'key';
						} else {
							cls = 'string';
						}
					} else if (/true|false/.test( match )) {
						cls = 'boolean';
					} else if (/null/.test( match )) {
						cls = 'null';
					}
					return '<span class="' + cls + '">' + match + '</span>';
				}
			);
		}
	}
);
