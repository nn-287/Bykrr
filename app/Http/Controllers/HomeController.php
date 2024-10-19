<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        /*$this->middleware('auth');*/
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('home');
    }

    public function about_us()
    {
        return view('about-us');
    }

    public function terms_and_conditions()
    {
        return view('terms-and-conditions');
    }

    public function privacy_policy()
    {
        return view('privacy-policy');
    }
     public function home_page()
    {
        return view('website-views.home-page.home-view');
    }
    
    public function app_store()
    {
        return view('about-us');
    }
    public function place_order()
    {
        return view('about-us');
    }

    public function dashboard()
    {
        return view('merchant-views.dashboard');
    }

}
