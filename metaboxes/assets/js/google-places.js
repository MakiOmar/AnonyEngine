var searchPlaces = 'anony__geolocation';
var Gisborne;

jQuery( document ).ready(
	function($){

		var autocomplete;

		autocomplete = new google.maps.places.Autocomplete(
			(document.getElementById( searchPlaces )),
			{
				types: ['geocode'],
			}
		);

		google.maps.event.addListener(
			autocomplete,
			'place_changed',
			function () {
				var near_place                                       = autocomplete.getPlace();
				document.getElementById( 'anony__entry_lat' ).value  = near_place.geometry.location.lat();
				document.getElementById( 'anony__entry_long' ).value = near_place.geometry.location.lng();
				Gisborne = new google.maps.LatLng( near_place.geometry.location.lat(),near_place.geometry.location.lng() );

				initialize();
			}
		);

	}
);

jQuery( document ).on(
	'change',
	'#' + searchPlaces,
	function () {
		document.getElementById( 'anony__entry_lat' ).value  = '';
		document.getElementById( 'anony__entry_long' ).value = '';
	}
);
