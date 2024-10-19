<div id="sidebarMain" class="d-none">
    <aside
        class="js-navbar-vertical-aside navbar navbar-vertical-aside navbar-vertical navbar-vertical-fixed navbar-expand-xl navbar-bordered  ">
        <div class="navbar-vertical-container">
            <div class="navbar-vertical-footer-offset">
                <div class="navbar-brand-wrapper justify-content-between">
                    <!-- Logo -->

                    @php($store_logo=\App\Model\BusinessSetting::where(['key'=>'logo'])->first()->value)
                    @php($branch = \App\Model\Branch::where(['id'=>auth('branch_admin')->user()->branch_id])->first())
                    <a class="navbar-brand" href="{{route('branch.admin-dashboard')}}" aria-label="Front">
                        <img class="navbar-brand-logo"
                             onerror="this.src='{{asset('public/assets/admin/img/160x160/img2.jpg')}}'"
                             src="{{asset('storage/app/public/store/'.$store_logo)}}"
                             alt="Logo">
                        <img class="navbar-brand-logo-mini"
                             onerror="this.src='{{asset('public/assets/admin/img/160x160/img2.jpg')}}'"
                             src="{{asset('storage/app/public/store/'.$store_logo)}}" alt="Logo">
                    </a>

                    <!-- End Logo -->

                    <!-- Navbar Vertical Toggle -->
                    <button type="button"
                            class="js-navbar-vertical-aside-toggle-invoker navbar-vertical-aside-toggle btn btn-icon btn-xs btn-ghost-dark">
                        <i class="tio-clear tio-lg"></i>
                    </button>
                    <!-- End Navbar Vertical Toggle -->
                </div>

                <!-- Content -->
                <div class="navbar-vertical-content text-capitalize">
                    <ul class="navbar-nav navbar-nav-lg nav-tabs">
                        <!-- Dashboards -->
                        <li class="navbar-vertical-aside-has-menu {{Request::is('branch')?'show':''}}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link"
                               href="{{route('branch.admin-dashboard')}}" title="Dashboards">
                                <i class="tio-home-vs-1-outlined nav-icon"></i>
                                <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                    {{trans('messages.dashboard')}}
                                </span>
                            </a>
                        </li>
                        <!-- End Dashboards -->

                      @if($branch->service_type == 'pharmacy')
                        <li class="nav-item">
                            <small class="nav-subtitle" title="Pages">{{trans('messages.order')}} {{trans('messages.section')}}</small>
                            <small class="tio-more-horizontal nav-subtitle-replacer"></small>
                        </li>

                        <!-- Pages -->
                        <li class="navbar-vertical-aside-has-menu {{Request::is('branch/orders*')?'active':''}}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link nav-link-toggle" href="javascript:"
                               title="Pages">
                                <i class="tio-shopping-cart nav-icon"></i>
                                <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                    {{trans('messages.order')}}
                                </span>
                            </a>
                            <ul class="js-navbar-vertical-aside-submenu nav nav-sub" style="display: none;">
                                <li class="nav-item {{Request::is('branch/orders/list/all')?'active':''}}">
                                    <a class="nav-link" href="{{route('branch.orders.list',['all'])}}" title="">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">
                                            {{trans('messages.all')}}
                                            <span class="badge badge-info badge-pill ml-1">
                                                {{\App\Model\Order::where(['branch_id'=>auth('branch_admin')->user()->branch_id])->count()}}
                                            </span>
                                        </span>
                                    </a>
                                </li>
                                <li class="nav-item {{Request::is('branch/orders/list/pending')?'active':''}}">
                                    <a class="nav-link " href="{{route('branch.orders.list',['pending'])}}" title="">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">
                                            {{trans('messages.pending')}}
                                            <span class="badge badge-soft-info badge-pill ml-1">
                                                {{\App\Model\Order::where(['order_status'=>'pending','branch_id'=>auth('branch_admin')->user()->branch_id])->count()}}
                                            </span>
                                        </span>
                                    </a>
                                </li>
                                <li class="nav-item {{Request::is('branch/orders/list/confirmed')?'active':''}}">
                                    <a class="nav-link " href="{{route('branch.orders.list',['confirmed'])}}" title="">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">
                                            {{trans('messages.confirmed')}}
                                                <span class="badge badge-soft-success badge-pill ml-1">
                                                {{\App\Model\Order::where(['order_status'=>'confirmed','branch_id'=>auth('branch_admin')->user()->branch_id])->count()}}
                                            </span>
                                        </span>
                                    </a>
                                </li>
                                <!--<li class="nav-item {{Request::is('branch/orders/list/processing')?'active':''}}">-->
                                <!--    <a class="nav-link " href="{{route('branch.orders.list',['processing'])}}" title="">-->
                                <!--        <span class="tio-circle nav-indicator-icon"></span>-->
                                <!--        <span class="text-truncate">-->
                                <!--            {{trans('messages.processing')}}-->
                                <!--                <span class="badge badge-warning badge-pill ml-1">-->
                                <!--                {{\App\Model\Order::where(['order_status'=>'processing','branch_id'=>auth('branch_admin')->user()->branch_id])->count()}}-->
                                <!--            </span>-->
                                <!--        </span>-->
                                <!--    </a>-->
                                <!--</li>-->
                                <li class="nav-item {{Request::is('branch/orders/list/out_for_delivery')?'active':''}}">
                                    <a class="nav-link " href="{{route('branch.orders.list',['out_for_delivery'])}}"
                                       title="">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">
                                            {{trans('messages.out_for_delivery')}}
                                                <span class="badge badge-warning badge-pill ml-1">
                                                {{\App\Model\Order::where(['order_status'=>'out_for_delivery','branch_id'=>auth('branch_admin')->user()->branch_id])->count()}}
                                            </span>
                                        </span>
                                    </a>
                                </li>
                                <li class="nav-item {{Request::is('branch/orders/list/delivered')?'active':''}}">
                                    <a class="nav-link " href="{{route('branch.orders.list',['delivered'])}}" title="">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">
                                            {{trans('messages.delivered')}}
                                                <span class="badge badge-success badge-pill ml-1">
                                                {{\App\Model\Order::where(['order_status'=>'delivered','branch_id'=>auth('branch_admin')->user()->branch_id])->count()}}
                                            </span>
                                        </span>
                                    </a>
                                </li>
                                <li class="nav-item {{Request::is('branch/orders/list/returned')?'active':''}}">
                                    <a class="nav-link " href="{{route('branch.orders.list',['returned'])}}" title="">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">
                                            {{trans('messages.returned')}}
                                                <span class="badge badge-soft-danger badge-pill ml-1">
                                                {{\App\Model\Order::where(['order_status'=>'returned','branch_id'=>auth('branch_admin')->user()->branch_id])->count()}}
                                            </span>
                                        </span>
                                    </a>
                                </li>
                                <li class="nav-item {{Request::is('branch/orders/list/failed')?'active':''}}">
                                    <a class="nav-link " href="{{route('branch.orders.list',['failed'])}}" title="">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">
                                            {{trans('messages.failed')}}
                                            <span class="badge badge-danger badge-pill ml-1">
                                                {{\App\Model\Order::where(['order_status'=>'failed','branch_id'=>auth('branch_admin')->user()->branch_id])->count()}}
                                            </span>
                                        </span>
                                    </a>
                                </li>

                                <li class="nav-item {{Request::is('branch/orders/list/canceled')?'active':''}}">
                                    <a class="nav-link " href="{{route('branch.orders.list',['canceled'])}}" title="">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">
                                            {{trans('messages.canceled')}}
                                                <span class="badge badge-soft-dark badge-pill ml-1">
                                                {{\App\Model\Order::where(['order_status'=>'canceled','branch_id'=>auth('branch_admin')->user()->branch_id])->count()}}
                                            </span>
                                        </span>
                                    </a>
                                </li>
                            </ul>
                        </li>
                        @endif
                        <!-- End Pages -->
                        
                         @if($branch->service_type == 'beauty_salon')
                        <!-- Bookings -->
                        <li class="nav-item">
                            <small class="nav-subtitle" title="Pages">Bookings</small>
                            <small class="tio-more-horizontal nav-subtitle-replacer"></small>
                        </li>
                        <!-- Pages -->
                        <li class="navbar-vertical-aside-has-menu {{Request::is('branch/booking*')?'active':''}}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link nav-link-toggle" href="javascript:"
                               title="Pages">
                                <i class="tio-print nav-icon"></i>
                                <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                    Bookings List
                                </span>
                            </a>
                           <ul class="js-navbar-vertical-aside-submenu nav nav-sub {{ Request::is('branch/booking/list*') ? 'show' : '' }}"
                                style="display: {{Request::is('branch/booking/list*')?'block':'none'}}">
                                <li class="nav-item {{Request::is('branch/booking/list/all')?'active':''}}">
                                    <a class="nav-link" href="{{route('branch.booking.list', ['status' => 'all'])}}" title="">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">
                                            {{trans('messages.all')}}
                                            <span class="badge badge-info badge-pill ml-1">
                                                {{\App\Model\Booking::where(['branch_id'=>auth('branch_admin')->user()->branch_id])->count()}}
                                            </span>
                                        </span>
                                    </a>
                                    
                                    <a class="nav-link " href="{{route('branch.booking.list',['pending'])}}" title="">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">
                                            {{trans('messages.pending')}}
                                            <span class="badge badge-soft-info badge-pill ml-1">
                                                {{\App\Model\Booking::where(['branch_id'=>auth('branch_admin')->user()->branch_id, 'status' => 'pending'])->count()}}
                                            </span>
                                        </span>
                                    </a>
                                    
                                    <a class="nav-link " href="{{route('branch.booking.list',['confirmed'])}}" title="">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">
                                            {{trans('messages.confirmed')}}
                                                <span class="badge badge-soft-success badge-pill ml-1">
                                                {{\App\Model\Booking::where(['branch_id'=>auth('branch_admin')->user()->branch_id, 'status' => 'confirmed'])->count()}}
                                            </span>
                                        </span>
                                    </a>
                                    
                                    <a class="nav-link " href="{{route('branch.booking.list',['finished'])}}" title="">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">
                                            Finished
                                                <span class="badge badge-success badge-pill ml-1">
                                                {{\App\Model\Booking::where(['branch_id'=>auth('branch_admin')->user()->branch_id, 'status' => 'finished'])->count()}}
                                            </span>
                                        </span>
                                    </a>
                                    
                                    <a class="nav-link " href="{{route('branch.booking.list',['canceled'])}}" title="">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">
                                            {{trans('messages.canceled')}}
                                                <span class="badge badge-soft-dark badge-pill ml-1">
                                                {{\App\Model\Booking::where(['branch_id'=>auth('branch_admin')->user()->branch_id, 'status' => 'canceled'])->count()}}
                                            </span>
                                        </span>
                                    </a>
                                </li>
                            </ul>
                        </li>
                        @endif

                         <!-- End Pages -->
                         <!-- End Bookings -->
                         
                        <li class="nav-item">
                            <small class="nav-subtitle" title="Pages">Invoices</small>
                            <small class="tio-more-horizontal nav-subtitle-replacer"></small>
                        </li>
                        
                        <!-- Pages -->
                        <li class="navbar-vertical-aside-has-menu {{Request::is('branch/invoices*')?'active':''}}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link nav-link-toggle" href="javascript:"
                               title="Pages">
                                <i class="tio-print nav-icon"></i>
                                <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                    Invoices
                                </span>
                            </a>
                            <ul class="js-navbar-vertical-aside-submenu nav nav-sub"
                               style="display: none;">
                                <li class="nav-item {{Request::is('branch/invoices')?'active':''}}">
                                    <a class="nav-link" href="{{route('branch.invoices.invoices-list')}}" title="">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">
                                            {{trans('messages.all')}}
                                            <span class="badge badge-info badge-pill ml-1">
                                                {{\App\Model\Invoice::where(['branch_id'=>auth('branch_admin')->user()->branch_id])->count()}}
                                            </span>
                                        </span>
                                    </a>
                                </li>
                            </ul>
                        </li>
                      
                        
                         <li class="nav-item">
                            <small class="nav-subtitle" title="Pages">Services</small>
                            <small class="tio-more-horizontal nav-subtitle-replacer"></small>
                        </li>
                        
                        <!-- Pages -->
                        <li class="navbar-vertical-aside-has-menu {{Request::is('branch/invoices*')?'active':''}}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link nav-link-toggle" href="javascript:"
                               title="Pages">
                                <i class="tio-settings nav-icon"></i>
                                <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                    Services
                                </span>
                            </a>
                            <ul class="js-navbar-vertical-aside-submenu nav nav-sub"
                                style="display: {{Request::is('branch*')?'display':'none'}}">
                                <li class="nav-item {{Request::is('branch/services/add-new')?'active':''}}">
                                    <a class="nav-link" href="{{route('branch.services.add-new')}}" title="">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">
                                          Add Service
                                        </span>
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <!-- End Pages -->
                    </ul>
                </div>
                <!-- End Content -->
            </div>
        </div>
    </aside>
</div>

<div id="sidebarCompact" class="d-none">

</div>


{{--<script>
    $(document).ready(function () {
        $('.navbar-vertical-content').animate({
            scrollTop: $('#scroll-here').offset().top
        }, 'slow');
    });
</script>--}}
