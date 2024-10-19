<?php

namespace App\CentralLogics;


use App\Model\BusinessSetting;
use App\Model\Currency;
use App\Model\DMReview;
use App\Model\Order;
use App\Model\Review;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use App\Model\CustomerAddress;
use App\Model\BranchProductInfo;
use App\Model\Zone;
use MatanYadaev\EloquentSpatial\Objects\Point;

class Helpers
{
    public static function error_processor($validator)
    {
        $err_keeper = [];
        foreach ($validator->errors()->getMessages() as $index => $error) {
            array_push($err_keeper, ['code' => $index, 'message' => $error[0]]);
        }
        return $err_keeper;
    }

    public static function combinations($arrays)
    {
        $result = [[]];
        foreach ($arrays as $property => $property_values) {
            $tmp = [];
            foreach ($result as $result_item) {
                foreach ($property_values as $property_value) {
                    $tmp[] = array_merge($result_item, [$property => $property_value]);
                }
            }
            $result = $tmp;
        }
        return $result;
    }

    public static function format_coordiantes($coordinates)
    {
        $data = [];
        foreach ($coordinates as $coord) {
            $data[] = (object)['lat' => $coord[1], 'lng' => $coord[0]];
        }
        return $data;
    }
    
    // public static function variation_price($product, $variation)
    // {
    //     $match = json_decode($variation, true)[0];
    //     $result = 0;
    //     foreach (json_decode($product['variations'], true) as $property => $value) {
    //         if ($value['type'] == $match['type']) {
    //             $result = $value['price'];
    //         }
    //     }
    //     return $result;
    // }

    public static function variation_price($product, $variation)
    {
        return $product;
        $match = json_decode($variation, true);
        $result = $product->price;
        foreach (json_decode($product['variations'], true) as $property => $value) {
            if ($value['type'] == $match['type']) {
                $result = $value['price'];
            }
        }
        return $result;
    }
    
    public static function product_data_formatting($products, $multi_data = false,$tags = [])
    {
       
        $storage = [];
        if ($multi_data == true) {
            
            foreach ($products as $item) {
                //var_dump($item['category_ids']);
                //var_dump(json_decode($item['category_ids']));
                $variations = [];
             
                if(is_array($item['category_ids'])){
                    $item['category_ids'] = $item['category_ids'];
                    
                }else{
                    $item['category_ids'] = json_decode($item['category_ids']);
                }
                if(is_array($item['attributes'])){
                    $item['attributes'] = $item['attributes'];
                }else{
                    $item['attributes'] = json_decode($item['attributes']);
                }
                if(is_array($item['choice_options'])){
                    $item['choice_options'] = $item['choice_options'];
                }else{
                    $item['choice_options'] = json_decode($item['choice_options']);
                }
                
                if(is_array($item['variations'])){
                    $item['variations'] = $item['variations'];
                }else{
                    $item['variations'] = json_decode($item['variations']);
                }
                  
                
                foreach ($item['variations'] as $var) {
                    $price =  (is_array($var))? $var["price"] : $var->price; 
                    $discount =  $item['discount'];
                    $type = (is_array($var))?  $var["type"] : $var->type;
                   // return $branch_product_info_variation;
                    
                    //var_dump($var);
                    //var_dump($var->type);
           
                    
                    if(isset($var->image)){
                        $image =  (is_array($var))? $var["image"] : $var->image;
                    }else {
                        $image = null;
                    }
                    
                    array_push($variations, [
                        'type' => $type,// $var['type'],
                        'price' => (double)$price,
                        'discount' => (double)$discount,
                        'image' => $image,
                    ]);
                }
                
                $item['variations'] = $variations;
                array_push($storage, $item);
            }
            $products = $storage;
        } else {
            $variations = [];
            $products['category_ids'] = json_decode($products['category_ids']);
            $products['attributes'] = json_decode($products['attributes']);
            $products['choice_options'] = json_decode($products['choice_options']);
           
            foreach (json_decode($products['variations'], true) as $var) {
                if(isset($var['image'])){
                    $var_image = $var['image'];
                }else {
                    $var_image = '';
                }
                 
                array_push($variations, [
                    'type' => $var['type'],
                    'price' => (double)$var['price'],
                    'image' => $var_image
                ]);
            }
            $products['variations'] = $variations;
           
        }

        return $products;
    }
    
