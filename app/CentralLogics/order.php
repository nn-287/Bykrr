<?php

namespace App\CentralLogics;

use App\Model\Order;
use App\Model\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class OrderLogic
{
    public static function track_order($order_id)
    {
        return Helpers::order_data_formatting(Order::with(['details', 'delivery_man.rating'])->where(['id' => $order_id])->first(), false);
    }

    public static function place_order($user_id, $order_amount, $coupon_discount_amount, $coupon_discount_title, $coupon_code, $payment_method, $order_note, $order_type, 
    $delivery_address_id, $delivery_charge, $zone_id, $cart_list)
    {
        $order = new Order();
            $order->user_id = $user_id;
            $order->order_amount = $order_amount;
            $order->coupon_discount_amount = $coupon_discount_amount;
            $order->coupon_discount_title =  $coupon_discount_title == 0 ? null : 'coupon_discount_title';
            $order->payment_status = 'unpaid';
            $order->order_status = 'pending';
            $order->coupon_code = $coupon_code;
            $order->payment_method = $payment_method;
            $order->transaction_reference = null;
            $order->order_note = $order_note;

            $order->order_type = $order_type;
            $order->delivery_address_id = $delivery_address_id;
            
            if($zone_id != null){
                $order->zone_id = $zone_id;
                $order->delivery_charge = $delivery_charge;
                $order->delivery_fee = $delivery_charge; // temporary will deleted
            }
            $order->created_at = now();
            $order->updated_at = now();
            $order->save();

            $o_id = $order->id;

            foreach ($cart_list as $c) {

                $product = Product::find($c['product_id']);

                $price = ProductLogic::get_price_info($c);

                $variation['type'] = null;
                $variation['image'] = null;
                $variation['price'] = $price['price'];

                $variationsArray = json_decode($product->variations, true);
               
                if(count($variationsArray) > 0){
                   
                    $variationStr = ProductLogic::product_attributes($product->id, json_decode($c->variation_index, true));
                    foreach(json_decode($product->variations, true) as $var){
                        
                        if($var['type'] == $variationStr){
                            $variation = $var;
                        }
                    }
                } 

                $or_d = [
                    'order_id' => $o_id,
                    'product_id' => $c['product_id'],
                    'product_details' => $product,
                    'quantity' => $c['quantity'],
                    'price' => $price['price'],
                    'tax_amount' => Helpers::tax_calculate($product, $price),
                    'discount_on_product' => $price['discount_amount'],
                    'discount_type' => 'discount_on_product',
                    'variant' => json_encode($c['variant']),
                    'variation' => json_encode($variation),
                    'created_at' => now(),
                    'updated_at' => now()
                ];

                DB::table('order_details')->insert($or_d);
            }

        return $o_id;
    }
}
