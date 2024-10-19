@extends('layouts.admin.app')

@section('title','Add new offer')

@push('css_or_js')

@endpush

@section('content')
<div class="content container-fluid">
    <!-- Page Header -->
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col-sm mb-2 mb-sm-0">
                <h1 class="page-header-title"><i class="tio-add-circle-outlined"></i> Add new offer</h1>
            </div>
        </div>
    </div>
    <!-- End Page Header -->
    <div class="row gx-2 gx-lg-3">
        <div class="col-sm-12 col-lg-12 mb-3 mb-lg-2">
            <form action="{{route('admin.offers.store')}}" method="post" enctype="multipart/form-data">
                @csrf
                <div class="row">
                    <div class="col-6">
                        <div class="form-group">
                            <label class="input-label" for="exampleFormControlInput1">{{trans('messages.name')}}</label>
                            <input type="text" name="name" class="form-control" placeholder="New offer" required>
                        </div>
                    </div>

                    <div class="col-6">
                        <div class="form-group" >
                            <label class="input-label">Category<span class="input-label-secondary"></span></label>
                            <select name="category_id" class="form-control js-select2-custom">
                                @foreach(\App\Model\Category::where('position', 1)->orderBy('name')->get() as $category)
                                <option value="{{$category['id']}}">{{$category['name']}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-6">
                        <div class="form-group">
                            <label class="input-label" for="offer_type">Offer Type</label>
                            <select name="offer_type" id="offer_type" class="form-control"  onchange="show_item(this.value)" required>
                                <option value="product">Product</option>
                                <option value="discount">Discount</option>
                            </select>
                        </div>
                    </div>
                </div>


                <div class="row">
                    <div class="col-6" id="type-discount" style="display: none">
                        <div class="form-group" >
                            <label class="input-label" for="discount">Discount %</label>
                            <input type="text" name="discount" class="form-control" placeholder="Discount">
                        </div>
                    </div>

                    <div class="col-6" id="type-product">
                        <div class="form-group" >
                            <label class="input-label">Offered Product<span class="input-label-secondary"></span></label>
                            <select name="product_id" class="form-control js-select2-custom" >
                                @foreach(\App\Model\Product::orderBy('name')->get() as $product)
                                <option value="{{$product['id']}}">{{$product['name']}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="col-6" id="type-product-quantity">
                        <div class="form-group" >
                            <label class="input-label" for="offered_product_quantity">Offered Product Quantity</label>
                            <input type="number" name="offered_product_quantity" class="form-control" placeholder="Offered Product Quantity">
                        </div>
                    </div>
                </div>
                
                <button type="submit" class="btn btn-primary">{{trans('messages.submit')}}</button>
            </form>
        </div>

        <div class="col-sm-12 col-lg-12 mb-3 mb-lg-2">
            <hr>
            <div class="card">
                <div class="card-header">
                    <h5 class="card-header-title"></h5>
                </div>
                <!-- Table -->
                <div class="table-responsive datatable-custom">
                    <table id="columnSearchDatatable" class="table table-borderless table-thead-bordered table-nowrap table-align-middle card-table" data-hs-datatables-options='{
                                 "order": [],
                                 "orderCellsTop": true
                               }'>
                        <thead class="thead-light">
                            <tr>
                                <th>{{trans('messages.#')}}</th>
                                <th style="width: 50%">{{trans('messages.name')}}</th>
                                <th style="width: 50%">{{trans('messages.action')}}</th>
                            </tr>
                            <tr>
                                <th></th>
                                <th>
                                    <input type="text" id="column1_search" class="form-control form-control-sm" placeholder="Search offer">
                                </th>

                                <th>
                                    {{--<input type="text" id="column4_search" class="form-control form-control-sm"
                                           placeholder="Search countries">--}}
                                </th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach($offers as $key=>$offer)
                            <tr>
                                <td>{{$key+1}}</td>
                                <td>
                                    <span class="d-block font-size-sm text-body">
                                        {{$offer['name']}}
                                    </span>
                                </td>

                                <td>
                                    <!-- Dropdown -->
                                    <div class="dropdown">
                                        <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <i class="tio-settings"></i>
                                        </button>
                                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">

                                            <a class="dropdown-item" href="{{route('admin.offers.edit',[$offer['id']])}}">Edit offer</a>
                                            <a class="dropdown-item" href="javascript:" onclick="form_alert('offer-{{$offer['id']}}','Want to delete this offer ?')">{{trans('messages.delete')}}</a>
                                            <form action="{{route('admin.offers.delete',[$offer['id']])}}" method="post" id="offer-{{$offer['id']}}">
                                                @csrf @method('delete')
                                            </form>
                                        </div>
                                    </div>
                                    <!-- End Dropdown -->
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <hr>
                    <table>
                        <tfoot>
                            {!! $offers->links() !!}
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
        <!-- End Table -->
    </div>
</div>

@endsection

@push('script_2')
<script>
    $(document).on('ready', function() {
        // INITIALIZATION OF DATATABLES
        // =======================================================
        var datatable = $.HSCore.components.HSDatatables.init($('#columnSearchDatatable'));

        $('#column1_search').on('keyup', function() {
            datatable
                .columns(1)
                .search(this.value)
                .draw();
        });


        $('#column3_search').on('change', function() {
            datatable
                .columns(2)
                .search(this.value)
                .draw();
        });

    });
</script>
@endpush

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