    public static function single_product_data_formatting($item)
    {
                $variations = [];
                if(is_array($item['category_ids'])){
                    $item['category_ids'] = $item['category_ids'];
                }else{
                    $item['category_ids'] = json_decode($item['category_ids']);
                }
                if(is_array($item['attributes'])){
                    $item['attributes'] = $item['attributes'];
                }else{
                    $item['attributes'] = json_decode($item['attributes']);
                }
                if(is_array($item['choice_options'])){
                    $item['choice_options'] = $item['choice_options'];
                }else{
                    $item['choice_options'] = json_decode($item['choice_options']);
                }
                
                if(is_array($item['variations'])){
                    $item['variations'] = $item['variations'];
                }else{
                    $item['variations'] = json_decode($item['variations']);
                }
                
                
                foreach ($item['variations'] as $var) {
                    //var_dump($var);
                    //var_dump($var->type);
           
                    $type =  (is_array($var))?  $var["type"] : $var->type ;
                    $price =  (is_array($var))? $var["price"] : $var->price ;
                    if(isset($var->image)){
                        $image =  (is_array($var))? $var["image"] : $var->image;
                    }else {
                        $image = null;
                    }
                    
                    array_push($variations, [
                        'type' => $type,// $var['type'],
                        'price' => (double)$price,
                        'image' => $image,
                    ]);
                }
                
                $item['variations'] = $variations;

        return $item;
    }
    
     public static function product_data_formatting_fav($data, $multi_data = false,$tags = [])
    {

        $storage = [];
        $target_prd_ids = [];
        if ($multi_data == true) {
            foreach ($data as $item) {
                //var_dump($item['category_ids']);
                //var_dump(json_decode($item['category_ids']));
                $variations = [];
                if(is_array($item['category_ids'])){
                    $item['category_ids'] = $item['category_ids'];
                }else{
                    $item['category_ids'] = json_decode($item['category_ids']);
                }
                if(is_array($item['attributes'])){
                    $item['attributes'] = $item['attributes'];
                }else{
                    $item['attributes'] = json_decode($item['attributes']);
                }
                if(is_array($item['choice_options'])){
                    $item['choice_options'] = $item['choice_options'];
                }else{
                    $item['choice_options'] = json_decode($item['choice_options']);
                }
                
                if(is_array($item['variations'])){
                    $item['variations'] = $item['variations'];
                }else{
                    $item['variations'] = json_decode($item['variations']);
                }
                
                
                foreach ($item['variations'] as $var) {
                    //var_dump($var);
                    //var_dump($var->type);
           
                    $type =  (is_array($var))?  $var["type"] : $var->type ;
                    $price =  (is_array($var))? $var["price"] : $var->price ;
                    if(isset($var->image)){
                        $image =  (is_array($var))? $var["image"] : $var->image;
                    }else {
                        $image = null;
                    }
                    
                    array_push($variations, [
                        'type' => $type,// $var['type'],
                        'price' => (double)$price,
                        'image' => $image,
                    ]);
                }
                
                if(count($tags) > 0) {
                    $matchedTags = [];
                    $prTags = explode(',', $item['tags']);
                    
                    foreach ($prTags as $tag) {
                        if (in_array(trim($tag), $tags)) {
                            // return $tag;
                            array_push($target_prd_ids, $item['id']);
                            // $matchedTags[] = trim($tag);
                        }
                    }
                    $item['matchedTag'] = implode(',', $matchedTags);
                }
                $item['variations'] = $variations;
                array_push($storage, $item);
            }
        
            $data = $storage;
        } else {
            $variations = [];
            $data['category_ids'] = json_decode($data['category_ids']);
            $data['attributes'] = json_decode($data['attributes']);
            $data['choice_options'] = json_decode($data['choice_options']);
           
            foreach (json_decode($data['variations'], true) as $var) {
                if(isset($var['image'])){
                    $var_image = $var['image'];
                }else {
                    $var_image = '';
                }
                 
                array_push($variations, [
                    'type' => $var['type'],
                    'price' => (double)$var['price'],
                    'image' => $var_image
                ]);
            }
            $data['variations'] = $variations;
            if(count($tags) > 0) {
                $matchedTags = [];
                $prTags = explode(',', $data['tags']);
                foreach ($prTags as $tag) {
                    if(!empty(trim($tag))){
                        if (in_array(trim($tag), $tags)) {
                            $matchedTags[] = trim($tag);
                        }
                    }
                }
                $data['matchedTag'] = implode(',', $matchedTags);
            }
        }

        return $data;
    }
    
   

