<?php

namespace App\Livewire\NewOrder;

use App\Model\Order;
use Livewire\Component;

class NewOrderComponent extends Component
{
    public $orders;
    public $order;
    public $search;

    public function mount() {
        $status_list = ['pending', 'confirmed'];
        $this->orders = Order::whereIn('order_status', $status_list)->where('user_id', auth()->user()->id)->get(); 
    }

    public function canceled_order($order_id) {
        $this->order = Order::where('id', $order_id)->first();
        $this->order->order_status = 'canceled';
        $this->order->save();
        $this->orders = Order::where('user_id', auth()->user()->id)->get();
    }

    public function render()
    {
        return view('livewire.new-order.new-order-component')->layout('livewire.layouts.base');
    }


    
    public function search() {
       $searched_items = Product::where-> query
        'name' like $search
    }
}
