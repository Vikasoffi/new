<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<style>
    html,
    body,
    #googleMap {
        height: 100%;
        width: 100%;
        margin: 0px;
        padding: 0px
    }
</style>

<body>
    <input type="file" name="file" id="file" onchange="handle_files(this.files)" />
    <div id="googleMap"></div>
    <script>
        var coordinates = [];
        var map;
        var google = unsafeWindow.google;

        function getCoor() {
            return coordinates;
        }

        function handle_files(files) {
            for (i = 0; i < files.length; i++) {
                file = files[i];
                console.log(file);
                var reader = new FileReader();
                ret = [];
                reader.onload = function(e) {
                    console.log(e.target.result);
                    var lines = e.target.result.split('\n');

                    for (var line = 0; line < lines.length; line++) {

                        var point = lines[line].split(',');

                        var count = 0;

                        for (var dua = 0; dua < point.length; dua++) {
                            if (dua % 2 === 0) {
                                var latitude = point[dua];
                            } else {
                                var longitude = point[dua];
                                coordinates[count] = new google.maps.LatLng(latitude, longitude);
                                console.log(coordinates[count]);
                                count++;
                            }
                        }
                        var myTrip = [];
                        var bounds = new google.maps.LatLngBounds();
                        for (var c = 0; c < coordinates.length; c++) {
                            myTrip.push(coordinates[c]);
                            bounds.extend(coordinates[c]);
                        }
                        map.fitBounds(bounds);

                        //console.log(myTrip);
                        var flightPath = new google.maps.Polyline({
                            path: myTrip,
                            strokeColor: "#0000FF",
                            strokeOpacity: 0.8,
                            strokeWeight: 8
                        });

                        flightPath.setMap(map);
                        // var marker = new google.maps.Marker({position: bounds});

                        // marker.setMap(map);

                    }

                }
                reader.onerror = function(stuff) {
                    console.log("error", stuff);
                    console.log(stuff.getMessage());
                }
                reader.readAsText(file); //readAsdataURL
            }

        }

        function initialize() {

            var mapProp = {

                center: new google.maps.LatLng(8.611911, 41.146056),
                zoom: 6,
                mapTypeId: google.maps.MapTypeId.ROADMAP
            };

            map = new google.maps.Map(document.getElementById("googleMap"), mapProp);
        }

        google.maps.event.addDomListener(window, 'load', initialize);
    </script>
    {{-- <script>
        var geocoder;
        var map;
        function initialize() {
          geocoder = new google.maps.Geocoder();
           var latlng = new google.maps.LatLng(50.804400, -1.147250);
          var mapOptions = {
           zoom: 6,
           center: latlng
          }
           map = new google.maps.Map(document.getElementById('googleMap'), mapOptions);
          }

         function codeAddress(address,tutorname,url,distance,prise,postcode) {
         var address = address;

          geocoder.geocode( { 'address': address}, function(results, status) {
           if (status == google.maps.GeocoderStatus.OK) {
            map.setCenter(results[0].geometry.location);
             var marker = new google.maps.Marker({
            map: map,
            position: results[0].geometry.location
        });

        var infowindow = new google.maps.InfoWindow({
           content: 'Tutor Name: '+tutorname+'<br>Price Guide: '+prise+'<br>Distance: '+distance+' Miles from you('+postcode+')<br> <a href="'+url+'" target="blank">View Tutor profile</a> '
         });
          infowindow.open(map,marker);

            } /*else {
            alert('Geocode was not successful for the following reason: ' + status);
          }*/
         });
       }


        google.maps.event.addDomListener(window, 'load', initialize);

       window.onload = function(){
        initialize();
        // your code here
        <?php //foreach($addr as $add) { ?>
        codeAddress('<?php // echo $add['address']; ?>');
        <?php //} ?>
      };
        </script> --}}
    {{-- <script src="https://maps.google.com/maps/api/js?sensor=false"></script> --}}
    <script src="https://maps.googleapis.com/maps/api/js?sensor=false&region=BR&callback=initialize"></script>
</body>

</html>
