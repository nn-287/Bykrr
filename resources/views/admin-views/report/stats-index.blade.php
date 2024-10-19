@extends('layouts.admin.app')

@section('title','Statistics')

@push('css_or_js')

@endpush

@section('content')
    <div class="content container-fluid">
        <!-- Page Header -->
        <div class="page-header">
            <div class="media mb-3">
                <!-- Avatar -->
                <div class="avatar avatar-xl avatar-4by3 mr-2">
                    <img class="avatar-img" src="{{asset('public/assets/admin')}}/svg/illustrations/earnings.png"
                         alt="Image Description">
                </div>
                <!-- End Avatar -->

                <div class="media-body">
                    <div class="row">
                        <div class="col-lg mb-3 mb-lg-0 text-capitalize">
                            <h1 class="page-header-title">Statistics Overview</h1>

                            <div class="row align-items-center">
                                <div class="col-auto">
                                    <span>{{trans('messages.admin')}}:</span>
                                    <a href="#">{{auth('admin')->user()->f_name.' '.auth('admin')->user()->l_name}}</a>
                                </div>

                                <div class="col-auto">
                                    <div class="row align-items-center g-0">
                                        <div class="col-auto pr-2">{{trans('messages.date')}}</div>

                                        <!-- Flatpickr -->
                                        <div>
                                            ( {{session('from_date')}} - {{session('to_date')}} )
                                        </div>
                                        <!-- End Flatpickr -->
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-auto">
                            <div class="d-flex">
                                <a class="btn btn-icon btn-primary rounded-circle" href="{{route('admin.dashboard')}}">
                                    <i class="tio-home-outlined"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- End Media -->

            <!-- Nav -->
            <!-- Nav -->
            <div class="js-nav-scroller hs-nav-scroller-horizontal">
            <span class="hs-nav-scroller-arrow-prev" style="display: none;">
              <a class="hs-nav-scroller-arrow-link" href="javascript:;">
                <i class="tio-chevron-left"></i>
              </a>
            </span>

                <span class="hs-nav-scroller-arrow-next" style="display: none;">
              <a class="hs-nav-scroller-arrow-link" href="javascript:;">
                <i class="tio-chevron-right"></i>
              </a>
            </span>

                <ul class="nav nav-tabs page-header-tabs" id="projectsTab" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" href="javascript:">{{trans('messages.overview')}}</a>
                    </li>
                </ul>
            </div>
            <!-- End Nav -->
        </div>
        <!-- End Page Header -->

        <div class="row border-bottom border-right border-left border-top" style="border-radius: 10px">
            <div class="col-lg-12 pt-3">
                <form action="{{route('admin.report.set-date')}}" method="post">
                    @csrf
                    <div class="row">
                        <div class="col-12">
                            <div class="mb-3">
                                <label for="exampleInputEmail1" class="form-label">{{trans('messages.show')}} {{trans('messages.data')}} by {{trans('messages.date')}}
                                    {{trans('messages.range')}}</label>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="mb-3">
                                <input type="date" name="from" id="from_date"
                                       class="form-control" required>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="mb-3">
                                <input type="date" name="to" id="to_date"
                                       class="form-control" required>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="mb-3">
                                <button type="submit" class="btn btn-primary btn-block">{{trans('messages.show')}}</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            @php
                $from = session('from_date');
               $to = session('to_date');
               $total_users=\App\User::whereBetween('created_at', [$from, $to])
               ->count();
               $today_users = \App\User::whereDate('created_at', '=', $today)
               ->count();
               $yestedray_users = \App\User::whereDate('created_at', '=', $yesterday)
               ->count();
               $age_under_20 = \App\User::where('age' , '<', 20)->where('age', '!=', 0)->count();
               $age_20_to_25 = \App\User::whereBetween('age' , [20, 25])->where('age', '!=', 0)->count();
               $age_25_to_30 = \App\User::whereBetween('age' , [25, 30])->where('age', '!=', 0)->count();
               $age_30_to_35 = \App\User::whereBetween('age' , [30, 35])->where('age', '!=', 0)->count();
               $age_35_to_40 = \App\User::whereBetween('age' , [35, 40])->where('age', '!=', 0)->count();
               $over_40 = \App\User::where('age' , '>', 40)->where('age', '!=', 0)->count();
              
               $weight_loss_goal = \App\Model\NutritionModel::where('weight_goal' , 1)->count();
               $weight_keep_goal = \App\Model\NutritionModel::where('weight_goal' , 2)->count();
               $weight_gain_goal = \App\Model\NutritionModel::where('weight_goal' , 3)->count();
               $total_nutrition_models = \App\Model\NutritionModel::count();
                
               $all_aged_users = \App\User::where('age', '!=', 0)->count();

            @endphp
            <div class="col-sm-6 col-lg-6 mb-3 mb-lg-6">

            <!-- Card -->
                <div class="card card-sm">
                    <div class="card-body">
                        <div class="row">
                            <div class="col">
                                <!-- Media -->
                                <div class="media">
                                    <i class="tio-dollar-outlined nav-icon"></i>

                                    <div class="media-body">
                                        <h4 class="mb-1">Total Users</h4>
                                        <span class="font-size-sm text-success">
                                          <i class="tio-trending-up"></i> {{$total_users}}
                                        </span>
                                    </div>

                                </div>
                                <!-- End Media -->
                             
                            </div>

                        </div>
                        <!-- End Row -->
                    </div>
                </div>
                <!-- End Card -->
                <hr>
                 <!-- Card -->
               
                <div class="card card-sm">
                    <div class="card-body">
                        <div class="row">
                            <div class="col">
             
                                <div class="media">
                                    <div class="media-body">
                                        <h4 class="mb-1">Today Users</h4>
                                        <span class="font-size-sm text-success">
                                          <i class="tio-trending-up"></i> {{$today_users}}
                                        </span>
                                    </div>
                                </div>
                          
                            </div>
                            <div class="col">
                                 <div class="media">
                                    <div class="media-body">
                                        <h4 class="mb-1">Yesterday Users</h4>
                                        <span class="font-size-sm text-success">
                                          <i class="tio-trending-up"></i> {{$yestedray_users}}
                                        </span>
                                    </div>
                                </div>
                                <!-- End Media -->
                            </div>
                        </div>
                        <!-- End Row -->
                    </div>
                </div>
                <!-- End Card -->
            </div>
            
            <div class="col-sm-6 col-lg-6 mb-3 mb-lg-6">

                <div class="card card-sm">
                    <div class="card-body">
                        <div class="row">
                            <div class="col">
          
                                <div class="media">
                                    <i class="tio-dollar-outlined nav-icon"></i>

                                    <div class="media-body">
                                        <h4 class="mb-1">Total Users</h4>
                                        <span class="font-size-sm text-success">
                                          <i class="tio-trending-up"></i> {{$total_users}}
                                        </span>
                                    </div>

                                </div>
             
                            </div>

                        </div>
                      
                    </div>
                </div>
               <hr>
          
               
                 <!--End Card -->
            </div>
            
            <div class="col-sm-6 col-lg-6 mb-3 mb-lg-6">

                <div class="card card-sm">
                    <div class="card-body">
                        <div class="row">
                            <div class="col">
                                <div class="media">
                                    <div class="media-body">
                                        <h4 class="mb-1">Weigth Loss</h4>
                                        <span class="font-size-sm text-success">
                                         {{$weight_loss_goal}}
                                         <p>{{ (int)(($weight_loss_goal / $total_nutrition_models) * 100)}} %</p>
                                        </span>
                                    </div>

                                </div>
             
                            </div>
                           <div class="col">
                                <div class="media">
                                    <div class="media-body">
                                        <h4 class="mb-1">Keep Weight</h4>
                                        <span class="font-size-sm text-success">
                                         {{$weight_keep_goal}}
                                         <p>{{ (int)(($weight_keep_goal / $total_nutrition_models) * 100)}} %</p>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="col">
                                <div class="media">
                                    <div class="media-body">
                                        <h4 class="mb-1">Weight Gain</h4>
                                        <span class="font-size-sm text-success">
                                         {{$weight_gain_goal}}
                                         <p>{{ (int)(($weight_gain_goal / $total_nutrition_models) * 100)}} %</p>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
               <hr>
          
                 <div class="card card-sm">
                    <div class="card-body">
                        <div class="row">
                            <div class="col">
                                <div class="media">
                                    <div class="media-body">
                                        <h4 class="mb-1">Under 20</h4>
                                        <span class="font-size-sm text-success">
                                           {{$age_under_20}} <p>{{(int)(($age_under_20 / $all_aged_users) * 100)}}%</p>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="col">
                                 <div class="media">
                                    <div class="media-body">
                                        <h4 class="mb-1">20 to 25</h4>
                                        <span class="font-size-sm text-success">
                                           {{$age_20_to_25}} <p>{{(int)(($age_20_to_25 / $all_aged_users) * 100)}}%</p>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="col">
                                 <div class="media">
                                    <div class="media-body">
                                        <h4 class="mb-1">25 to 30</h4>
                                        <span class="font-size-sm text-success">
                                           {{$age_25_to_30}} <p>{{(int)(($age_25_to_30 / $all_aged_users) * 100)}}%</p>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="col">
                                 <div class="media">
                                    <div class="media-body">
                                        <h4 class="mb-1">30 to 35</h4>
                                        <span class="font-size-sm text-success">
                                         {{$age_30_to_35}} <p>{{(int)(($age_30_to_35 / $all_aged_users) * 100)}}%</p>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                 <!--End Card -->
            </div>
            
            
          
        </div>
        <!-- End Stats -->
        <hr>
        <!-- Card -->

    </div>
@endsection

