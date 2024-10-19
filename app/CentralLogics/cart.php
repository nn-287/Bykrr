<?php

namespace App\CentralLogics;

use App\Model\CartProduct;

class CartLogic
{
    public static function cart_list($user_id)
    {
        $cart_list = CartProduct::with('product')->whereHas('product')->where('user_id', $user_id)->get();
        
        $new_cart_list = [];

        foreach($cart_list as $item){
            $product = $item->product;
            if (is_array($product->variations)) {
                $variations = $item->product->variations;  
                $attributes = $item->product->attributes;
                $category_ids = $item->product->category_ids;
                $choice_options = $item->product->choice_options;
            }else{
                $variations = json_decode($item->product->variations, true);  
                $attributes = json_decode($item->product->attributes, true);
                $category_ids = json_decode($item->product->category_ids, true);
                $choice_options = json_decode($item->product->choice_options, true);
            }
          
            $product['variations'] = $variations;
            $product['attributes'] = $attributes;
            $product['category_ids'] = $category_ids;
            $product['choice_options'] = $choice_options;

            $item['product'] = $product;
            array_push($new_cart_list, $item);
        }
        return $new_cart_list;
    }


    public static function empty_cart($user_id)
    {
        $cart_list = CartProduct::where('user_id', $user_id)->get();
        foreach($cart_list as $cart){
            $cart->delete();
        }
    }

}