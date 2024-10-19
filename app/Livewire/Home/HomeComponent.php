<?php

namespace App\Livewire\Home;

use App\Model\Banner;
use App\Model\Category;
use App\Model\Product;
use App\Model\Wishlist;
use Livewire\Component;

class HomeComponent extends Component
{

    public $categories;
    public $banners;
    public $selected_banner_id;
    public $selected_banner;
    public $featured_products;
    public $selected_category;
    public $selected_category_id = 0;
    public $selected_products;
    public $hovered_product_id;

    public function mount()
    {
        $this->categories = Category::where(['position' => 0, 'status' => 1])->get();
        $this->banners = Banner::active()->where('banner_position', 'main')->get();
        $this->selected_banner_id = Banner::active()->first()->id;
        $this->selected_banner = Banner::active()->first();
        $this->selected_products = Product::featured()->get();

        //$this->selected_category = Category::where(['position' => 0, 'status' => 1])->first();

       
     //   $this->initializeSelectedProducts($categoryId);
      
    }

    public function initializeSelectedProducts($categoryId)
    {
        // Fetch selected products based on the selected category
        $this->selected_products = Product::active()
            ->whereJsonContains('category_ids', ['id' => 297])
            ->get();
    }

    public function render()
    {
        return view('livewire.home.home-component')->layout('livewire.layouts.base');
    }
   
    public function updateHoveredProduct($id)
    {
        $this->hovered_product_id = $id;
    }
    
    
    public function updateSelectedCategory($id)
    {
        $this->selected_category_id = $id;
        if($id == 0){
            $this->selected_products = Product::featured()->get();
        }else{
            $this->selected_category = Category::find($id);

            $this->selected_products = Product::active()
            ->whereJsonContains('category_ids', ['id' => $id])
            ->get();  
        }
    }


    public function updateSelectedBanner($id)
    {
        $this->selected_banner_id = $id;
        $this->selected_banner = Banner::find($id);
    }



    public function addToWhishlist($product_id)
    {
        $wishlist = Wishlist::where('user_id', auth()->user()->id)->where('product_id', $product_id)->first();
        if ($wishlist) {
            Wishlist::where('user_id', auth()->user()->id)->where('product_id', $product_id)->delete();
        } else {
            $wishlist = new Wishlist;
            $wishlist->user_id = auth()->user()->id;
            $wishlist->product_id = $product_id;
            $wishlist->save();
        }
    }

    public function showModal()
    {
        $this->emit('show-modal', ['title' => 'My Modal']);
    }
}
