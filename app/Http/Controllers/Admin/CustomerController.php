<?php

namespace App\Http\Controllers\Admin;

use App\Model\Services;
use App\Http\Controllers\Controller;
use App\Model\Order;
use App\User;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use App\Model\Food;
use App\Model\ServiceAnsweredQuestions;
use App\Model\ServicesQuestionsAnswers;
use App\Model\ServicesQuestions;
class CustomerController extends Controller
{
    public function customer_list()
    {
        $customers = User::with(['orders'])->latest()->paginate(10);
        return view('admin-views.customer.list', compact('customers'));
    }

    public function search(Request $request){
        $key = explode(' ', $request['search']);
        $customers=User::where(function ($q) use ($key) {
            foreach ($key as $value) {
                $q->orWhere('f_name', 'like', "%{$value}%")
                    ->orWhere('l_name', 'like', "%{$value}%")
                    ->orWhere('email', 'like', "%{$value}%")
                    ->orWhere('phone', 'like', "%{$value}%");
            }
        })->get();
        return response()->json([
            'view'=>view('admin-views.customer.partials._table',compact('customers'))->render()
        ]);
    }

    public function view($id)
    {
        $customer = User::find($id);
        if (isset($customer)) {
            $orders = Order::latest()->where(['user_id' => $id])->paginate(10);

            $serviceData=[];
            $services = Services::get();
            foreach ($services as $service){
                $temp = [];
                $temp['service']=$service->title;
                $tags=[];
                $answers = ServiceAnsweredQuestions::where('service_id',$service->id)->where('customer_id',$id)->get()->pluck('answer_id');
                foreach ($answers as $answer){
                    $an=ServicesQuestionsAnswers::whereId($answer)->first();
                    if(!empty($an)){
                        $ids = explode(',',$an->ids);
                        foreach ($ids as $id1){
                            $tags[] = trim($id1);
                        }
                    }
                }
                $answer_keys = implode(',',$tags);
                $temp['keys']=$answer_keys;
                $serviceData[]=$temp;
            }
            $meals = $customer->meals;
            $user_meals = [];
            foreach($meals as $meal){
                $food = Food::where('id', $meal->food_id)->first();
                $main_meal['food'] = $food;
                $main_meal['meal_type'] = $meal->meal_type;
                $main_meal['quantity'] = $meal->quantity;
                array_push($user_meals, $main_meal);
            }
     
            return view('admin-views.customer.customer-view', compact('customer', 'orders','serviceData', 'user_meals'));
        }
        Toastr::error('Customer not found!');
        return back();
    }
  
}
