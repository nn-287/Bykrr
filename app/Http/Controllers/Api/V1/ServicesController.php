<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\Service;
use Illuminate\Support\Facades\Log;

class ServicesController extends Controller
{
    public function get_services()
    {
        try 
        {
            $services = Service::where('status', 1)->get();
            return response()->json($services, 200);
        } 
        catch (\Exception $e) 
        {
           
            Log::error('Error fetching services: ' . $e->getMessage());
            return response()->json([], 200);
        }
    }


    public function get_service($id)
    {
        try 
        {
            $category = Service::where('id', $id)->first();
            return response()->json($category, 200);
        } 
        catch (\Exception $e) 
        {
            return response()->json([
                'errors' => ['code' => 'services-001', 'message' => 'service not found!']
            ], 404);
        }
    }
}
