<?php

namespace App\Http\Controllers\Admin;

use App\CentralLogics\Helpers;
use App\Http\Controllers\Controller;
use App\Model\Order;
use App\Model\OrderDetail;
use App\Model\Coupon;
use App\Model\Branch;
use App\Model\SubBranch;
use App\Model\Invoice;
use App\Model\InvoiceProduct;
use App\Model\Product;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use App\User;
use App\Model\BusinessSetting;

class OrderController extends Controller
{
    public function list($status)
    {
        if (session()->has('branch_filter') == false) {
            session()->put('branch_filter', 0);
        }

        Order::where(['checked' => 0])->update(['checked' => 1]);

        if (session('branch_filter') == 0) {
            if ($status != 'all') {
                $orders = Order::with(['customer', 'branch'])
                    ->latest()->where(['order_status' => $status])
                    ->paginate(25);
            } else {
                $orders = Order::with(['customer', 'branch'])->latest()->paginate(25);
            }
        } else {
            if ($status != 'all') {
                $orders = Order::with(['customer', 'branch'])
                    ->latest()->where(['order_status' => $status, 'branch_id' => session('branch_filter')])
                    ->paginate(25);
            } else {
                $orders = Order::with(['customer', 'branch'])->where(['branch_id' => session('branch_filter')])->latest()->paginate(25);
            }
        }

        return view('admin-views.order.list', compact('orders', 'status'));
    }

    public function details($id)
    {
        $order = Order::with('details')->where(['id' => $id])->first();
        if(isset($order->delivery_address->longitude)){
            $long = $order->delivery_address->longitude;
        }else {
            $long = 0.0;
        }
        
        if(isset($order->delivery_address->latitude)){
            $lat = $order->delivery_address->latitude;
            
        }else {
           $lat = 0.0;
        }
        

        if (isset($order)) {
            return view('admin-views.order.order-view', compact('order'));
        } else {
            Toastr::info('No more orders!');
            return back();
        }
    }

    public function search(Request $request)
    {
        $key = explode(' ', $request['search']);
        $orders = Order::where(function ($q) use ($key) {
            foreach ($key as $value) {
                $q->orWhere('id', 'like', "%{$value}%")
                    ->orWhere('order_status', 'like', "%{$value}%")
                    ->orWhere('transaction_reference', 'like', "%{$value}%");
            }
        })->get();
        return response()->json([
            'view' => view('admin-views.order.partials._table', compact('orders'))->render()
        ]);
    }

    public function status(Request $request)
    {
        $order = Order::find($request->id);
        $user = User::find($order->user_id);

        // if ($order['delivery_man_id'] == null && $request->order_status == 'out_for_delivery') {
        //     Toastr::warning('Please assign delivery man first!');
        //     return back();
        // }
        
        if($request->order_status == 'delivered'){
            $points_value = ($order->order_amount - BusinessSetting::where(['key' => 'delivery_charge'])->first()->value) * BusinessSetting::where(['key' => 'points_value'])->first()->value;
        
        if($order->order_points == 0){
            $user->purchases_points = $user->purchases_points + $points_value;
            $user->save();
         }
        $order->order_points = $points_value;
        }
        
        
        $order->order_status = $request->order_status;
        $order->save();
        $fcm_token = $order->customer->cm_firebase_token;
        
        
        
        $value = Helpers::order_status_update_message($request->order_status);
        
        try {
            if ($value) {
                $data = [
                    'title' => 'Order',
                    'description' => $value,
                    'order_id' => $order['id'],
                    'image' => '',
                ];
                Helpers::send_push_notif_to_device($fcm_token, $data);
            }
        } catch (\Exception $e) {
            Toastr::warning('Push notification failed!');
        }

        Toastr::success('Order status updated!');
        return back();
    }

    public function add_delivery_man($order_id, $delivery_man_id)
    {
        if ($delivery_man_id == 0) {
            return response()->json([], 401);
        }
        $order = Order::find($order_id);
        $order->delivery_man_id = $delivery_man_id;
        $order->save();

        $fcm_token = $order->delivery_man->fcm_token;
        $value = Helpers::order_status_update_message('del_assign');
        try {
            if ($value) {
                $data = [
                    'title' => 'Order',
                    'description' => $value,
                    'order_id' => $order['id'],
                    'image' => '',
                ];
                Helpers::send_push_notif_to_device($fcm_token, $data);
            }
        } catch (\Exception $e) {
        }

        Toastr::success('Order deliveryman added!');
        return response()->json([], 200);
    }

    public function add_branch($order_id, $branch_id)
    {
        if ($branch_id == 0) {
            return response()->json([], 401);
        }

        $order = Order::find($order_id);
        $order->branch_id = $branch_id;
        $order->save();

        Toastr::success('Branch is added!');
        return response()->json([], 200);
    }