    public static function order_data_formatting($data, $multi_data = false)
    {
        $storage = [];
        if ($multi_data == true) {
            foreach ($data as $item) {
                $item['add_on_ids'] = json_decode($item['add_on_ids']);
                array_push($storage, $item);
            }
            $data = $storage;
        } else {
            $data['add_on_ids'] = json_decode($data['add_on_ids']);
        }

        return $data;
    }

    public static function get_business_settings($name)
    {
        $config = null;
        foreach (BusinessSetting::all() as $setting) {
            if ($setting['key'] == $name) {
                $config = json_decode($setting['value'], true);
            }
        }
        return $config;
    }

    public static function currency_code()
    {
        $currency_code = BusinessSetting::where(['key' => 'currency'])->first()->value;
        return $currency_code;
    }

    public static function currency_symbol()
    {
        $currency_symbol = Currency::where(['currency_code' => Helpers::currency_code()])->first()->currency_symbol;
        return $currency_symbol;
    }

    public static function send_push_notif_to_device($fcm_token, $data)
    {
        /*https://fcm.googleapis.com/v1/projects/myproject-b5ae1/messages:send*/
        $key = BusinessSetting::where(['key' => 'push_notification_key'])->first()->value;
        /*$project_id = BusinessSetting::where(['key' => 'fcm_project_id'])->first()->value;*/

        $url = "https://fcm.googleapis.com/fcm/send";
        $header = array("authorization: key=" . $key . "",
            "content-type: application/json"
        );

        $postdata = '{
            "to" : "' . $fcm_token . '",
            "data" : {
                "title":"' . $data['title'] . '",
                "body" : "' . $data['description'] . '",
                "image" : "' . $data['image'] . '",
                "order_id":"' . $data['order_id'] . '",
                "type": "0",
                "is_read": 0
              }
        }';
        

        $ch = curl_init();
        $timeout = 120;
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);

        // Get URL content
        $result = curl_exec($ch);
        // close handle to release resources
        curl_close($ch);

        return $result;
    }
    
    public static function send_notif_to_device_new($fcm_token, $data)
    {   
      
        $notification = [
            'title' =>$data['title'],
            'body' => $data['description'],
            'image' => "https://bykr.com/",
            'icon' => "https://bykr.com/",
            'sound' => 'mySound'
        ];
       
        $extraNotificationData = ["message" => $notification,"moredata" =>'dd'];
         

        $fcmNotification = [
            'to'=> $fcm_token,
           // 'registration_ids' => ["cfZ_NIbbTluaNpZB-xE3YI:APA91bF1pOGXeTbHkwh_-fJ3cw9vwwauaiqXcUc-GrxtIqHy4aI51btZW3HN0b3gNVaryD0J9QbrKF9QxH9YVS-zN0OFfG6LiPFIaX2yKBexQwsurRbZhFyBgkz0SNjcSGiTmoDAqtwo"], //multiple token array
            'notification' => $notification,
            'data' => $extraNotificationData
        ];
        
        $fcmUrl = "https://fcm.googleapis.com/fcm/send";
         
        $server_key = BusinessSetting::where(['key' => 'push_notification_key'])->first()->value;
            $apiKey="key=$server_key"; 
            

        $http=Http::withHeaders([
            'Authorization'=>$apiKey,
            'Content-Type'=>'application/json'
        ])  ->post($fcmUrl,$fcmNotification);
       return 'success';
    }
    
     public static function new_chat_message($data, $user_fcm)
    {
        /*https://fcm.googleapis.com/v1/projects/myproject-b5ae1/messages:send*/
        $key = BusinessSetting::where(['key' => 'push_notification_key'])->first()->value;
        /*$project_id = BusinessSetting::where(['key' => 'fcm_project_id'])->first()->value;*/

        $url = "https://fcm.googleapis.com/fcm/send";
        $header = array("authorization: key=" . $key . "",
            "content-type: application/json"
        );

        $screenChat = "chat";
        $postdata = '{
            "to" : "' . $user_fcm . '",
            "data" : {
                "title":"' . $data['title'] . '",
                "body" : "' . $data['description'] . '",
                "image" : "' . $data['image'] . '",
                "order_id":"""' . $data['order_id'] . '""",
                  "screen": "' . $screenChat . '",
                "type": "0",
                "is_read": 0
              }
        }';

        $ch = curl_init();
        $timeout = 120;
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);

        // Get URL content
        $result = curl_exec($ch);
        // close handle to release resources
        curl_close($ch);

        return $result;
    }
    
   


