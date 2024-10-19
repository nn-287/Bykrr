<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Model\Conversation;
use App\Model\ConversationService;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Model\ServiceAnsweredQuestions;
use App\Model\ServicesQuestionsAnswers;
use App\Model\Services;

class ConversationController extends Controller
{
    public function list()
    {
        $conversations = DB::table('conversations')
            ->latest()
            ->get();
        return view('admin-views.messages.index', compact('conversations'));
    }

 public function services_list()
    {
        $conversations = DB::table('conversation_services')
    ->whereIn('id', function ($query) {
        $query->select(DB::raw('MAX(id)'))
            ->from('conversation_services')
            ->groupBy('user_id');
    })
    ->latest()
    ->paginate(10);
          
        return view('admin-views.services-messages.index', compact('conversations'));
    }

    public function view($user_id)
    {
        $convs = Conversation::where(['user_id' => $user_id])->get();
        Conversation::where(['user_id' => $user_id])->update(['checked' => 1]);
        $user = User::find($user_id);
        $get_answers = ServiceAnsweredQuestions::where(['customer_id' => $user_id])->get()->pluck('answer_id');
       
       
            $serviceData=[];
            $services = Services::get();
            foreach ($services as $service){
                $temp = [];
                $temp['service']=$service->title;
                $tags=[];
                $answers = ServiceAnsweredQuestions::where('service_id',$service->id)->where('customer_id',$user_id)->get()->pluck('answer_id');
                foreach ($answers as $answer){
                    $an=ServicesQuestionsAnswers::whereId($answer)->first();
                    if(!empty($an)){
                        $ids = explode(',',$an->answer_desc);
                        foreach ($ids as $id1){
                            $tags[] = trim($id1);
                        }
                    }
                }
                $answer_keys = implode(',',$tags);
                $temp['keys']=$answer_keys;
                $serviceData[]=$temp;
            }

        return response()->json([
            'view' => view('admin-views.messages.partials._conversations', compact('convs', 'user', 'user_id','serviceData'))->render()
        ]);
    }
    
    public function view_refresh($user_id)
    {
        $convs = Conversation::where(['user_id' => $user_id])->get();
        $convs_count = Conversation::where(['user_id' => $user_id])->where('checked', 0)->count();
        
        if($convs_count > 0){
            Conversation::where(['user_id' => $user_id])->update(['checked' => 1]);
        $user = User::find($user_id);
        $get_answers = ServiceAnsweredQuestions::where(['customer_id' => $user_id])->get()->pluck('answer_id');
       
       
            $serviceData=[];
            $services = Services::get();
            foreach ($services as $service){
                $temp = [];
                $temp['service']=$service->title;
                $tags=[];
                $answers = ServiceAnsweredQuestions::where('service_id',$service->id)->where('customer_id',$user_id)->get()->pluck('answer_id');
                foreach ($answers as $answer){
                    $an=ServicesQuestionsAnswers::whereId($answer)->first();
                    if(!empty($an)){
                        $ids = explode(',',$an->answer_desc);
                        foreach ($ids as $id1){
                            $tags[] = trim($id1);
                        }
                    }
                }
                $answer_keys = implode(',',$tags);
                $temp['keys']=$answer_keys;
                $serviceData[]=$temp;
            }

        return response()->json([
            'view' => view('admin-views.messages.partials._conversations', compact('convs', 'user', 'user_id','serviceData'))->render()
        ]);
        }
        
    }

    
     public function services_view($user_id)
    {
        $convs = ConversationService::where(['user_id' => $user_id])->get();
        ConversationService::where(['user_id' => $user_id])->update(['checked' => 1]);
        $user = User::find($user_id);
        $get_answers = ServiceAnsweredQuestions::where(['customer_id' => $user_id])->get()->pluck('answer_id');
       
       
            $serviceData=[];
            $services = Services::get();
            foreach ($services as $service){
                $temp = [];
                $temp['service']=$service->title;
                $tags=[];
                $answers = ServiceAnsweredQuestions::where('service_id',$service->id)->where('customer_id',$user_id)->get()->pluck('answer_id');
                foreach ($answers as $answer){
                    $an=ServicesQuestionsAnswers::whereId($answer)->first();
                    if(!empty($an)){
                        $ids = explode(',',$an->answer_desc);
                        foreach ($ids as $id1){
                            $tags[] = trim($id1);
                        }
                    }
                }
                $answer_keys = implode(',',$tags);
                $temp['keys']=$answer_keys;
                $serviceData[]=$temp;
            }

        return response()->json([
            'view' => view('admin-views.services-messages.partials._conversations', compact('convs', 'user', 'user_id','serviceData'))->render()
        ]);
    }
    
    public function services_view_refresh($user_id)
    {
        $convs = ConversationService::where(['user_id' => $user_id])->get();
        $convs_count = ConversationService::where(['user_id' => $user_id])->where('checked', 0)->count();
        
        if($convs_count > 0){
          ConversationService::where(['user_id' => $user_id])->update(['checked' => 1]);
        $user = User::find($user_id);
        $get_answers = ServiceAnsweredQuestions::where(['customer_id' => $user_id])->get()->pluck('answer_id');
       
       
            $serviceData=[];
            $services = Services::get();
            foreach ($services as $service){
                $temp = [];
                $temp['service']=$service->title;
                $tags=[];
                $answers = ServiceAnsweredQuestions::where('service_id',$service->id)->where('customer_id',$user_id)->get()->pluck('answer_id');
                foreach ($answers as $answer){
                    $an=ServicesQuestionsAnswers::whereId($answer)->first();
                    if(!empty($an)){
                        $ids = explode(',',$an->answer_desc);
                        foreach ($ids as $id1){
                            $tags[] = trim($id1);
                        }
                    }
                }
                $answer_keys = implode(',',$tags);
                $temp['keys']=$answer_keys;
                $serviceData[]=$temp;
            }

        return response()->json([
            'view' => view('admin-views.services-messages.partials._conversations', compact('convs', 'user', 'user_id','serviceData'))->render()
        ]);  
        }
        
    }
    

