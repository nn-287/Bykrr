<?php

namespace App\Http\Controllers\Api\V1;
use App\CentralLogics\Helpers;
use App\Http\Controllers\Controller;
use App\Model\CustomerAddress;
use App\Model\Order;
use App\Model\Product;
use App\Model\OrderDetail;
use App\Model\SpinWheel;
use App\Model\Gift;
use App\Model\SearchedItem;
use App\Model\AppReview;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\Facades\Image;

class CustomerController extends Controller
{

    public function address_list(Request $request)
    {
        return response()->json(CustomerAddress::where('user_id', $request->user()->id)->latest()->get(), 200);
    }

    public function add_new_address(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'contact_person_name' => 'required',
            'address_type' => 'required',
            'contact_person_number' => 'required',
            'address' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        $address = [
            'user_id' => $request->user()->id,
            'contact_person_name' => $request->contact_person_name,
            'contact_person_number' => $request->contact_person_number,
            'address_type' => $request->address_type,
            'address' => $request->address,
            'longitude' => $request->longitude,
            'latitude' => $request->latitude,
            'created_at' => now(),
            'updated_at' => now()
        ];
        DB::table('customer_addresses')->insert($address);
        return response()->json(['message' => 'successfully added!'], 200);
    }
    
     public function update_address(Request $request,$id)
    {
        $validator = Validator::make($request->all(), [
            'contact_person_name' => 'required',
            'address_type' => 'required',
            'contact_person_number' => 'required',
            'address' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        $address = [
            'user_id' => $request->user()->id,
            'contact_person_name' => $request->contact_person_name,
            'contact_person_number' => $request->contact_person_number,
            'address_type' => $request->address_type,
            'address' => $request->address,
            'longitude' => $request->longitude,
            'latitude' => $request->latitude,
            'created_at' => now(),
            'updated_at' => now()
        ];
        DB::table('customer_addresses')->where('id',$id)->update($address);
        return response()->json(['message' => 'successfully updated!'], 200);
    }

    public function update_answer(Request $request)
    {
        $user = $request->user();
        if($request->answer_hair){
            $user->answer_hair = '';
            foreach($request->answer_hair as $key => $item){
                if($key == 0){
                    $user->answer_hair = $item["answer"];
                }else{
                    $user->answer_hair =  $user->answer_hair . ',' .$item["answer"];
                }
                
            }
            $user->answer_hair_object =  json_encode($request->answer_hair );
        }
        //var_dump($user->answer_hair_object);
        //var_dump($user->answer_hair);exit;
        if($request->answer_skin){
            $user->answer_skin = '';
            foreach($request->answer_skin as $key => $item){
                if($key == 0){
                    $user->answer_skin = $item["answer"];
                }else{
                    $user->answer_skin =  $user->answer_skin . ',' .$item["answer"];
                }
                
            }
            $user->answer_skin_object =  json_encode($request->answer_skin );
        }
        if($request->answer_nutrition){
            $user->answer_nutrition = '';
            foreach($request->answer_nutrition as $key => $item){
                if($key == 0){
                    $user->answer_nutrition = $item["answer"];
                }else{
                    $user->answer_nutrition =  $user->answer_nutrition . ',' .$item["answer"];
                }
                
            }
            $user->answer_nutrition_object =  json_encode($request->answer_nutrition );
        }
        $user->save();
        return response()->json(['message' => 'successfully updated!'], 200);
    }

    public function answer_old(Request $request)
    {

        $user = $request->user();
        $user->jsonAnser = $request->data;
        $answer_tag = [];
        foreach( $request->data as $items){
            //var_dump($items);
            foreach( $items["data"] as $item){
                $answer_tag[] =  $item["answer"];
                
            }
        }
        $user->answer_tag = $answer_tag;
        var_dump($answer_tag);exit;
        var_dump($request->data);exit;
        
        $validator = Validator::make($request->all(), [
            'contact_person_name' => 'required',
            'address_type' => 'required',
            'contact_person_number' => 'required',
            'address' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        $address = [
            'user_id' => $request->user()->id,
            'contact_person_name' => $request->contact_person_name,
            'contact_person_number' => $request->contact_person_number,
            'address_type' => $request->address_type,
            'address' => $request->address,
            'longitude' => $request->longitude,
            'latitude' => $request->latitude,
            'created_at' => now(),
            'updated_at' => now()
        ];
        DB::table('customer_addresses')->where('id',$id)->update($address);
        return response()->json(['message' => 'successfully updated!'], 200);
    }

    public function delete_address(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'address_id' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        if (DB::table('customer_addresses')->where(['id' => $request['address_id'], 'user_id' => $request->user()->id])->first()) {
            DB::table('customer_addresses')->where(['id' => $request['address_id'], 'user_id' => $request->user()->id])->delete();
            return response()->json(['message' => 'successfully removed!'], 200);
        }
        return response()->json(['message' => 'No such data found!'], 404);
    }

    public function get_order_list(Request $request)
    {
        $orders = Order::where(['user_id' => $request->user()->id])->get();
        return response()->json($orders, 200);
    }

    public function get_order_details(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'order_id' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        $details = OrderDetail::where(['order_id' => $request['order_id']])->get();
        foreach ($details as $det) {
            $det['product_details'] = Helpers::product_data_formatting(json_decode($det['product_details'], true));
        }

        return response()->json($details, 200);
    }

    public function info(Request $request)
    {
        $user = User::with('addresses')->where('id', $request->user()->id)->first();
        return response()->json($user, 200);
    }

    public function update_profile(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'f_name' => 'required',
            'l_name' => 'required',
          //  'phone' => 'required',
        ], [
            'f_name.required' => 'First name is required!',
            'l_name.required' => 'Last name is required!',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        $image = $request->file('image');

        if ($image != null) {
            $data = getimagesize($image);
            $imageName = Carbon::now()->toDateString() . "-" . uniqid() . "." . 'png';
            if (!Storage::disk('public')->exists('profile')) {
                Storage::disk('public')->makeDirectory('profile');
            }
            $note_img = Image::make($image)->fit($data[0], $data[1])->stream();
            Storage::disk('public')->put('profile/' . $imageName, $note_img);
        } else {
            $imageName = $request->user()->image;
        }

        if ($request['password'] != null && strlen($request['password']) > 5) {
            $pass = bcrypt($request['password']);
        } else {
            $pass = $request->user()->password;
        }

        $userDetails = [
            'f_name' => $request->f_name,
            'l_name' => $request->l_name,
           // 'phone' => $request->phone,
            'image' => $imageName,
            'password' => $pass,
            'updated_at' => now()
        ];

        User::where(['id' => $request->user()->id])->update($userDetails);

        return response()->json(['message' => 'successfully updated!'], 200);
    }
    
    public function update_gift(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'gift_info' => 'required',
           
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        $userDetails = [
            'gift_info' => $request->gift_info,
     
        ];

        User::where(['id' => $request->user()->id])->update($userDetails);

        return response()->json(['message' => 'successfully updated!'], 200);
    }
    
    public function update_name_age(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'f_name' => 'required',
            'l_name' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        $userDetails = [
            'f_name' => $request->f_name,
            'l_name' => $request->l_name,
            'age' => $request->age,
        ];

        User::where(['id' => $request->user()->id])->update($userDetails);

        return response()->json(['message' => 'successfully updated!'], 200);
    }
    
    
    public function update_version(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'update_version' => 'required',
           
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }



        $userDetails = [
            'update_version' => $request->update_version,
     
        ];

        User::where(['id' => $request->user()->id])->update($userDetails);

        return response()->json(['message' => 'successfully updated!'], 200);
    }

    public function update_cm_firebase_token(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'cm_firebase_token' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        DB::table('users')->where('id',$request->user()->id)->update([
            'cm_firebase_token'=>$request['cm_firebase_token']
        ]);

        return response()->json(['message' => 'successfully updated!'], 200);
    }
    
    public function wheel_info(Request $request)
    {
        $wheel = SpinWheel::where('status', '1')->first();
     
       return response()->json($wheel, 200);
    }
    

    
    public function gifts(Request $request)
    {
        $wheel = SpinWheel::where('status', '1')->first();
        $gifts = Gift::where('wheel_id',$wheel->id)->get();
        $data['gifts']=$gifts;
     
       return response()->json($data, 200);
        
    }
    
     public function product_gifts(Request $request)
    {
        $wheel = SpinWheel::where('status', '1')->first();
        $gifts = Gift::where('wheel_id',$wheel->id)->get();
       
       
       $data=[];
        
            $stack = array("orange", "banana");
        foreach($gifts as $gift){
            if($gift->type = 'product_gift'){
                $product = Product::where('id',$gift->product_id)->first();
              
             array_push($stack, $product);
                
            }
        }
        
            $data['gifts']=$gifts;
            $data['products']=$stack;
            
      // return response()->json($data, 200);
       return response()->json(['data'=>$data,'status'=>200], 200);
    }
    
     public function user_gifts(Request $request)
    {
       $limit = $request->user()->wheel_limit;
       
        $giftList = json_decode($request->user()->gift_info, true);
       // $giftList = $request->user()->gift_info;
   
        $giftList =  $giftList['gifts'] ?? [];
        
   
         $currentDate = date('Y-m-d');
           $giftList2 = array();
        
      foreach ($giftList as $gift) {
          $time = strtotime($gift['expire_date']);
                 $newformat = date('Y-m-d',$time);
                  if (( $newformat > $currentDate)){
                     // unset($giftList[$key]);
                      array_push($giftList2, $gift);
                  } 
                
            }
    
        $productsArr = array();
  
   
              foreach ($giftList as $gift) {
                  $time = strtotime($gift['expire_date']);
                 $newformat = date('Y-m-d',$time);
                  if (( $newformat > $currentDate)){
                                   
                 $productId = $gift['product_id'];
                 $product = Product::where('id',$gift['product_id'])->first();
              if (is_null($product)){}else {
                   array_push($productsArr, $product);
              }
          }
      
          }
 
         foreach($productsArr as $item)
         {
               $item['category_ids'] = json_decode($item['category_ids'], true);
                    $item['attributes'] = json_decode($item['attributes'], true);
                    $item['choice_options'] = json_decode($item['choice_options'], true);
                    $item['variations'] = json_decode($item['variations'], true);
                    $item['add_ons'] = json_decode($item['add_ons'], true);
                    $item['tags'] = [];
        
         }
         
       
         $data['limit']=$limit;   
         $data['gifts']=$giftList2;
         $data['products']=$productsArr;
         
      /*   if (is_null($data['products'])){
            
         } else {
              $data['products'] = Helpers::product_data_formatting($data['products'], true);
         }*/
 
     
       return response()->json(['data'=>$data,'status'=>200], 200);
    }
    
     public function update_user_wheel_limit(Request $request)
    {
        $wheel_limit = $request->wheel_limit;
        $wheel_limit_int = (int)$wheel_limit;
  
        $userDetails = [
            'wheel_limit' => $wheel_limit_int,
            'updated_at' => now()
        ];

        User::where(['id' => $request->user()->id])->update($userDetails);

        return response()->json(['message' => 'successfully updated!'], 200);
    }
    
    public function send_search(Request $request)
    {
      
        if(SearchedItem::where('user_id', $request->user()->id)->where('search', $request->search)->first()){
        $old_search = SearchedItem::where('user_id', $request->user()->id)->where('search', $request->search)->first();
        $number = $old_search->number;
        $old_search->search = $request->search;
        $old_search->user_id = $request->user()->id;
        $old_search->number = $number + 1;
        $old_search->save(); 
          }else {
        $search = New SearchedItem();
        $search->search = $request->search;
        $search->user_id = $request->user()->id;
        $search->save();  
          }
        
        return response()->json(['message' => 'successfully updated!'], 200);
    }
  
  
      public function send_app_review(Request $request)
    {
        $review = New AppReview();
        $review->review = $request->review;
        $review->rating = $request->rating;
        $review->user_id = $request->user()->id;
        $review->save();  
        
        return response()->json(['message' => 'successfully updated!'], 200);
    }
    
    public function request_chances(Request $request)
    {
        $user = User::where('id', $request->user()->id)->first();
        $wheel = SpinWheel::where('id', $request->wheel_id)->first();
        
        $user->purchases_points = $user->purchases_points - $wheel->unlock_points;
        $user->wheel_limit = $user->wheel_limit + $wheel->unlock_chances;
        $user->save();
        
        return response()->json(['message' => 'successfully updated!'], 200);
    }
      
      
    public function check_password(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'password' => 'required|min:6'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }
        
        $user = User::where('id', $request->user()->id)->first();
    
        
        $data = [
            'email' => $user->email,
            'password' => $request->password
        ];

        if (auth('web')->attempt($data)) {
            return response()->json(['message' => 'Success'], 200);
        } else {
            $errors = [];
            array_push($errors, ['code' => 'auth-001', 'message' => 'Wrong password']);
            return response()->json([
                'errors' => $errors
            ], 401);
        }
    }  
    
    public function delete_account(Request $request)
    {
        if(User::where('id', $request->user()->id)->first()){
        $user = User::where('id', $request->user()->id)->first();
        $user->deleted = 1;
        $user->image = null;
        $user->gift_info = 'no gifts';
        $user->wheel_limit = 0;
        $user->save();
 
        DB::table('conversations')->where('user_id', $user->id)->delete();
        DB::table('conversation_services')->where('user_id', $user->id)->delete();
        DB::table('customer_addresses')->where('user_id', $user->id)->delete();
        DB::table('customer_question_answers')->where('customer_id', $user->id)->delete();
        DB::table('d_m_reviews')->where('user_id', $user->id)->delete();
        DB::table('email_verifications')->where('email', $user->email)->delete();
        DB::table('nutrition_models')->where('user_id', $user->id)->delete();
        DB::table('orders')->where('user_id',$user->id)->update(array('user_id'=>0));
        DB::table('searched_items')->where('user_id', $user->id)->delete();
        DB::table('user_meals')->where('user_id', $user->id)->delete();
        DB::table('wishlists')->where('user_id', $user->id)->delete();
        
        }else {
             return 'No token';
        }
        
    }   
}
