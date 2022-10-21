<?php

namespace App\Http\Controllers;

use App\Models\Coordinates;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
// use Salman\GeoCode\Services\GeoCode;
// use GeoCode;
use Symfony\Component\CssSelector\Parser\Reader;

class MapController extends Controller
{

    public function index()
    {
        $map_cord = Coordinates::orderBy('id', 'DESC')->first();
        // dd($map_cord);
        if(!empty($map_cord)){
            $map = public_path('all_image/'.$map_cord->address);
            $address = $this->csvToArray($map);
            // return view('map',compact('address'));
            // dd($address);
            $lol = [];
            $result1 = [
                "status" => true,
                "lat" => $map_cord->lat,
                "long" => $map_cord->long,
                ];
                $result[] = $result1;
                foreach ($address as $value) {
                    // dd($value['address']);
                    $lol[] = $this->geoLocate($value['address']);
                    // dd($lol);
                    // $data_location = "https://maps.google.com/maps/api/geocode/json?key={{ env('GOOGLE_MAP_KEY') }}&address=".str_replace(" ", "+", $value['address'])."&sensor=false";
                }
                $lol = array_merge($lol, $result);

                $base_location = array(
                    'lat' => $map_cord->lat,
                    'lng' => $map_cord->long
                  );

                foreach ($lol as $key => $location)
                {
                    $a = $base_location['lat'] - $location['lat'];
                    $b = $base_location['lng'] - $location['long'];
                    $distance = sqrt(($a**2) + ($b**2));
                    $location['distance'] = $distance;
                    $lol[$key] = $location;
                }

                usort($lol, function($a, $b) {
                    return $a['distance'] <=> $b['distance'];
                });

               // $lol1  = $this->sortByNearestLatLong($lol,$map_cord->lat, $map_cord->long);
                // dd($lol1);
           // $lol = $lol1['latitudes'];
            // dd($lol);
            // $result1 = [
            // "status" => true,
            // "lat" => $map_cord->lat,
            // "long" => $map_cord->long,
            // ];
            // $result[] = $result1;
            // $lol = array_merge($lol, $result);
            // dd($arr1);
        //    echo "<pre>";print_r($lol); dd();
        //    echo "<pre>";print_r($lol1); dd();
            return view('map_new',compact('lol'));
        }else{
            $lol = null;
            return view('map_new',compact('lol'));
        }
    }

    function sortByNearestLatLong($geoData, $lat, $long, $returnNearestOnly=true){
        $arrCloseMatchLat   = array();
        $arrCloseMatchLong  = array();
        $matchedGeoSet      = array();

        // LOOP THROUGH ALL THE $geoData ARRAY AND SUBTRACT THE GIVEN LAT & LONG VALUES
        // FROM THOSE CONTAINED IN THE ORIGINAL ARRAY: $geoData
        // WE KNOW THAT THE SMALLER THE RESULT OF THE SUBTRACTION; THE CLOSER WE ARE
        // WE DO THIS FOR BOTH THE LONGITUDE & LATITUDE... HENCE OUR ARRAY:
        // $arrCloseMatchLat AND $arrCloseMatchLong RESPECTIVELY
        foreach($geoData as $iKey=>$arrGeoStrip){
            $arrCloseMatchLat[$iKey]    =  abs(floatval( ($arrGeoStrip['lat'])  - $lat  ));
            $arrCloseMatchLong[$iKey]   =  abs(floatval( ($arrGeoStrip['long']) - $long ));
        }


        // dd($arrCloseMatchLat);
    // WE SORT BOTH ARRAYS NUMERICALLY KEEPING THE KEYS WHICH WE NEED FOR OUR FINAL RESULT
        asort($arrCloseMatchLat, SORT_NUMERIC);
        asort($arrCloseMatchLong, SORT_NUMERIC);

        // WE CAN RETURN ONLY THE RESULT OF THE FIRST, CLOSEST MATCH
        // if($returnNearestOnly){
            // foreach($arrCloseMatchLat as $index=>$difference){
            //     $matchedGeoSet['latitudes'][]  = $geoData[$index];
            //     break;
            // }
            // foreach($arrCloseMatchLong as $index=>$difference){
            //     $matchedGeoSet['longitudes'][] = $geoData[$index];
            //     break;
            // }
        //     // OR WE CAN RETURN THE ENTIRE $geoData ARRAY ONLY SORTED IN A "CLOSEST FIRST" FASHION...
        //     // WE DO THIS FOR BOTH THE LONGITUDE & LATITUDE RESPECTIVELY SO WE END UP HAVING 2
        //     // ARRAYS: ONE THAT SORTS THE CLOSEST IN TERMS OF LONG VALUES
        //     // AN ONE THAT SORTS THE CLOSEST IN TERMS OF LAT VALUES...
        // }else{
            foreach($arrCloseMatchLat as $index=>$difference){
                $matchedGeoSet['latitudes'][]  = $geoData[$index];
            }
            foreach($arrCloseMatchLong as $index=>$difference){
                $matchedGeoSet['longitudes'][] = $geoData[$index];
            }
        // // }
        return $matchedGeoSet;
    }

    public function store(Request $request)
    {
        $request->validate([
            'file' => 'required',
        ]);
        // dd($request->all());
        $uploaded = '';
        if ($request->hasFile('file'))
        {

            $file = $request->file('file');

            $uploaded = time() . '.' . $file->getClientOriginalExtension();

            $destinationPath = public_path('all_image');

            $file->move($destinationPath, $uploaded);
        }

        $new = new Coordinates();
        $new->address = $uploaded;
        $new->lat = $request->lat;
        $new->long = $request->long;
        $new->save();
        // $map = Coordinates::first();
        // $map = public_path('all_image/'.$map->address);
        // $address = $this->csvToArray($map);
        return redirect()->back();
    }

    public function csvToArray($filename = '', $delimiter = ',')
    {
        if (!file_exists($filename) || !is_readable($filename))
            return false;

        $header = null;
        $data = array();
        if (($handle = fopen($filename, 'r')) !== false)
        {
            while (($row = fgetcsv($handle, 1000, $delimiter)) !== false)
            {
                if (!$header)
                $header = $row;
                else
                // dd($header,$row);
                    $data[] = array_combine($header, $row);
            }
            fclose($handle);
        }

        return $data;
    }

    public function geoLocate($address)
    {
        try {
            $lat = 0;
            $lng = 0;
            // dd(str_replace(" ", "+", $address));
            $data_location = "https://maps.google.com/maps/api/geocode/json?key=AIzaSyDK_8fPB07DLQhW7ttLAuOffsQ0DO3yqs8&address=".str_replace(" ", "+", $address)."&sensor=false";
            // echo $data_location;
            $data = file_get_contents($data_location);
            usleep(200000);
            // turn this on to see if we are being blocked
            // echo $data;
            $data = json_decode($data);
            // dd($data);
            if ($data->status=="OK") {
                // dd($data);
                $lat = $data->results[0]->geometry->location->lat;
                $lng = $data->results[0]->geometry->location->lng;

                // dd($lat);
                if($lat && $lng) {
                    return array(
                        'status' => true,
                        'lat' => $lat,
                        'long' => $lng,
                    );
                }
            }
            // if($data->status == 'OVER_QUERY_LIMIT') {
            //     return array(
            //         'status' => false,
            //         'message' => 'Google Amp API OVER_QUERY_LIMIT, Please update your google map api key or try tomorrow'
            //     );
            // }

        } catch (Exception $e) {

        }

        return array('lat' => null, 'long' => null, 'status' => false);
    }

}
