<?php

namespace App\Http\Controllers\Merchant\Home;

use App\Http\Controllers\Controller;
use App\Model\Merchant;

class HomeController extends Controller
{
    public function dashboard()
    {
        // return auth('merchant')->id(); 
        $merchant = Merchant::where('id', auth('merchant')->user()->id)->first();
        
        
        return view('merchant-views.dashboard', compact('merchant'));
    }
}
