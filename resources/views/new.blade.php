$nearestLongLat     = sortByNearestLatLong($arrGeoData, 50.6000, -74.15000);
    var_dump(nearestLongLat);

    function sortByNearestLatLong($geoData, $lat, $long, $returnNearestOnly=true){
        // CREATE AN ARRAY FOR USE INSIDE THE FUNCTION
        $arrCloseMatchLat   = array();
        $arrCloseMatchLong  = array();
        $matchedGeoSet      = array();

        // LOOP THROUGH ALL THE $geoData ARRAY AND SUBTRACT THE GIVEN LAT & LONG VALUES
        // FROM THOSE CONTAINED IN THE ORIGINAL ARRAY: $geoData
        // WE KNOW THAT THE SMALLER THE RESULT OF THE SUBTRACTION; THE CLOSER WE ARE
        // WE DO THIS FOR BOTH THE LONGITUDE & LATITUDE... HENCE OUR ARRAY:
        // $arrCloseMatchLat AND $arrCloseMatchLong RESPECTIVELY
        foreach($geoData as $iKey=>$arrGeoStrip){
            $arrCloseMatchLat[$iKey]    =  abs(floatval( ($arrGeoStrip['latitude'])  - $lat  ));
            $arrCloseMatchLong[$iKey]   =  abs(floatval( ($arrGeoStrip['longitude']) - $long ));
        }


    // WE SORT BOTH ARRAYS NUMERICALLY KEEPING THE KEYS WHICH WE NEED FOR OUR FINAL RESULT
        asort($arrCloseMatchLat, SORT_NUMERIC);
        asort($arrCloseMatchLong, SORT_NUMERIC);

        // WE CAN RETURN ONLY THE RESULT OF THE FIRST, CLOSEST MATCH
        if($returnNearestOnly){
            foreach($arrCloseMatchLat as $index=>$difference){
                $matchedGeoSet['latitudes'][]  = $geoData[$index];
                break;
            }
            foreach($arrCloseMatchLong as $index=>$difference){
                $matchedGeoSet['longitudes'][] = $geoData[$index];
                break;
            }
            // OR WE CAN RETURN THE ENTIRE $geoData ARRAY ONLY SORTED IN A "CLOSEST FIRST" FASHION...
            // WE DO THIS FOR BOTH THE LONGITUDE & LATITUDE RESPECTIVELY SO WE END UP HAVING 2
            // ARRAYS: ONE THAT SORTS THE CLOSEST IN TERMS OF LONG VALUES
            // AN ONE THAT SORTS THE CLOSEST IN TERMS OF LAT VALUES...
        }else{
            foreach($arrCloseMatchLat as $index=>$difference){
                $matchedGeoSet['latitudes'][]  = $geoData[$index];
            }
            foreach($arrCloseMatchLong as $index=>$difference){
                $matchedGeoSet['longitudes'][] = $geoData[$index];
            }
        }
        return $matchedGeoSet;
    }
    <script>
        // <?php foreach($lol as $l){ ?>
            // dd($l['lat']);

        function initMap() {

        //   const myLatLng = { lat: 22.2734719, lng: 70.7512559 };
        //   const myLatLng = { lat: <?php echo $l['lat'] ?>, lng: <?php echo $l['long'] ?> };
        var myLatLng = [<?php foreach($lol as $house) {
        echo '['.$house['lat']. ',' .$house['long'].'],';
        } ?>];

          const map = new google.maps.Map(document.getElementById("googleMap"), {
            zoom: 5,
            center: myLatLng,
          });


          if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(function (position) {
                    initialLocation = new google.maps.LatLng(position.coords.latitude, position.coords.longitude);
                    map.setCenter(initialLocation);
                });
            }
            for (i = 0; i < myLatLng.length; i++) {
             new google.maps.Marker({
                position: new google.maps.LatLng(locations[i][1], locations[i][2]),
                map: map
            });

            markers.push(marker);
            }
        //   new google.maps.Marker({

        //     position: myLatLng,

        //     map,

        //     title: "Hello Rajkot!",

        //   });

        }

        // <?php }?>
        window.initMap = initMap;

    </script>