    public function add_sub_branch($order_id, $sub_branch_id)
    {
        if ($sub_branch_id == 0) {
            return response()->json([], 401);
        }
        $sub_branch = SubBranch::where('id', $sub_branch_id)->first();
        $branch = Branch::where('id', $sub_branch->branch_id)->first();

        $order = Order::find($order_id);
        $order->branch_id = $branch->id;
        $order->save();

        Toastr::success('Sub Branch is added!');
        
        return response()->json([], 200);
    }

    public function payment_status(Request $request)
    {
        $order = Order::find($request->id);
        if ($request->payment_status == 'paid' && $order['transaction_reference'] == null && $order['payment_method'] != 'cash_on_delivery') {
            Toastr::warning('Add your payment reference code first!');
            return back();
        }
        $order->payment_status = $request->payment_status;
        $order->save();
        Toastr::success('Payment status updated!');
        return back();
    }

    public function update_shipping(Request $request, $id)
    {
        $request->validate([
            'contact_person_name' => 'required',
            'address_type' => 'required',
            'contact_person_number' => 'required',
            'address' => 'required'
        ]);

        $address = [
            'contact_person_name' => $request->contact_person_name,
            'contact_person_number' => $request->contact_person_number,
            'address_type' => $request->address_type,
            'address' => $request->address,
            'longitude' => $request->longitude,
            'latitude' => $request->latitude,
            'created_at' => now(),
            'updated_at' => now()
        ];

        DB::table('customer_addresses')->where('id', $id)->update($address);
        Toastr::success('Payment status updated!');
        return back();
    }

    // public function generate_invoice($id)
    // {
    //     $order = Order::where('id', $id)->first();
    //     $gift = json_decode($order->gift, true);

    //     if ($gift != null) {

    //         if ($gift['product_id'] != 0) {
    //             $giftProduct = Product::where('id', $gift['product_id'])->first();
    //             $gift_discount = 0;
    //         } else {
    //             $giftProduct = '';
    //             $gift_discount = $gift['discount'];
    //         }
    //     } else {
    //         $giftProduct = 0;
    //         $gift_discount = 0;
    //         $giftProduct = null;
    //     }
    //   // var_dump($gift_discount);exit;
    //     return view('admin-views.order.invoice', compact('order', 'gift_discount'));
    // }
    
    public function generate_invoice($id)
    {
        $order = Order::where('id', $id)->first();
        if($order->branch_id == null){
            Toastr::warning('Please assign branch first!');
            return back();
        }
        
        $details = OrderDetail::where('order_id', $order->id)->get();
        
        if(Invoice::where('order_id', $id)->first()){
            $old_invoices = Invoice::where('order_id', $id)->get();
            foreach($old_invoices as $old_invoice){
                $old_invoice->invoice_products()->delete();
                $old_invoice->delete();
            }
        }
        $branch_ids = [];
        foreach($details as $detail){
            if($detail->branch_id != null){
                    $branch_id = $detail->branch_id;
                }else {
                    $branch_id = $order->branch_id;
                }
                if(!in_array($branch_id, $branch_ids)){
                  array_push($branch_ids,$branch_id);  
                }
        }
        foreach($branch_ids as $branch_id){
                $invoice = new Invoice();
                $invoice->order_id = $id;
                $invoice->user_id = $order->user_id;
                $invoice->branch_id = $branch_id;
                $invoice->created_at = now();
                $invoice->updated_at = now();
                $invoice->save();
        }         
        $total_price = 0;   
        $total_product_discount = 0;
                
        $branch_ids = [];
        foreach($details as $detail){
            
                $product_details = json_decode($detail->product_details, true);
                $variation_1 = json_decode($detail->variation, true);
                $variation = $variation_1[0];
                $variation_type = $variation['type'];
                $product_id = $product_details['id'];
                $product_name = $product_details['name'];
                
                $price = 0;   
                $product_discount = 0;
                
                
                
                if($detail->modified_price != null){
                    $price = $detail->modified_price;
                    
                    $total_price += $detail->modified_price;
                }else {
                    
                if($product_details['discount_type'] == 'amount'){
                    $product_discount = $detail['discount_on_product'];
                    $price = $detail['price'];
                    
                    $total_price += $detail['price'];
                    $total_product_discount += $detail['discount_on_product'];
                     
                 }else if($product_details['discount_type'] == 'percent'){
                    $product_discount = ($detail['discount_on_product'] * $detail['price']) / 100;
                    $price = $detail['price'];
                    
                    $total_price += $detail['price'];
                    $total_product_discount += ($detail['discount_on_product'] * $detail['price']) / 100;
                 }
                }
                if($detail->branch_id != null){
                    $branch_id = $detail->branch_id;
                }else {
                    $branch_id = $order->branch_id;
                }
                $saved_invoice = Invoice::where(['id'=> $invoice->id])->first();
                $invoice_product = new InvoiceProduct();
                $invoice_product->invoice_id = $saved_invoice->id;
                $invoice_product->branch_id = $branch_id;
                $invoice_product->product_id = $detail->product_id;
                $invoice_product->name = $product_name;
                $invoice_product->quantity = $detail->quantity;
                $invoice_product->variation_type = $variation_type;
                $invoice_product->price = $price;
                $invoice_product->discount = $product_discount;
                $invoice_product->save();
                
                array_push($branch_ids, $detail->branch_id);
              }
              
            /// AWARDED POINTS
            if($order->order_points == 0){
            $final_price =  $total_price - $total_product_discount;
              
            $points_value = BusinessSetting::where(['key' => 'points_value'])->first()->value;
            $awarded_points = $points_value * $final_price;
        
            $user = User::where('id', $invoice->user_id)->first();
            $user->purchases_points = $user->purchases_points + $awarded_points;
            $user->save(); 
            
            $order->order_points = $awarded_points;
            $order->save();
            }
       
        Toastr::success('Invoice generated!');
        return back();
    }
    
