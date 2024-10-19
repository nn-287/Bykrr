@extends('layouts.merchant.app')

@section('title', 'Dashboard')

@section('content')
    <div class="content container-fluid">
        <!-- Page Header -->
        <div class="page-header">
            <div class="row align-items-center">
                <div class="col-sm">
                    <h1 class="page-header-title">{{ trans('messages.welcome') }}, {{ auth('merchant')->user()->f_name }}.</h1>
                    <p class="page-header-text">{{ trans('messages.welcome_message') }}</p>
                </div>
            </div>
        </div>
        <!-- End Page Header -->

        <!-- Quick Actions -->
        <div class="row">
            <div class="col-sm-6 col-lg-3">
                <a href="{{ route('merchant.orders.list', ['pending']) }}" class="card">
                    <div class="card-body">
                        <h5 class="card-title">{{ trans('messages.pending_orders') }}</h5>
                        <p class="card-text">{{ \App\Model\Order::where('order_status', 'pending')->count() }}</p>
                    </div>
                </a>
            </div>
            <!-- Add more quick action cards here -->
        </div>
        <!-- End Quick Actions -->
    </div>
@endsection
