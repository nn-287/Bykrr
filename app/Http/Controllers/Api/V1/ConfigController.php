<?php

namespace App\Http\Controllers\Api\V1;

use App\CentralLogics\Helpers;
use App\Http\Controllers\Controller;
use App\Model\Branch;
use App\Model\BusinessSetting;
use App\Model\Nutritionist;
use App\Model\Currency;

class ConfigController extends Controller
{
    public function configuration()
    {
        $currency_symbol = Currency::where(['currency_code' => Helpers::currency_code()])->first()->currency_symbol;
        $cod = json_decode(BusinessSetting::where(['key' => 'cash_on_delivery'])->first()->value, true);
        $dp = json_decode(BusinessSetting::where(['key' => 'digital_payment'])->first()->value, true);

        $timeRanges = json_decode(BusinessSetting::where(['key' => 'store_opening_hours'])->first()->value, true);
        
            
            $opening_hours = collect($timeRanges)->map(function ($range) {
                return [
                    'start' => $range['start'],
                    'end' => $range['end']
                ];
            })->values();

        return response()->json([
            'store_name' => BusinessSetting::where(['key' => 'store_name'])->first()->value,
            'store_open_time' => BusinessSetting::where(['key' => 'store_open_time'])->first()->value,
            'store_close_time' => BusinessSetting::where(['key' => 'store_close_time'])->first()->value,
            'store_logo' => BusinessSetting::where(['key' => 'logo'])->first()->value,
            'store_address' => BusinessSetting::where(['key' => 'address'])->first()->value,
            'store_phone' => BusinessSetting::where(['key' => 'phone'])->first()->value,
            'store_email' => BusinessSetting::where(['key' => 'email_address'])->first()->value,
            'store_location_coverage' => Branch::where(['id' => 1])->first(['longitude', 'latitude', 'coverage']),
            'minimum_order_value' => (float)BusinessSetting::where(['key' => 'minimum_order_value'])->first()->value,
            'app_version' => BusinessSetting::where(['key' => 'app_version'])->first()->value,
            'phone_otp' => BusinessSetting::where(['key' => 'phone_otp'])->first()->value,
            'opening_hours' => $opening_hours,

            'base_urls' => [
                'product_image_url' => asset('storage/app/public/product'),
                'product_thumb_image_url' => asset('storage/app/public/product-thumbnail'),
                'customer_image_url' => asset('storage/app/public/profile'),
                'banner_image_url' => asset('storage/app/public/banner'),
                'category_image_url' => asset('storage/app/public/category'),
                'review_image_url' => asset('storage/app/public/review'),
                'notification_image_url' => asset('storage/app/public/notification'),
                'store_image_url' => asset('storage/app/public/store'),
                'contest_image_url' => asset('storage/app/public/uploads/contests'),
                'delivery_man_image_url' => asset('storage/app/public/delivery-man'),
                'chat_image_url' => asset('storage/app/public/conversation'),
                'nutrition_image_url' => asset('storage/app/public/nutrition'),
                'nutritionist_image_url' => asset('storage/app/public/nutritionist'),
                'admin_image_url' => asset('storage/app/public/admin'),
                'branch_image_url' => asset('storage/app/public/branch'),
            ],
            'currency_symbol' => $currency_symbol,
            'delivery_charge' => BusinessSetting::where(['key' => 'delivery_charge'])->first()->value,
            'cash_on_delivery' => $cod['status'] == 1 ? 'true' : 'false',
            'digital_payment' => $dp['status'] == 1 ? 'true' : 'false',
            'branches' => Branch::all(['id', 'name', 'email', 'longitude', 'latitude', 'address', 'coverage']),
            /*'terms_and_conditions' => BusinessSetting::where(['key' => 'terms_and_conditions'])->first()->value,
            'privacy_policy' => BusinessSetting::where(['key' => 'privacy_policy'])->first()->value,
            'about_us' => BusinessSetting::where(['key' => 'about_us'])->first()->value*/
            'terms_and_conditions' => BusinessSetting::where(['key' => 'terms_and_conditions'])->first()->value,
            'privacy_policy' => BusinessSetting::where(['key' => 'privacy_policy'])->first()->value,
           // 'privacy_policy' => route('privacy-policy'),
            'about_us' => route('about-us')
        ]);
    }
}
