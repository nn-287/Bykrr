<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Model\Admin;
use App\Model\EmployeeRole;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class SystemController extends Controller
{
    public function dashboard()
    {
        $roles = EmployeeRole::where('admin_id', auth('admin')->user()->id)->first();
        $admin = Admin::where('id', auth('admin')->user()->id)->first();
        return view('admin-views.dashboard', compact('roles', 'admin'));
    }
    
    public function staff_dashboard()
    {
        return view('admin-views.staff-dashboard');
    }

    public function store_data()
    {
        $new_order = DB::table('orders')->where(['checked' => 0])->count();
        return response()->json([
            'success' => 1,
            'data' => ['new_order' => $new_order]
        ]);
    }

    public function settings()
    {
         return back();
       
    }

    public function settings_update(Request $request)
    {
        
       // Toastr::success('Admin updated successfully!');
        return back();
    }

    public function settings_password_update(Request $request)
    {
        
       // Toastr::success('Admin password updated successfully!');
        return back();
    }
}
