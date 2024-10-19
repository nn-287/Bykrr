<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Model\MerchantService;
use App\Model\Service;
use Brian2694\Toastr\Facades\Toastr;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class ServiceController extends Controller
{
    function index()
    {
        $services=Service::latest()->paginate(10);
        return view('admin-views.service.index',compact('services'));
    }

    function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
        ], [
            'name.required' => 'Name is required!',
        ]);

        if (!empty($request->file('image'))) {
            $image_name = Carbon::now()->toDateString() . "-" . uniqid() . "." . 'png';
            if (!Storage::disk('public')->exists('service')) {
                Storage::disk('public')->makeDirectory('service');
            }
            $note_img = Image::make($request->file('image'))->stream();
            Storage::disk('public')->put('service/' . $image_name, $note_img);
        } else {
            $image_name = 'def.png';
        }

        $service = new Service();
        $service->name = $request->name;
        $service->image = $image_name;
        $service->save();
        return back();
    }

    public function edit($id)
    {
        $service = Service::find($id);
        return view('admin-views.service.edit', compact('service'));
    }

    public function status(Request $request)
    {
        $service = Service::find($request->id);
        $service->status = $request->status;
        $service->save();
        Toastr::success('Service status updated!');
        return back();
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required',
        ], [
            'name.required' => 'Name is required!',
        ]);
        $service = Service::find($id);
        if (!empty($request->file('image'))) {
            $image_name = Carbon::now()->toDateString() . "-" . uniqid() . "." . 'png';
            if (!Storage::disk('public')->exists('service')) {
                Storage::disk('public')->makeDirectory('service');
            }
            if (Storage::disk('public')->exists('service/' . $service['image'])) {
                Storage::disk('public')->delete('service/' . $service['image']);
            }
            $note_img = Image::make($request->file('image'))->stream();
            Storage::disk('public')->put('service/' . $image_name, $note_img);
        } else {
            $image_name = $service['image'];
        }

        $service->name = $request->name;
        $service->image = $image_name;
        $service->save();
        Toastr::success('Service updated successfully!');
        return back();
    }

    public function delete(Request $request)
    {
        $service = Service::find($request->id);
        $merchant_services = MerchantService::where('service_id', $service->id)->get();
        foreach($merchant_services as $merchant_service){
            $merchant_service->delete();
        }
        $service->delete();
        Toastr::success('Service removed!');
        
        return back();
    }
}
