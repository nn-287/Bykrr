<?php

namespace App\Livewire\Order;

use App\Model\Order;
use Livewire\Component;

class OrderPlacedComponent extends Component
{

    public $order;

    public function mount($id){
        $this->order = Order::find($id);
    }

    public function render()
    {
         return view('livewire.order.order-placed')->layout('livewire.layouts.base');
    }

}