    public function store(Request $request, $user_id)
    {
        $validator = Validator::make($request->all(), [
            'reply' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([], 403);
        }
        
        $user = User::find($user_id);
        $get_answers = ServiceAnsweredQuestions::where(['customer_id' => $user_id])->get()->pluck('answer_id');
       
       
            $serviceData=[];
            $services = Services::get();
            foreach ($services as $service){
                $temp = [];
                $temp['service']=$service->title;
                $tags=[];
                $answers = ServiceAnsweredQuestions::where('service_id',$service->id)->where('customer_id',$user_id)->get()->pluck('answer_id');
                foreach ($answers as $answer){
                    $an=ServicesQuestionsAnswers::whereId($answer)->first();
                    if(!empty($an)){
                        $ids = explode(',',$an->answer_desc);
                        foreach ($ids as $id1){
                            $tags[] = trim($id1);
                        }
                    }
                }
                $answer_keys = implode(',',$tags);
                $temp['keys']=$answer_keys;
                $serviceData[]=$temp;
            }
      
        DB::table('conversations')->insert([
            'user_id' => $user_id,
            'reply' => $request->reply,
            'checked' => 1,
            'created_at' => now(),
            'updated_at' => now()
        ]);
        $convs = Conversation::where(['user_id' => $user_id])->get();
        $user = User::find($user_id);
        return response()->json([
           // 'view' => view('admin-views.messages.partials._conversations', compact('convs', 'user'))->render()
            'view' => view('admin-views.messages.partials._conversations', compact('convs', 'user', 'user_id','serviceData'))->render()
            
        ]);
    }
    
    
    
    public function services_store(Request $request, $user_id)
    {
        $validator = Validator::make($request->all(), [
            'reply' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([], 403);
        }
        
        $user = User::find($user_id);
        $get_answers = ServiceAnsweredQuestions::where(['customer_id' => $user_id])->get()->pluck('answer_id');
    
            $serviceData=[];
            $services = Services::get();
            foreach ($services as $service){
                $temp = [];
                $temp['service']=$service->title;
                $tags=[];
                $answers = ServiceAnsweredQuestions::where('service_id',$service->id)->where('customer_id',$user_id)->get()->pluck('answer_id');
                foreach ($answers as $answer){
                    $an=ServicesQuestionsAnswers::whereId($answer)->first();
                    if(!empty($an)){
                        $ids = explode(',',$an->answer_desc);
                        foreach ($ids as $id1){
                            $tags[] = trim($id1);
                        }
                    }
                }
                $answer_keys = implode(',',$tags);
                $temp['keys']=$answer_keys;
                $serviceData[]=$temp;
            }

        DB::table('conversation_services')->insert([
            'user_id' => $user_id,
            'reply' => $request->reply,
            'service_id' => $request->service_id,
            'checked' => 1,
            'created_at' => now(),
            'updated_at' => now()
        ]);
        $convs = ConversationService::where(['user_id' => $user_id])->get();
        return response()->json([
         //   'view' => view('admin-views.services-messages.partials._conversations', compact('convs', 'user'))->render()
            
            'view' => view('admin-views.services-messages.partials._conversations', compact('convs', 'user', 'user_id','serviceData'))->render()
        ]);
    }
    
  public function store_product_message(Request $request, $user_id)
    {   
        $validator = Validator::make($request->all(), [
            'product_id' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([], 403);
        }
        
        $user = User::find($user_id);
        $get_answers = ServiceAnsweredQuestions::where(['customer_id' => $user_id])->get()->pluck('answer_id');
 
            $serviceData=[];
            $services = Services::get();
            foreach ($services as $service){
                $temp = [];
                $temp['service']=$service->title;
                $tags=[];
                $answers = ServiceAnsweredQuestions::where('service_id',$service->id)->where('customer_id',$user_id)->get()->pluck('answer_id');
                foreach ($answers as $answer){
                    $an=ServicesQuestionsAnswers::whereId($answer)->first();
                    if(!empty($an)){
                        $ids = explode(',',$an->answer_desc);
                        foreach ($ids as $id1){
                            $tags[] = trim($id1);
                        }
                    }
                }
                $answer_keys = implode(',',$tags);
                $temp['keys']=$answer_keys;
                $serviceData[]=$temp;
            }

        DB::table('conversation_services')->insert([
            'user_id' => $user_id,
            'reply' => 'product',
            'product_id' => $request->product_id,
            'service_id' => $request->service_id,
            'checked' => 1,
            'created_at' => now(),
            'updated_at' => now()
        ]);
        
        $convs = ConversationService::where(['user_id' => $user_id])->get();
   
        return response()->json([
            'view' => view('admin-views.services-messages.partials._conversations', compact('convs', 'user', 'user_id','serviceData'))->render()
        ]);
    }
}
