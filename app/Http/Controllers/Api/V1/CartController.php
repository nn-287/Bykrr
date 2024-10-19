<?php

namespace App\Http\Controllers\Api\V1;

use App\CentralLogics\CartLogic;
use App\CentralLogics\Helpers;
use App\Http\Controllers\Controller;
use App\Model\CartProduct;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CartController extends Controller
{
    public function add_to_cart(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        if($request->id != 0){
            $cart = CartProduct::find($request->id);
        }else{
            $cart = new CartProduct(); 
        }
            $cart->user_id = $request->user()->id;
            $cart->product_id = $request['product_id'];
            $cart->quantity = $request['quantity'];
            $cart->variation_index = json_encode($request['variation_index']);
            $cart->save();
            return response()->json(['message' => 'successfully added!'], 200);

        return response()->json(['message' => 'Already in your cart'], 409);
    }

    public function remove_from_cart(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'cart_id' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        $cart = CartProduct::where('id', $request->cart_id)->first();

        if (!empty($cart)) {
            CartProduct::where('id', $request->cart_id)->delete();
            return response()->json(['message' => 'successfully removed!'], 200);
        }
        return response()->json(['message' => 'No such data found!'], 404);
    }

    public function cart_list(Request $request)
    {
        $cart_list = CartLogic::cart_list($request->user()->id);
        
        return response()->json($cart_list, 200);
    }

    public function cart_product_ids(Request $request)
    {
        $product_ids = CartProduct::whereHas('product')->where('user_id', $request->user()->id)->pluck('product_id');
        return response()->json($product_ids, 200);
    }
}
