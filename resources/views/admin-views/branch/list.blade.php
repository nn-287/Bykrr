@extends('layouts.admin.app')

@section('title','Add New Branch')

@push('css_or_js')

@endpush

@section('content')
<div class="content container-fluid">
    <!-- Page Header -->
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col-sm mb-2 mb-sm-0">
                <h1 class="page-header-title"><i class="tio-add-circle-outlined"></i> Branches list</h1>
                <a href="{{ route('admin.branch.add-new') }}">{{trans('messages.add')}} {{trans('messages.new')}} {{trans('messages.branch')}}</a>
            </div>
        </div>
    </div>
    <!-- End Page Header -->
    <div class="row gx-2 gx-lg-3">
        

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
                            <th scope="col">#</th>
                            <th scope="col">Name</th>
                            <th scope="col">Status</th>
                            <th scope="col">Actions</th>  
                            </tr>
                            <tr>
                                <th></th>
                                <th>
                                    <input type="text" id="column1_search" class="form-control form-control-sm" placeholder="Search branch">
                                </th>
                                <th>
                                    <select id="column3_search" class="js-select2-custom" data-hs-select2-options='{
                                              "minimumResultsForSearch": "Infinity",
                                              "customClass": "custom-select custom-select-sm text-capitalize"
                                            }'>
                                        <option value="">{{trans('messages.any')}}</option>
                                        <option value="Active">{{trans('messages.active')}}</option>
                                        <option value="Disabled">{{trans('messages.disabled')}}</option>
                                    </select>
                                </th>
                                <th>
                                    {{--<input type="text" id="column4_search" class="form-control form-control-sm"
                                           placeholder="Search countries">--}}
                                </th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach($branches as $key=>$branch)
                            <tr>
                                <td>{{$key+1}}</td>
                                <td>
                                    <span class="d-block font-size-sm text-body">
                                        {{$branch['name']}}
                                    </span>
                                </td>
                                <td>
                                    <form action="{{ route('admin.branch.status', [$branch['id']]) }}" method="post">
                                        @csrf
                                        <button type="submit" style="padding: 10px;border: 1px solid;cursor: pointer">
                                            <span class="legend-indicator bg-{{ $branch['status'] == 1 ? 'success' : 'danger' }}"></span>
                                            {{ $branch['status'] == 1 ? trans('messages.active') : trans('messages.disabled') }}
                                        </button>
                                    </form>
                                </td>
                                <td>
                                    <!-- Dropdown -->
                                    <div class="dropdown">
                                        <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <i class="tio-settings"></i>
                                        </button>
                                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                            <a class="dropdown-item" href="{{route('admin.branch.edit',[$branch['id']])}}">{{trans('messages.edit')}}</a>

                                            <a class="dropdown-item" href="javascript:" onclick="form_alert('branch-{{$branch['id']}}','Want to delete this branch')">{{trans('messages.delete')}}</a>

                                            <form action="{{route('admin.branch.delete',[$branch['id']])}}" method="post" id="branch-{{$branch['id']}}">
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
                            {!! $branches->links() !!}
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