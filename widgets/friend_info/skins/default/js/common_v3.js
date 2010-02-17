// from sample code of Google Maps API v3.
  var geocoder;
  var map;
  var infowindow = new google.maps.InfoWindow();
  var marker;

  function initialize() {
    geocoder = new google.maps.Geocoder();
    var latlng = new google.maps.LatLng(40.730885,-73.997383);
    var myOptions = {
      zoom: 8,
      center: latlng,
      mapTypeId: google.maps.MapTypeId.ROADMAP
    }
    map = new google.maps.Map(xGetElementById("map_canvas"), myOptions);
  }

  function codeLatLng() {
    var input = xGetElementById("latlng").value;
    var latlngStr = input.split(",",2);
    var lat = latlngStr[0];
    var lng = latlngStr[1];
    var latlng = new google.maps.LatLng(lat, lng);
    if (geocoder) {
      geocoder.geocode({'latLng': latlng}, function(results, status) {
        if (status == google.maps.GeocoderStatus.OK) {
          if (results[1]) {
            map.setZoom(11);
            marker = new google.maps.Marker({
                position: latlng, 
                map: map
            }); 
            infowindow.setContent(results[1].formatted_address);
            infowindow.open(map, marker);
          }
        } else {
          alert("Geocoder failed due to: " + status);
        }
      });
    }
  }
