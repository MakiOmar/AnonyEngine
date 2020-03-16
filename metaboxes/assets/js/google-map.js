var Gisborne;
function initialize(){
  if(document.getElementById("anony_map") !== null){
    var mapProp = {
                  center:Gisborne,
                  zoom:18,
                  scrollwheel: false,
                  mapTypeId:google.maps.MapTypeId.SATELLITE
            }; 

          
  var map = new google.maps.Map(document.getElementById("anony_map"),mapProp);
  
  var marker=new google.maps.Marker({
            position:Gisborne,
            icon: AnonyMB.MbUri+'imgs/map-marker.png'
      });
  marker.setMap(map);
  }
        
}

if (typeof google !== 'undefined') {
  Gisborne = new google.maps.LatLng(AnonyMB.geolat,AnonyMB.geolong);

  google.maps.event.addDomListener(window, 'load', initialize);

  jQuery(document).ready(function($){
    $('#anony_map').parent('fieldset').css('flex-direction', 'column');
  });
}

