<?php

namespace App\Http\Controllers\Api\V1\Merchant;

use App\CentralLogics\CategoryLogic;
use App\Http\Controllers\Controller;
use App\Model\Banner;
use App\Model\MerchantService;
use App\Model\Service;
use Illuminate\Http\Request;

class ServicesController extends Controller
{
    public function get_services(){
        try {
            $services = Service::active()->get();
            return response()->json($services, 200);
        } catch (\Exception $e) {
            return response()->json([], 200);
        }
    }

    public function store_services(Request $request){
        $services_ids = json_decode($request->service_ids);
        $merchant_services = MerchantService::where('merchant_id', auth('merchant-api')->user()->id)->get();
        foreach($merchant_services as $merchant_service){
            $merchant_service->delete();
        }

        foreach($services_ids as $services_id){
            $new_merchant_service = new MerchantService();
            $new_merchant_service->service_id = $services_id;
            $new_merchant_service->merchant_id = auth('merchant-api')->user()->id;
            $new_merchant_service->save();
        }
        return response()->json('Services updated!', 200);
    }
}