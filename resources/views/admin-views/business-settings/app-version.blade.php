@extends('layouts.admin.app')

@section('title','Settings')

@push('css_or_js')

@endpush

@section('content')
    <div class="content container-fluid">
    @php($branch_count=\App\Model\Branch::count())
    <!-- Page Header -->
        <div class="page-header">
            <div class="row align-items-center">
                <div class="col-sm mb-2 mb-sm-0">
                    <h1 class="page-header-title">App current version</h1>
                    <span class="badge badge-soft-danger" style="text-align: left">
                      
                    </span>
                </div>
            </div>
        </div>
        <!-- End Page Header -->
        <div class="row gx-2 gx-lg-3">
            <div class="col-sm-12 col-lg-12 mb-3 mb-lg-2">
                <form action="{{env('APP_MODE')!='demo'?route('admin.business-settings.force-update'):'javascript:'}}" method="post"
                      enctype="multipart/form-data">
                    @csrf
   
                    <div class="row">
                      @php($app_version=\App\Model\BusinessSetting::where('key','app_version')->first()->value)
                        <div class="col-md-4 col-12">
                            <div class="form-group">
                                <label class="input-label" for="exampleFormControlInput1">Version number: {{$app_version}}</label>
                              
                            </div>
                        </div>

                        
                    </div>
                    <hr>
                    <button type="submit" class="btn btn-primary">Force users to update</button>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('script_2')

@endpush