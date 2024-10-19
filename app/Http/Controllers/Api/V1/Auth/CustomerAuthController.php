<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\CentralLogics\Helpers;
use App\Http\Controllers\Controller;
use App\Mail\EmailVerification;
use App\Model\BusinessSetting;
use App\Model\EmailVerifications;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Firebase\Auth\Token\Exception\InvalidToken;
use Kreait\Firebase\Factory;


class CustomerAuthController extends Controller
{
    public function verify_phone(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone' => 'required|min:11|max:14|unique:users'
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
            'email' => 'required|unique:users'
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
            'f_name' => 'required',
            'l_name' => 'required',
            // 'email' => 'required|unique:users',
            //'phone' => 'required|unique:users',
            'password' => 'required|min:6',
        ], [
            'f_name.required' => 'The first name field is required.',
            'l_name.required' => 'The last name field is required.',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }
         if(User::where('email', $request->email)->first()){
            $user = User::where('email', $request->email)->first();
        }else {
            $user = New User();
        }
        $user->f_name = $request->f_name;
        $user->l_name = $request->l_name;
        $user->email = $request->email;
        $user->phone = $request->phone;
       
        $user->password = bcrypt($request->password);
        if(User::where('email', $request->email)->first()){
            $user->deleted = 0;
        }
        $user->save();

        // $user = User::create([
        //     'f_name' => $request->f_name,
        //     'l_name' => $request->l_name,
        //     'email' => $request->email,
        //     //'phone' => $request->phone,
        //     'password' => bcrypt($request->password)
        // ]);

        $token = $user->createToken('StoreCustomerAuth')->accessToken;

        return response()->json([
            'token' => $token,
            'user' => $user,
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

        if (auth()->attempt($data)) {
            
            $user = User::where('email', $request->email)->first();
            if($user->deleted == 0){
                $token = auth()->user()->createToken('CustomerAuth')->accessToken;
                return response()->json([
                    'token' => $token,
                    'user' => auth()->user(),
                ], 200);
            }else {
            $errors = [];
            array_push($errors, ['code' => 'auth-001', 'message' => 'Unauthorized.']);
            return response()->json([
                'errors' => $errors
            ], 401);
        }
            return response()->json([
                'token' => $token,
                'user' => auth()->user(),
            ], 200);
        } else {
            $errors = [];
            array_push($errors, ['code' => 'auth-001', 'message' => 'Unauthorized.']);
            return response()->json([
                'errors' => $errors
            ], 401);
        }
    }

    public function storeUserInfo(Request $request)
    {

    }

    public function loginByPhone(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'firebasetoken' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }
        try {
            $factory = (new Factory)->withServiceAccount('./key/la-crostini-firebase-adminsdk-sl6cj-2ccb8ca93d.json');
            $auth = $factory->createAuth();
            $idTokenString = $request->get("firebasetoken");
            $verifiedIdToken = $auth->verifyIdToken($idTokenString);
            $uid = $verifiedIdToken->claims()->get('sub');
            //var_dump( $verifiedIdToken->claims()->get("phone_number"));
           // var_dump( $uid);exit;
            $phone = $verifiedIdToken->claims()->get("phone_number");//$request->get("firebasetoken");
            if($phone){
                //check phone exist or not
                $data = [
                    'phone' => $phone,
                ];
                $user = User::where("phone",$phone)->first();
                if ($user) {
                    $user->deleted = 0;
                    $user->is_phone_verified = 1;
                    $user->save();
                    //$user = User::where("phone",$phone)->first();
                    $token = $user->createToken('StoreCustomerAuth')->accessToken;
                    return response()->json(['token' => $token], 200);
                } else {
                    $user = User::create([
                        'f_name' => $request->f_name,
                        'l_name' => $request->l_name,
                        'email' => "",
                        'is_phone_verified' => 1,
                        'phone' => $phone,
                        'password' => ""
                    ]);
                    $token = $user->createToken('storeCustomerAuth')->accessToken;
                    return response()->json(['token' => $token], 200);
                }
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
    
    public function social_login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }
        try {
                $user = User::where("email", $request->email)->first();
                if ($user) {
                    $user->deleted = 0;
                    $user->provider_name = $request->provider_name;
                    $user->provider_id = $request->provider_id;
                    $user->save();
                    $token = $user->createToken('StoreCustomerAuth')->accessToken;
                    return response()->json(['token' => $token], 200);
                } else {
                    $nameParts = explode(' ', $request->full_name);
                    $f_name = $nameParts[0]; // First name
                    $l_name = $nameParts[1]; // Last Name

                    $user = User::create([
                        'f_name' => $f_name,
                        'l_name' => $l_name,
                        'email' => $request->email,
                        'gift_info' => "no gifts",
                        'password' => "",
                        'provider_name' => $request->provider_name,
                        'provider_id' => $request->provider_id,
                    ]);
                    $token = $user->createToken('storeCustomerAuth')->accessToken;
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

