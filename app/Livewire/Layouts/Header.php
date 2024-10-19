<?php

namespace App\Livewire\Layouts;

use Livewire\Component;

class Header extends Component
{
    public $searchQuery;
    
    public function submitSearch()
    {
        // Emit an event to pass the search query to the parent component
        $this->emit('searchQueryUpdated', $this->searchQuery);
    }

    public function render()
    {
        return view('livewire.layouts.header');
    }
}
