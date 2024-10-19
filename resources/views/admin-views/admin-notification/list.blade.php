@extends('layouts.admin.app')

@section('title','Notifications list')

@push('css_or_js')

@endpush

@section('content')
<div class="content container-fluid">
    <!-- Page Header -->
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col-sm mb-2 mb-sm-0">
                <h1 class="page-header-title">Notifications</h1>
            </div>
        </div>
    </div>
    <!-- End Page Header -->
    <div class="row gx-2 gx-lg-3">

        <div class="col-sm-12 col-lg-12 mb-3 mb-lg-2">
            <hr>
            <div class="card">
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
                                    <input id="datatableSearch_" type="search" name="search" class="form-control" placeholder="Search" aria-label="Search" value="{{$search}}" required>
                                    <button type="submit" class="btn btn-primary">search</button>

                                </div>
                                <!-- End Search -->
                            </form>
                        </div>
                    </div>
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
                                <th style="width: 50%">Title</th>
                                <th style="width: 20%">Status</th>
                                <th style="width: 20">Created at</th>
                                <th style="width: 10%">{{trans('messages.action')}}</th>
                            </tr>

                        </thead>

                        <tbody>
                            @foreach($notifications as $key=>$notification)
                           
                            <tr>
                                <td>{{$key+1}}</td>
                                <td>
                                    <span class="d-block font-size-sm text-body">
                                        {{$notification->title}}
                                    </span>
                                </td>

                                <td>

                                    @if($notification->checked == 1)
                                    <span class="d-block font-size-sm text-body">
                                        <b style="color:green">Checked</b>
                                    </span>
                                    @else
                                    <span class="d-block font-size-sm text-body">
                                        <b style="color:red">Not checked</b>
                                    </span>
                                    @endif
                                </td>

                                <td>
                                    <span class="d-block font-size-sm text-body">
                                        {{$notification->created_at}}
                                    </span>
                                </td>

                                <td>
                                    <!-- Dropdown -->
                                    <div class="dropdown">
                                        <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <i class="tio-settings"></i>
                                        </button>

                                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                            <a class="dropdown-item" href="{{route('admin.admin-notification.view',[$notification['id'], $notification['category']])}}">View</a>

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
                            {!! $notifications->links() !!}
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


        // INITIALIZATION OF SELECT2
        // =======================================================
        $('.js-select2-custom').each(function() {
            var select2 = $.HSCore.components.HSSelect2.init($(this));
        });
    });
</script>
@endpush

@push('script_2')
<script>
    function readURL(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();

            reader.onload = function(e) {
                $('#viewer').attr('src', e.target.result);
            }

            reader.readAsDataURL(input.files[0]);
        }
    }

    $("#customFileEg1").change(function() {
        readURL(this);
    });
</script>
@endpush
