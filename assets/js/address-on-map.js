
function mapDisplay(address, targetId){
    // Create a geocoder object to convert the address into coordinates
    var geocoder = new google.maps.Geocoder();
    // Convert the address into coordinates
    geocoder.geocode({ 'address': address }, function (results, status) {
        if (status === google.maps.GeocoderStatus.OK) {

            var target = document.getElementById(targetId);
            if (target && !target.classList.contains('anony-address-on-map')) {
                target.classList.add('anony-address-on-map');
            }
            // Get the coordinates of the first result
            var location = results[0].geometry.location;
            if( target instanceof HTMLElement) { 
                // Create a map object centered at the location
                var map = new google.maps.Map( target , {
                    center: location,
                    zoom: 12 // Adjust the zoom level as needed
                });
                // Add a marker to the map
                var marker = new google.maps.Marker({
                    position: location,
                    map: map,
                    title: address // Use the address as the marker title
                });
            }
        } else {
            alert("Geocode was not successful for the following reason: " + status);
        }
    });
}
function hasElementWithId(id) {
    var element = document.getElementById(id);
    return element !== null;
}
function initMap(){
    
    if( mapsDetails !== undefined ){
        mapsDetails.forEach(function (map) {
            //if( hasElementWithId( target_id ) ){
                mapDisplay(map.address, map.target_id);
            //}
            
        });
    }
}