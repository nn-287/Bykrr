@extends('layouts.admin.app')

@section('title','')

@push('css_or_js')
    <style>
        @media print {
            .non-printable {
                display: none;
            }

            .printable {
                display: block;
                font-family: emoji !important;
            }

            body {
                -webkit-print-color-adjust: exact !important; /* Chrome, Safari */
                color-adjust: exact !important;
                font-family: emoji !important;
            }
        }
    </style>

    <style type="text/css" media="print">
        @page {
            size: auto;   /* auto is the initial value */
            margin: 2px;  /* this affects the margin in the printer settings */
            font-family: emoji !important;
        }

    </style>
@endpush

@section('content')

    <div class="content container-fluid">
        <div class="row" id="printableArea" style="font-family: emoji;">
            <div class="col-md-12">
                <center>
                    <input type="button" class="btn btn-primary non-printable" onclick="printDiv('printableArea')"
                           value="Proceed, If thermal printer is ready."/>
                    <a href="{{url()->previous()}}" class="btn btn-danger non-printable">Back</a>
                </center>
                <hr class="non-printable">
            </div>
            <div class="col-5">
                <div class="text-center pt-4 mb-3">
                    <h2 style="line-height: 1">Bykr</h2>
                    
                    <h5 style="font-size: 20px;font-weight: lighter;line-height: 1">
                        {{\App\Model\BusinessSetting::where(['key'=>'address'])->first()->value}}
                    </h5>
                    <h5 style="font-size: 16px;font-weight: lighter;line-height: 1">
                        Phone : {{\App\Model\BusinessSetting::where(['key'=>'phone'])->first()->value}}
                    </h5>
                </div>

                <span>---------------------------------------------------------------------------------</span>
                <div class="row mt-3">
                    <div class="col-6">
                        @if($invoice != null && $invoice['order_id'] != null)
                        <h5>Order ID : {{$invoice['order_id']}}</h5>
                        @else
                         <h5>Booking ID : {{$invoice['booking_id']}}</h5>
                         @endif
                    </div>
                    <div class="col-6">
                        <h5 style="font-weight: lighter">
                            {{date('d/M/Y h:m a',strtotime($invoice['created_at']))}}
                        </h5>
                    </div>
                    <div class="col-12">
                        @php($customer = App\User::where('id', $invoice->user_id)->first())   
                        <h5>
                            Customer Name : {{$customer['f_name'].' '.$customer['l_name']}}
                        </h5>
                        <h5>
                            Phone : {{$customer['phone']}}
                        </h5>
                        @if($invoice['order_id'] != null)
                        @php($order=\App\Model\Order::where('id', $invoice['order_id'])->first())
                        @php($address=\App\Model\CustomerAddress::find($order['delivery_address_id']))
                        <h5>
                            Address : {{isset($address)?$address['address']:''}}
                        </h5>
                        @endif
                    </div>
                </div>
                <h5 class="text-uppercase"></h5>
                <span>---------------------------------------------------------------------------------</span>
                <table class="table table-bordered mt-3" style="width: 98%">
                    <thead>
                    <tr>
                        <th style="width: 10%">QTY</th>
                        <th class="">DESC</th>
                        <th class="">Price</th>
                    </tr>
                    </thead>

                    <tbody>
                    @php($sub_total=0)
                    @php($items_discount=0)
                  
                    @foreach($invoice->invoice_products as $product)
                            <tr>
                                <td class="">
                                    {{$product['quantity']}}
                                </td>
                                <td class="">
                                    {{$product->name}} <br>
                                    @if($product->variation_type != null)
                                                    <strong><u>{{trans('messages.variation')}} : </u></strong>
                                                    <div class="font-size-sm text-body">
                                                            <span class="font-weight-bold">{{$product->variation_type}}</span>
                                                        </div>
                                     @endif
                                </td>
                                <td style="width: 28%">
                                    {{$product->price." ".\App\CentralLogics\Helpers::currency_symbol()}}
                                </td>
                            </tr>
                            
                            @php($items_discount+=($product->discount)* $product->quantity)
                            @php($sub_total+=($product->price)* $product->quantity)
                            
                    @endforeach
                    </tbody>
                </table>
                <span>---------------------------------------------------------------------------------</span>
                 
                <div class="row justify-content-md-end mb-3" style="width: 97%">
                    <div class="col-md-7 col-lg-7">
                        <dl class="row text-right">
                            <!--<dt class="col-6">Subtotal:</dt>-->
                            <!--<dd class="col-6">{{$sub_total." ".\App\CentralLogics\Helpers::currency_symbol()}}</dd>-->
                            <dt class="col-6">Items price:</dt>
                            <dd class="col-6">{{$sub_total." ".\App\CentralLogics\Helpers::currency_symbol()}}</dd>
                            
                            <dt class="col-6"></dt>
                            <dd class="col-6">
                                
                                <hr>
                            </dd>

                            <dt class="col-6">Items disc:</dt>
                            <dd class="col-6">
                                {{$items_discount." ".\App\CentralLogics\Helpers::currency_symbol()}}</dd>

                            <dt class="col-6" style="font-size: 20px">Total:</dt>
                            <dd class="col-6" style="font-size: 20px">{{$sub_total-$items_discount." ".\App\CentralLogics\Helpers::currency_symbol()}}</dd>
                        </dl>
                    </div>
                </div>
                <span>---------------------------------------------------------------------------------</span>
                <h5 class="text-center pt-3">
                    """THANK YOU"""
                </h5>
                <span>---------------------------------------------------------------------------------</span>
            </div>
        </div>
    </div>

@endsection

@push('script')
    <script>
        function printDiv(divName) {
            var printContents = document.getElementById(divName).innerHTML;
            var originalContents = document.body.innerHTML;
            document.body.innerHTML = printContents;
            window.print();
            document.body.innerHTML = originalContents;
        }
    </script>
@endpush
