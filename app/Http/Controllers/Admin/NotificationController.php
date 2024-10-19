<?php

namespace App\Http\Controllers\Admin;

// use App\CentralLogics\Helpers;
use App\Http\Controllers\Controller;
use App\Model\Notification;
use App\Model\ChatNotification;
use App\Model\Conversation;
use App\Model\ConversationService;
use Brian2694\Toastr\Facades\Toastr;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use App\User;

class NotificationController extends Controller
{
    function index()
    {
        $notifications = Notification::latest()->paginate(20);
        return view('admin-views.notification.index', compact('notifications'));
    }
    
      function filter_index()
    {
        $notifications = Notification::latest()->paginate(20);
        return view('admin-views.notification.specific-index', compact('notifications'));
    }
    
      function chat_index($user_id)
    {
        $notifications = Notification::latest()->paginate(20);
        $user = User::whereId($user_id)->first();
        $user_name = $user->f_name;
        $user_message = Conversation::where('user_id',$user_id)->latest()->first();
        $last_message = $user_message->reply;
        return view('admin-views.notification.chat-index', compact('notifications', 'user_id', 'user_name', 'last_message'));
    }
    
        function services_chat_index($user_id)
    {
        $notifications = Notification::latest()->paginate(20);
        $user = User::whereId($user_id)->first();
        $user_name = $user->f_name;
        $user_message = ConversationService::where('user_id',$user_id)->latest()->first();
        $last_message = $user_message->reply;
        return view('admin-views.notification.chat-index', compact('notifications', 'user_id', 'user_name', 'last_message'));
    }

    public function store(Request $request)
    {
      
        $request->validate([
            'title' => 'required',
            'description' => 'required'
        ], [
            'title.required' => 'title is required!',
        ]);

        if (!empty($request->file('image'))) {
            $image_name = Carbon::now()->toDateString() . "-" . uniqid() . "." . 'png';
            if (!Storage::disk('public')->exists('notification')) {
                Storage::disk('public')->makeDirectory('notification');
            }
            $note_img = Image::make($request->file('image'))->stream();
            Storage::disk('public')->put('notification/' . $image_name, $note_img);
        } else {
            $image_name = null;
        }

        $notification = new Notification;
        $notification->title = $request->title;
        $notification->description = $request->description;
        $notification->image = $image_name;
        $notification->status = 1;
        $notification->save();
   

  
        try {
          //  Helpers::send_push_notif_to_topic($notification);
         
        } catch (\Exception $e) {
          //  Toastr::warning('Push notification failed!');
        }

        Toastr::success('Notification sent successfully!');
       return back();
      
    }
    
     public function store_msg_notification(Request $request)
    {
      
        $request->validate([
            'title' => 'required',
            'description' => 'required'
        ], [
            'title.required' => 'title is required!',
        ]);

        if (!empty($request->file('image'))) {
            $image_name = Carbon::now()->toDateString() . "-" . uniqid() . "." . 'png';
            if (!Storage::disk('public')->exists('notification')) {
                Storage::disk('public')->makeDirectory('notification');
            }
            $note_img = Image::make($request->file('image'))->stream();
            Storage::disk('public')->put('notification/' . $image_name, $note_img);
        } else {
            $image_name = null;
        }

        $notification = new ChatNotification;
        $notification->title = $request->title;
        $notification->description = $request->description;
        $notification->image = $image_name;
        $notification->status = 1;
        $notification->save();
        
        $user_id = $request->user_id;
        
         $user = User::whereId($user_id)->first();
         $user_fcm = $user->cm_firebase_token;
  
        try {
            //Helpers::new_chat_message($notification, $user_fcm);
         
        } catch (\Exception $e) {
            //Toastr::warning('Push notification failed!');
        }

        Toastr::success('Notification sent successfully!');
       return back();
      
    }

 public function notifyFilter(Request $request)
    {
      
        $request->validate([
            'title' => 'required',
            'description' => 'required'
        ], [
            'title.required' => 'title is required!',
        ]);

        if (!empty($request->file('image'))) {
            $image_name = Carbon::now()->toDateString() . "-" . uniqid() . "." . 'png';
            if (!Storage::disk('public')->exists('notification')) {
                Storage::disk('public')->makeDirectory('notification');
            }
            $note_img = Image::make($request->file('image'))->stream();
    
            $img_link =  Storage::disk('public')->put('notification/' . $image_name, $note_img);
          
           
        } else {
            $image_name = null;
        }

        $notification = new Notification;
        $notification->title = $request->title;
        $notification->description = $request->description;
       // $notification->topic = $request->topic;
        $notification->image = $image_name;
        $notification->status = 1;
        $notification->save();
        
        $topic = $request->topic;
        $search = $request->search;
        
        $filterId = User::where('answer_hair', ('black')) -> get();
        $fcm_token = $filterId-> transform(function ($value, $key){
        return $value['cm_firebase_token'];
      });

  
        try {
          //  Helpers::send_push_notif_to_topic($notification);
          // Helpers:: send_notif_to_filtered_topic($notification, $topic, $search);
        } catch (\Exception $e) {
            Toastr::warning('Push notification failed!');
        }

        Toastr::success('Notification sent successfully!');
       return back();
      
    }

    public function edit($id)
    {
        $notification = Notification::find($id);
        return view('admin-views.notification.edit', compact('notification'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required',
            'description' => 'required',
        ], [
            'title.required' => 'title is required!',
        ]);

        $notification = Notification::find($id);

        if (!empty($request->file('image'))) {
            $image_name = Carbon::now()->toDateString() . "-" . uniqid() . "." . 'png';
            if (!Storage::disk('public')->exists('notification')) {
                Storage::disk('public')->makeDirectory('notification');
            }
            if (Storage::disk('public')->exists('notification/' . $notification['image'])) {
                Storage::disk('public')->delete('notification/' . $notification['image']);
            }
            $note_img = Image::make($request->file('image'))->stream();
            Storage::disk('public')->put('notification/' . $image_name, $note_img);
        } else {
            $image_name = $notification['image'];
        }

        $notification->title = $request->title;
        $notification->description = $request->description;
        $notification->image = $image_name;
        $notification->save();
        Toastr::success('Notification updated successfully!');
        return back();
    }

    public function status(Request $request)
    {
        $notification = Notification::find($request->id);
        $notification->status = $request->status;
        $notification->save();
        Toastr::success('Notification status updated!');
        return back();
    }

    public function delete(Request $request)
    {
        $notification = Notification::find($request->id);
        if (Storage::disk('public')->exists('notification/' . $notification['image'])) {
            Storage::disk('public')->delete('notification/' . $notification['image']);
        }
        $notification->delete();
        Toastr::success('Notification removed!');
        return back();
    }
   public function userFilter()
    {
     
       
       $usersnotify = User::where('answer_hair', ('oilyhairlossblonde')) -> paginate(5) -> appends('answer_hair', ('oilyhairlossblonde'));
   
        
        

              
    // return $users;
    return view('admin-views.notification.specific')->with('usersnotify', $usersnotify);
   // return view('admin-views.notification.specific', compact('users'));
      
    }
    
    public function userFilterId()
    {
     
      $filterId = User::where('answer_hair', ('dryhairlossblonde')) -> get();
      $resultOfFilter = $filterId-> transform(function ($value, $key){
          return $value['cm_firebase_token'];
      });
      return $resultOfFilter;
      
    }
}