public static function send_notif_to_filtered_topic($data, $topic, $search)
    {
        /*https://fcm.googleapis.com/v1/projects/myproject-b5ae1/messages:send*/
        $key = BusinessSetting::where(['key' => 'push_notification_key'])->first()->value;
        /*$topic = BusinessSetting::where(['key' => 'fcm_topic'])->first()->value;*/
        /*$project_id = BusinessSetting::where(['key' => 'fcm_project_id'])->first()->value;*/

        $url = "https://fcm.googleapis.com/fcm/send";
        $header = array("authorization: key=" . $key . "",
            "content-type: application/json"
        );
        $screen = "screenPage";
        $postdata = '{
            "to" : "/topics/' . $topic . '",
            "data" : {
                "title":"' . $data->title . '",
                "body" : "' . $data->description . '",
                "image" : "' . $data->image . '",
                "screen": "' . $search . '",
                "order_id": "' . $search . '",
                "type": "1",
                "is_read": 0
              }
        }';

        $ch = curl_init();
        $timeout = 120;
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);

        // Get URL content
        $result = curl_exec($ch);
        // close handle to release resources
        curl_close($ch);
        return $result;
       // dd($topic);
    }
    

    public static function send_push_notif_to_topic($data)
    {
        /*https://fcm.googleapis.com/v1/projects/myproject-b5ae1/messages:send*/
        $key = BusinessSetting::where(['key' => 'push_notification_key'])->first()->value;
        /*$topic = BusinessSetting::where(['key' => 'fcm_topic'])->first()->value;*/
        /*$project_id = BusinessSetting::where(['key' => 'fcm_project_id'])->first()->value;*/

        $url = "https://fcm.googleapis.com/fcm/send";
        $header = array("authorization: key=" . $key . "",
            "content-type: application/json"
        );
        $postdata = '{
            "to" : "/topics/notify",
            "data" : {
                "title":"' . $data->title . '",
                "body" : "' . $data->description . '",
                "image" : "' . $data->image . '",
                "type": "0",
                "is_read": 0
              }
        }';

        $ch = curl_init();
        $timeout = 120;
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);

        // Get URL content
        $result = curl_exec($ch);
        // close handle to release resources
        curl_close($ch);

        return $result;
    }

    public static function rating_count($product_id, $rating)
    {
        return Review::where(['product_id' => $product_id, 'rating' => $rating])->count();
    }

    public static function dm_rating_count($deliveryman_id, $rating)
    {
        return DMReview::where(['delivery_man_id' => $deliveryman_id, 'rating' => $rating])->count();
    }

    public static function tax_calculate($product, $price)
    {
        if ($product['tax_type'] == 'percent') {
            $price_tax = ($price / 100) * $product['tax'];
        } else {
            $price_tax = $product['tax'];
        }
        return $price_tax;
    }

    public static function discount_calculate($product, $price)
    {
        if ($product['discount_type'] == 'percent') {
            $price_discount = ($price / 100) * $product['discount'];
        } else {
            $price_discount = $product['discount'];
        }
        return $price_discount;
    }

    public static function max_earning()
    {
        $data = Order::where(['order_status' => 'delivered'])->select('id', 'created_at', 'order_amount')
            ->get()
            ->groupBy(function ($date) {
                return Carbon::parse($date->created_at)->format('m');
            });

        $max = 0;
        foreach ($data as $month) {
            $count = 0;
            foreach ($month as $order) {
                $count += $order['order_amount'];
            }
            if ($count > $max) {
                $max = $count;
            }
        }
        return $max;
    }

    public static function max_orders()
    {
        $data = Order::select('id', 'created_at')
            ->get()
            ->groupBy(function ($date) {
                return Carbon::parse($date->created_at)->format('m');
            });

        $max = 0;
        foreach ($data as $month) {
            $count = 0;
            foreach ($month as $order) {
                $count += 1;
            }
            if ($count > $max) {
                $max = $count;
            }
        }
        return $max;
    }

    public static function order_status_update_message($status)
    {
        if ($status == 'pending') {
            $data = BusinessSetting::where('key', 'order_pending_message')->first()->value;
        } elseif ($status == 'confirmed') {
            $data = BusinessSetting::where('key', 'order_confirmation_msg')->first()->value;
        } elseif ($status == 'processing') {
            $data = BusinessSetting::where('key', 'order_processing_message')->first()->value;
        } elseif ($status == 'out_for_delivery') {
            $data = BusinessSetting::where('key', 'out_for_delivery_message')->first()->value;
        } elseif ($status == 'delivered') {
            $data = BusinessSetting::where('key', 'order_delivered_message')->first()->value;
        } elseif ($status == 'delivery_boy_delivered') {
            $data = BusinessSetting::where('key', 'delivery_boy_delivered_message')->first()->value;
        } elseif ($status == 'del_assign') {
            $data = BusinessSetting::where('key', 'delivery_boy_assign_message')->first()->value;
        } elseif ($status == 'ord_start') {
            $data = BusinessSetting::where('key', 'delivery_boy_start_message')->first()->value;
        } else {
            $data = '{"status":"0","message":""}';
        }

        $res = json_decode($data, true);

        if ($res['status'] == 0) {
            return 0;
        }
        return $res['message'];
    }

    public static function day_part()
    {
        $part = "";
        $morning_start = date("h:i:s", strtotime("5:00:00"));
        $afternoon_start = date("h:i:s", strtotime("12:01:00"));
        $evening_start = date("h:i:s", strtotime("17:01:00"));
        $evening_end = date("h:i:s", strtotime("21:00:00"));

        if (time() >= $morning_start && time() < $afternoon_start) {
            $part = "morning";
        } elseif (time() >= $afternoon_start && time() < $evening_start) {
            $part = "afternoon";
        } elseif (time() >= $evening_start && time() <= $evening_end) {
            $part = "evening";
        } else {
            $part = "night";
        }

        return $part;
    }

    public static function env_update($key,$value){
        $path = base_path('.env');
        if (file_exists($path)) {
            file_put_contents($path, str_replace(
                $key.'='.env($key), $key.'='.$value, file_get_contents($path)
            ));
        }
    }

    public static function env_key_replace($key_from,$key_to,$value){
        $path = base_path('.env');
        if (file_exists($path)) {
            file_put_contents($path, str_replace(
                $key_from.'='.env($key_from), $key_to.'='.$value, file_get_contents($path)
            ));
        }
    }

    public static  function remove_dir($dir) {
        if (is_dir($dir)) {
            $objects = scandir($dir);
            foreach ($objects as $object) {
                if ($object != "." && $object != "..") {
                    if (filetype($dir."/".$object) == "dir") Helpers::remove_dir($dir."/".$object); else unlink($dir."/".$object);
                }
            }
            reset($objects);
            rmdir($dir);
        }
    }
    
     public static  function nearby_branch_id($user_id) {
         
            $user_address = CustomerAddress::where(['user_id'=> $user_id, 'is_current'=>1])->first();
            if($user_address){
            $latitude = $user_address->latitude;
            $longitude = $user_address->longitude;
            
            if($latitude != null){
               $nearby_branches= DB::table('sub_branches')->where('has_products_list', 1)
             -> select(["*",DB::raw("ACOS( SIN( RADIANS( `latitude` ) ) * SIN( RADIANS($latitude) ) + COS( RADIANS( `latitude` ) )* COS( RADIANS($latitude)) * COS( RADIANS( `longitude` ) - RADIANS($longitude))) * 6380 AS distance")])
             ->havingRaw("distance<=80")
             ->orderBy('distance', 'asc')
             ->get(); 
            }else {
                $nearby_branches= DB::table('sub_branches')->where('id', 3)->get(); // el zomor our standard branch
            }
             
            if(count($nearby_branches) > 0){
                $branch_id = $nearby_branches->first()->branch_id;
            }else {
                $branch_id = 3; 
             }
            } else {
               $branch_id = 3; 
            }
             
             return $branch_id;
    }


    public static function getZone($address)
    {
        
        $POINT_SRID = 0; // For MariaDB use 4326
        $point = new Point($address->latitude, $address->longitude, $POINT_SRID);
     
        $zone = Zone::active()->whereContains('coordinates', $point)->first();
        return $zone;
    }

}
