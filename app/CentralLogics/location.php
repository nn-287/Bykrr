<?php

namespace App\CentralLogics;

use App\Model\DeliveryMan;
use App\Model\Merchant;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\DB;

class LocationHelper
{
    public static function nearby_merchants($user_lat, $user_lng, $service_id)
    {
        ///////////////////// 1- FIRST I WILL GET ALL ROUTES DISTANCE AND DURATION //////////////////////////
        $client = new Client();

        // Replace 'YOUR_API_KEY' with your actual Google Maps API key
        $apiKey = config('app.google_map_api_key');

        // Origin and destination coordinates
        $origin = "$user_lat,$user_lng";
        $merchantsCollection = collect([]);
        
        $max_distance = 1000;
        
        $merchants = Merchant::where(['status'=>'active'])
                ->select([
                    "*",
                    DB::raw("ACOS( SIN( RADIANS( `latitude` ) ) * SIN( RADIANS($user_lat) ) + COS( RADIANS( `latitude` ) )* COS( RADIANS($user_lat)) * COS( RADIANS( `longitude` ) - RADIANS($user_lng))) * 6380 AS distance")
                ])
                ->havingRaw("distance <= ?", [$max_distance])
                ->whereHas('services', function($query) {
                    $query->where('service_id', 1);
                })
                ->get();
                
        
       foreach($merchants as $merchant){
            // Make an API request to get directions
        $destination = "$merchant->latitude,$merchant->longitude";
         
        $response = $client->get("https://maps.googleapis.com/maps/api/directions/json", [
            'query' => [
                'origin' => $origin,
                'destination' => $destination,
                'key' => $apiKey,
            ]
        ]);

        // Parse the response as JSON
        $data = json_decode($response->getBody(), true);

        // Extract the distance and duration from the response
        $distance = $data['routes'][0]['legs'][0]['distance']['text'];
        $duration = $data['routes'][0]['legs'][0]['duration']['text'];
        
        $parts = explode(" ", $distance);
        
        if (is_numeric($parts[0])) {
       // Check if it's an integer or a double
            if (strpos($parts[0], '.') !== false) {
                // It's a double
                if($parts[1] == 'm'){
                  $distance = (double)$parts[0] * 1000;  
                }else {
                   $distance = (double)$parts[0];   
                }
                
            } else {
                 // It's an integer
                if($parts[1] == 'm'){
                  $distance = (int)$parts[0] * 1000;  
                }else {
                   $distance = (int)$parts[0];   
                }
            }
        } else {
            // Handle the case when the first part is not numeric
            $distance = null; // Or handle it as needed
        }
       
        $merchant['distance'] = $distance;
        $merchant['duration'] = $duration;
        
        $merchantsCollection->push($merchant);
        }
         
        ///////////////////// NOW I ASSIGNED DISTANCE AND DURATION TO EACH ROUTE -- OSAMA  //////////////////////////
        
        
        //////////////////////// 2- SORT THE ROUTES BASED ON DSITANCE IN ASCENDING ORDER  //////////////////////////////
        $sortedDrivers = $merchantsCollection->sortBy('distance');
        $sorted_values = $sortedDrivers->values()->all();
        //////////////////////// SORTED //////////////////////////////
        
        return $sorted_values;
    }


    public static function nearby_drivers($user_lat, $user_lng, $service_id)
    {
        ///////////////////// 1- FIRST I WILL GET ALL ROUTES DISTANCE AND DURATION //////////////////////////
        $client = new Client();

        // Replace 'YOUR_API_KEY' with your actual Google Maps API key
        $apiKey = config('app.google_map_api_key');

        // Origin and destination coordinates
        $origin = "$user_lat,$user_lng";
        $merchantsCollection = collect([]);
        
        $max_distance = 1000;
        
        $drivers = DeliveryMan::select([
                    "*",
                    DB::raw("ACOS( SIN( RADIANS( `latitude` ) ) * SIN( RADIANS($user_lat) ) + COS( RADIANS( `latitude` ) )* COS( RADIANS($user_lat)) * COS( RADIANS( `longitude` ) - RADIANS($user_lng))) * 6380 AS distance")
                ])
                ->havingRaw("distance <= ?", [$max_distance])
                ->get();
                
        
       foreach($drivers as $driver){
            // Make an API request to get directions
        $destination = "$driver->latitude,$driver->longitude";
         
        $response = $client->get("https://maps.googleapis.com/maps/api/directions/json", [
            'query' => [
                'origin' => $origin,
                'destination' => $destination,
                'key' => $apiKey,
            ]
        ]);

        // Parse the response as JSON
        $data = json_decode($response->getBody(), true);

        // Extract the distance and duration from the response
        $distance = $data['routes'][0]['legs'][0]['distance']['text'];
        $duration = $data['routes'][0]['legs'][0]['duration']['text'];
        
        $parts = explode(" ", $distance);
        
        if (is_numeric($parts[0])) {
       // Check if it's an integer or a double
            if (strpos($parts[0], '.') !== false) {
                // It's a double
                if($parts[1] == 'm'){
                  $distance = (double)$parts[0] * 1000;  
                }else {
                   $distance = (double)$parts[0];   
                }
                
            } else {
                 // It's an integer
                if($parts[1] == 'm'){
                  $distance = (int)$parts[0] * 1000;  
                }else {
                   $distance = (int)$parts[0];   
                }
            }
        } else {
            // Handle the case when the first part is not numeric
            $distance = null; // Or handle it as needed
        }
       
        $driver['distance'] = $distance;
        $driver['duration'] = $duration;
        
        $merchantsCollection->push($driver);
        }
         
        ///////////////////// NOW I ASSIGNED DISTANCE AND DURATION TO EACH ROUTE -- OSAMA  //////////////////////////
        
        
        //////////////////////// 2- SORT THE ROUTES BASED ON DSITANCE IN ASCENDING ORDER  //////////////////////////////
        $sortedDrivers = $merchantsCollection->sortBy('distance');
        $sorted_values = $sortedDrivers->values()->all();
        //////////////////////// SORTED //////////////////////////////
        
        return $sorted_values;
    }
}