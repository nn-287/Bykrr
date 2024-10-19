@extends('layouts.admin.app')

@section('title', 'Create New Branch')

@push('css_or_js')
    
@endpush

@section('content')
    <div class="content container-fluid">
        <!-- Page Header -->
        <div class="page-header">
            <div class="row align-items-center">
                <div class="col-sm mb-2 mb-sm-0">
                    <h1 class="page-header-title"><i class="tio-add-circle-outlined"></i> Create New Branch</h1>
                </div>
            </div>
        </div>
       
        <div class="row">
            <div class="col-md-6">
                <form action="{{ route('admin.branch.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group">
                        <label for="name">Name</label>
                        <input type="text" class="form-control" id="name" name="name" >
                    </div>
                   
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" class="form-control" id="email" name="email" >
                    </div>
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" class="form-control" id="password" name="password" >
                    </div>

                    <div class="form-group">
                        <label for="service_type">Service Type</label>
                        <input type="text" class="form-control" id="service_type" name="service_type" >
                    </div>

                    <div class="form-group">
                        <label for="address">Address</label>
                        <input type="text" class="form-control" id="address" name="address" >
                    </div>

                    <div class="form-group">
                        <label for="coverage">Coverage</label>
                        <input type="text" class="form-control" id="coverage" name="coverage" >
                    </div>

                    <div class="form-group">
                        <label for="image">Image</label>
                        <input type="file" class="form-control" id="image" name="image" >
                    </div>

                    

                    <button type="submit" class="btn btn-primary">Create Branch</button>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('script')


    
@endpush
