<?php

namespace App\Http\Controllers\Api\V1;

use App\CentralLogics\Helpers;
use App\Http\Controllers\Controller;
use App\Model\Wishlist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class WishlistController extends Controller
{
    public function add_to_wishlist(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        $wishlist = Wishlist::where('user_id', $request->user()->id)->where('product_id', $request->product_id)->first();

        if (empty($wishlist)) {
            $wishlist = new Wishlist;
            $wishlist->user_id = $request->user()->id;
            $wishlist->product_id = $request->product_id;
            $wishlist->save();
            return response()->json(['message' => 'successfully added!'], 200);
        }

        return response()->json(['message' => 'Already in your wishlist'], 409);
    }

    public function remove_from_wishlist(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        $wishlist = Wishlist::where('user_id', $request->user()->id)->where('product_id', $request->product_id)->first();

        if (!empty($wishlist)) {
            Wishlist::where(['user_id' => $request->user()->id, 'product_id' => $request->product_id])->delete();
            return response()->json(['message' => 'successfully removed!'], 200);

        }
        return response()->json(['message' => 'No such data found!'], 404);
    }

    public function wish_list(Request $request)
    {
        $wishlists = Wishlist::with('product')->whereHas('product')->where('user_id', $request->user()->id)->get();
        $new_wishlists = [];
        foreach($wishlists as $wishlist){
            $product = $wishlist->product;
            $variations = json_decode($wishlist->product->variations);
            $attributes = json_decode($wishlist->product->attributes);
            $category_ids = json_decode($wishlist->product->category_ids);
            $choice_options = json_decode($wishlist->product->choice_options);
            $product['variations'] = $variations;
            $product['attributes'] = $attributes;
            $product['category_ids'] = $category_ids;
            $product['choice_options'] = $choice_options;

            $wishlist['product'] = $product;
            array_push($new_wishlists, $wishlist);
        }
        return $new_wishlists;
        return response()->json(Wishlist::with('product')->whereHas('product')->where('user_id', $request->user()->id)->get(), 200);
    }

    public function wishlist_product_ids(Request $request)
    {
        $product_ids = Wishlist::whereHas('product')->where('user_id', $request->user()->id)->pluck('product_id');
        return response()->json($product_ids, 200);
    }

    public function addToWhishlist($product_id)
    { 
        $wishlist = Wishlist::where('user_id', auth()->user()->id)->where('product_id', $product_id)->first();
        if ($wishlist) {
            Wishlist::where('user_id', auth()->user()->id)->where('product_id', $product_id)->delete();
        }else{
            $wishlist = new Wishlist;
            $wishlist->user_id = auth()->user()->id;
            $wishlist->product_id = $product_id;
            $wishlist->save();
        }
    }
}
