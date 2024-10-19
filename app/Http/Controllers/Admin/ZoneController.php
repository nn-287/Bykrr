<?php

namespace App\Http\Controllers\Admin;

use Exception;
use App\Model\Zone;
use App\Exports\ZoneExport;
use Illuminate\Http\Request;
use App\CentralLogics\Helpers;
use App\Http\Controllers\Controller;
use Brian2694\Toastr\Facades\Toastr;
use Maatwebsite\Excel\Facades\Excel;
use Rap2hpoutre\FastExcel\FastExcel;
use Illuminate\Support\Facades\Validator;
use MatanYadaev\EloquentSpatial\Objects\Point;
use MatanYadaev\EloquentSpatial\Objects\Polygon;
use MatanYadaev\EloquentSpatial\Objects\LineString;

class ZoneController extends Controller
{
    public function index(Request $request)
    {
        $key = explode(' ', $request['search'] ?? null );
        $zones = Zone::when(isset($key), function($query)use($key){
            $query->where( function ($q) use ($key) {
                foreach ($key as $value) {
                    $q->orWhere('name', 'like', "%{$value}%");
                }
            });
        })
        ->latest()->paginate(config('default_pagination'));
        return view('admin-views.zone.index', compact('zones'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:zones|max:191',
            'coordinates' => 'required',
        ]);


        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)]);
        }
        $value = $request->coordinates;
        foreach(explode('),(',trim($value,'()')) as $index=>$single_array){
            if($index == 0)
            {
                $lastcord = explode(',',$single_array);
            }
            $coords = explode(',',$single_array);
            $polygon[] = new Point($coords[0], $coords[1]);
        }
        $zone_id=Zone::all()->count() + 1;
        $polygon[] = new Point($lastcord[0], $lastcord[1]);
        $zone = new Zone();
        $zone->name = $request->name;
        $zone->coordinates = new Polygon([new LineString($polygon)]);
        $zone->delivery_fee = $request->delivery_fee ?? 0;
        $zone->save();

        Toastr::success('Zone saved successfully');
        return back();
    }

    public function list()
    {
        $zones = Zone::latest()->paginate(10);
        return view('admin-views.zone.list', compact(['zones']));
    }

    public function edit($id)
    {
        if(env('APP_MODE')=='demo' && $id == 1)
        {
            Toastr::warning('you_can_not_edit_this_zone_please_add_a_new_zone_to_edit');
            return back();
        }
        $zone=Zone::selectRaw("*,ST_AsText(ST_Centroid(`coordinates`)) as center")->findOrFail($id);
        $area = json_decode($zone->coordinates[0]->toJson(),true);
        return view('admin-views.zone.edit', compact(['zone','area']));
    }
    public function zone_settings($id)
    {
        if(env('APP_MODE')=='demo' && $id == 1)
        {
            Toastr::warning('messages.you_can_not_edit_this_zone_please_add_a_new_zone_to_edit');
            return back();
        }
        $zone=Zone::with('incentives')->selectRaw("*,ST_AsText(ST_Centroid(`coordinates`)) as center")->findOrFail($id);
        return view('admin-views.zone.settings', compact('zone'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|max:191|unique:zones,name,'.$id,
            'coordinates' => 'required',
        ]);

        $value = $request->coordinates;
        foreach(explode('),(',trim($value,'()')) as $index=>$single_array){
            if($index == 0)
            {
                $lastcord = explode(',',$single_array);
            }
            $coords = explode(',',$single_array);
            $polygon[] = new Point((float)$coords[0], (float)$coords[1]);
        }
        $polygon[] = new Point((float)$lastcord[0], (float)$lastcord[1]);
        $zone=Zone::findOrFail($id);
        $zone->name = $request->name;
        
        $zone->save();

        try {
            $zone->coordinates = new Polygon([new LineString($polygon)]);
            $zone->name = $request->name;
            $zone->delivery_fee = $request->delivery_fee;
            $zone->save();
        } catch (\Exception $exception) {

        }

        Toastr::success('Zone_updated_successfully');
        return back();
    }

    public function zone_settings_update(Request $request, $id){
        $request->validate([
            'delivery_fee'=>'required|numeric|between:0.001,999999999999.99',
            ], [
                'increased_delivery_fee.required_if' => 'delivery fee field is required'
            ]);


        $zone=Zone::findOrFail($id);
       
        $zone->delivery_fee = $request->delivery_fee ?? 0;
        $zone->save();
        Toastr::success('Zone settings_updated_successfully');
        return back();
    }

    public function destroy(Zone $zone)
    {
        if(env('APP_MODE')=='demo' && $zone->id == 1)
        {
            Toastr::warning('messages.you_can_not_delete_this_zone_please_add_a_new_zone_to_delete');
            return back();
        }
        $zone->delete();
        Toastr::success('messages.zone_deleted_successfully');
        return back();
    }

    public function status(Request $request)
    {
        if(env('APP_MODE')=='demo' && $request->id == 1)
        {
            Toastr::warning('Sorry!You can not inactive this zone!');
            return back();
        }
        $zone = Zone::findOrFail($request->id);
        $zone->status = $request->status;
        $zone->save();
        Toastr::success('Status Updated');
        return back();
    }

    // public function search(Request $request){
    //     $key = explode(' ', $request['search']);
    //     $zones=Zone::withCount(['restaurants','deliverymen'])
    //     ->where(function ($q) use ($key) {
    //         foreach ($key as $value) {
    //             $q->orWhere('name', 'like', "%{$value}%");
    //         }
    //     })->limit(50)->get();
    //     return response()->json([
    //         'view'=>view('admin-views.zone.partials._table',compact('zones'))->render(),
    //         'total'=>$zones->count()
    //     ]);
    // }

    public function get_coordinates($id){
        $zone=Zone::withoutGlobalScopes()->selectRaw("*,ST_AsText(ST_Centroid(`coordinates`)) as center")->findOrFail($id);
        $area = json_decode($zone->coordinates[0]->toJson(),true);
        $data = Helpers::format_coordiantes($area['coordinates']);
        $center = (object)['lat'=>(float)trim(explode(' ',$zone->center)[1], 'POINT()'), 'lng'=>(float)trim(explode(' ',$zone->center)[0], 'POINT()')];
        return response()->json(['coordinates'=>$data, 'center'=>$center]);
    }

    public function zone_filter($id)
    {
        if($id == 'all')
        {
            if(session()->has('zone_id')){
                session()->forget('zone_id');
            }
        }
        else{
            session()->put('zone_id', $id);
        }

        return back();
    }

    public function get_all_zone_cordinates($id = 0)
    {
        $zones = Zone::where('id', '<>', $id)->active()->get();
        $data = [];
        foreach($zones as $zone)
        {
            $area = json_decode($zone->coordinates[0]->toJson(),true);
            $data[] = Helpers::format_coordiantes($area['coordinates']);
        }
        return response()->json($data,200);
    }

}
