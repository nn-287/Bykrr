<?php

namespace App\Http\Controllers\Api\V1\Merchant\Auth;

use App\CentralLogics\Helpers;
use App\Http\Controllers\Controller;
use App\Model\Merchant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class MerchantPasswordResetController extends Controller
{
    public function reset_password_request(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        $merchant = Merchant::Where(['email' => $request['email']])->first();
        if(!$merchant){
            return response()->json(['errors' => [
                ['code' => 'invalid', 'message' => 'This email is not registered.']
            ]], 400);
        }
        $count = DB::table('password_resets')->where('email', $merchant->email)->where('created_at', '>=', \Carbon\Carbon::now()->subHour())->count();
        if($count > 20){
          return response()->json(['errors' => [
            ['code' => 'invalid', 'message' => 'You made a lot of requests! Try again later.']
        ]], 400);
        }
        
        $count = DB::table('password_resets')->where('email', $merchant->email)->where('created_at', '>=', Carbon::today())->count();

        if($count > 5){
            return response()->json(['errors' => [
            ['code' => 'invalid', 'message' => 'You made a lot of requests! Try again later.']
        ]], 400);

        }

        if (isset($merchant)) {
            $token = rand(1000,9999);
            DB::table('password_resets')->insert([
                'email' => $merchant['email'],
                'token' => $token,
                'created_at' => now(),
            ]);
            Mail::to($merchant['email'])->send(new \App\Mail\PasswordResetMail($token));
            return response()->json(['message' => 'Email sent successfully.'], 200);
        }
        return response()->json(['errors' => [
            ['code' => 'not-found', 'message' => 'Email not found!']
        ]], 404);
    }

    public function verify_token(Request $request)
    {
        $data = DB::table('password_resets')->where(['token' => $request['reset_token'],'email'=>$request['email']])->first();
        if (isset($data)) {
            return response()->json(['message'=>"Token found, you can proceed"], 200);
        }
        return response()->json(['errors' => [
            ['code' => 'invalid', 'message' => 'Invalid token.']
        ]], 400);
    }

    public function reset_password_submit(Request $request)
    {
        $data = DB::table('password_resets')->where(['token' => $request['reset_token']])->first();
        if (isset($data)) {
            if ($request['password'] == $request['confirm_password']) {
                DB::table('merchants')->where(['email' => $data->email])->update([
                    'password' => bcrypt($request['confirm_password'])
                ]);
                DB::table('password_resets')->where(['token' => $request['reset_token']])->delete();
                return response()->json(['message' => 'Password changed successfully.'], 200);
            }
            return response()->json(['errors' => [
                ['code' => 'mismatch', 'message' => 'Password did,t match!']
            ]], 401);
        }
        return response()->json(['errors' => [
            ['code' => 'invalid', 'message' => 'Invalid token.']
        ]], 400);
    }
}
