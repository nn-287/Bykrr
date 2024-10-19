<?php

namespace App\Livewire\Order;

use App\Model\Order;
use Livewire\Component;

class NewOrderComponent extends Component
{
    public $orders;
    

    public $isDialogOpen = false;

    public function mount() {
        $status_list = ['pending', 'confirmed', 'on_the_way'];
        $this->orders = Order::whereIn('order_status', $status_list)->where('user_id', auth()->user()->id)->get();
        
    }

    // public function dialog()
    // {
        
    //     $this->isDialogOpen = true;
    // }

    public function canceledOrder($order_id) {
        $orderWillRemove = Order::find($order_id);
        $orderWillRemove->order_status = 'canceled';
        $orderWillRemove->save();
        //$this->isDialogOpen = false;
        return redirect()->route('order.new');
    }

    public function render()
    {
        return view('livewire.order.new-order-component')->layout('livewire.layouts.base');
    }
}
