<?php

namespace App\Livewire\Cart;

use App\CentralLogics\CartLogic;
use App\CentralLogics\CouponLogic;
use App\CentralLogics\Helpers;
use App\CentralLogics\OrderLogic;
use App\CentralLogics\ProductLogic;
use App\Model\CartProduct;
use App\Model\Category;
use App\Model\CustomerAddress;
use App\Model\Product;
use Livewire\Component;
use App\Model\Zone;
use MatanYadaev\EloquentSpatial\Objects\Point;

class CartComponent extends Component
{

    public $cart_products;
    public $current_id = 0;
    public $coupon;
    public $coupon_response;
    public $coupon_code;
    public $coupon_discount;
    public $selected_address;
    public $showDialog = false;
    public $show_address_list = false;
    public $address_list;
    public $zone;
    public $recommended_products;

    public function mount(){
        $this->cart_products = CartLogic::cart_list(auth()->user()->id);

        $category_ids = [];
        foreach($this->cart_products as $cart_product){
           $product = Product::find($cart_product->product_id);
           $categories = json_decode($product->category_ids, true);
           foreach($categories as $category){
            array_push($category_ids, $category['id']);
           }
        }

        $category_ids = array_unique($category_ids);


        $this->recommended_products = Product::active()
            ->where(function ($query) use ($category_ids) {
                foreach ($category_ids as $cate_id) {
                    $query->orWhereJsonContains('category_ids', ['id' => $cate_id]);
                }
            })
            ->take(10)
            ->get();

        $this->selected_address = CustomerAddress::where('user_id', auth()->user()->id)->first();
        $this->address_list = CustomerAddress::where('user_id', auth()->user()->id)->get();

        foreach($this->address_list as $address){
            $zone = Helpers::getZone($address);
            if($zone != null){
                $this->selected_address = $address;
                $this->zone = $zone;
            }
        }

        $cart_quantities= [];
        foreach($this->cart_products as $cart){
            $cart_quantities['id'] = $cart->id;
            $cart_quantities['quantity'] = $cart->quantity;
        }
    }

    public function render()
    {
         return view('livewire.cart.cart')->layout('livewire.layouts.base');
    }

    public function increaseQuantity($id)
    {
        $this->current_id = $id;

        $cart_product = CartProduct::find($id);
        $cart_product->quantity = $cart_product->quantity + 1;
        $cart_product->save();
        
        $this->cart_products = CartLogic::cart_list(auth()->user()->id);
    }

    public function decreaseQuantity($id)
    {
        $this->current_id = $id;
        $cart_product = CartProduct::find($id);
        if ($cart_product->quantity > 1) {
        $cart_product->quantity = $cart_product->quantity - 1;
        $cart_product->save();
        $this->cart_products = CartLogic::cart_list(auth()->user()->id);
        }
    }

    public function addToWhishlist($product_id)
    {
        ProductLogic::addToWhishlist(auth()->user()->id, $product_id);
    }

    public function showAddressList()
    {
        $this->show_address_list = !$this->show_address_list;
    }

    public function removeCart($cart_id)
    {
        CartProduct::where('id', $cart_id)->delete();
        $this->cart_products = CartLogic::cart_list(auth()->user()->id);
    }

    public function apply()
    {
        
        $coupon = CouponLogic::apply(auth()->user()->id, $this->coupon_code);
        if($coupon == 'limit_exceeded'){
            $this->coupon_response['status'] = 'failed';
            $this->coupon_response['message'] = 'Coupon code usage limit is over for you!';
        }else if($coupon == 'not_found'){
            $this->coupon_response['status'] = 'failed';
            $this->coupon_response['message'] = 'Not found!';
        }else {
           $this->coupon_response['status'] = 'success';
           $this->coupon_response['message'] = 'Coupon applied!';
           $this->coupon = $coupon;
          
        }
    }

    public function showDialog()
    {
        $this->showDialog = true;
    }

    public function selectAddress($id)
    {
        $selected_address = CustomerAddress::find($id);
        $this->selected_address = $selected_address;

        $POINT_SRID = 0; // For MariaDB use 4326
        $point = new Point($selected_address->latitude, $selected_address->longitude, $POINT_SRID);
     
        $zone = Zone::active()->whereContains('coordinates', $point)->first();
        if($zone!=null){
          $this->zone = $zone;
        }else{
            $this->zone = null;
        }
    }

    public function getZone($id)
    {
        $selected_address = CustomerAddress::find($id);
        $this->selected_address = $selected_address;

        $zone = Helpers::getZone($selected_address);
      
        if($zone!=null){
          $this->zone = $zone;
        }else{
            $this->zone = null;
        }
    }

    public function placeOrder()
    {
        $price_info = ProductLogic::calculateCartPrice(auth()->user()->id);
        $price_with_discount = $price_info['total_price'] - $price_info['total_discount_amount'];
        $coupon_discount_amount = 0;
        if($this->coupon!=null){
            $coupon_discount_amount = CouponLogic::calculateCouponDiscount($price_with_discount, $this->coupon);
        }
        
        $delivery_charge = $this->zone !=null? $this->zone->delivery_fee:null; 
        $zone_id = $this->zone !=null? $this->zone->id:null; 
        $delivery_address_id = $this->selected_address->id; 

        $final_price = $price_info['total_price'] - $price_info['total_discount_amount'] - $coupon_discount_amount + $delivery_charge;

        $o_id = OrderLogic::place_order(
                auth()->user()->id, 
                $final_price,
                $coupon_discount_amount,
                null,
                $this->coupon_code,
                'cash_on_delivery',
                '',
                'cart',
                $delivery_address_id,
                $delivery_charge, 
                $zone_id,
                $this->cart_products
            );
           // CartLogic::empty_cart(auth()->user()->id);
            return redirect('/order/success', [$o_id]);
    }
}
