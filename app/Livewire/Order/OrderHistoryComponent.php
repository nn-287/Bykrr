<?php

namespace App\Livewire\Order;

use App\Model\Order;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

class OrderHistoryComponent extends Component
{
    use WithPagination;

    public function mount(){
        
        
    }
    public function render()
    {
        $status_list = ['delivered', 'canceled'];
        $orders = Order::whereIn('order_status', $status_list)->where('user_id', auth()->user()->id)->paginate(5);
        return view('livewire.order.order-history-component',compact('orders') )->layout('livewire.layouts.base');
    }
}


