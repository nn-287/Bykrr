<?php

namespace App\Livewire\Profile;

use App\User;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Livewire\Component;

class ChangePasswordComponent extends Component
{
    public $user;
    public $password;
    public $confirm_password;

    public $successMessage='';

    public function updatePassword(){
        $this->validate([
            'password' => 'required|same:confirm_password|min:6',
            'confirm_password' => 'required|min:6',
        ]);

        $this->user = User::find(auth()->user()->id);
        $this->user->password = bcrypt($this->password);
        $this->user->save();
        $this->successMessage = "Profile Update Successfuly";
    }

    public function render()
    {
        return view('livewire.profile.change-password-component')->layout('livewire.layouts.base');
    }
}
