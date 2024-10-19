<?php

namespace App\Livewire\Product;

use App\CentralLogics\ProductLogic;
use App\Model\CartProduct;
use App\Model\Category;
use App\Model\Product;
use App\Model\Wishlist;
use Livewire\Component;

class DetailsComponent extends Component
{
    public $product;
    public $similar_products;
    public $categories;
    public $choice_options;
    public $selected_option;
    public $price;
    public $selected_option_new = [];
    public $selected_options_array = [];
    public $quantity = 1;
    public $variation_index = [];
    public $show_cart_success_dialog = false;

    public function mount($id)
    {
        $product = Product::with('rating')->find($id);

        $this->product = $product;
        $category_ids = json_decode($product->category_ids, true);

        $cate_ids_arr = [];
        foreach ($category_ids as $category_id) {
            array_push($cate_ids_arr, $category_id['id']);
        }
        $cate_ids_arr = [];
        foreach ($category_ids as $category_id) {
            $cate_ids_arr[] = $category_id['id'];
        }

        $similar_products = Product::active()
            ->where(function ($query) use ($cate_ids_arr) {
                foreach ($cate_ids_arr as $cate_id) {
                    $query->orWhereJsonContains('category_ids', ['id' => $cate_id]);
                }
            })
            ->where('id', '!=', $product->id)
            ->take(10)
            ->get();

        $this->similar_products = $similar_products;

        $this->categories = Category::whereIn('id', $cate_ids_arr)->get();
        $this->choice_options = json_decode($product->choice_options, true);

        $arr = [];
        foreach ($this->choice_options as $choice_option) {
            array_push($arr, $choice_option['options'][0]);
        }

        if ($this->selected_options_array == []) {
            $this->selected_options_array = $arr;
            $combinedString = implode('-', $arr);
        }

        $variationCombinedString = ProductLogic::get_product_variation($product);

        /// variation
        
        $this->variation_index = [];
        foreach (json_decode($this->product->choice_options, true) as $choice_option) {
            array_push($this->variation_index, 0);
        }


        // PRICE CALCULATIONS
        $this->price = $product->price * $this->quantity;

        foreach (json_decode($product->variations, true) as $variation) {
            if ($variation['type'] == $variationCombinedString) {
                $this->price = $variation['price'];
            }
        }


        // result here like ['cheese', 'small'], convert it to 'cheese-small'
    }

    public function render()
    {
        return view('livewire.product.details')->layout('livewire.layouts.base');
    }
    // this one also


    public function addToWhishlist($product_id)
    {
        ProductLogic::addToWhishlist(auth()->user()->id, $product_id); 
    }

    public function handleSelection($key_1, $selectedValue)
    {  
        $variation_key = 0;
        $choice_options = json_decode($this->product->choice_options, true);

        foreach ($choice_options[$key_1]['options'] as $key => $option) {
            if ($option == $selectedValue) {
                $variation_key = $key;
                break;
            }
        }
        // $this->variation_index = [1, 1];
        // Update the value at the specified index
        $this->variation_index[$key_1] = $variation_key;

        $this->selected_options_array[$key_1] = $selectedValue;

        $combinedString = implode('-', $this->selected_options_array);

        foreach (json_decode($this->product->variations, true) as $variation) {
            if ($variation['type'] == $combinedString) {
                $this->price = $variation['price'];
            }
        }
    }

    public function increaseQuantity()
    {
        $this->quantity++;
    }

    public function decreaseQuantity()
    {
        if ($this->quantity > 1) {
            $this->quantity--;
        }
    }

    public function addToCart()
    {
        $cart = new CartProduct();

        $cart->user_id = auth()->user()->id;
        $cart->product_id = $this->product->id;
        $cart->quantity = $this->quantity;
        $cart->variation_index = json_encode($this->variation_index, true);
        $cart->save();
        $this->show_cart_success_dialog = true;
    }

    public function buyNow()
    {
        $cart = new CartProduct();

        $cart->user_id = auth()->user()->id;
        $cart->product_id = $this->product->id;
        $cart->quantity = $this->quantity;
        $cart->variation_index = json_encode($this->variation_index, true);
        $cart->save();
        $this->show_cart_success_dialog = true;

         return redirect('/order');
    }
}
