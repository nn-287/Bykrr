@extends('layouts.admin.app')

@section('title','Loyality & Detention Coupons')

@push('css_or_js')

@endpush

@section('content')
<div class="content container-fluid">
    <!-- Page Header -->
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col-sm mb-2 mb-sm-0">
                <h1 class="page-header-title">Loyality & Detention Coupons</h1>
            </div>
        </div>
    </div>
    <!-- End Page Header -->
    <div class="row gx-2 gx-lg-3">
        <div class="col-sm-12 col-lg-12 mb-3 mb-lg-2">
            <form action="{{route('admin.coupon.custom.update')}}" method="post" enctype="multipart/form-data">
                @csrf
                <h1 class="page-header-title">Loyality Coupon</h1>
                <div class="row">
                    <div class="col-6">
                        <div class="form-group">
                            <label class="input-label">Discount percentage</label>
                            <input type="text" name="loyality_discount" class="form-control" value="{{$loyality_coupon['loyality_discount']}}" placeholder="Discount" required>
                        </div>
                    </div>

                    <div class="col-6">
                        <div class="form-group">
                            <label class="input-label">Orders Number</label>
                            <input type="text" name="loyality_orders_number" class="form-control" value="{{$loyality_coupon['loyality_orders_number']}}" placeholder="Orders Number" required>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-6">
                        <div class="form-group">
                            <label class="input-label" for="offer_type">Status</label>
                            <select name="loyality_status" class="form-control">
                                <option value="1" {{ ( $loyality_coupon['status'] ==1) ? 'selected' : '' }}>Active</option>
                                <option value="0" {{ ( $loyality_coupon['status'] ==0) ? 'selected' : '' }}>Not Active</option>
                            </select>
                        </div>
                    </div>
                </div>

                <hr>

                <h1 class="page-header-title">Detention Coupon</h1>
                <div class="row">
                    <div class="col-6">
                        <div class="form-group">
                            <label class="input-label">Discount percentage</label>
                            <input type="text" name="detention_discount" class="form-control" value="{{$detention_coupon['detention_discount']}}" placeholder="Discount" required>
                        </div>
                    </div>

                    <div class="col-6">
                        <div class="form-group">
                            <label class="input-label">Detention days number</label>
                            <input type="text" name="detention_days_number" class="form-control" value="{{$detention_coupon['detention_days_number']}}" placeholder="Detention Days Number" required>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-6">
                        <div class="form-group">
                            <label class="input-label" for="offer_type">Status</label>
                            <select name="detention_status" class="form-control">
                                <option value="1" {{ ( $detention_coupon['status'] ==1) ? 'selected' : '' }}>Active</option>
                                <option value="0" {{ ( $detention_coupon['status'] ==0) ? 'selected' : '' }}>Not Active</option>
                            </select>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">{{trans('messages.update')}}</button>
            </form>
        </div>

    </div>
</div>

@endsection



@push('script_2')
<script>
    function show_item(type) {
        if (type === 'product') {
            $("#type-product").show();
            $("#type-product-quantity").show();
            $("#type-discount").hide();
        } else {
            $("#type-product").hide();
            $("#type-product-quantity").hide();
            $("#type-discount").show();
        }
    }
</script>

<script>
    $(document).on('ready', function() {
        // INITIALIZATION OF SELECT2
        // =======================================================
        $('.js-select2-custom').each(function() {
            var select2 = $.HSCore.components.HSSelect2.init($(this));
        });
    });
</script>

@endpush