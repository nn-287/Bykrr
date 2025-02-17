<?php

namespace App\Http\Controllers\Api\V1\Merchant\Auth;

use App\CentralLogics\Helpers;
use App\Http\Controllers\Controller;
use App\Mail\EmailVerification;
use App\Model\BusinessSetting;
use App\Model\EmailVerifications;
use App\Model\Merchant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Firebase\Auth\Token\Exception\InvalidToken;
use Kreait\Firebase\Factory;


class MerchantAuthController extends Controller
{
    public function verify_phone(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone' => 'required|min:11|max:14|unique:merchants'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        return response()->json([
            'message' => 'Number is ready to register',
            'otp' => 'inactive'
        ], 200);
    }

    
    public function check_email(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|unique:merchants'
        ]);

        if ($validator->fails()) {
            $token = rand(1000, 9999);
            DB::table('email_verifications')->insert([
                'email' => $request['email'],
                'token' => $token,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            Mail::to($request['email'])->send(new EmailVerification($token));

            return response()->json([
                'message' => 'Email is ready to register',
                'token' => 'active'
            ], 200);
            // return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }
      
        if (BusinessSetting::where(['key'=>'email_verification'])->first()->value){
            $token = rand(1000, 9999);
            DB::table('email_verifications')->insert([
                'email' => $request['email'],
                'token' => $token,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            Mail::to($request['email'])->send(new EmailVerification($token));

            return response()->json([
                'message' => 'Email is ready to register',
                'token' => 'active'
            ], 200);
        }else{
            return response()->json([
                'message' => 'Email is ready to register',
                'token' => 'inactive'
            ], 200);
        }
    }

    public function verify_email(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        $verify = EmailVerifications::where(['email' => $request['email'], 'token' => $request['token']])->first();

        if (isset($verify)) {
            $verify->delete();
            return response()->json([
                'message' => 'Token verified!',
            ], 200);
        }

        return response()->json(['errors' => [
            ['code' => 'token', 'message' => 'Token is not found!']
        ]], 404);
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'owner_name' => 'required',
            // 'email' => 'required|unique:merchants',
            //'phone' => 'required|unique:merchants',
             'email' => 'required',
             'phone' => 'required',
            'password' => 'required|min:6',
        ], [
            'name.required' => 'The first name field is required.',
            'owner_name.required' => 'The last name field is required.',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }
         if(Merchant::where('email', $request->email)->first()){
            $merchant = Merchant::where('email', $request->email)->first();
        }else {
            $merchant = New Merchant();
        }
        $merchant->name = $request->name;
        $merchant->owner_name = $request->owner_name;
        $merchant->email = $request->email;
        $merchant->phone = $request->phone;
       
        $merchant->password = bcrypt($request->password);
        
        $merchant->save();


        $token = $merchant->createToken('MerchantAuth')->accessToken;

        return response()->json([
            'token' => $token,
            'merchant' => $merchant,
        ], 200);
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required',
            'password' => 'required|min:6'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        $data = [
            'email' => $request->email,
            'password' => $request->password
        ];

        if (auth('merchant')->attempt($data)) {
            
           $token = auth('merchant')->user()->createToken('MerchantAuth')->accessToken;
            return response()->json([
                'token' => $token,
                'merchant' => auth('merchant')->user(),
            ], 200);
        } else {
            $errors = [];
            array_push($errors, ['code' => 'auth-001', 'message' => 'Unauthorized.']);
            return response()->json([
                'errors' => $errors
            ], 401);
        }
    }

    // public function loginByPhone(Request $request)
    // {
    //     $validator = Validator::make($request->all(), [
    //         'firebasetoken' => 'required',
    //     ]);
    //     if ($validator->fails()) {
    //         return response()->json(['errors' => Helpers::error_processor($validator)], 403);
    //     }
    //     try {
    //        // $factory = (new Factory)->withServiceAccount('./key/bykrfirebase-adminsdk-sl6cj-2ccb8ca93d.json');
    //         $auth = $factory->createAuth();
    //         $idTokenString = $request->get("firebasetoken");
    //         $verifiedIdToken = $auth->verifyIdToken($idTokenString);
    //         $uid = $verifiedIdToken->claims()->get('sub');
    //         //var_dump( $verifiedIdToken->claims()->get("phone_number"));
    //        // var_dump( $uid);exit;
    //         $phone = $verifiedIdToken->claims()->get("phone_number");//$request->get("firebasetoken");
    //         if($phone){
    //             //check phone exist or not
    //             $data = [
    //                 'phone' => $phone,
    //             ];
    //             $merchant = Merchant::where("phone",$phone)->first();
    //             if ($merchant) {
    //                 $merchant->save();
    //                 $token = $merchant->createToken('StoreCustomerAuth')->accessToken;
    //                 return response()->json(['token' => $token], 200);
    //             } else {
    //                 $merchant = Merchant::create([
    //                     'f_name' => $request->f_name,
    //                     'l_name' => $request->l_name,
    //                     'email' => "",
    //                     'is_phone_verified' => 1,
    //                     'phone' => $phone,
    //                     'password' => ""
    //                 ]);
    //                 $token = $merchant->createToken('storeCustomerAuth')->accessToken;
    //                 return response()->json(['token' => $token], 200);
    //             }
    //         }
    //     } catch (InvalidToken $e) {
    //         //echo 'The token is invalid: '.$e->getMessage();
    //         return response()->json([
    //             'errors' => 'The token is invalid: '.$e->getMessage()
    //         ], 401);
    //     } catch (\InvalidArgumentException $e) {
    //         return response()->json([
    //             'errors' => 'The token could not be parsed: '.$e->getMessage()
    //         ], 401);
    //         //echo 'The token could not be parsed: '.$e->getMessage();
    //     }
    // }
    
    public function social_login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }
        try {
                $merchant = Merchant::where("email", $request->email)->first();
                if ($merchant) {
                    $merchant->provider_name = $request->provider_name;
                    $merchant->provider_id = $request->provider_id;
                    $merchant->save();
                    $token = $merchant->createToken('StoreCustomerAuth')->accessToken;
                    return response()->json(['token' => $token], 200);
                } else {
                  
                    $merchant = Merchant::create([
                        'name' => $request->name,
                        'owner_name' => $request->full_name,
                        'email' => $request->email,
                        'password' => "",
                        'provider_name' => $request->provider_name,
                        'provider_id' => $request->provider_id,
                    ]);
                    $token = $merchant->createToken('storeCustomerAuth')->accessToken;
                    return response()->json(['token' => $token], 200);
                }
        
        } catch (InvalidToken $e) {
            //echo 'The token is invalid: '.$e->getMessage();
            return response()->json([
                'errors' => 'The token is invalid: '.$e->getMessage()
            ], 401);
        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'errors' => 'The token could not be parsed: '.$e->getMessage()
            ], 401);
            //echo 'The token could not be parsed: '.$e->getMessage();
        }
    }
}

