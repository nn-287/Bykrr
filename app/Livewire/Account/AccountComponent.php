<?php

namespace App\Livewire\Account;

use App\Model\CustomerAddress;
use App\User;
use Illuminate\Http\Request;
use Livewire\Component;

class AccountComponent extends Component
{
    public $user;
    

    public function mount(){
        $this->user = User::where('id', auth()->user()->id)->first();
    }

    public function addAddress(Request $request , $user_id){
        $new_address = new CustomerAddress();
        $new_address->user_id = $user_id;
        $new_address->address =  $request->address;
        $new_address->save();
        $this->user = User::where('id', auth()->user()->id)->first();
    }

    public function addPaymentMethod(){

    }

    public function render()
    {
        return view('livewire.account.account-component')->layout('livewire.layouts.base');
    }
}