    public function view_invoice($branch_id, $order_id)
    {
        $invoice = Invoice::with('invoice_products')->where(['branch_id'=> $branch_id, 'order_id'=> $order_id])->first();
        return view('admin-views.order.invoice', compact('invoice'));
    }

    public function add_payment_ref_code(Request $request, $id)
    {
        Order::where(['id' => $id])->update([
            'transaction_reference' => $request['transaction_reference']
        ]);

        Toastr::success('Payment reference code is added!');
        return back();
    }
    
    public function update_order_details(Request $request, $id)
    {
        $variation = $request->variation;
        $quantity = $request->quantity;
        $modified_price = $request->modified_price;

        $orderDetail = OrderDetail::where('id', $id)->first();
        $old_price = ($orderDetail->price - $orderDetail->discount_on_product) * $orderDetail->quantity; 
        $mainProduct = Product::where('id', $orderDetail->product_id)->first();
        $mainVariations = json_decode($mainProduct->variations, true);

        if($request->variation!=0){
           $item = $mainVariations[$variation]; 
        }else {
            $item['price'] = $orderDetail->price;
        }
        
        $order = Order::where('id', $orderDetail->order_id)->first();
        $gift = json_decode($order->gift, true);
        
        $old_total_order_amount = $order->order_amount + $order->coupon_discount_amount - $order->delivery_charge;
        $total_minus_old_price = $old_total_order_amount - $old_price;
        
        $product_discount = Helpers::discount_calculate($mainProduct, $item['price']);
        $new_price = ($item['price'] - $product_discount) * $quantity;
        
        $new_total = $total_minus_old_price + $new_price;
        
       if(Coupon::where('code', $order->coupon_code)->first()){
          $coupon = Coupon::where('code', $order->coupon_code)->first();
        if($coupon->discount_type == 'amount'){
           $final_amount = $new_total - $coupon->discount;
           $coupon_discount = $coupon->discount;
        }else {
            $coupon_discount = ($coupon->discount * $new_total) / 100;
            $final_amount = $new_total - $coupon_discount;
        } 
       }
       if($gift!=null){
          if($gift['discount'] > 0){
           $gift_discount = ($gift['discount'] * $new_total) / 100;
           }
       }
       
        if($request->variation != 0){
            OrderDetail::where(['id' => $id])->update([
            'price' => $item['price'],
            'quantity' => $quantity,
            'variation' => [$mainVariations[$variation]]
        ]);
        }else {
            OrderDetail::where(['id' => $id])->update([
            'price' => $item['price'],
            'quantity' => $quantity,
        ]);
        }
        
        $new_price = $item['price'] * $quantity;
        
        if(Coupon::where('code', $order->coupon_code)->first()){
          $order->order_amount = $final_amount + $order->delivery_charge;
          $order->coupon_discount_amount = $coupon_discount;  
        }else {
        if($gift!=null){
         if($gift['discount'] > 0){
            $order->order_amount = $new_total - $gift['discount'] + $order->delivery_charge;
         }else {
           $order->order_amount = $new_total + $order->delivery_charge;  
         }
        }
        }
        
        $order->save();
        
        $orderDetail->discount_on_product = $product_discount;
        $orderDetail->modified_price = $modified_price;
        $orderDetail->branch_id = $request->branch_id;
        $orderDetail->save();
        Toastr::success('Order details updated!');

        return back();
    }

