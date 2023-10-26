jQuery(document).ready(function($){
    $('body').on('click', '.fetch-location-button', function(e){
        e.preventDefault();

        var clicked = $(this);
        var targetID = clicked.data('target');
        var responseTargetID =  targetID + '-response';
        console.log();
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(function(position) {
              var geocoder = new google.maps.Geocoder();
              var latLng = new google.maps.LatLng(position.coords.latitude, position.coords.longitude);
      
              geocoder.geocode({ 'latLng': latLng, language: "en" }, function(results, status) {
                if (status === google.maps.GeocoderStatus.OK) {
                  if (results[0]) {
                    $('#' + targetID).val(results[0].formatted_address);
                    $('#' + responseTargetID).text('');
                    
                  } else {
                    $('#' + responseTargetID).text('No results found');
                  }
                } else {
                    $('#' + responseTargetID).text('Geocoder failed due to: ' + status);
                }
              });
            }, function() {
                $('#' + responseTargetID).text('Unable to fetch location');
            });
          } else {
            $('#' + responseTargetID).text('Geolocation is not supported by this browser');
          }
    });
});