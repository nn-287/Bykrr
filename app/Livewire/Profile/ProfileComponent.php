<?php

namespace App\Livewire\Profile;

use App\Model\CustomerAddress;
use App\User;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use Livewire\Component;
use Livewire\WithFileUploads;

class ProfileComponent extends Component
{
    use WithFileUploads;

    public $user;
    public $user_address;
    
    public $f_name;
    public $l_name;
    public $email;
    public $phone;
    public $address;
    public $file;

    public $successMessage = '';
    public $isDialogOpen = false;

    public function mount(){
        $this->user = User::where('id', auth()->user()->id)->first();
        $user_address = CustomerAddress::where('user_id', $this->user->id)->first();

        $this->f_name = $this->user->f_name;
        $this->l_name = $this->user->l_name;
        $this->email = $this->user->email;
        $this->phone = $this->user->phone;
        $this->address = $user_address->address;
    }

    public function updateSettings(){
        $this->validate([
            'f_name' => 'required|min:3',
            'l_name' => 'required|min:3',
            'email' => 'required|email',
            'phone' => 'required',
            'address' => 'required',
        ], [
            'f_name.required' => 'First name is required!',
            'l_name.required' => 'Last name is required!',
        ]);

        $this->user = User::where('id', auth()->user()->id)->first();
        $this->user_address = CustomerAddress::where('user_id', auth()->user()->id)->first();

        if (!empty($this->file)) {
            $image_name = Carbon::now()->toDateString() . "-" . uniqid() . "." . 'png';
            
            if (!Storage::disk('public')->exists('user')) {
                Storage::disk('public')->makeDirectory('user');
            }
            if (Storage::disk('public')->exists('user/' . $this->user['image'])) {
                Storage::disk('public')->delete('user/' . $this->user['image']);
            }
            $note_img = Image::make($this->file)->stream();
            Storage::disk('public')->put('user/' . $image_name, $note_img);
        } else {
            $image_name = $this->user->image;
        }
        
        $this->user->f_name = $this->f_name;
        $this->user->l_name = $this->l_name;
        $this->user->email = $this->email;
        $this->user->phone = $this->phone;
        $this->user->image = $image_name;
        $this->user->save();

        $this->user_address->address = $this->address;
        $this->user_address->save();

        $this->user = User::find(auth()->user()->id);
       $this->successMessage = "Profile Update Successfuly";
    }

    
    public function deleteImage(){
        $this->user = User::where('id', auth()->user()->id)->first();
        Storage::delete('public/user/' . $this->user->image);
        $this->user->image = null;
        $this->user->save();

        $this->user = User::where('id', auth()->user()->id)->first();
    }


    public function removeAccount(){
        // $this->user = User::find(auth()->user()->id);
        // $this->user->delete();
        redirect()->route('login');
    }

    public function dialog()
    {
        $this->isDialogOpen = true;
    }

    public function reloadPage()
    {
        return redirect()->to(route('profile.profile'));
    }

    public function render()
    {
        return view('livewire.profile.profile-component')->layout('livewire.layouts.base');
    }
    
}






