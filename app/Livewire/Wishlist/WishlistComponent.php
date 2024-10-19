<?php

namespace App\Livewire\Wishlist;

use App\CentralLogics\ProductLogic;
use App\Model\CartProduct;
use App\Model\Product;
use App\Model\Wishlist;
use Illuminate\Http\Request;
use Livewire\Component;

class WishlistComponent extends Component
{
    public $wishlists;
    public $wishlist;
    public $product_category;
    public $wishlist_products_id;
   


    public function mount()
    {
       $this->wishlists = ProductLogic::wishlist_items(auth()->user()->id);
    }

    public function addToWishlist($product_id){
        $wishlist = new Wishlist;
        $wishlist->user_id = auth()->user()->id;
        $wishlist->product_id = $product_id;
        $wishlist->save();
        $this->wishlists = ProductLogic::wishlist_items(auth()->user()->id);
    }

    public function addToCart($product_id){
        $checkProduct = CartProduct::where('user_id', auth()->user()->id)->where('product_id', $product_id)->first();
        if(!$checkProduct){
            $myCart = new CartProduct();
            $myCart->user_id = auth()->user()->id;
            $myCart->product_id = $product_id;
            $myCart->save();
        }
        $this->wishlists = ProductLogic::wishlist_items(auth()->user()->id);
    }

    public function removeFromWishlist($product_id){
       $wishlist = Wishlist::where('product_id', $product_id)->first();
       $wishlist->delete();
       $this->wishlists = ProductLogic::wishlist_items(auth()->user()->id);
    }

    public function render()
    {
        return view('livewire.wishlist.wishlist-component')->layout('livewire.layouts.base');
        
    }

}

