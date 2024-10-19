<?php

namespace App\CentralLogics;

use App\Model\Coupon;
use App\Model\Order;

class CouponLogic
{
    public static function apply($user_id, $code)
    {
        try {
            $coupon = Coupon::active()->where(['code' => $code])->first();
            if (isset($coupon)) {
                if ($coupon['limit'] == null) {
                    return response()->json($coupon, 200);
                } else {
                    $total = Order::where(['user_id' => $user_id, 'coupon_code' => $code])->count();
                    if ($total < $coupon['limit']) {
                        return $coupon;
                    } else {
                        return 'limit_exceeded';
                    }
                }
            } else {
                return 'not_found';
            }
        } catch (\Exception $e) {
            return response()->json(['errors' => $e], 403);
        }
    }

    public static function calculateCouponDiscount($price, $coupon)
    {
        $coupon_discount_amount = 0;
        if ($coupon) {
            if($coupon->min_purchase != null && $coupon->min_purchase < $price){
                if ($coupon->discount_type == 'amount') {
                    $coupon_discount_amount = $coupon->discount;
                } else {
                    $coupon_discount_amount = ($coupon->discount * $price) / 100;
                }
            }
            
        }
        return $coupon_discount_amount;
    }
}
