<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use App\Model\ProductOffer;

class OffersController extends Controller
{
    public function index()
    {
        $offers = ProductOffer::latest()->paginate(10);
        return view('admin-views.offers.index', compact('offers'));
    }
    

    public function store(Request $request)
    {
        if($request->offer_type == 'discount'){
            $request->validate([
                'name' => 'required',
                'category_id' => 'required',
                'discount' => 'required',
            ], [
                'name.required' => 'Name is required!',
                'category_id.required' => 'Category is required!',
                'discount.required' => 'Discount is required!',
            ]);
        }else{
            $request->validate([
                'name' => 'required',
                'category_id' => 'required',
                'product_id' => 'required',
                'offered_product_quantity' => 'required',
            ], [
                'name.required' => 'Name is required!',
                'category_id.required' => 'Category is required!',
                'product_id.required' => 'Product is required!',
                'offered_product_quantity.required' => 'Offered product quantity is required!',
            ]);
        }
        

        $offer = new ProductOffer();
        $offer->name = $request->name;
        $offer->category_id = $request->category_id;
        $offer->offer_type = $request->offer_type;
        if($request->offer_type == 'discount'){
            $offer->discount = $request->discount;
        }else{
            $offer->product_id = $request->product_id;
            $offer->offered_product_quantity = $request->offered_product_quantity;
        }
        
        $offer->save();
        Toastr::success('Offer added successfully!');
        return back();
    }

    public function edit($id)
    {
        $offer = ProductOffer::find($id);
        return view('admin-views.offers.edit', compact('offer'));
    }

    public function update(Request $request)
    {
        if($request->offer_type == 'discount'){
            $request->validate([
                'name' => 'required',
                'category_id' => 'required',
                'discount' => 'required',
            ], [
                'name.required' => 'Name is required!',
                'category_id.required' => 'Category is required!',
                'discount.required' => 'Discount is required!',
            ]);
        }else{
            $request->validate([
                'name' => 'required',
                'category_id' => 'required',
                'product_id' => 'required',
                'offered_product_quantity' => 'required',
            ], [
                'name.required' => 'Name is required!',
                'category_id.required' => 'Category is required!',
                'product_id.required' => 'Product is required!',
                'offered_product_quantity.required' => 'Offered product quantity is required!',
            ]);
        }
        
         $offer = ProductOffer::find($request->offer_id);
         $offer->name = $request->name;
         $offer->category_id = $request->category_id;
         $offer->offer_type = $request->offer_type;
         if($request->offer_type == 'discount'){
             $offer->discount = $request->discount;
         }else{
             $offer->product_id = $request->product_id;
             $offer->offered_product_quantity = $request->offered_product_quantity;
         }
        
        $offer->save();
        Toastr::success('Offer updated successfully!');
        return back();
    }

    public function delete(Request $request)
    {
        $offer = ProductOffer::find($request->id);
        $offer->delete();
        Toastr::success('Offer removed!');
        return back();
    }
}
