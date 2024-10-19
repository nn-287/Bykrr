<?php

namespace App\Livewire\Auth;


use Livewire\Component;

class LoginComponent extends Component
{

    public $email;
    public $password;
    public $errorMessage;

    public function mount() {
      
        $this->fill(['email' => 'user@user.com', 'password' => '123456']);    
    }

    public function render()
    {
        return view('livewire.auth.login')->layout('livewire.layouts.base');
    }


    public function submit()
    {
        if (auth()->attempt(['email' => $this->email, 'password' => $this->password])) {
            return redirect()->route('home');
         }else{
            dd('failed');
         }

    }
    
}
