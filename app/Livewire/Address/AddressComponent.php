<?php

namespace App\Livewire\Address;

use App\CentralLogics\ProductLogic;
use App\Model\CustomerAddress;
use Livewire\Component;

class AddressComponent extends Component
{

    public $contact_person_name;
    public $contact_person_number;
    public $address_type;
    public $address_list;

    // public $address;
    // public $coordinates;


    protected $rules = [
        'contact_person_name' => 'required|string',
        'contact_person_number' => 'required|integer',
        'address_type' => 'required' 
    ];

    public function mount()
    {
        $this->address_list = CustomerAddress::where('user_id', auth()->user()->id)->get();
    }

    public function render()
    {
        
        return view('livewire.address.address')->layout('livewire.layouts.base');
    }
   
    public function saveAddress()
    {

        // CALCULATE COORDINATES
        // $coordinates = $this->coordinates;
        // $coordinatesArray = explode(",", $coordinates);
        // $latitude = $coordinatesArray[0];
        // $longitude = $coordinatesArray[1];

         
       // 40.437575998162416, -3.6936270247324647
      //  $this->validate(); 
        $customer_address = new CustomerAddress();
        $customer_address->contact_person_name = $this->contact_person_name;
        $customer_address->contact_person_number = $this->contact_person_number;
        $customer_address->address_type = $this->address_type!=null? $this->address_type : 'Home';
        $customer_address->address = 'Madrid';
        $customer_address->latitude = '40.437575998162416';
        $customer_address->longitude = '-3.6936270247324647';
        $customer_address->save();
        
        return view('livewire.address.address')->layout('livewire.layouts.base');
    }


}
