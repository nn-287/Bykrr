<?php

namespace App\Http\Controllers\Api\V1;

use App\CentralLogics\Helpers;
use App\CentralLogics\LocationHelper;
use App\CentralLogics\Merchants;
use App\Model\Merchant;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use MatanYadaev\EloquentSpatial\Objects\Point;
use App\Model\Zone;
use Illuminate\Support\Facades\Validator;

class MerchantController extends Controller
{

    public function getNearbyMerchants(Request $request)
    {
        //  user's latitude and longitude from the request
        $userLatitude = $request->input('latitude');
        $userLongitude = $request->input('longitude');

    
        $radius = 10; // eg:: 10 km

    
        $zones = DB::table('zones')
        ->select('merchant_id', 'coordinates')
        ->get();

        $nearbyMerchantIds = [];

    
        if (!$zones) 
        {
            throw new \Exception('Data not found!');
        }

        foreach ($zones as $zone) {
            // Decode the binary data in zone's table <coordinates column .bin extension>
            list($decodedLatitude, $decodedLongitude) = $this->decodeCoordinates($zone->coordinates);
    
            // Calculate distance between the user's coordinates and the decoded coordinates from zones table
            $distance = $this->calculateDistance($userLatitude, $userLongitude, $decodedLatitude, $decodedLongitude);
    
            if ($distance <= $radius) 
            {
                $nearbyMerchantIds[] = $zone->merchant_id;
            }
        }
    
       
        $nearbyMerchants = DB::table('merchants')
            ->whereIn('id', $nearbyMerchantIds)
            ->distinct()
            ->get();
    
        if ($nearbyMerchants->isEmpty()) 
        {
            return response()->json([
                'message' => 'No nearby merchants found within the specified radius',
                'user_coordinates' => [
                    'latitude' => $userLatitude,
                    'longitude' => $userLongitude
                ]
            ], 404);
        }
    
        return response()->json([
            'nearby_merchants' => $nearbyMerchants,
            'user_coordinates' => [
                'latitude' => $userLatitude,
                'longitude' => $userLongitude
            ]
        ], 200);
    }



//Decoding coordinates column .bin extension in zones table related to merchant
private function decodeCoordinates($binaryData)
{
    $binaryString = trim(preg_replace('/\s+/', '', $binaryData)); 
    $coordinateSize = 4; // Size of each coordinate in bytes

    // Extract lat & long from string
    $latitude = unpack('f', substr($binaryString, 0, $coordinateSize))[1];
    $longitude = unpack('f', substr($binaryString, $coordinateSize, $coordinateSize))[1];

    return [$latitude, $longitude];
}



public function getNearbyServicesMerchants(Request $request)
{
    $validator = Validator::make($request->all(), [
        'service_id' => 'required',
        'latitude' => 'required',
        'longitude' => 'required',
    ]);

    if ($validator->fails()) {
        return response()->json(['errors' => Helpers::error_processor($validator)], 403);
    }
    $merchants = LocationHelper::nearby_merchants($request->latitude, $request->longitude, $request->service_id);

    return response()->json($merchants, 200);
}




//Ready made formula to calculate distance
private function calculateDistance($lat1, $lon1, $lat2, $lon2)
{
    $earthRadius = 6371; 
    $latDiff = deg2rad($lat2 - $lat1);
    $lonDiff = deg2rad($lon2 - $lon1);
    $a = sin($latDiff / 2) * sin($latDiff / 2) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($lonDiff / 2) * sin($lonDiff / 2);
    $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
    $distance = $earthRadius * $c; 

    return $distance;
}


   



    

   




















}

