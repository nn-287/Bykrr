<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Brian2694\Toastr\Facades\Toastr;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use App\User;
use App\Model\AdminNotification;
use Illuminate\Support\Facades\DB;



class AdminNotificationController extends Controller
{
    public function notifications_list(Request $request)
    {
        $search = $request->search;
        
        if($request->search != null){
            $words = explode(" ", $search);
            $words_2 = [];
            
        for ($i = 0; $i < count($words); $i++) {
        $word_2 = strtolower($words[$i]);
         array_push($words_2, $word_2); 
        }
        
        $admin_notifications = AdminNotification::get();
         $search_ids = [];
         foreach($admin_notifications as $notification){
             $title = strtolower($notification->title);
             $description = strtolower($notification->description);
             
             foreach($title as $word){
                if(in_array($word, $words_2)){
                    array_push($search_ids, $notification->id);
                }
            } 
         }
         
         $unique_search_ids = array_unique($search_ids);
         
         $notifications = AdminNotification::whereIn('id', $unique_search_ids)->latest()->paginate(20);
        }else {
            $notifications = AdminNotification::latest()->paginate(20);
        }
        return view('admin-views.admin-notification.list', compact('notifications', 'search'));
    }
    
    public function view($id, $category)
    {
         AdminNotification::where('id', $id)
          ->update([
           'checked' => 1
          ]);
      
     if($category == 'diet_plan'){
         return redirect()->route('admin.nutrition.subscribers.list');
     } else if($category == 'order'){
         return redirect()->route('admin.order.list', ['pending']);
     }  
    }
}