<?php

namespace App\Http\Controllers\Api\V1;

use App\CentralLogics\CartLogic;
use App\CentralLogics\Helpers;
use App\CentralLogics\LocationHelper;
use App\CentralLogics\OrderLogic;
use App\Http\Controllers\Controller;
use App\Model\BusinessSetting;
use App\Model\Order;
use App\Model\OrderDetail;
use App\Model\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\User;
use App\Model\AdminNotification;
use App\Model\DeliveryMan;
use App\Model\Zone;
use MatanYadaev\EloquentSpatial\Objects\Point;

class OrderController extends Controller
{
    public function track_order(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'order_id' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        return response()->json(OrderLogic::track_order($request['order_id']), 200);
    }


    public function get_zone(Request $request)
    {
         $POINT_SRID = 0; // For MariaDB use 4326
         $point = new Point($request->latitude, $request->longitude, $POINT_SRID);
     
        $zone = Zone::active()->whereContains('coordinates', $point)->first();
  
        return response()->json($zone, 200);
    }



    public function place_order(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'order_amount' => 'required',
            'order_type' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }


        try {
            $order = new Order();
            $order->user_id = $request->user()->id;
            $order->order_amount = $request['order_amount'];
            $order->coupon_discount_amount = $request['coupon_discount_amount'];
            $order->coupon_discount_title =  $request['coupon_discount_title'] == 0 ? null : 'coupon_discount_title';
            $order->payment_status = 'unpaid';
            $order->order_status = 'pending';
            $order->coupon_code = $request['coupon_code'];
            $order->payment_method = $request['payment_method'];
            $order->transaction_reference = null;
            $order->order_note = $request['order_note'];

            $order->order_type = $request['order_type'];
            $order->delivery_address_id = $request['delivery_address_id'];
            $order->delivery_charge = BusinessSetting::where(['key' => 'delivery_charge'])->first()->value;
            if($request['zone_id'] != null){
                $order->zone_id = $request['zone_id'];
                $order->delivery_fee = $request['delivery_fee'];
            }
            $order->created_at = now();
            $order->updated_at = now();
            $order->save();

            $o_id = $order->id;

            foreach ($request['cart'] as $c) {

                $product = Product::find($c['product_id']);

                // if (count(json_decode($product['variations'],true)) > 0) {
                //     $price = Helpers::variation_price($product, json_encode($c['variation']));
                // } else {
                //     $price = $product['price'];
                // }

                $price = Helpers::variation_price($product, json_encode($c['variation']));

                $or_d = [
                    'order_id' => $o_id,
                    'product_id' => $c['product_id'],
                    'product_details' => $product,
                    'quantity' => $c['quantity'],
                    'price' => $c['price'],
                    'tax_amount' => Helpers::tax_calculate($product, $price),
                    'discount_on_product' => $c['discount_amount'],
                    'discount_type' => 'discount_on_product',
                    'variant' => json_encode($c['variant']),
                    'variation' => json_encode($c['variation']),
                    'created_at' => now(),
                    'updated_at' => now()
                ];

                DB::table('order_details')->insert($or_d);
            }

            CartLogic::empty_cart($request->user()->id);
            // for admin
            $osama = User::where('id', 1)->first();
            $my_fcm_token = $osama->cm_firebase_token;
            // end admin 

            $new_notification = new AdminNotification();
            $new_notification->title = 'New Order';
            $new_notification->category = 'order';
            $new_notification->created_at = now();
            $new_notification->updated_at =  now();
            $new_notification->save();

            $fcm_token = $request->user()->cm_firebase_token;
            $value = Helpers::order_status_update_message('pending');
            try {
                if ($value) {
                    $data = [
                        'title' => 'Order',
                        'description' => $value,
                        'order_id' => $o_id,
                        'image' => '',
                    ];

                    Helpers::send_push_notif_to_device($fcm_token, $data);
                }
            } catch (\Exception $e) {
            }

            try {

                $data = [
                    'title' => 'Order',
                    'description' => 'New Order',
                    'image' => '',
                ];

                Helpers::send_notif_to_device_new($my_fcm_token, $data);
            } catch (\Exception $e) {
            }


            // $user = User::where('id', $request->user()->id)->first();
            // $user->purchases_points = $user->purchases_points + $points_value;
            // $user->save();
            return response()->json([
                'message' => 'Order placed successfully!',
                'order_id' => $o_id,
                //'points_awarded' => $points_value
            ], 200);

            /*Mail::to($email)->send(new \App\Mail\OrderPlaced($o_id));*/
        } catch (\Exception $e) {
            return response()->json([$e], 403);
        }
    }

    //Jerushan
    public function get_order_list_details(Request $request)
    {
        $orders = Order::with(['customer', 'delivery_address', 'delivery_man.rating'])
            ->withCount('details')
            ->where(['user_id' => $request->user()->id])
            ->where(function ($query) use ($request) {
                if ($request->order_status == 'delivered') {
                    $query->whereIn('order_status', ['delivered']);
                } else $query->whereNotIn('order_status', ['delivered']);
            })
            ->orderBy('id', 'DESC')
            ->paginate();

        $orders->getCollection()->transform(function ($order) {
            $orderDetails = OrderDetail::where(['order_id' => $order->id])->get();
            if ($orderDetails->count() > 0) {
                foreach ($orderDetails as $det) {

                    $det['variation'] = json_decode($det['variation'], true);
                    $det['product_details'] = Helpers::product_data_formatting(json_decode($det['product_details'], true));
                }
            }

            $order->details_count = (int)$order->details_count;
            $order->details = $orderDetails;
            return $order;
        });

        return response()->json($orders, 200);
    }

    public function get_order_list(Request $request)
    { /// old part, works for old versions (below 35)

        $orders = Order::with(['customer', 'delivery_man.rating'])->withCount('details')->where(['user_id' => $request->user()->id])->get();
        return $orders;
        return response()->json($orders->map(function ($data) {
            $data->details_count = (int)$data->details_count;
            return $data;
        }), 200);
    }


    public function get_running_orders(Request $request)
    {
        $status_list = ['pending', 'confirmed', 'processing', 'out_for_delivery'];
        $paginator = Order::with(['customer', 'delivery_man.rating'])->withCount('details')
        ->where(['user_id' => $request->user()->id])->whereIn('order_status', $status_list)->latest()
            ->paginate(20, ['*'], 'page', $request['offset']);

        $orders = $paginator->items();

        $paginator = [
            'total_size' => $paginator->total(),
            'limit' => 20,
            'offset' => $request['offset'],
            'orders' => $orders

        ];
        return $paginator;
    }

    public function get_history_orders(Request $request)
    {
        $status_list = ['delivered', 'returned', 'failed', 'canceled'];
        $paginator = Order::with(['customer', 'delivery_man.rating'])->withCount('details')
        ->where(['user_id' => $request->user()->id])->whereIn('order_status', $status_list)->latest()
            ->paginate(20, ['*'], 'page', $request['offset']);

        $orders = $paginator->items();

        $paginator = [
            'total_size' => $paginator->total(),
            'limit' => 20,
            'offset' => $request['offset'],
            'orders' => $orders

        ];
        return $paginator;
    }


    public function get_order_details(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'order_id' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        $details = OrderDetail::where(['order_id' => $request['order_id']])->get();
        if ($details->count() > 0) {
            foreach ($details as $det) {

                $det['variation'] = json_decode($det['variation']);
                $det['product_details'] = Helpers::product_data_formatting(json_decode($det['product_details'], true));
            }
            return response()->json($details, 200);
        } else {
            return response()->json([
                'errors' => [
                    ['code' => 'order', 'message' => 'not found!']
                ]
            ], 401);
        }
    }

    public function cancel_order(Request $request)
    {
        if (Order::where(['user_id' => $request->user()->id, 'id' => $request['order_id']])->first()) {
            Order::where(['user_id' => $request->user()->id, 'id' => $request['order_id']])->update([
                'order_status' => 'canceled'
            ]);
            return response()->json(['message' => 'Order canceled'], 200);
        }
        return response()->json([
            'errors' => [
                ['code' => 'order', 'message' => 'not found!']
            ]
        ], 401);
    }

    public function update_payment_method(Request $request)
    {
        if (Order::where(['user_id' => $request->user()->id, 'id' => $request['order_id']])->first()) {
            Order::where(['user_id' => $request->user()->id, 'id' => $request['order_id']])->update([
                'payment_method' => $request['payment_method']
            ]);
            return response()->json(['message' => 'Payment method is updated.'], 200);
        }
        return response()->json([
            'errors' => [
                ['code' => 'order', 'message' => 'not found!']
            ]
        ], 401);
    }

public function getNearbyDeliveryMen(Request $request)
{
    $validator = Validator::make($request->all(), [
        'latitude' => 'required',
        'longitude' => 'required',
    ]);

    if ($validator->fails()) {
        return response()->json(['errors' => Helpers::error_processor($validator)], 403);
    }
    $drivers = LocationHelper::nearby_drivers($request->latitude, $request->longitude, $request->service_id);

    return response()->json($drivers, 200);
}
}
