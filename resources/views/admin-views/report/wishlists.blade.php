
@extends('layouts.admin.app')


@section('title','Product Wishlists')

@push('css_or_js')

@endpush

@section('content')
    <div  class="content container-fluid">
        <!-- Page Header -->
        <div class="page-header">
            <div class="row align-items-center">
                <div class="col-sm mb-2 mb-sm-0">
                    <h1 class="page-header-title"><i class="tio-filter-list"></i>Wishlists</h1>
                </div>
            </div>
        </div>
        @if($latest == 0)
        <span class="d-block font-size-sm text-body">
            <a style="white-space:nowrap; padding-bottom:15px;
            
            display: -webkit-box;
                overflow: hidden;
                max-width:200;
                text-overflow: ellipsis;" 
            href="{{route('admin.report.wishlists',[1])}}">
            Sort By Latest
            </a>
        </span>
        @elseif($latest == 1)
        <span class="d-block font-size-sm text-body">
            <a style="white-space: nowrap; padding-bottom:15px;
            
            display: -webkit-box;
                overflow: hidden;
                max-width:200;
                text-overflow: ellipsis;" 
            href="{{route('admin.report.wishlists',[0])}}">
            Sort By Top Products
            </a>
        </span>
        @endif
        <!-- End Page Header -->
        <div class="row gx-2 gx-lg-3">
            <div class="col-sm-12 col-lg-12 mb-3 mb-lg-2">
                <!-- Card -->
                <div class="card">
                    <!-- Header -->
                    <div class="card-header">
                        <div class="row" style="width: 100%">
                            <div class="col-8 mb-3 mb-lg-0">
                                <form action="{{url()->current()}}" method="GET">
                                    <!-- Search -->
                                    <div class="input-group input-group-merge input-group-flush">
                                        <div class="input-group-prepend">
                                            <div class="input-group-text">
                                                <i class="tio-search"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- End Search -->
                                </form>
                            </div>
                        
                        </div>
                    </div>
                    <!-- End Header -->
          
                    <!-- Table -->
                    <div class="table-responsive datatable-custom">
                        <table id="columnSearchDatatable"
                            class="table table-borderless table-thead-bordered table-nowrap table-align-middle card-table"
                            data-hs-datatables-options='{
                                "order": [],
                                "orderCellsTop": true}'>
                            <thead class="thead-light">
                            <tr>
                                <th>#sl</th>
                                <th style="width: 30%">Name</th>
                                <th style="width: 25%">Image</th>
                                <th>Count</th>
                                <th>Price</th>
                                <th>Action</th>
                            </tr>
                            
                            </thead>

                            <tbody>
                            @foreach($wishlists as $key=>$wishlist)
                                <tr>
                                    <td>{{$key+1}}</td>
                                    @php($product=\App\Model\Product::where('id', $wishlist->product_id)->first())
                                    <td>
                                        <span class="d-block font-size-sm text-body">
                                             <a style="white-space: nowrap;
                                             display: -webkit-box;
                                                 overflow: hidden;
                                                  max-width:200;
                                                 text-overflow: ellipsis;" 
                                               href="{{route('admin.product.view',[$product['id']])}}">
                                               {{$product['name']}}
                                             </a>
                                        </span>
                                    </td>
                                    <td>
                                        <div style="height: 100px; width: 100px; overflow-x: hidden;overflow-y: hidden">
                                            <img src="{{asset('storage/app/public/product')}}/{{$product['image']}}" style="width: 100px"
                                                 onerror="this.src='{{asset('public/assets/admin/img/160x160/img2.jpg')}}'">
                                        </div>
                                    </td>
                                   
                                    
                                    <td>{{$wishlist['count']}}</td>
                                    <td>{{$product['price']." ".\App\CentralLogics\Helpers::currency_symbol()}}</td>
                                    <td>
                                        <!-- Dropdown -->
                                        <div class="dropdown">
                                            <button class="btn btn-secondary dropdown-toggle" type="button"
                                                    id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true"
                                                    aria-expanded="false">
                                                <i class="tio-settings"></i>
                                            </button>
                                            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                                <a class="dropdown-item"
                                                   href="{{route('admin.product.edit',[$product['id']])}}">Edit</a>
                                                <a class="dropdown-item" href="javascript:"
                                                   onclick="form_alert('product-{{$product['id']}}','Want to delete this item ?')">Delete</a>
                                                
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
                            <tfoot class="border-top">
                            {!! $wishlists->links() !!}
                            </tfoot>
                        </table>
                    </div>
                    <!-- End Table -->
                </div>
                <!-- End Card -->
            </div>
        </div>
    </div>

@endsection

@push('script_2')
    <script>
        $(document).on('ready', function () {
            // INITIALIZATION OF DATATABLES
            // =======================================================
            var datatable = $.HSCore.components.HSDatatables.init($('#columnSearchDatatable'));

            $('#column1_search').on('keyup', function () {
                datatable
                    .columns(1)
                    .search(this.value)
                    .draw();
            });

            $('#column2_search').on('keyup', function () {
                datatable
                    .columns(2)
                    .search(this.value)
                    .draw();
            });

            $('#column3_search').on('change', function () {
                datatable
                    .columns(3)
                    .search(this.value)
                    .draw();
            });

            $('#column4_search').on('keyup', function () {
                datatable
                    .columns(4)
                    .search(this.value)
                    .draw();
            });


            // INITIALIZATION OF SELECT2
            // =======================================================
            $('.js-select2-custom').each(function () {
                var select2 = $.HSCore.components.HSSelect2.init($(this));
            });
        });
    </script>
@endpush