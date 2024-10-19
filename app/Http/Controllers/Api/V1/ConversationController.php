<?php

namespace App\Http\Controllers\Api\V1;

use App\CentralLogics\Helpers;
use App\CentralLogics\ProductLogic;
use App\Http\Controllers\Controller;
use App\Model\Conversation;
use App\Model\ConversationService;
use App\Model\Product;
use Carbon\Carbon;
use App\Model\BusinessSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\Facades\Image;
use App\User;

class ConversationController extends Controller
{
    public function messages_store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'message' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        if (!empty($request->file('image'))) {
            $image_name = Carbon::now()->toDateString() . "-" . uniqid() . "." . 'png';
            if (!Storage::disk('public')->exists('conversation')) {
                Storage::disk('public')->makeDirectory('conversation');
            }
            $note_img = Image::make($request->file('image'))->stream();
            Storage::disk('public')->put('conversation/' . $image_name, $note_img);
        } else {
            $image_name = null;
        }

        $conv = new Conversation;
        $conv->user_id = $request->user()->id;
        $conv->message = $request->message;
        $conv->image = $image_name;
        $conv->save();

        return response()->json(['message' => 'successfully sent!'], 200);
    }
    
     public function services_messages_store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'message' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        if (!empty($request->file('image'))) {
            $image_name = Carbon::now()->toDateString() . "-" . uniqid() . "." . 'png';
            if (!Storage::disk('public')->exists('conversation')) {
                Storage::disk('public')->makeDirectory('conversation');
            }
            $note_img = Image::make($request->file('image'))->stream();
            Storage::disk('public')->put('conversation/' . $image_name, $note_img);
        } else {
            $image_name = null;
        }
        
        
        $conv = new ConversationService;
        $conv->user_id = $request->user()->id;
        $conv->message = $request->message;
        if($request->service_id){
            $conv->service_id = $request->service_id;
        }
        $conv->image = $image_name;
        $conv->save();
        
        $points_value =  BusinessSetting::where(['key' => 'message_points_value'])->first()->value;
        $user = User::where('id', $request->user()->id)->first();
        $user->purchases_points = $user->purchases_points - $points_value;
        $user->save();
        
        return response()->json(['message' => 'successfully sent!'], 200);
    }


    public function chat_image(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'image' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        if (!empty($request->file('image'))) {
            $image_name = Carbon::now()->toDateString() . "-" . uniqid() . "." . 'png';
            if (!Storage::disk('public')->exists('conversation')) {
                Storage::disk('public')->makeDirectory('conversation');
            }
            $note_img = Image::make($request->file('image'))->stream();
            Storage::disk('public')->put('conversation/' . $image_name, $note_img);
        } else {
            $image_name = 'def.png';
        }

        $url = asset('storage/app/public/conversation') . '/' . $image_name;

        return response()->json(['image_url' => $url], 200);
    }


    public function messages(Request $request)
    {
        return response()->json([Conversation::where(['user_id' => $request->user()->id])->latest()->get()], 200);
    }
    
     public function services_messages(Request $request)
    {   
        if($request->service_id){
            $conversations = ConversationService::where(['user_id' => $request->user()->id])->where('service_id', $request->service_id)->latest()->take(25)->get();
        }else {
            $conversations = ConversationService::where(['user_id' => $request->user()->id])->latest()->take(25)->get();
        }
        
        foreach($conversations as $conversation){
            if($conversation->product_id != null){
                $product = Product::where('id', $conversation->product_id)->first();
                $product = Helpers::single_product_data_formatting($product);
                $overallRating = ProductLogic::get_overall_rating($product->reviews);
                $conversation['product'] = $product;
                $conversation['rating'] = $overallRating;
                
            }else {
                $conversation['product'] = null;
            }
        }
   
        ConversationService::where('user_id', $request->user()->id)->update(array(
                         'seen'=>1,
        )); 
        return response()->json([$conversations], 200);
    }
    
    public function services_messages_history(Request $request)
    {
        if($request->service_id){
            $conversations = ConversationService::where(['user_id' => $request->user()->id])->where('service_id', $request->service_id)->latest()->paginate('50', ['*'], 'page', $request['offset']);
        }else {
          $conversations = ConversationService::where(['user_id' => $request->user()->id])->latest()->paginate('50', ['*'], 'page', $request['offset']);  
        }
        
        foreach($conversations as $conversation){
            if($conversation->product_id != null){
                $product = Product::where('id', $conversation->product_id)->first();
                $product = Helpers::single_product_data_formatting($product);
                $overallRating = ProductLogic::get_overall_rating($product->reviews);
                $conversation['product'] = $product;
                $conversation['rating'] = $overallRating;
                
            }else {
                $conversation['product'] = null;
            }
        }
        $conversations = [
            'total_size' => $conversations->total(),
            'limit' => 50,
            'offset' => $request['offset'],
            'conversations' => $conversations->items()
        ];

        return $conversations;
    }
  
  
     public function send_image(Request $request)
    {
      
        $image = $request->file('image');
        $image_id = $request->image_id;

        if ($request->hasFile('image')) {
            // return response()->json(['message' => 'image'], 200);
            $data = getimagesize($image);
            $imageName = Carbon::now()->toDateString() . "-" . uniqid() . "." . 'png';
            if (!Storage::disk('public')->exists('conversation')) {
                Storage::disk('public')->makeDirectory('conversation');
            }
            $img = Image::make($image)->fit($data[0], $data[1])->stream();
            Storage::disk('public')->put('conversation/' . $imageName, $img);
        } else {
            return response()->json($request, 200);
            $imageName = 'No image';
        }

        if($image_id == '0'){
        $conversation = New Conversation();
        $conversation->user_id = $request->user()->id;
        $conversation->message = !empty($request->message) ? $request->message : '';
        $conversation->image = $imageName;
        $conversation->save();  
        } else if($image_id == '1'){
            
        $conversation = new ConversationService;
        $conversation->user_id = $request->user()->id;
        $conversation->message = !empty($request->message) ? $request->message : '';
        $conversation->image = $imageName;
        if($request->service_id){
            if($request->service_id!=0){
                $conversation->service_id = $request->service_id;
            }
        
        }
        $conversation->save();   
        }
            
        $points_value =  BusinessSetting::where(['key' => 'message_points_value'])->first()->value;
        $user = User::where('id', $request->user()->id)->first();
        $user->purchases_points = $user->purchases_points - $points_value;
        $user->save();
 
        return response()->json(['message' => 'Message sent'], 200);
    }
}

