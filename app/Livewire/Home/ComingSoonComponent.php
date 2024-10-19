<?php

namespace App\Livewire\Home;

use Livewire\Component;

class ComingSoonComponent extends Component
{

    public function mount(){
    }

    public function render()
    {
        // return view('livewire.designs.tracking-design')->layout('livewire.layouts.base');
       return view('livewire.designs.products-list')->layout('livewire.layouts.base');
       // return view('livewire.home.coming-soon')->layout('livewire.layouts.base');
    }
   
}
