<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use App\Model\Category;
use App\Model\Product;
use Brian2694\Toastr\Facades\Toastr;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class HomeController extends Controller
{
    function home()
    {
        $categories=Category::where(['position'=>0])->latest()->paginate(10);
        return view('admin-views.category.index',compact('categories'));
    }
    public function home_page()
    {
        $categories=Category::where('position',0)->get();
        $products=Product::where('status',1)->latest()->paginate(10);
        //$variations = json_decode($products->variations, true);
       
        return view('website-views.home-page.home-view',compact('categories','products'));
    }
    
  
}
