@extends('layouts.admin.app')

@section('title', 'Update User')

@push('css_or_js')

@endpush

@section('content')
    <div class="content container-fluid">
        <!-- Page Header -->
        <div class="page-header">
            <div class="row align-items-center">
                <div class="col-sm mb-2 mb-sm-0">
                    <h1 class="page-header-title"><i class="tio-edit"></i> {{trans('messages.update')}} {{trans('messages.user')}}</h1>
                </div>
            </div>
        </div>
        <!-- End Page Header -->
        <div class="row gx-2 gx-lg-3">
            <div class="col-sm-12 col-lg-12 mb-3 mb-lg-2">
                <form action="{{ route('admin.branch.update', [$branch['id']]) }}" method="post" enctype="multipart/form-data">
                    @csrf @method('put')
                    
                    <div class="row">
                        <div class="col-6">
                            <div class="form-group">
                                <label class="input-label" for="name">{{trans('messages.name')}}</label>
                                <input type="text" name="name" value="{{ $branch['name'] }}" class="form-control" placeholder="Enter name" required>
                            </div>
                        </div>
                        
                        <div class="col-6">
                            <div class="form-group">
                                <label class="input-label" for="email">{{trans('messages.email')}}</label>
                                <input type="email" name="email" value="{{ $branch['email'] }}" class="form-control" placeholder="Enter email" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-6">
                            <div class="form-group">
                                <label class="input-label" for="password">{{trans('messages.password')}}</label>
                                <input type="password" name="password" class="form-control" placeholder="Enter password" required>
                            </div>
                        </div>
                        
                        <div class="col-6">
                            <div class="form-group">
                                <label class="input-label" for="service_type">{{trans('messages.service_type')}}</label>
                                <input type="text" name="service_type" value="{{ $branch['service_type'] }}" class="form-control" placeholder="Enter service type" required>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-6">
                            <div class="form-group">
                                <label class="input-label" for="address">{{trans('messages.address')}}</label>
                                <input type="text" name="address" value="{{ $branch['address'] }}" class="form-control" placeholder="Enter address" required>
                            </div>
                        </div>
                        
                        <div class="col-6">
                            <div class="form-group">
                                <label class="input-label" for="coverage">{{trans('messages.coverage')}}</label>
                                <input type="text" name="coverage" value="{{ $branch['coverage'] }}" class="form-control" placeholder="Enter coverage" required>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-6">
                            <div class="form-group">
                                <label>{{trans('messages.branch')}} {{trans('messages.image')}}</label><small style="color: red">* ( {{trans('messages.ratio')}} 3:1 )</small>
                                <div class="custom-file">
                                    <input type="file" name="image" id="customFileEg1" class="custom-file-input"
                                           accept=".jpg, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*">
                                    <label class="custom-file-label" for="customFileEg1">{{trans('messages.choose')}} {{trans('messages.file')}}</label>
                                </div>
                                <hr>
                                <center>
                                    <img style="width: 80%;border: 1px solid; border-radius: 10px;" id="viewer"
                                         src="{{asset('storage/app/public/branch')}}/{{$branch['image']}}" alt="branch image"/>
                                </center>
                            </div>
                        </div>
                        
                    </div>

                    <hr>
                    <button type="submit" class="btn btn-primary">{{trans('messages.update')}}</button>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('script_2')
    <script>
        function readURL(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();

                reader.onload = function (e) {
                    $('#viewer').attr('src', e.target.result);
                }

                reader.readAsDataURL(input.files[0]);
            }
        }

        $("#customFileEg1").change(function () {
            readURL(this);
        });

        function show_item(type) {
            if (type === 'product') {
                $("#type-product").show();
                $("#type-category").hide();
            } else {
                $("#type-product").hide();
                $("#type-category").show();
            }
        }
    </script>
@endpush