    public function delete_item($id)
    {
        $item = OrderDetail::where('id', $id)->first();
        
        $order = Order::where('id', $item->order_id)->first();
        $gift = json_decode($order->gift, true);
        $old_total_order_amount = $order->order_amount + $order->coupon_discount_amount - $order->delivery_charge;
        $old_price = ($item->price - $item->discount_on_product) * $item->quantity;
        $total_minus_old_price = $old_total_order_amount - $old_price;
        
        
        if(Coupon::where('code', $order->coupon_code)->first()){
          $coupon = Coupon::where('code', $order->coupon_code)->first();
        if($coupon->discount_type == 'amount'){
           $final_amount = $total_minus_old_price - $coupon->discount;
           $coupon_discount = $coupon->discount;
        }else {
            $coupon_discount = ($coupon->discount * $total_minus_old_price) / 100;
            $final_amount = $total_minus_old_price - $coupon_discount;
        } 
       }
       
     $gift_discount = 0.0;  
     if($gift != null){   
       if($gift['discount'] > 0){
           $gift_discount = ($gift['discount'] * $total_minus_old_price) / 100;
       }else {
          $gift_discount = 0.0; 
       }
     }
       
        if(Coupon::where('code', $order->coupon_code)->first()){
          $order->order_amount = $final_amount + $order->delivery_charge;
          $order->coupon_discount_amount = $coupon_discount;  
        }else {
          $order->order_amount = $total_minus_old_price + $order->delivery_charge - $gift_discount;
        }
        $order->save();
        $item->delete();
        Toastr::success('Item removed!');

        return back();
    }
    public function products_list(Request $request, $orderId)
    {
        $query_param = [];
        $search = $request['search'];
        if ($request->has('search')) {
            $key = explode(' ', $request['search']);
            $query = Product::where(function ($q) use ($key) {
                foreach ($key as $value) {
                    $q->orWhere('id', 'like', "%{$value}%")
                        ->orWhere('name', 'like', "%{$value}%");
                }
            })->latest();
            $query_param = ['search' => $request['search']];
        } else {
            $query = Product::latest();
        }
        $products = $query->paginate(25)->appends($query_param);
        return view('admin-views.order.products-list', compact('products', 'search', 'orderId'));
    }

    public function add_product(Request $request, $orderId, $productId)
    {
        $variationKey = $request->variation;
        // $product = Product::where('product_id', $productId)->first();

        $product = Product::find($productId);
        $variations = json_decode($product->variations);
        //  $price = $variations['price'];

        if ($variationKey > 0) {
            $variations2 = $variations[$variationKey - 1];
            $price = $variations2->price;
        } else {
            $variations2['type'] = null;
            $variations2['price'] = null;
            $price = $product->price;
        }

        $or_d = [
            'order_id' => $orderId,
            'product_id' => $productId,
            'product_details' => $product,
            'quantity' => 1,

            'price' => $price,
            'tax_amount' => Helpers::tax_calculate($product, $price),
            'discount_on_product' => Helpers::discount_calculate($product, $price),
            'discount_type' => 'discount_on_product',
            'variant' => 'null',
            'variation' => json_encode([$variations2]),
            'add_on_ids' => '[]',
            'add_on_qtys' => '[]',
            'created_at' => now(),
            'updated_at' => now()
        ];
        DB::table('order_details')->insert($or_d);
        $order = Order::where('id', $orderId)->first();
        $new_order_price = $order->order_amount - $order->delivery_charge + $order->coupon_discount_amount + ($price - Helpers::discount_calculate($product, $price));
       
        $gift = json_decode($order->gift, true);
        
        if(Coupon::where('code', $order->coupon_code)->first()){
          $coupon = Coupon::where('code', $order->coupon_code)->first();
        if($coupon->discount_type == 'amount'){
           $final_amount = $new_order_price - $coupon->discount;
           $coupon_discount = $coupon->discount;
        }else {
            $coupon_discount = ($coupon->discount * $new_order_price) / 100;
            $final_amount = $new_order_price - $coupon_discount;
        } 
       $order->order_amount = $final_amount;
       $order->coupon_discount_amount = $coupon_discount;
       $order->save();
       }else
       if($gift != null){   
       if($gift['discount'] > 0){
           $gift_discount = ($gift['discount'] * $new_order_price) / 100;
           $order->order_amount = $new_order_price - $gift_discount;
           $order->save();
       }
       }
        // dd($orderId, $productId, $price,$variationKey );
        // return view('admin-views.order-view', compact('products', 'search', 'orderId'));
        return redirect()->route('admin.orders.details', [$orderId]);
    }

    public function branch_filter($id)
    {
        session()->put('branch_filter', $id);
        return back();
    }
    
    public function delete_order($id)
    {
        $order = Order::find($id);
        $invoice = Invoice::where('order_id', $id)->first();
        DB::table('invoice_products')->where('booking_id', $invoice->id)->delete();
        
        DB::table('order_details')->where('order_id', $id)->delete();
        DB::table('invoices')->where('order_id', $id)->delete();
        $order->delete();

        Toastr::success('Booking deleted!');
        return back();
    }
}
