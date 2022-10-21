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
    <?php //echo'<pre>';print_r($lol);die; ?>
    {{-- @dd($lol); --}}
    {{-- @foreach ($lol as $l)
        @dd($l['lat'])
    @endforeach --}}
    <form action="{{route('store')}}" method="post" enctype="multipart/form-data">
        @csrf
    <input type="file" name="file" id="file">
    <input type="hidden" id="lat" name="lat">
    <input type="hidden" id="long" name="long">
    {{-- onchange="handle_files(this.files)" --}}
    <button type="submit">Submit</button>
</form>
    <div id="googleMap"></div>
    <script>
        var coordinates = [];
        var map;
        var google = unsafeWindow.google;

        function getCoor() {
            return coordinates;
        }

        function initialize() {

            var mapProp = {

                center: new google.maps.LatLng(20.5937,78.9629),
                zoom: 6,
                mapTypeId: google.maps.MapTypeId.ROADMAP
            };

            map = new google.maps.Map(document.getElementById("googleMap"), mapProp);

            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(function (position) {

                    var currentLatitude = position.coords.latitude;
                    var currentLongitude = position.coords.longitude;
                    $('#lat').val(currentLatitude);
                    $('#long').val(currentLongitude);
                    // initialLocation = new google.maps.LatLng(position.coords.latitude, position.coords.longitude);
                    // console.log(currentLatitude);
                    // map.setCenter(initialLocation);
                });
            }


            var point = <?php echo json_encode($lol); ?>;

            var count = 0;

            for (var dua = 0; dua < point.length; dua++) {

                    var latitude = point[dua]['lat'];
                    var longitude = point[dua]['long'];
                    coordinates[count] = new google.maps.LatLng(latitude, longitude);
                    count++;

                    mark = new google.maps.LatLng(latitude, longitude);
                    var marker = new google.maps.Marker({
                        position: mark,
                        map: map
                    });
                    marker.setMap(map);
                }
                // console.log(coordinates[0]);
            var myTrip = [];
            var bounds = new google.maps.LatLngBounds();
            for (var c = 0; c < coordinates.length; c++) {
                myTrip.push(coordinates[c]);
                bounds.extend(coordinates[c]);
            }
            map.fitBounds(bounds);

            // console.log(myTrip);

            var flightPath = new google.maps.Polyline({
                path: myTrip,
                strokeColor: "#0000FF",
            });

            flightPath.setMap(map);
        }

        google.maps.event.addDomListener(window, 'load', initialize);
    </script>
<script src="https://maps.googleapis.com/maps/api/js?sensor=false&region=BR&callback=initialize"></script>
<script src="https://code.jquery.com/jquery-3.6.1.min.js" integrity="sha256-o88AwQnZB+VDvE9tvIXrMQaPlFFSUTR+nldQm1LuPXQ=" crossorigin="anonymous"></script>

</body>

</html>
