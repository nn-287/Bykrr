<?php

namespace App\Http\Controllers\Merchant\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

class LoginController extends Controller
{
    public function __construct()
    {
        $this->middleware('guest:merchant', ['except' => ['logout']]);
    }

    public function login()
    {
        return view('merchant-views.auth.login');
    }
    

    public function submit(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6'
        ]);

        if (auth('merchant')->attempt(['email' => $request->email, 'password' => $request->password], $request->remember)) 
        {
            return redirect()->route('merchant.dashboard');
            
        }

        return redirect()->back()->withInput($request->only('email', 'remember'))->withErrors(['Credentials do not match.']);
    }

    

    public function logout(Request $request)
    {
        auth()->guard('merchant')->logout();
      
        return redirect()->route('merchant.auth.login');
    }
    
}
