<?php

namespace App\Http\Controllers\Admin;

use App\CentralLogics\Helpers;
use App\Http\Controllers\Controller;
use App\Model\Order;
use App\Model\Wishlist;
use App\Model\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Collection;
use Auth;

class ReportController extends Controller
{
    public function order_index()
    {
        if (session()->has('from_date') == false) {
            session()->put('from_date', date('Y-m-01'));
            session()->put('to_date', date('Y-m-30'));
        }
        return view('admin-views.report.order-index');
    }

    public function earning_index()
    {
        if (session()->has('from_date') == false) {
            session()->put('from_date', date('Y-m-01'));
            session()->put('to_date', date('Y-m-30'));
        }
        return view('admin-views.report.earning-index');
    }
    
    public function stats_index()
    {
        if (session()->has('from_date') == false) {
            session()->put('from_date', date('Y-m-01'));
            session()->put('to_date', date('Y-m-30'));
        }
        $today = Carbon::today();
        $yesterday = Carbon::yesterday();
        return view('admin-views.report.stats-index', compact('today', 'yesterday'));
    }

    public function set_date(Request $request)
    {
        session()->put('from_date', date('Y-m-d', strtotime($request['from'])));
        session()->put('to_date', date('Y-m-d', strtotime($request['to'])));
        return back();
    }
    
    public function wishlists($latest)
    {

        // $arr = [];
        // $wishlists = Wishlist::get();
        // foreach($wishlists as $wishlist){
        //     if(Product::where('id',$wishlist->product_id)->first()){
        //         array_push($arr, $wishlist);
        //     }else {
        //         DB::table('wishlists')->where('id', $wishlist->id)->delete();
        //     }
        // }

        if($latest == 0){
          $wishlists= Wishlist::select('product_id', DB::raw('COUNT(product_id) as count'))->groupBy('product_id')->orderBy('count','DESC')->paginate(10);
         
        }

       else if($latest == 1){
            $wishlists = Wishlist::latest()->paginate(10);
        } 
       return view('admin-views.report.wishlists', compact('wishlists', 'latest'));
    }
}
