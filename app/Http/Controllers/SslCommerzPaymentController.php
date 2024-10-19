<?php

namespace App\Http\Controllers;

use App\CentralLogics\Helpers;
use App\Library\SslCommerz\SslCommerzNotification;
use App\Model\Order;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SslCommerzPaymentController extends Controller
{

    public function index(Request $request)
    {

        $order = Order::with(['details'])->where(['id' => session('order_id')])->first();
        $tr_ref = Str::random(6) . '-' . rand(1, 1000);

        $post_data = array();
        $post_data['total_amount'] = $order->order_amount;
        $post_data['currency'] = Helpers::currency_code();
        $post_data['tran_id'] = $tr_ref;

        # CUSTOMER INFORMATION
        $post_data['cus_name'] = $order->customer['f_name'];
        $post_data['cus_email'] = $order->customer['email'] == null ? "example@example.com" : $order->customer['email'];
        $post_data['cus_add1'] = 'Customer Address';
        $post_data['cus_add2'] = "";
        $post_data['cus_city'] = "";
        $post_data['cus_state'] = "";
        $post_data['cus_postcode'] = "";
        $post_data['cus_country'] = "Bangladesh";
        $post_data['cus_phone'] = $order->customer['phone'] == null ? '0000000000' : $order->customer['phone'];
        $post_data['cus_fax'] = "";

        # SHIPMENT INFORMATION
        $post_data['ship_name'] = "Shipping";
        $post_data['ship_add1'] = "address 1";
        $post_data['ship_add2'] = "address 2";
        $post_data['ship_city'] = "City";
        $post_data['ship_state'] = "State";
        $post_data['ship_postcode'] = "ZIP";
        $post_data['ship_phone'] = "";
        $post_data['ship_country'] = "Country";

        $post_data['shipping_method'] = "NO";
        $post_data['product_name'] = "Computer";
        $post_data['product_category'] = "Goods";
        $post_data['product_profile'] = "physical-goods";

        # OPTIONAL PARAMETERS
        $post_data['value_a'] = "ref001";
        $post_data['value_b'] = "ref002";
        $post_data['value_c'] = "ref003";
        $post_data['value_d'] = "ref004";

        $update_product = DB::table('orders')
            ->where('id', $order['id'])
            ->update([
                'transaction_reference' => $tr_ref,
                'payment_method' => session('payment_method'),
                'updated_at' => now(),
            ]);

        $sslc = new SslCommerzNotification();
        # initiate(Transaction Data , false: Redirect to SSLCOMMERZ gateway/ true: Show all the Payement gateway here )
        $payment_options = $sslc->makePayment($post_data, 'hosted');

        if (!is_array($payment_options)) {
            print_r($payment_options);
            $payment_options = array();
        }

    }

    public function success(Request $request)
    {
        $tran_id = $request->input('tran_id');
        $amount = $request->input('amount');
        $currency = $request->input('currency');
        $sslc = new SslCommerzNotification();

        #Check order status in order tabel against the transaction id or order id.
        $order_detials = DB::table('orders')
            ->where('transaction_reference', $tran_id)
            ->select('id', 'transaction_reference', 'order_status', 'order_amount', 'callback')->first();

        try {
            $order = Order::where('transaction_reference', $tran_id)->first();
            $fcm_token = $order->customer->cm_firebase_token;
            $value = Helpers::order_status_update_message('confirmed');
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

        if ($order_detials->order_status == 'pending' || $order_detials->order_status == 'failed' || $order_detials->order_status == 'canceled') {
            $validation = $sslc->orderValidate($tran_id, $amount, $currency, $request->all());
            if ($validation == TRUE) {
                DB::table('orders')
                    ->where('transaction_reference', $tran_id)
                    ->update(['order_status' => 'confirmed', 'payment_status' => 'paid']);

                if ($order_detials->callback != null) {
                    return redirect($order_detials->callback . '/success');
                }
                return \redirect()->route('payment-success');

            } else {
                DB::table('orders')
                    ->where('transaction_reference', $tran_id)
                    ->update(['order_status' => 'pending', 'payment_status' => 'unpaid']);
                if ($order_detials->callback != null) {
                    return redirect($order_detials->callback . '/fail');
                }
                return \redirect()->route('payment-fail');
            }
        } else if ($order_detials->order_status == 'confirmed' || $order_detials->order_status == 'complete') {
            if ($order_detials->callback != null) {
                return redirect($order_detials->callback . '/success');
            }
            return \redirect()->route('payment-success');
        } else {
            if ($order_detials->callback != null) {
                return redirect($order_detials->callback . '/fail');
            }
            return \redirect()->route('payment-fail');
        }
    }

    public function fail(Request $request)
    {
        $tran_id = $request->input('tran_id');
        DB::table('orders')
            ->where('transaction_reference', $tran_id)
            ->update(['order_status' => 'pending', 'payment_status' => 'unpaid']);

        $order_detials = DB::table('orders')
            ->where('transaction_reference', $tran_id)
            ->select('id', 'transaction_reference', 'order_status', 'order_amount', 'callback')->first();

        if ($order_detials->callback != null) {
            return redirect($order_detials->callback . '/fail');
        }
        return \redirect()->route('payment-fail');
    }

    public function cancel(Request $request)
    {
        $tran_id = $request->input('tran_id');
        DB::table('orders')
            ->where('transaction_reference', $tran_id)
            ->update(['order_status' => 'canceled', 'payment_status' => 'unpaid']);

        $order_detials = DB::table('orders')
            ->where('transaction_reference', $tran_id)
            ->select('id', 'transaction_reference', 'order_status', 'order_amount', 'callback')->first();

        if ($order_detials->callback != null) {
            return redirect($order_detials->callback . '/cancel');
        }
        return \redirect()->route('payment-cancel');
    }

    public function ipn(Request $request)
    {
        #Received all the payement information from the gateway
        if ($request->input('tran_id')) #Check transation id is posted or not.
        {
            $tran_id = $request->input('tran_id');
            #Check order status in order tabel against the transaction id or order id.
            $order_details = DB::table('orders')
                ->where('transaction_reference', $tran_id)
                ->select('transaction_reference', 'order_status', 'order_amount')->first();

            if ($order_details->order_status == 'pending') {
                $sslc = new SslCommerzNotification();
                $validation = $sslc->orderValidate($tran_id, $order_details->order_amount, 'BDT', $request->all());
                if ($validation == TRUE) {
                    /*
                    That means IPN worked. Here you need to update order status
                    in order table as confirmed or Complete.
                    Here you can also sent sms or email for successful transaction to customer
                    */
                    $update_product = DB::table('orders')
                        ->where('transaction_reference', $tran_id)
                        ->update(['order_status' => 'confirmed', 'payment_status' => 'paid']);

                    echo "Transaction is successfully completed";
                } else {
                    /*
                    That means IPN worked, but Transation validation failed.
                    Here you need to update order status as Failed in order table.
                    */
                    $update_product = DB::table('orders')
                        ->where('transaction_reference', $tran_id)
                        ->update(['order_status' => 'confirmed', 'payment_status' => 'unpaid']);

                    echo "validation Fail";
                }

            } else if ($order_details->order_status == 'confirmed' || $order_details->order_status == 'complete') {

                #That means Order status already updated. No need to udate database.

                echo "Transaction is already successfully completed";
            } else {
                #That means something wrong happened. You can redirect customer to your product page.

                echo "Invalid Transaction";
            }
        } else {
            echo "Invalid Data";
        }
    }

}
